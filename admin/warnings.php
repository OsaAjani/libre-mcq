<?php
session_start();

require_once '../incs/functions.php';
require_once '../incs/model.php';

$title = 'Admin Warnings - Libre MCQ';

$warnings = get_warnings();

require_once '../templates/admin/warnings.php';