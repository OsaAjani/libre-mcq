<article class="card mb-3 <?= $question['is_correct'] === null ? 'unknown' : ($question['is_correct'] ? 'correct' : 'incorrect') ?>">
    <header>
        <h4 class="card-title mb-0"><?php echo secure($question['question']); ?>
            <?php if (isset($question['points'])): ?>
                <span class="chip"><?php echo htmlspecialchars($question['points']); ?> pts</span>
            <?php endif; ?>
        </h4>
    </header>
    <main>
        <?php if ($question['details'] ?? false): ?>
            <section><?= secure($question['details']); ?></section>
        <?php endif; ?>

        <?php foreach ($question['images'] ?? [] as $image): ?>
            <div class="question-image mt-3">
                <img src="<?php echo htmlspecialchars($image); ?>" alt="Question Image" class="img-fluid">
            </div>
        <?php endforeach; ?>

        <div class="question-input mt-3">
            <?php if ($question['type'] === 'text'): ?>
                <div><strong>This question will be graded manually.</strong></div>
                <div><strong>Your answer:</strong> <?= htmlspecialchars($question['user_answers'][0] ?? '') ?></div>
                <div><strong>Correct answer:</strong> <?= secure($question['answer'] ?? '') ?></div>

            <?php elseif ($question['type'] === 'open'): ?>
                <div class="mb-2"><strong>Not automatically graded.</strong></div>
                <div><strong>Your answer:</strong><br/><?= htmlspecialchars($question['user_answers'][0] ?? '') ?></div>
                <div><strong>Correct answer:</strong><br/><?= secure($question['answer'] ?? '') ?></div>

            <?php elseif ($question['type'] === 'single_choice'): ?>
                <?php if ($question['is_correct'] === false): ?>
                    <section class="mb-3">
                        <h5>Your answer :</h5> 
                        <?php foreach ($question['options'] as $option_id => $option): ?>
                            <label>
                                <input <?= in_array($option_id, $question['user_answers']) ? 'checked' : '' ?> disabled type="radio">
                                <?php echo htmlspecialchars("$option_id) ") . secure($option); ?>
                            </label>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <section>
                    <h5>Correct answer :</h5> 
                    <?php foreach ($question['options'] as $option_id => $option): ?>
                        <label>
                            <input <?= in_array($option_id, $question['correct_answers']) ? 'checked' : '' ?> disabled type="radio">
                            <?php echo htmlspecialchars("$option_id) ") . secure($option); ?>
                        </label>
                    <?php endforeach; ?>
                </section>

            <?php elseif ($question['type'] === 'multiple_choice'): ?>

                <?php if ($question['is_correct'] === false): ?>
                    <section class="mb-3">
                        <h5>Your answers :</h5> 
                        <?php foreach ($question['options'] as $option_id => $option): ?>
                            <label>
                                <input <?= in_array($option_id, $question['user_answers']) ? 'checked' : '' ?> disabled type="checkbox">
                                <?php echo htmlspecialchars("$option_id) ") . secure($option); ?>
                            </label>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <section>
                    <h5>Correct answers :</h5> 
                    <?php foreach ($question['options'] as $option_id => $option): ?>
                        <label>
                            <input <?= in_array($option_id, $question['correct_answers']) ? 'checked' : '' ?> disabled type="checkbox">
                            <?php echo htmlspecialchars("$option_id) ") . secure($option); ?>
                        </label>
                    <?php endforeach; ?>
                </section>

            <?php endif; ?>
        </div>
    </main>
</article>
