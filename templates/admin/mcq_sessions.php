<?php require_once '../templates/admin/incs/header.php'; ?>
    
    <main class="container">

        <nav class="mb-4">
            <a href="index.php">← Retour à l'administration</a>
        </nav>
        
        <section>
            <h1>📊 Sessions - <?= htmlspecialchars($mcq_data['title']) ?></h1>
            <p><?= htmlspecialchars($mcq_data['description']) ?></p>
        </section>
            

        <?php if ($mcq_stats && $mcq_stats['total_sessions'] > 0): ?>
            <section class="grid my-4">
                <div class="stat-card">
                    <div class="stat-number"><?= $mcq_stats['total_sessions'] ?></div>
                    <div class="stat-label">Sessions totales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($mcq_stats['average_percentage'], 1) ?>%</div>
                    <div class="stat-label">Moyenne générale</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($mcq_stats['min_percentage'], 1) ?>%</div>
                    <div class="stat-label">Score minimum</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($mcq_stats['max_percentage'], 1) ?>%</div>
                    <div class="stat-label">Score maximum</div>
                </div>
            </section>
        <?php endif; ?>

        <section>
            <h2>📋 Liste des sessions</h2>
            
            <?php if (empty($sessions)): ?>
                <article>
                    <p style="text-align: center; color: #6c757d;">
                        Aucune session enregistrée pour ce MCQ.
                    </p>
                </article>
            <?php else: ?>
                <article class="pico-background-grey-50 no-shadow filter-controls">
                    <label for="search">🔍 Rechercher un étudiant:</label>
                    <input type="text" id="search" placeholder="Nom de l'étudiant..." onkeyup="filterSessions()">
                </article>

                <div style="overflow-x: auto;">
                    <table class="session-table mt-2 striped" id="sessionsTable">
                        <thead>
                            <tr>
                                <th>👤 Étudiant</th>
                                <th>📅 Date</th>
                                <th>⏱️ Durée</th>
                                <th>🎯 Score</th>
                                <th>📊 Pourcentage</th>
                                <th>⚙️ Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $session): ?>
                                <?php
                                $start_time = new DateTime($session['start_time']);
                                $end_time = new DateTime($session['end_time']);
                                $duration = $start_time->diff($end_time);
                                
                                $percentage = floatval($session['percentage']);
                                $score_class = 'score-poor';
                                if ($percentage >= 80) $score_class = 'score-excellent';
                                elseif ($percentage >= 65) $score_class = 'score-good';
                                elseif ($percentage >= 50) $score_class = 'score-average';
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($session['student_name']) ?></strong>
                                    </td>
                                    <td>
                                        <?= $end_time->format('d/m/Y H:i') ?>
                                    </td>
                                    <td>
                                        <?= $duration->format('%H:%I:%S') ?>
                                    </td>
                                    <td>
                                        <strong><?= $session['total_score'] ?>/<?= $session['max_score'] ?></strong>
                                    </td>
                                    <td>
                                        <div class="score-bar">
                                            <div class="score-fill <?= $score_class ?>" 
                                                 style="width: <?= min(100, $percentage) ?>%">
                                            </div>
                                        </div>
                                        <small><?= number_format($percentage, 1) ?>%</small>
                                    </td>
                                    <td>
                                        <a href="session_details.php?id=<?= $session['id'] ?>" 
                                           role="button" 
                                           class="outline" 
                                           style="font-size: 0.8em; padding: 0.3rem 0.6rem;">
                                            👁️ Détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 2rem; text-align: center;">
                    <button onclick="exportToCSV()" role="button" class="outline">
                        📥 Exporter en CSV
                    </button>
                    <button onclick="window.print()" role="button" class="outline">
                        🖨️ Imprimer
                    </button>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function filterSessions() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const table = document.getElementById('sessionsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const studentName = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                if (studentName.includes(searchTerm)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        function exportToCSV() {
            const table = document.getElementById('sessionsTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].querySelectorAll('th, td');
                let row = [];
                
                for (let j = 0; j < cells.length - 1; j++) { // -1 pour exclure la colonne Actions
                    let cellText = cells[j].textContent.trim();
                    row.push('"' + cellText.replace(/"/g, '""') + '"');
                }
                csv.push(row.join(','));
            }
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'sessions_<?= $mcq_id ?>_<?= date("Y-m-d") ?>.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    </script>

    <style>
        @media print {
            .filter-controls, button {
                display: none !important;
            }
        }
    </style>

<?php require_once '../templates/admin/incs/footer.php'; ?>
