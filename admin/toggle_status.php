<?php
session_start();

require_once '../incs/functions.php';

$mcq_id = str_replace('.', '', ($_GET['id'] ?? ''));
$action = $_GET['action'] ?? '';

if (empty($mcq_id) || !in_array($action, ['open', 'close'])) {
    header('Location: index.php');
    exit;
}

// Vérifier que le QCM existe
$mcq_path = "../data/" . $mcq_id;
if (!is_dir($mcq_path)) {
    flash('error', 'Cannot found this MCQ.');
    header('Location: index.php');
    exit;
}

$status_file = $mcq_path . "/status.txt";
$new_status = ($action === 'open') ? 'open' : 'closed';

// Écrire le nouveau statut
$success = file_put_contents($status_file, $new_status);
if ($success === false) {
    flash('error', 'Cannot write status.txt file.');
    header('Location: index.php');
    exit;
}


// Message de confirmation
$message = ($action === 'open') ? 'QCM ouvert avec succès' : 'QCM fermé avec succès';

// Rediriger avec message
flash('success', $message);
header('Location: index.php');
exit;
?>
