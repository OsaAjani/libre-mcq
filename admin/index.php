<?php
session_start();

require_once '../incs/functions.php';
require_once '../incs/model.php';

// Récupérer tous les MCQ (ouverts et fermés)
$all_mcqs = get_mcqs('../data');

// Trier par nom
usort($all_mcqs, function($a, $b) {
    return strcmp($a['title'], $b['title']);
});

foreach ($all_mcqs as $id => $mcq) {
    // Charger les statistiques du MCQ
    $all_mcqs[$id]['stats'] = get_mcq_statistics($mcq['id']);
}

$title = "Administration - Libre MCQ";

require_once '../templates/admin/index.php';