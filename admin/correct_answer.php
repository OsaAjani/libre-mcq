<?php
session_start();

require_once '../incs/functions.php';
require_once '../incs/model.php';

$answer_id = $_GET['answer_id'] ?? false;
$is_correct = $_GET['is_correct'] ?? null;

if (!$answer_id || $is_correct === null) {
    header('Location: index.php');
    exit;
}

$answer = get_answer_by_id($answer_id);
if (!$answer) {
    flash('error', 'Cannot found this answer.');
    header('Location: index.php');
    exit;
}

$session_id = $answer['session_id'] ?? null;
update_answer_correctness($answer_id, $is_correct);


$message = ($is_correct === true) ? 'Réponse marquée comme correcte' : 'Réponse marquée comme incorrecte';
flash('success', $message);

// Redirect to session page and specific answer
header('Location: session_details.php?id=' . urlencode($session_id) . '#answer-' . urlencode($answer_id));
exit;
