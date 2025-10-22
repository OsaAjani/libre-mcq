<?php
session_start();

require_once '../incs/functions.php';
require_once '../incs/model.php';


$session_id = intval($_GET['id'] ?? 0);

if ($session_id <= 0) {
    header('Location: index.php');
    exit;
}

// Récupérer les détails de la session
try {
    $session_data = get_session_results($session_id);
} catch (Exception $e) {
    header('Location: index.php?error=session_not_found');
    exit;
}

if (!$session_data) {
    header('Location: index.php?error=session_not_found');
    exit;
}

$session = $session_data['session'];
$answers = $session_data['answers'];

// Récupérer les données du MCQ pour avoir les questions
$mcq_data = read_mcq_data("../data/" . $session['mcq_id']);

require_once '../templates/admin/session_details.php';
