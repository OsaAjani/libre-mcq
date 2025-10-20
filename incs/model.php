<?php

/**
* Obtient une connexion PDO à la base de données SQLite
* 
* @param string $db_path Chemin vers le fichier de base de données
* @return PDO
*/
function get_database_connection($db_path = null) {
    if ($db_path === null) {
        $db_path = __DIR__ . '/../data/database.sqlite';
    }
    
    try {
        $pdo = new PDO("sqlite:$db_path");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

/**
 * Check if a session exists by his id
 */
function session_exists($session_id) {
    try {
        $pdo = get_database_connection();
        
        // Récupérer les informations de la session
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch();
        
        return (bool) $session;
        
    } catch (PDOException $e) {
        throw new Exception("Error while trying to check if session #$session_id exists : " . $e->getMessage());
    }
}

/**
* Récupère les résultats d'une session par son ID
* 
* @param int $session_id ID de la session
* @return array|null
*/
function get_session_results($session_id) {
    try {
        $pdo = get_database_connection();
        
        // Récupérer les informations de la session
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch();
        
        if (!$session) {
            return null;
        }
        
        // Récupérer les réponses de la session
        $stmt = $pdo->prepare("SELECT * FROM answers WHERE session_id = ? ORDER BY question_id");
        $stmt->execute([$session_id]);
        $answers = $stmt->fetchAll();
        
        return [
            'session' => $session,
            'answers' => $answers
        ];
        
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des résultats : " . $e->getMessage());
    }
}

/**
* Récupère toutes les sessions pour un MCQ donné
* 
* @param string $mcq_id ID du MCQ
* @return array
*/
function get_mcq_sessions($mcq_id) {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("
                SELECT id, student_name, start_time, end_time, total_score, max_score, percentage, status 
                FROM sessions 
                WHERE mcq_id = ? 
                AND status = 'completed'
                ORDER BY end_time DESC
            ");
        $stmt->execute([$mcq_id]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des sessions : " . $e->getMessage());
    }
}

/**
* Récupère les statistiques globales d'un MCQ
* 
* @param string $mcq_id ID du MCQ
* @return array
*/
function get_mcq_statistics($mcq_id) {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_sessions,
                    AVG(percentage) as average_percentage,
                    MIN(percentage) as min_percentage,
                    MAX(percentage) as max_percentage,
                    AVG(total_score) as average_score,
                    MIN(total_score) as min_score,
                    MAX(total_score) as max_score
                FROM sessions 
                WHERE mcq_id = ? AND status = 'completed'
            ");
        $stmt->execute([$mcq_id]);
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        throw new Exception("Erreur lors du calcul des statistiques : " . $e->getMessage());
    }
}

/**
 * Close in progress QCM sessions
 * 
 * @param int $session_id ID of QCM session to close
 */
function cancel_mcq_session($session_id) {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("
            UPDATE sessions
            SET end_time = CURRENT_TIMESTAMP, status = 'canceled'
            WHERE id = :id
            AND status = 'in_progress'
        ");

        $stmt->execute([
            'id' => $session_id,
        ]);
    } catch (PDOException $e) {
        throw new Exception("Error while trying to cancel session #$session_id");
    }
}

/**
 * Update mcq session to store score etc.
 */
function update_mcq_session($session_id, $score, $total_score, $percentage) {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("
            UPDATE sessions 
            SET end_time = CURRENT_TIMESTAMP, total_score = ?, max_score = ?, percentage = ?, status = 'completed'
            WHERE id = ?
            AND status = 'in_progress'
        ");
        
        $stmt->execute([
            $score,
            $total_score,
            $percentage,

            $session_id,
        ]);
        
        if ($stmt->rowCount() <= 0) {
            throw new Exception('No matching session to update');
        }
    } catch (PDOException $e) {
        throw new Exception("Error while trying to update session #$session_id : " . $e->getMessage());
    }
}

/**
 * Save answers for a QCM
 */
function save_mcq_answers($session_id, $mcq_data) {
    try {
        $pdo = get_database_connection();

        // Insérer les réponses individuelles
        $stmt_answer = $pdo->prepare("
            INSERT INTO answers (session_id, question_id, question_type, student_answer, correct_answer, is_correct, points_earned, max_points) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($mcq_data['questions'] as $question) {
            if (!isset($question['id'])) {
                continue;
            }
            
            $question_id = $question['id'];
            $student_answer = $question['user_answers'] ?? [];
            $correct_answers = $question['correct_answers'] ?? [];
            $is_correct = $question['is_correct'] ?? null;
            $question_type = $question['type'] ?? 'single_choice';
            $max_points = $question['points'] ?? 1;
            $points_earned = ($is_correct === true) ? $max_points : 0;
            
            // Convertir en JSON pour le stockage
            $student_answer_json = json_encode($student_answer);
            $correct_answer_json = json_encode($correct_answers);
            
            $stmt_answer->execute([
                $session_id,
                $question_id,
                $question_type,
                $student_answer_json,
                $correct_answer_json,
                $is_correct,
                $points_earned,
                $max_points
            ]);
        }
    } catch (PDOException $e) {
        throw new Exception("Error while trying to save answers for session #$session_id");
    }
}