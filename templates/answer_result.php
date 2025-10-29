<?php include 'templates/incs/header.php'; ?>

<main class="container">
    <!-- Validation message and score -->
    <section class="result-summary">
        <article>
            <header>
                <h1><?= htmlspecialchars($mcq_data['title']) ?></h1>
            </header>
            
            <main>
                <p>✅ Answers successfully saved!</p>
            </main>
            
            <div class="score-display">
                <h2>Your Result</h2>
                <div class="score-container grid">
                    <!-- Automatic correction score -->
                    <div class="score-box">
                        <h3>Automatic Correction</h3>
                        <span class="score-points"><?= $score ?> / <?= $total_automatic_score ?> points</span>
                        <progress value="<?= $score ?>" max="<?= $total_automatic_score ?>" style="width: 100%;"></progress>
                    </div>
                    
                    <!-- Remaining points to attribute -->
                    <?php if ($remaining_points > 0): ?>
                        <div class="remaining-points">
                            <h3>Remaining Points</h3>
                            <p><strong><?= $remaining_points ?></strong> Points are still to be attributed manually by the teacher.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($session_id): ?>
                    <small>Session ID: <?= $session_id ?> | Saved on <?= date('d/m/Y at H:i:s') ?></small>
                <?php endif; ?>
            </div>
        </article>
    </section>

    <!-- Conditional display of detailed results -->
    <?php if ($mcq_data['show_results'] ?? false): ?>
        <section class="detailed-results">
            <h2>Detailed Correction</h2>

            <article class="card mb-3">
                <header>
                    <h4 class="mb-0">Your full name</h4>
                </header>
                <main>
                    <p><?= htmlspecialchars($fullname) ?></p>
                </main>
            </article>
            
            <?php foreach ($mcq_data['questions'] as $index => $question): ?>
                <?php include 'templates/incs/answer.php'; ?>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <section class="no-details">
            <article>
                <p>Detailed results are not available for this quiz.</p>
                <p>Your teacher will be able to review your answers and provide personalized feedback.</p>
            </article>
        </section>
    <?php endif; ?>

    <!-- Actions -->
    <section class="actions">
        <div class="grid">
            <a href="index.php" role="button" class="secondary">
                🏠 Back to Home
            </a>
            <?php if ($mcq_data['show_results'] ?? false): ?>
                <button onclick="window.print()" role="button" class="outline">
                    🖨️ Print Results
                </button>
            <?php endif; ?>
        </div>
    </section>
</main>

<link rel="stylesheet" href="assets/highlight.min.css"/>
<script src="assets/highlight.min.js"></script>


<?php include 'templates/incs/footer.php'; ?>
