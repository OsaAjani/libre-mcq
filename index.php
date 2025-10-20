<?php
    session_start();
    require('incs/functions.php');

    $data_dir = './data';
    $open_mcqs = get_mcqs($data_dir, status: 'open');

    include 'templates/index.php';
?>