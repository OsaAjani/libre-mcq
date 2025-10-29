<?php include 'templates/incs/header.php'; ?>
    <main class="container">

        <h1 class="mb-4">Available Quizzes</h1>

        <?php if (count($open_mcqs) === 0): ?>
            <p style="text-align: center;">No available quizzes at the moment. Please check back later.</p>
        <?php else: ?>
            <div class="">
            <?php foreach ($open_mcqs as $mcq): ?>
                <article class="card">
                    <header>
                        <h2 class="mb-0"><?php echo htmlspecialchars($mcq['title']); ?></h2>
                    </header>
                    <main>
                        <p><?php echo htmlspecialchars($mcq['description']); ?></p>
                    </main>
                    <footer>
                        <a role="button" href="mcq.php?id=<?php echo urlencode($mcq['id']); ?>">Take this Quiz</a>
                    </footer>
                </article>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
<?php include 'templates/incs/footer.php'; ?>