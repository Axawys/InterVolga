<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/layout.php';

session_start();

$password_error = '';
if (isset($_POST['password_check'])) {
    $password_error = loginAdmin((string)($_POST['password'] ?? ''));
    if ($password_error === '') {
        header('Location: edit');
        exit;
    }
}

if (empty($_SESSION['edit_page_access'])) {
    require __DIR__ . '/../includes/admin_login.php';
    exit;
}

$backupMessages = [
    'ok' => ['success', 'Бэкап успешно загружен.'],
    'error' => ['danger', 'Не удалось загрузить бэкап.'],
];
$backupMessage = $backupMessages[$_GET['backup'] ?? ''] ?? null;

pageHeader('Сотрудники');
?>
<main class="flex-grow-1">
<div class="container mt-4">
<div class="d-flex justify-content-between mb-3">
    <h2>Сотрудники</h2>
    <div class="d-flex gap-2">
        <a href="/" class="btn btn-outline-light">На главную</a>
        <a href="logout" class="btn btn-outline-light">Выйти</a>
        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#addModal">Добавить</button>
    </div>
</div>
<form action="import" method="POST" enctype="multipart/form-data" class="d-flex gap-2 mb-3">
    <a href="export" class="btn btn-outline-light">Скачать бэкап</a>
    <input type="file" name="sql_file" accept=".sql" class="form-control" required>
    <button class="btn btn-outline-light text-nowrap"
            onclick="return confirm('Текущие данные будут заменены данными из бэкапа. Продолжить?')">
        Загрузить бэкап
    </button>
</form>
<?php if ($backupMessage): ?>
<div class="alert alert-<?= $backupMessage[0] ?>"><?= htmlspecialchars($backupMessage[1]) ?></div>
<?php endif; ?>
<div id="apiMessage" class="mb-3"></div>
<div class="row g-3" id="employees"></div>
</div>
</main>

<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Добавить</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input id="add_name" class="form-control mb-2" placeholder="ФИО">
                <input id="add_birth" type="number" class="form-control mb-2" placeholder="Год">
                <select id="add_job_title" class="form-select mb-2"></select>
                <label for="add_photo" class="form-label mb-1">Фотография</label>
                <input id="add_photo" type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                       class="form-control mb-2">
                <textarea id="add_desc" class="form-control" placeholder="Описание"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-light" onclick="createEmployee()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Редактировать</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input id="edit_name" class="form-control mb-2">
                <input id="edit_birth" type="number" class="form-control mb-2">
                <select id="edit_job_title" class="form-select mb-2"></select>
                <label for="edit_photo" class="form-label mb-1">Новая фотография (необязательно)</label>
                <input id="edit_photo" type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                       class="form-control mb-2">
                <textarea id="edit_desc" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-light" onclick="updateEmployee()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<?php pageFooter(['js/bootstrap.bundle.min.js', 'js/edit.js']); ?>
