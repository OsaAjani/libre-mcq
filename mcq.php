<?php
    session_start();

    require('incs/functions.php');
    require('incs/model.php');

    if ($_POST['fullname'] ?? false) {
        $_SESSION['fullname'] = trim($_POST['fullname']);
    }

    $fullname = trim($_SESSION['fullname'] ?? '');
    if (!$fullname) {
        include 'templates/fullname.php';
        exit();
    }

    $pdo = get_database_connection();

    # Cancel old mcq sessions
    $session_id = $_SESSION['mcq_session_id'] ?? false;
    if ($session_id)
    {
        cancel_mcq_session($session_id);
    }

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

    $stmt = $pdo->prepare("
        INSERT INTO sessions (mcq_id, student_name) 
        VALUES (?, ?)
    ");
    
    $stmt->execute([
        $mcq_id,
        $fullname,
    ]);
    
    $_SESSION['mcq_session_id'] = $pdo->lastInsertId();

    include 'templates/mcq.php';
?>