<?php foreach (get_flashes() as $flash) : ?>
    <article class="flash <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']); ?>
    </article>
<?php endforeach; ?>