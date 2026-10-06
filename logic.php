<?php

declare(strict_types=1);

require_once 'includes/employee_repository.php';
require_once 'includes/helpers.php';

$filters = [
    'birth' => normalizeString($_GET['birth'] ?? ''),
    'name' => normalizeString($_GET['name'] ?? ''),
    'description' => normalizeString($_GET['description'] ?? ''),
    'job_title_id' => normalizeString($_GET['job_title_id'] ?? ''),
];

$employees = getEmployees($connect, $filters);
$job_titles = getAllJobTitles($connect);
$birth_filter = $filters['birth'];
$name_filter = $filters['name'];
$description_filter = $filters['description'];
$job_title_filter = $filters['job_title_id'];
