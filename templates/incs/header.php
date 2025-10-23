<!DOCTYPE html>
<html lang="en" class="pico-background-grey-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Libre MCQ') ?></title>
    <link rel="stylesheet" href="assets/pico.min.css">
    <link rel="stylesheet" href="assets/pico.colors.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <header class="container">
        <?php include 'templates/incs/nav.php'; ?>
        <?php include 'templates/incs/flash.php'; ?>
    </header>

