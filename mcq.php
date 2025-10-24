<?php
    session_start();

    require('incs/functions.php');
    require('incs/model.php');

    $mcq_id = str_replace('.', '', ($_GET['id'] ?? null));
    if ($mcq_id === null) {
        header('Location: index.php');
        exit();
    }

    $mcq = read_mcq_data("./data/" . $mcq_id);
    if ($mcq === null || $mcq['status'] !== 'open') {
        header('Location: index.php');
        exit();
    }

    if ($_POST['fullname'] ?? false) {
        $_SESSION['fullname'] = trim($_POST['fullname']);
    }

    $fullname = trim($_SESSION['fullname'] ?? '');
    if (!$fullname) {
        include 'templates/fullname.php';
        exit();
    }

    # Cancel old mcq sessions
    $session_id = $_SESSION['mcq_session_id'] ?? false;
    if ($session_id)
    {
        cancel_mcq_session($session_id);
    }

    if ($mcq['randomize'] ?? false) {
        shuffle($mcq['questions']);
    }

    $mcq_session_id = create_new_mcq_session($mcq_id, $fullname);

    $_SESSION['mcq_session_id'] = $mcq_session_id;

    $title = 'MCQ - ' . ($mcq['title'] ?? 'Libre MCQ');

    include 'templates/mcq.php';
?>