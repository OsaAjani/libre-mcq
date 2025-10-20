<?php
session_start();

require_once '../incs/functions.php';
require_once '../incs/model.php';

// Récupérer tous les QCM (ouverts et fermés)
$all_mcqs = get_mcqs('../data');

// Trier par nom
usort($all_mcqs, function($a, $b) {
    return strcmp($a['title'], $b['title']);
});
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Open QCM</title>
    <link rel="stylesheet" href="../assets/pico.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-open {
            background: #28a745;
            color: white;
        }
        
        .status-closed {
            background: #dc3545;
            color: white;
        }
        
        .status-unknown {
            background: #6c757d;
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .action-buttons a, .action-buttons button {
            font-size: 0.85em;
            padding: 0.4rem 0.8rem;
        }
        
        .mcq-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.3s ease;
        }
        
        .mcq-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .mcq-stats {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            text-align: center;
        }
        
        .stat-item {
            background: white;
            padding: 1rem;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .stat-number {
            font-size: 1.5em;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>🛠️ Administration Open QCM</h1>
            <p>Gestion des questionnaires et consultation des résultats</p>
        </div>
    </header>

    <main class="container">
        <?php require_once('../templates/incs/flash.php'); ?>
        
        <section class="grid">
            <div>
                <h2>📊 Vue d'ensemble</h2>
                <div class="stats-grid">
                    <?php
                    $total_mcqs = count($all_mcqs);
                    $open_mcqs = array_filter($all_mcqs, function($mcq) { return $mcq['status'] === 'open'; });
                    $closed_mcqs = array_filter($all_mcqs, function($mcq) { return $mcq['status'] === 'closed'; });
                    ?>
                    <div class="stat-item">
                        <div class="stat-number"><?= $total_mcqs ?></div>
                        <div class="stat-label">QCM Total</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= count($open_mcqs) ?></div>
                        <div class="stat-label">Ouverts</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= count($closed_mcqs) ?></div>
                        <div class="stat-label">Fermés</div>
                    </div>
                </div>
            </div>
            
            <div>
                <h2>🔗 Actions rapides</h2>
                <div class="action-buttons">
                    <a href="../index.php" role="button" class="outline">
                        🏠 Voir le site
                    </a>
                    <button onclick="location.reload()" role="button" class="outline">
                        🔄 Actualiser
                    </button>
                </div>
            </div>
        </section>

        <section>
            <h2>📋 Gestion des QCM</h2>
            
            <?php if (empty($all_mcqs)): ?>
                <article>
                    <p style="text-align: center; color: #6c757d;">
                        Aucun QCM trouvé dans le dossier data/
                    </p>
                </article>
            <?php else: ?>
                <?php foreach ($all_mcqs as $mcq): ?>
                    <?php
                    // Calculer les statistiques du QCM
                    $mcq_stats = null;
                    try {
                        $mcq_stats = get_mcq_statistics($mcq['id']);
                    } catch (Exception $e) {
                        // Base de données peut ne pas exister encore
                    }
                    ?>
                    
                    <article class="mcq-card">
                        <header>
                            <div class="grid">
                                <div>
                                    <h3><?= htmlspecialchars($mcq['title']) ?></h3>
                                    <p style="margin: 0; color: #6c757d;">
                                        ID: <?= htmlspecialchars($mcq['id']) ?>
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <span class="status-badge status-<?= $mcq['status'] === 'open' ? 'open' : ($mcq['status'] === 'closed' ? 'closed' : 'unknown') ?>">
                                        <?= $mcq['status'] === 'open' ? '🟢 Ouvert' : ($mcq['status'] === 'closed' ? '🔴 Fermé' : '⚪ Inconnu') ?>
                                    </span>
                                </div>
                            </div>
                        </header>
                        
                        <p><?= htmlspecialchars($mcq['description']) ?></p>
                        
                        <div class="grid">
                            <div>
                                <strong>Questions:</strong> <?= count($mcq['questions']) ?><br>
                                <strong>Afficher résultats:</strong> <?= $mcq['show_results'] ? '✅ Oui' : '❌ Non' ?>
                            </div>
                            
                            <?php if ($mcq_stats && $mcq_stats['total_sessions'] > 0): ?>
                                <div class="mcq-stats">
                                    <strong>📊 Statistiques:</strong>
                                    <div style="margin-top: 0.5rem;">
                                        <small>
                                            <?= $mcq_stats['total_sessions'] ?> sessions • 
                                            Moyenne: <?= number_format($mcq_stats['average_percentage'], 1) ?>% • 
                                            Min: <?= number_format($mcq_stats['min_percentage'], 1) ?>% • 
                                            Max: <?= number_format($mcq_stats['max_percentage'], 1) ?>%
                                        </small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div style="color: #6c757d; font-style: italic;">
                                    Aucune session enregistrée
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <footer>
                            <div class="action-buttons">
                                <?php if ($mcq['status'] === 'open'): ?>
                                    <a href="toggle_status.php?id=<?= urlencode($mcq['id']) ?>&action=close" 
                                       onclick="return confirm('Fermer ce QCM ?')" 
                                       role="button" 
                                       class="outline secondary">
                                        🔒 Fermer
                                    </a>
                                    <a href="../mcq.php?id=<?= urlencode($mcq['id']) ?>" 
                                       role="button" 
                                       class="outline">
                                        👀 Voir
                                    </a>
                                <?php else: ?>
                                    <a href="toggle_status.php?id=<?= urlencode($mcq['id']) ?>&action=open" 
                                       onclick="return confirm('Ouvrir ce QCM ?')" 
                                       role="button" 
                                       class="outline">
                                        🔓 Ouvrir
                                    </a>
                                <?php endif; ?>
                                
                                <a href="mcq_sessions.php?id=<?= urlencode($mcq['id']) ?>" 
                                   role="button" 
                                   class="outline">
                                    📊 Sessions (<?= $mcq_stats ? $mcq_stats['total_sessions'] : 0 ?>)
                                </a>
                            </div>
                        </footer>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer style="margin-top: 3rem; padding: 2rem 0; background: #f8f9fa; text-align: center;">
        <div class="container">
            <p style="margin: 0; color: #6c757d;">
                Administration Open QCM • Connecté en tant qu'administrateur
            </p>
        </div>
    </footer>
</body>
</html>
