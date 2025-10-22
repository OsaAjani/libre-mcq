<?php
session_start();

require_once '../incs/functions.php';
require_once '../incs/model.php';

$mcq_id = str_replace('.', '', ($_GET['id'] ?? ''));

if (empty($mcq_id)) {
    header('Location: index.php');
    exit;
}

// Vérifier que le MCQ existe
$mcq_data = read_mcq_data("../data/" . $mcq_id);
if (!$mcq_data) {
    header('Location: index.php?error=mcq_not_found');
    exit;
}

// Récupérer les sessions de ce MCQ
$sessions = [];
$mcq_stats = null;
try {
    $sessions = get_mcq_sessions($mcq_id);
    $mcq_stats = get_mcq_statistics($mcq_id);
} catch (Exception $e) {
    // Base de données peut ne pas exister
}

require_once '../templates/admin/mcq_sessions.php';