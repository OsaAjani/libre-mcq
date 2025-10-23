<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Libre MCQ') ?></title>
    <link rel="stylesheet" href="../assets/pico.min.css">
    <link rel="stylesheet" href="../assets/pico.colors.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>🛠️ Administration Libre MCQ</h1>
            <p>Gestion des questionnaires et consultation des résultats</p>
        </div>
    </header>
    <div class="container">
        <?php include '../templates/incs/flash.php'; ?>
    </div>

