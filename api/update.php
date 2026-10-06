<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
requireAdminAccess();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'METHOD_NOT_ALLOWED', 'message' => 'Используйте POST.'], 405);
}

$data = getJsonInput();
$id = isset($data['id']) ? (int)$data['id'] : 0;
if ($id < 1) {
    jsonResponse(['success' => false, 'error' => 'VALIDATION_ERROR', 'message' => 'Поле id обязательно и должно быть положительным числом.'], 422);
}

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

if (!findEmployeeById($connect, $id)) {
    jsonResponse(['success' => false, 'error' => 'NOT_FOUND', 'message' => 'Сотрудник не найден.'], 404);
}

try {
    $stmt = $connect->prepare('UPDATE employees SET name = ?, birth = ?, job_title_id = ?, description = ? WHERE id = ?');
    $stmt->execute([
        $payload['name'],
        $payload['birth'],
        $payload['job_title_id'],
        $payload['description'],
        $id
    ]);

    jsonResponse(['success' => true, 'message' => 'Сотрудник обновлён.', 'data' => ['id' => $id]]);
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'error' => 'SERVER_ERROR', 'message' => 'Не удалось обновить сотрудника.'], 500);
}
