<?php
session_start();

require_once '../incs/functions.php';

$mcq_id = str_replace('.', '', ($_GET['id'] ?? ''));
$action = $_GET['action'] ?? '';

check_csrf_token();

if (empty($mcq_id) || !in_array($action, ['on', 'off'])) {
    header('Location: index.php');
    exit;
}

// Vérifier que le MCQ existe
$mcq_path = "../data/" . $mcq_id;
if (!is_dir($mcq_path)) {
    flash('error', 'Cannot found this MCQ.');
    header('Location: index.php');
    exit;
}

$ai_protect_file = $mcq_path . "/ai_protect.txt";
$new_status = ($action === 'on') ? 'on' : 'off';

// Écrire le nouveau statut
$success = file_put_contents($ai_protect_file, $new_status);
if ($success === false) {
    flash('error', 'Cannot write ai_protect.txt file.');
    header('Location: index.php');
    exit;
}


// Message de confirmation
$message = ($action === 'on') ? 'Protection IA activée avec succès' : 'Protection IA désactivée avec succès';

// Rediriger avec message
flash('success', $message);
header('Location: index.php');
exit;
