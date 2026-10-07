<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'error' => 'METHOD_NOT_ALLOWED', 'message' => 'Используйте GET.'], 405);
}

try {
    $filters = [
        'birth' => normalizeString($_GET['birth'] ?? ''),
        'name' => normalizeString($_GET['name'] ?? ''),
        'description' => normalizeString($_GET['description'] ?? ''),
        'job_title_id' => normalizeString($_GET['job_title_id'] ?? ''),
    ];

    jsonResponse([
        'success' => true,
        'data' => getEmployees(db(), $filters)
    ]);
} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Не удалось получить список сотрудников.',
    ], 500);
}
