<?php require_once '../templates/admin/incs/header.php'; ?>
    
    <main class="container">
        <?php require_once('../templates/incs/flash.php'); ?>
        
        <section class="grid mb-6">
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
                        <div class="stat-label">MCQ Total</div>
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
                <div class="grid">
                    <a href="../index.php" role="button" class="outline">
                        🏠 Voir le site
                    </a>
                    <button onclick="location.reload()" role="button" class="outline">
                        🔄 Actualiser
                    </button>
                    <a href="warnings.php" role="button" class="outline">
                        ⚠️ Les warnings
                    </a>
                </div>
            </div>
        </section>

        <section>
            <h2>📋 Gestion des MCQ</h2>
            
            <?php if (empty($all_mcqs)): ?>
                <article>
                    <p style="text-align: center; color: #6c757d;">
                        Aucun MCQ trouvé dans le dossier data/
                    </p>
                </article>
            <?php else: ?>
                <?php foreach ($all_mcqs as $mcq): ?>                    
                    <article>
                        <header class="pt-3 px-3">
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
                        <main class="px-1">
                            <p><?= htmlspecialchars($mcq['description']) ?></p>

                            <div class="grid">
                                <div>
                                    <strong>Questions:</strong> <?= count($mcq['questions']) ?><br>
                                    <strong>Afficher résultats:</strong> <?= $mcq['show_results'] ? '✅ Oui' : '❌ Non' ?><br/>
                                    <strong>Mélanger questions:</strong> <?= $mcq['randomize'] ? '✅ Oui' : '❌ Non' ?><br/>
                                    <strong>Protection contre IA:</strong> <?= $mcq['ai_protect'] ? '✅ Oui' : '❌ Non' ?>
                                </div>

                                <?php if ($mcq['stats'] ?? null && $mcq['stats']['total_sessions'] > 0): ?>
                                    <div class="mcq-stats">
                                        <strong>📊 Statistiques:</strong>
                                        <div style="margin-top: 0.5rem;">
                                            <small>
                                                <?= $mcq['stats']['total_sessions'] ?> sessions • 
                                                Moyenne: <?= number_format($mcq['stats']['average_percentage'], 1) ?>% • 
                                                Min: <?= number_format($mcq['stats']['min_percentage'], 1) ?>% • 
                                                Max: <?= number_format($mcq['stats']['max_percentage'], 1) ?>%
                                            </small>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div style="color: #6c757d; font-style: italic;">
                                        Aucune session enregistrée
                                    </div>
                                <?php endif; ?>
                            </div>
                        </main>                        
                        <footer class="px-3">
                            <?php if ($mcq['status'] === 'open'): ?>
                                <a href="toggle_status.php?id=<?= urlencode($mcq['id']) ?>&csrf=<?= urlencode(csrf_token()) ?>&action=close" 
                                    onclick="return confirm('Fermer ce MCQ ?')" 
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
                                <a href="toggle_status.php?id=<?= urlencode($mcq['id']) ?>&csrf=<?= urlencode(csrf_token()) ?>&action=open" 
                                    onclick="return confirm('Ouvrir ce MCQ ?')" 
                                    role="button" 
                                    class="outline">
                                    🔓 Ouvrir
                                </a>
                            <?php endif; ?>

                            <?php if ($mcq['ai_protect']): ?>
                                <a href="toggle_ai_protect.php?id=<?= urlencode($mcq['id']) ?>&csrf=<?= urlencode(csrf_token()) ?>&action=off" 
                                    onclick="return confirm('Désactiver la protection IA pour ce MCQ ?')" 
                                    role="button" 
                                    class="outline secondary">
                                    🤖 Disable AI Protect
                                </a>
                            <?php else: ?>
                                <a href="toggle_ai_protect.php?id=<?= urlencode($mcq['id']) ?>&csrf=<?= urlencode(csrf_token()) ?>&action=on" 
                                    onclick="return confirm('Activer la protection IA pour ce MCQ ?')" 
                                    role="button" 
                                    class="outline">
                                    🤖 Enable AI Protect
                                </a>
                            <?php endif; ?>

                            <a href="mcq_sessions.php?id=<?= urlencode($mcq['id']) ?>" 
                                role="button" 
                                class="outline">
                                📊 Sessions (<?= $mcq['stats'] ?? null ? $mcq['stats']['total_sessions'] : 0 ?>)
                            </a>
                        </footer>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

<?php require_once '../templates/admin/incs/footer.php'; ?>
