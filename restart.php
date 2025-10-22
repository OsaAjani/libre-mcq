<?php
session_start();

require_once 'incs/functions.php';
require_once 'incs/model.php';

// Récupérer l'ID du MCQ
$mcq_id = str_replace('.', '', ($_GET['id'] ?? null));

if ($mcq_id === null) {
    // Si pas d'ID de MCQ, rediriger vers l'accueil
    header('Location: index.php');
    exit();
}

# Cancel old mcq sessions
$session_id = $_SESSION['mcq_session_id'] ?? false;
if ($session_id)
{
    cancel_mcq_session($session_id);
}

session_destroy();

// Rediriger vers le MCQ
header('Location: mcq.php?id=' . urlencode($mcq_id));
exit();
?>
