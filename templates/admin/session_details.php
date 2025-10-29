<?php require_once '../templates/admin/incs/header.php'; ?>

    <main class="container">

        <nav class="mb-4">
            <a href="mcq_sessions.php?id=<?= urlencode($session['mcq_id']) ?>">← Retour aux sessions</a>
        </nav>

        <section class="mb-4 px-4 py-4 radius pico-background-azure-100">
            <div class="grid">
                <div>
                    <h3>📋 Session #<?= $session_id ?></h3>
                    <h4><?= htmlspecialchars($mcq_data['title'] ?? 'MCQ') ?></h4>
                </div>
                <div style="text-align: right;">
                    <h4>Score final : </h4>
                    <span class="score-badge" style="font-size: 1.2em;">
                        <?= $session['total_score'] ?>/<?= $session['max_score'] ?> 
                        (<?= number_format($session['percentage'], 1) ?>%)
                    </span>
                </div>
            </div>
        </section>

        <?php if ($session['warning_count'] > 0): ?>
            <section class="mb-4 px-4 py-4 radius pico-background-red-100">
                <h2>⚠️ Avertissements</h2>
                <p>
                    Cette session a généré <strong><?= $session['warning_count'] ?></strong> avertissement(s) pour des comportements suspects pendant le test.
                    Veuillez consulter la section des avertissements pour plus de détails.
                </p>
                <a href="warnings.php" role="button" class="outline">
                    Voir les avertissements
                </a>
            </section>
        <?php endif; ?>

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
                    
                    // Trouver la question correspondante dans le MCQ
                    $question = null;
                    if ($mcq_data && isset($mcq_data['questions'])) {
                        foreach ($mcq_data['questions'] as $q) {
                            if ($q['id'] == $answer['question_id']) {
                                $question = $q;
                                break;
                            }
                        }
                    }
                    
                    if ($answer['correction_needed']) {
                        $css_class = 'answer-manual';
                    } elseif ($answer['is_correct']) {
                        $css_class = 'answer-correct';
                    } else {
                        $css_class = 'answer-incorrect';
                    }
                ?>
                    <article class="answer-card px-3 py-3 mb-2 <?= $css_class ?>" id="answer-<?= htmlspecialchars($answer['id'])?>">
                        <header>
                            <div class="grid">
                                <div>
                                    <h3>Question <?= $question_index++ ?></h3>
                                    <?php if ($question): ?>
                                        <p><strong><?= secure($question['question']) ?></strong></p>
                                        <?php if ($question['details'] ?? false): ?>
                                            <div><?= secure($question['details']); ?></div>
                                        <?php endif; ?>
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
                        <div class="my-2 py-2">
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
                                                <strong><?= secure($ans) ?>)</strong>
                                                <?php if ($question && isset($question['options'][$ans])): ?>
                                                    <?= secure($question['options'][$ans]) ?>
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
                                            <strong><?= secure($correct) ?>)</strong>
                                            <?php if ($question && isset($question['options'][$correct])): ?>
                                                <?= secure($question['options'][$correct]) ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php elseif ($answer['question_type'] === 'open'): ?>
                            <div class="correct-answer">
                                <?php if ($answer['correction_needed']): ?>
                                    <h4>⚠️ Correction manuelle nécessaire</h4>
                                    <p>Cette question ouverte nécessite une évaluation manuelle.</p>
                                <?php endif ; ?>
                                <?php if ($question && !empty($question['answer'])): ?>
                                    <details>
                                        <summary>Voir un exemple de réponse</summary>
                                        <div style="background: white; padding: 1rem; border-radius: 4px; margin-top: 0.5rem;">
                                            <?= nl2br(htmlspecialchars($question['answer'])) ?>
                                        </div>
                                    </details>
                                <?php endif; ?>
                                <?php if ($answer['correction_needed']): ?>
                                    <section>
                                        <a role="button" class="secondary" href="./correct_answer.php?answer_id=<?= urlencode($answer['id']) ?>&csrf=<?= urlencode(csrf_token()) ?>&is_correct=0">Refuser la réponse</a>
                                        <a role="button" class="primary" href="./correct_answer.php?answer_id=<?= urlencode($answer['id']) ?>&csrf=<?= urlencode(csrf_token()) ?>&is_correct=1">Accepter la réponse</a>
                                    </section>
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

    <link rel="stylesheet" href="../assets/highlight.min.css"/>
    <script src="../assets/highlight.min.js"></script>

<?php require_once '../templates/admin/incs/footer.php'; ?>
