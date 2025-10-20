<?php
require_once '../incs/functions.php';
require_once '../incs/model.php';


$session_id = intval($_GET['id'] ?? 0);

if ($session_id <= 0) {
    header('Location: index.php');
    exit;
}

// Récupérer les détails de la session
try {
    $session_data = get_session_results($session_id);
} catch (Exception $e) {
    header('Location: index.php?error=session_not_found');
    exit;
}

if (!$session_data) {
    header('Location: index.php?error=session_not_found');
    exit;
}

$session = $session_data['session'];
$answers = $session_data['answers'];

// Récupérer les données du QCM pour avoir les questions
$mcq_data = read_mcq_data("../data/" . $session['mcq_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Session #<?= $session_id ?> - Admin</title>
    <link rel="stylesheet" href="../assets/pico.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .session-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .answer-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .answer-correct {
            border-left: 5px solid #28a745;
            background: #f8fff9;
        }
        
        .answer-incorrect {
            border-left: 5px solid #dc3545;
            background: #fff8f8;
        }
        
        .answer-manual {
            border-left: 5px solid #ffc107;
            background: #fffef8;
        }
        
        .answer-content {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
        }
        
        .correct-answer {
            background: #d4edda;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
            border: 1px solid #c3e6cb;
        }
        
        .score-badge {
            background: #007bff;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .info-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="container">
        <header style="margin: 2rem 0;">
            <nav>
                <a href="mcq_sessions.php?id=<?= urlencode($session['mcq_id']) ?>">← Retour aux sessions</a>
                <span style="margin: 0 1rem;">|</span>
                <a href="index.php">🏠 Administration</a>
            </nav>
        </header>

        <div class="session-header">
            <div class="grid">
                <div>
                    <h1>📋 Session #<?= $session_id ?></h1>
                    <h2><?= htmlspecialchars($mcq_data['title'] ?? 'QCM') ?></h2>
                </div>
                <div style="text-align: right;">
                    <div class="score-badge" style="font-size: 1.2em;">
                        <?= $session['total_score'] ?>/<?= $session['max_score'] ?> 
                        (<?= number_format($session['percentage'], 1) ?>%)
                    </div>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <h3>👤 Étudiant</h3>
                <p><strong><?= htmlspecialchars($session['student_name']) ?></strong></p>
            </div>
            <div class="info-card">
                <h3>📅 Date de soumission</h3>
                <p><?= date('d/m/Y à H:i:s', strtotime($session['end_time'])) ?></p>
            </div>
            <div class="info-card">
                <h3>⏱️ Durée</h3>
                <p>
                    <?php
                    $start = new DateTime($session['start_time']);
                    $end = new DateTime($session['end_time']);
                    $duration = $start->diff($end);
                    echo $duration->format('%H:%I:%S');
                    ?>
                </p>
            </div>
            <div class="info-card">
                <h3>📊 Statut</h3>
                <p><?= ucfirst($session['status']) ?></p>
            </div>
        </div>

        <section>
            <h2>📝 Réponses détaillées</h2>
            
            <?php if (empty($answers)): ?>
                <article>
                    <p style="text-align: center; color: #6c757d;">
                        Aucune réponse enregistrée pour cette session.
                    </p>
                </article>
            <?php else: ?>
                <?php 
                $question_index = 1;
                foreach ($answers as $answer): 
                    $student_answers = json_decode($answer['student_answer'], true);
                    $correct_answers = json_decode($answer['correct_answer'], true);
                    
                    // Trouver la question correspondante dans le QCM
                    $question = null;
                    if ($mcq_data && isset($mcq_data['questions'])) {
                        foreach ($mcq_data['questions'] as $q) {
                            if ($q['id'] == $answer['question_id']) {
                                $question = $q;
                                break;
                            }
                        }
                    }
                    
                    $css_class = 'answer-manual';
                    if ($answer['is_correct'] == 1) $css_class = 'answer-correct';
                    elseif ($answer['is_correct'] == 0) $css_class = 'answer-incorrect';
                ?>
                    <article class="answer-card <?= $css_class ?>">
                        <header>
                            <div class="grid">
                                <div>
                                    <h3>Question <?= $question_index++ ?></h3>
                                    <?php if ($question): ?>
                                        <p><strong><?= htmlspecialchars($question['question']) ?></strong></p>
                                    <?php else: ?>
                                        <p><em>Question ID: <?= htmlspecialchars($answer['question_id']) ?></em></p>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: right;">
                                    <span class="score-badge">
                                        <?= $answer['points_earned'] ?>/<?= $answer['max_points'] ?> pts
                                    </span>
                                </div>
                            </div>
                        </header>
                        
                        <!-- Réponse de l'étudiant -->
                        <div class="answer-content">
                            <h4>👤 Réponse de l'étudiant:</h4>
                            <?php if (empty($student_answers) || (is_array($student_answers) && empty($student_answers[0]))): ?>
                                <p><em style="color: #dc3545;">Aucune réponse fournie</em></p>
                            <?php else: ?>
                                <?php if ($answer['question_type'] === 'open'): ?>
                                    <div style="background: white; padding: 1rem; border-radius: 4px; border: 1px solid #dee2e6;">
                                        <?= nl2br(htmlspecialchars(is_array($student_answers) ? ($student_answers[0] ?? '') : $student_answers)) ?>
                                    </div>
                                <?php else: ?>
                                    <ul style="margin: 0.5rem 0;">
                                        <?php 
                                        $answers_array = is_array($student_answers) ? $student_answers : [$student_answers];
                                        foreach ($answers_array as $ans): 
                                            if (!empty($ans)):
                                        ?>
                                            <li>
                                                <strong><?= htmlspecialchars($ans) ?>)</strong>
                                                <?php if ($question && isset($question['options'][$ans])): ?>
                                                    <?= htmlspecialchars($question['options'][$ans]) ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </ul>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Correction -->
                        <?php if (!empty($correct_answers) && $answer['question_type'] !== 'open'): ?>
                            <div class="correct-answer">
                                <h4>✅ Réponse(s) correcte(s):</h4>
                                <ul style="margin: 0.5rem 0;">
                                    <?php foreach ($correct_answers as $correct): ?>
                                        <li>
                                            <strong><?= htmlspecialchars($correct) ?>)</strong>
                                            <?php if ($question && isset($question['options'][$correct])): ?>
                                                <?= htmlspecialchars($question['options'][$correct]) ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php elseif ($answer['question_type'] === 'open'): ?>
                            <div class="correct-answer">
                                <h4>⚠️ Correction manuelle nécessaire</h4>
                                <p>Cette question ouverte nécessite une évaluation manuelle.</p>
                                <?php if ($question && !empty($question['answer'])): ?>
                                    <details>
                                        <summary>Voir un exemple de réponse</summary>
                                        <div style="background: white; padding: 1rem; border-radius: 4px; margin-top: 0.5rem;">
                                            <?= nl2br(htmlspecialchars($question['answer'])) ?>
                                        </div>
                                    </details>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <footer style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #dee2e6; font-size: 0.9em; color: #6c757d;">
                            Type: <?= ucfirst($answer['question_type']) ?> • 
                            Soumis le: <?= date('d/m/Y H:i:s', strtotime($answer['created_at'])) ?>
                        </footer>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section style="margin-top: 3rem; text-align: center;">
            <div class="grid">
                <button onclick="window.print()" role="button" class="outline">
                    🖨️ Imprimer
                </button>
                <a href="mcq_sessions.php?id=<?= urlencode($session['mcq_id']) ?>" role="button" class="outline">
                    📊 Retour aux sessions
                </a>
            </div>
        </section>
    </main>

    <style>
        @media print {
            nav, button, .outline {
                display: none !important;
            }
            
            .answer-card {
                page-break-inside: avoid;
            }
        }
    </style>
</body>
</html>
