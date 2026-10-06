<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
requireAdminAccess();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'METHOD_NOT_ALLOWED', 'message' => 'Используйте POST.'], 405);
}

$data = getJsonInput();
$validation = validateEmployeePayload($data);
if ($validation['errors']) {
    jsonResponse([
        'success' => false,
        'error' => 'VALIDATION_ERROR',
        'message' => 'Ошибка валидации входных данных.',
        'fields' => $validation['errors']
    ], 422);
}

$payload = $validation['data'];
if (!jobTitleExists($connect, $payload['job_title_id'])) {
    jsonResponse(['success' => false, 'error' => 'INVALID_JOB_TITLE', 'message' => 'Указанная должность не найдена.'], 422);
}

try {
    $stmt = $connect->prepare('INSERT INTO employees (img_path, name, birth, job_title_id, description) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $payload['img_path'],
        $payload['name'],
        $payload['birth'],
        $payload['job_title_id'],
        $payload['description']
    ]);

    jsonResponse([
        'success' => true,
        'message' => 'Сотрудник добавлен.',
        'data' => ['id' => (int)$connect->lastInsertId()]
    ], 201);
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'error' => 'SERVER_ERROR', 'message' => 'Не удалось добавить сотрудника.'], 500);
}
