<?php

declare(strict_types=1);

function jsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        jsonResponse([
            'success' => false,
            'error' => 'INVALID_JSON',
            'message' => 'Некорректный JSON в теле запроса.'
        ], 400);
    }

    return $data;
}

function normalizeString(mixed $value): string
{
    return trim((string)($value ?? ''));
}

function validateEmployeePayload(array $data): array
{
    $errors = [];
    $currentYear = (int)date('Y');

    $name = normalizeString($data['name'] ?? '');
    $birth = filter_var($data['birth'] ?? '', FILTER_VALIDATE_INT);
    $jobTitleId = filter_var($data['job_title_id'] ?? '', FILTER_VALIDATE_INT);
    $description = normalizeString($data['description'] ?? '');

    if ($name === '' || mb_strlen($name) > 45) {
        $errors['name'] = 'ФИО обязательно, не длиннее 45 символов.';
    }

    if ($birth === false || $birth < 1900 || $birth > $currentYear) {
        $errors['birth'] = "Год рождения должен быть от 1900 до {$currentYear}.";
    }

    if ($jobTitleId === false || $jobTitleId < 1) {
        $errors['job_title_id'] = 'Выберите должность.';
    }

    if ($description === '' || mb_strlen($description) > 255) {
        $errors['description'] = 'Описание обязательно, не длиннее 255 символов.';
    }

    return [
        'errors' => $errors,
        'data' => [
            'name' => $name,
            'birth' => $birth,
            'job_title_id' => $jobTitleId,
            'description' => $description,
        ]
    ];
}

const EMPLOYEE_PHOTOS_DIR = __DIR__ . '/../public/catalogue_images';

const EMPLOYEE_PHOTO_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];

// Имя файла фото: <id сотрудника>_<время загрузки>.<расширение>, например 16_2026-03-19_00-13-33.jpg
function employeePhotoFileName(int $employeeId, string $extension, ?int $uploadedAt = null): string
{
    return sprintf('%d_%s.%s', $employeeId, date('Y-m-d_H-i-s', $uploadedAt ?? time()), $extension);
}

function deleteEmployeePhoto(string $fileName): void
{
    $fileName = basename($fileName);
    if ($fileName === '') {
        return;
    }

    $path = EMPLOYEE_PHOTOS_DIR . '/' . $fileName;
    if (is_file($path)) {
        unlink($path);
    }
}

function requireAdminAccess(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['edit_page_access'])) {
        jsonResponse([
            'success' => false,
            'error' => 'UNAUTHORIZED',
            'message' => 'Требуется доступ администратора.'
        ], 401);
    }
}
