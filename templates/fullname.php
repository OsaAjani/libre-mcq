<?php include 'templates/incs/header.php'; ?>
    <main class="container">
        <h1><?= htmlspecialchars($mcq['title']); ?></h1>
        <p><?= htmlspecialchars($mcq['description']); ?></p>

        <form method="post" action="">

            <article class="card mb-3">
                <header>
                    <h4 class="card-title mb-0">What is your name?</h4>
                </header>
                <main>
                    <div class="question-input mt-3">                        
                            <input type="text" class="form-control" name="fullname" placeholder="Your full name here" required>
                    </div>
                </main>
            </article>

            <button type="submit" class="btn btn-primary">Start MCQ</button>
        </form>
    </main>
<?php include 'templates/incs/footer.php'; ?>