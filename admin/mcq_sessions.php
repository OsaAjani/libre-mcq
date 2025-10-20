<?php
require_once '../incs/functions.php';
require_once '../incs/model.php';

$mcq_id = str_replace('.', '', ($_GET['id'] ?? ''));

if (empty($mcq_id)) {
    header('Location: index.php');
    exit;
}

// Vérifier que le QCM existe
$mcq_data = read_mcq_data("../data/" . $mcq_id);
if (!$mcq_data) {
    header('Location: index.php?error=mcq_not_found');
    exit;
}

// Récupérer les sessions de ce QCM
$sessions = [];
$mcq_stats = null;
try {
    $sessions = get_mcq_sessions($mcq_id);
    $mcq_stats = get_mcq_statistics($mcq_id);
} catch (Exception $e) {
    // Base de données peut ne pas exister
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions - <?= htmlspecialchars($mcq_data['title']) ?> - Admin</title>
    <link rel="stylesheet" href="../assets/pico.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .session-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        .session-table th,
        .session-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .session-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .session-table tr:hover {
            background: #f8f9fa;
        }
        
        .score-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            position: relative;
        }
        
        .score-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        
        .score-excellent {
            background: linear-gradient(90deg, #28a745, #20c997);
        }
        
        .score-good {
            background: linear-gradient(90deg, #17a2b8, #007bff);
        }
        
        .score-average {
            background: linear-gradient(90deg, #ffc107, #fd7e14);
        }
        
        .score-poor {
            background: linear-gradient(90deg, #dc3545, #e74c3c);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            color: #6c757d;
            margin-top: 0.5rem;
        }
        
        .filter-controls {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        @media (max-width: 768px) {
            .session-table {
                font-size: 0.9em;
            }
            
            .session-table th,
            .session-table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <header style="margin: 2rem 0;">
            <nav>
                <a href="index.php">← Retour à l'administration</a>
            </nav>
            <h1>📊 Sessions - <?= htmlspecialchars($mcq_data['title']) ?></h1>
            <p><?= htmlspecialchars($mcq_data['description']) ?></p>
        </header>

        <?php if ($mcq_stats && $mcq_stats['total_sessions'] > 0): ?>
            <section class="stats-grid">
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
                        Aucune session enregistrée pour ce QCM.
                    </p>
                </article>
            <?php else: ?>
                <div class="filter-controls">
                    <label for="search">🔍 Rechercher un étudiant:</label>
                    <input type="text" id="search" placeholder="Nom de l'étudiant..." onkeyup="filterSessions()">
                </div>

                <div style="overflow-x: auto;">
                    <table class="session-table" id="sessionsTable">
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
</body>
</html>
