<?php
session_start();
require_once 'incs/functions.php';
require_once 'incs/model.php';

$fullname = trim($_SESSION['fullname'] ?? '');
if (!$fullname) {
    echo json_encode(['error' => 'Your fullname is required.']);
    exit;
}

$session_id = $_SESSION['qcm_session_id'] ?? false;
if (!$session_id) {
    echo json_encode(['error' => "We have no open session for this QCM"]);
    exit;
}

if (!session_exists($session_id)) {
    echo json_encode(['error' => "We cannot find your QCM session, please try again"]);
}

$mcq_id = $_POST['mcq_id'] ?? false;
if (!$mcq_id)
{
    echo json_encode(['error' => 'ID du MCQ manquant']);
    exit;
}

$mcq_data = read_mcq_data(__DIR__ . '/data/' . $mcq_id);
if (!$mcq_data) 
{
    echo json_encode(['error' => 'MCQ non trouvé']);
    exit;
}

$total_score = 0;
$total_automatic_score = 0;
$score = 0;
foreach ($mcq_data['questions'] as $i => $question) 
{
    $question_id = $question['id'];
    $answers = $_POST['answer'][$question_id] ?? null;
    if (!is_array($answers)) 
    {
        $answers = [$answers];
    }

    $mcq_data['questions'][$i]['is_correct'] = false;
    $mcq_data['questions'][$i]['user_answers'] = $answers;

    $total_score += $question['points'] ?? 1;

    # Automatic scoring
    if (in_array($question['type'], ['multiple_choice', 'single_choice'])) 
    {
        $total_automatic_score += $question['points'] ?? 1;
    }

    if (!$answers)
    {
        continue;
    }

    $correct_answers = $question['correct_answers'] ?? [];
    if (!$correct_answers) 
    {
        $mcq_data['questions'][$i]['is_correct'] = null;
        $mcq_data['questions'][$i]['correction_needed'] = true;
        continue;
    }

    $diff = array_diff($answers, $correct_answers) || array_diff($correct_answers, $answers);
    if ($diff) 
    {
        continue;
    }

    $mcq_data['questions'][$i]['is_correct'] = true;

    $score += $question['points'] ?? 1;
}

# points to be manually attributed
$remaining_points = $total_score - $total_automatic_score;

// Compute percentage
$percentage = $total_score > 0 ? ($score / $total_score) * 100 : 0;

try {
    // Update qcm session info
    update_mcq_session($session_id, $score, $total_score, $percentage);

    // Insert individual answers
    save_mcq_answers($session_id, $mcq_data);

    // MCQ is ended, we can remove session id from session
    unset($_SESSION['qcm_session_id']);
} catch (Exception $e) {
    throw new Exception('Error saving results : ' . $e->getMessage());
}


require 'templates/answer_result.php';
