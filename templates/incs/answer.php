<article class="card mb-3 <?= $question['is_correct'] === null ? 'unknown' : ($question['is_correct'] ? 'correct' : 'incorrect') ?>">
    <header>
        <h4 class="card-title mb-0"><?php echo htmlspecialchars($question['question']); ?>
            <?php if (isset($question['points'])): ?>
                <span class="chip"><?php echo htmlspecialchars($question['points']); ?> pts</span>
            <?php endif; ?>
        </h4>
    </header>
    <main>
        <?php foreach ($question['images'] ?? [] as $image): ?>
            <div class="question-image mt-3">
                <img src="<?php echo htmlspecialchars($image); ?>" alt="Question Image" class="img-fluid">
            </div>
        <?php endforeach; ?>

        <div class="question-input mt-3">
            <?php if ($question['type'] === 'text'): ?>
                <div><strong>This question will be graded manually.</strong></div>
                <div><strong>Your answer:</strong> <?= htmlspecialchars($question['user_answers'][0] ?? '') ?></div>
                <div><strong>Correct answer:</strong> <?= htmlspecialchars($question['answer'] ?? '') ?></div>

            <?php elseif ($question['type'] === 'open'): ?>
                <div>Not graded automatically.</div>
                <div><strong>Your answer:</strong> <?= htmlspecialchars($question['user_answers'][0] ?? '') ?></div>
                <div><strong>Correct answer:</strong> <?= htmlspecialchars($question['answer'] ?? '') ?></div>

            <?php elseif ($question['type'] === 'single_choice'): ?>
                
                <?php foreach ($question['options'] as $option_id => $option): ?>
                    <label>
                        <input <?= in_array($option_id, $question['user_answers']) ? 'checked' : '' ?> disabled type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($option); ?>" required>
                        <?php echo htmlspecialchars("$option_id) $option"); ?>
                    </label>
                <?php endforeach; ?>

            <?php elseif ($question['type'] === 'multiple_choice'): ?>

                <?php foreach ($question['options'] as $option_id => $option): ?>
                    <label>
                        <input <?= in_array($option_id, $question['user_answers']) ? 'checked' : '' ?>  disabled type="checkbox" name="answer[<?php echo $question['id']; ?>][]" value="<?php echo htmlspecialchars($option); ?>" required>
                        <?php echo htmlspecialchars("$option_id) $option"); ?>
                    </label>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </main>
</article>
