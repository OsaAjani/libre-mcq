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

    global $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
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

function session_exists($session_id) {
    try {
        $pdo = get_database_connection();
        
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = :session_id");
        $stmt->execute(['session_id' => $session_id]);
        $session = $stmt->fetch();
        
        return (bool) $session;
        
    } catch (PDOException $e) {
        throw new Exception("Error while trying to check if session #$session_id exists : " . $e->getMessage());
    }
}

function get_session_results($session_id) {
    try {
        $pdo = get_database_connection();
        
        $stmt = $pdo->prepare("
            SELECT sessions.*, COUNT(warnings.id) as warning_count
            FROM sessions 
            LEFT JOIN warnings ON warnings.session_id = sessions.id
            WHERE sessions.id = :session_id
        ");
        $stmt->execute(['session_id' => $session_id]);
        $session = $stmt->fetch();
        
        if (!$session) {
            return null;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM answers WHERE session_id = :session_id ORDER BY question_id");
        $stmt->execute(['session_id' => $session_id]);
        $answers = $stmt->fetchAll();
        
        return [
            'session' => $session,
            'answers' => $answers
        ];
        
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des résultats : " . $e->getMessage());
    }
}

function get_mcq_sessions($mcq_id) {
    try {
        $pdo = get_database_connection();
        $stmt = $pdo->prepare("
                SELECT sessions.*, COUNT(w.id) as warning_count
                FROM sessions
                LEFT JOIN warnings w ON w.session_id = sessions.id
                WHERE sessions.mcq_id = :mcq_id
                AND status = 'completed'
                GROUP BY sessions.id
                ORDER BY sessions.end_time DESC
            ");
        $stmt->execute(['mcq_id' => $mcq_id]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des sessions : " . $e->getMessage());
    }
}

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
                WHERE mcq_id = :mcq_id AND status = 'completed'
            ");
        $stmt->execute(['mcq_id' => $mcq_id]);
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        throw new Exception("Erreur lors du calcul des statistiques : " . $e->getMessage());
    }
}

function cancel_mcq_session($session_id) {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("
            UPDATE sessions
            SET end_time = CURRENT_TIMESTAMP, status = 'canceled'
            WHERE id = :id
            AND status = 'in_progress'
        ");

        $stmt->execute(['id' => $session_id]);
    } catch (PDOException $e) {
        throw new Exception("Error while trying to cancel session #$session_id");
    }
}

function update_mcq_session($session_id, $score, $total_score, $percentage) {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("
            UPDATE sessions 
            SET end_time = CURRENT_TIMESTAMP, total_score = :score, max_score = :total_score, percentage = :percentage, status = 'completed'
            WHERE id = :session_id
            AND status = 'in_progress'
        ");
        
        $stmt->execute([
            'score' => $score,
            'total_score' => $total_score,
            'percentage' => $percentage,
            'session_id' => $session_id,
        ]);
        
        if ($stmt->rowCount() <= 0) {
            throw new Exception('No matching session to update');
        }
    } catch (PDOException $e) {
        throw new Exception("Error while trying to update session #$session_id : " . $e->getMessage());
    }
}

function save_mcq_answers($session_id, $mcq_data) {
    try {
        $pdo = get_database_connection();

        $stmt_answer = $pdo->prepare("
            INSERT INTO answers (session_id, question_id, question_type, student_answer, correct_answer, is_correct, points_earned, max_points, correction_needed) 
            VALUES (:session_id, :question_id, :question_type, :student_answer, :correct_answer, :is_correct, :points_earned, :max_points, :correction_needed)
        ");
        
        foreach ($mcq_data['questions'] as $question) {
            if (!isset($question['id'])) {
                continue;
            }
            
            $stmt_answer->execute([
                'session_id' => $session_id,
                'question_id' => $question['id'],
                'question_type' => $question['type'] ?? 'single_choice',
                'student_answer' => json_encode($question['user_answers'] ?? []),
                'correct_answer' => json_encode($question['correct_answers'] ?? []),
                'is_correct' => $question['is_correct'] ?? null,
                'points_earned' => ($question['is_correct'] ?? false) ? ($question['points'] ?? 1) : 0,
                'max_points' => $question['points'] ?? 1,
                'correction_needed' => boolval($question['correction_needed'] ?? false),
            ]);
        }
    } catch (PDOException $e) {
        throw new Exception("Error while trying to save answers for session #$session_id");
    }
}

function get_answer_by_id($answer_id) {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("SELECT * FROM answers WHERE id = :answer_id");
        $stmt->execute(['answer_id' => $answer_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        throw new Exception("Error while trying to get answer #$answer_id : " . $e->getMessage());
    }
}

function update_answer_correctness($answer_id, $is_correct) {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("
            UPDATE answers
            SET is_correct = :is_correct, correction_needed = 0
            WHERE id = :answer_id
        ");
        $stmt->execute(['is_correct' => $is_correct, 'answer_id' => $answer_id]);

        if ($is_correct) {
            $stmt = $pdo->prepare("
                UPDATE answers
                SET points_earned = max_points
                WHERE id = :answer_id
            ");
            $stmt->execute(['answer_id' => $answer_id]);

            $stmt = $pdo->prepare("
                UPDATE sessions
                SET total_score = total_score + (SELECT points_earned FROM answers WHERE id = :answer_id)
                , percentage = ((total_score + (SELECT points_earned FROM answers WHERE id = :answer_id)) * 100.0) / max_score
                WHERE id = (SELECT session_id FROM answers WHERE id = :answer_id)
            ");
            $stmt->execute(['answer_id' => $answer_id]);
        }
    } catch (PDOException $e) {
        throw new Exception("Error while trying to update answer #$answer_id correctness : " . $e->getMessage());
    }
}

function insert_warning($session_id, $warning_type) {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("
            INSERT INTO warnings (session_id, warning_type, created_at) 
            VALUES (:session_id, :warning_type, CURRENT_TIMESTAMP)
        ");

        $stmt->execute([
            'session_id' => $session_id,
            'warning_type' => $warning_type,
        ]);
    } catch (PDOException $e) {
        throw new Exception("Error while trying to insert warning for session #$session_id : " . $e->getMessage());
    }
}

function get_warnings() {
    try {
        $pdo = get_database_connection();

        $stmt = $pdo->prepare("
            SELECT warnings.*, sessions.id as session_id, sessions.student_name as student_name
            FROM warnings 
            JOIN sessions ON warnings.session_id = sessions.id
            ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        throw new Exception("Error while trying to get warnings : " . $e->getMessage());
    }
}


function create_new_mcq_session($mcq_id, $fullname) {
    $pdo = get_database_connection();
    $stmt = $pdo->prepare("
            INSERT INTO sessions (mcq_id, student_name) 
            VALUES (?, ?)
        ");
        
        $stmt->execute([
            $mcq_id,
            $fullname,
        ]);
    
    return $pdo->lastInsertId();
}