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

// Вход в админку по паролю из ADMIN_PASSWORD_HASH_B64. Возвращает текст ошибки или '' при успехе.
function loginAdmin(string $password): string
{
    $hashB64 = getenv('ADMIN_PASSWORD_HASH_B64');
    $adminHash = $hashB64 ? base64_decode($hashB64, true) : false;

    if (!$adminHash) {
        return 'Не задан ADMIN_PASSWORD_HASH_B64';
    }
    if (!password_verify($password, $adminHash)) {
        return 'Неверный пароль';
    }

    session_regenerate_id(true);
    $_SESSION['edit_page_access'] = true;

    return '';
}

// Вход пользователя по email и паролю. При успехе записывает пользователя в сессию.
function loginUser(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, email, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];

    return true;
}

function emailExists(string $email): bool
{
    $stmt = db()->prepare('SELECT 1 FROM users WHERE email = ?');
    $stmt->execute([$email]);

    return $stmt->fetchColumn() !== false;
}

/**
 * Проверка формы регистрации.
 *
 * @return array{errors: list<string>, data: array<string, string>}
 */
function validateRegistration(array $input): array
{
    $data = [];
    foreach (['FIO', 'date', 'address', 'sex', 'interes', 'vk', 'blood', 'resus', 'email'] as $field) {
        $data[$field] = normalizeString($input[$field] ?? '');
    }
    $password = (string)($input['password'] ?? '');
    $confirmPassword = (string)($input['confirmPassword'] ?? '');

    $errors = [];
    if ($data['FIO'] === '') {
        $errors[] = 'ФИО обязательно.';
    }
    if ($data['date'] === '') {
        $errors[] = 'Дата рождения обязательна.';
    }
    if ($data['address'] === '') {
        $errors[] = 'Адрес обязателен.';
    }
    if (!in_array($data['sex'], ['М', 'Ж'], true)) {
        $errors[] = 'Пол некорректен.';
    }
    if ($data['interes'] === '') {
        $errors[] = 'Интересы обязательны.';
    }
    if ($data['vk'] !== '' && !filter_var($data['vk'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Некорректная ссылка VK.';
    }
    if ($data['blood'] === '') {
        $errors[] = 'Группа крови обязательна.';
    }
    if (!in_array($data['resus'], ['+', '-'], true)) {
        $errors[] = 'Резус-фактор некорректен.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
    } elseif (emailExists($data['email'])) {
        $errors[] = 'Эта почта уже используется';
    }
    if (strlen($password) < 7) {
        $errors[] = 'Пароль минимум 7 символов.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Пароли не совпадают.';
    }

    $data['password'] = $password;

    return ['errors' => $errors, 'data' => $data];
}

/**
 * @param array<string, string> $data данные из validateRegistration()
 */
function registerUser(array $data): void
{
    $stmt = db()->prepare(
        'INSERT INTO users (FIO, date, address, sex, interes, vk, blood, resus, email, password)
         VALUES (:FIO, :date, :address, :sex, :interes, :vk, :blood, :resus, :email, :password)'
    );
    $stmt->execute([
        ':FIO' => $data['FIO'],
        ':date' => $data['date'],
        ':address' => $data['address'],
        ':sex' => $data['sex'],
        ':interes' => $data['interes'],
        ':vk' => $data['vk'],
        ':blood' => $data['blood'],
        ':resus' => $data['resus'],
        ':email' => $data['email'],
        ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
    ]);
}
