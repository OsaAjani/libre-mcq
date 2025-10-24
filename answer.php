<?php
session_start();
require_once 'incs/functions.php';
require_once 'incs/model.php';

check_csrf_token();

$fullname = trim($_SESSION['fullname'] ?? '');
if (!$fullname) {
    flash('error', 'Failed to sumbit answers. Your fullname is required');
    header('Location: index.php');
    exit;
}

$session_id = $_SESSION['mcq_session_id'] ?? false;
if (!$session_id) {
    flash('error', 'Failed to sumbit answers. We have no open session for this MCQ');
    header('Location: index.php');
    exit;
}

if (!session_exists($session_id)) {
    flash('error', 'Failed to sumbit answers. We cannot find your MCQ session, please try again');
    header('Location: index.php');
    exit;
}

$mcq_id = $_POST['mcq_id'] ?? false;
if (!$mcq_id)
{
    flash('error', 'Failed to sumbit answers. MCQ ID is missing');
    header('Location: index.php');
    exit;
}

$mcq_data = read_mcq_data(__DIR__ . '/data/' . $mcq_id);
if (!$mcq_data) 
{
    flash('error', 'Failed to sumbit answers. Cannot found info on this MCQ.');
    header('Location: index.php');
    exit;
}

if ($mcq_data['status'] != 'open')
{
    flash('error', 'Failed to sumbit answers. This MCQ is not open anymore.');
    header('Location: index.php');
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
    // Update mcq session info
    update_mcq_session($session_id, $score, $total_score, $percentage);

    // Insert individual answers
    save_mcq_answers($session_id, $mcq_data);

    // MCQ is ended, we can remove session id from session
    unset($_SESSION['mcq_session_id']);
} catch (Exception $e) {
    error_log('Error saving results for session #' . $session_id .  ' : ' . $e->getMessage());
    
    flash('error', 'Failed to save results in database, contact the admin.');
    header('Location: index.php');
    exit;
}


require 'templates/answer_result.php';
