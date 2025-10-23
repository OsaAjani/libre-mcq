<?php
session_start();

require_once 'incs/functions.php';
require_once 'incs/model.php';

# Cancel old mcq sessions
$session_id = $_SESSION['mcq_session_id'] ?? false;
if (!$session_id)
{
    return;
}

$type = $_GET['type'] ?? 'tab_switch';

insert_warning($session_id, $type);
echo json_encode(['status' => 'success']);