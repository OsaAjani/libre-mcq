<?php include 'templates/incs/header.php'; ?>
    <main class="container">        
        <div class="mb-4">
            Your name is <strong><?= htmlspecialchars($fullname); ?></strong>.
            If this is not correct, please <a href="restart.php?id=<?= htmlspecialchars(urlencode($mcq_id)); ?>" onclick="return confirm('Are you sure you want to restart this MCQ ? All your answers will be lost.');">click here to restart and change your name.</a>
        </div>

        <h1><?= htmlspecialchars($mcq['title']); ?></h1>
        <p><?= htmlspecialchars($mcq['description']); ?></p>

        <form id="mcq-form" method="post" action="answer.php">
            <input type="hidden" name="mcq_id" value="<?= htmlspecialchars($mcq_id); ?>">
            <?php foreach ($mcq['questions'] as $index => $question): ?>
                <?php include 'templates/incs/question.php'; ?>
            <?php endforeach; ?>
            
            <div class="grid">
                <button type="submit" class="btn btn-primary">Valider les réponses</button>
            </div>
        </form>
    </main>
<?php include 'templates/incs/footer.php'; ?>