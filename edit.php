<?php
session_start();

$password_error = '';

$hashB64 = getenv('ADMIN_PASSWORD_HASH_B64');
$adminHash = $hashB64 ? base64_decode($hashB64, true) : false;

if (isset($_POST['password_check'])) {
    $password = (string)($_POST['password'] ?? '');

    if (!$adminHash) {
        $password_error = 'Не задан ADMIN_PASSWORD_HASH_B64';
    } elseif (password_verify($password, $adminHash)) {
        session_regenerate_id(true);
        $_SESSION['edit_page_access'] = true;
        header('Location: edit');
        exit;
    } else {
        $password_error = 'Неверный пароль';
    }
}

if (empty($_SESSION['edit_page_access'])) :
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-dark text-white">

<form method="POST" class="p-4 bg-black rounded">
    <h4>Доступ</h4>
    <input type="password" name="password" class="form-control mb-2" placeholder="Пароль" required>
    <button name="password_check" class="btn btn-outline-light w-100">Войти</button>
    <div class="text-danger mt-2"><?= htmlspecialchars($password_error) ?></div>
</form>

</body>
</html>
<?php exit; endif;

$backupMessages = [
    'ok' => ['success', 'Бэкап успешно загружен.'],
    'error' => ['danger', 'Не удалось загрузить бэкап.'],
];
$backupMessage = $backupMessages[$_GET['backup'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Сотрудники</title>
<link href="css/footer.css" rel="stylesheet">
<link href="css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: url('images/background.jpg') no-repeat center center fixed; background-size: cover; color: #fff; font-family: "Segoe UI", sans-serif; }
.card { background: rgba(0,0,0,0.85); border-radius: 12px; height: 100%; display: flex; flex-direction: column; box-shadow: 0 4px 15px rgba(0,0,0,0.5); transition: transform 0.2s; }
.card:hover { transform: translateY(-5px); }
.employee-img { width: 33%; aspect-ratio: 1/1; object-fit: cover; margin: 0 auto 10px auto; border-radius: 10px; border: 2px solid rgba(255,255,255,0.2); }
.card-body-fix { flex-grow: 1; text-align: center; padding: 0 5px; }
.card-body-fix h5 { margin-bottom: 5px; font-weight: 600; color: #fff; text-shadow: 0 1px 3px rgba(0,0,0,0.8); }
.card-body-fix p { margin: 2px 0; font-size: 0.9rem; color: #fff; text-shadow: 0 1px 3px rgba(0,0,0,0.7); text-align: justify; }
.btn-soft { border: 1px solid rgba(255,255,255,0.3); color: #fff; transition: background 0.2s; }
.btn-soft:hover { background: rgba(255,255,255,0.1); }
.modal-content { background: #222; color: #fff; }
@media (max-width: 768px) { .employee-img { width: 50%; } }
</style>
</head>
<body>
<main class="page-content">
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
    <button class="btn btn-outline-light text-nowrap" onclick="return confirm('Текущие данные будут заменены данными из бэкапа. Продолжить?')">Загрузить бэкап</button>
</form>
<?php if ($backupMessage): ?>
<div class="alert alert-<?= $backupMessage[0] ?>"><?= $backupMessage[1] ?></div>
<?php endif; ?>
<div id="apiMessage" class="mb-3"></div>
<div class="row g-3" id="employees"></div>
</div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5>Добавить</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><input id="add_name" class="form-control mb-2" placeholder="ФИО"><input id="add_birth" type="number" class="form-control mb-2" placeholder="Год"><select id="add_job_title" class="form-control mb-2"></select><input id="add_img_path" class="form-control mb-2" placeholder="Имя файла изображения"><textarea id="add_desc" class="form-control" placeholder="Описание"></textarea></div><div class="modal-footer"><button class="btn btn-outline-light" onclick="createEmployee()">Сохранить</button></div></div></div></div>

<div class="modal fade" id="editModal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5>Редактировать</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><input id="edit_name" class="form-control mb-2"><input id="edit_birth" type="number" class="form-control mb-2"><select id="edit_job_title" class="form-control mb-2"></select><input id="edit_img_path" class="form-control mb-2" placeholder="Имя файла изображения"><textarea id="edit_desc" class="form-control"></textarea></div><div class="modal-footer"><button class="btn btn-outline-light" onclick="updateEmployee()">Сохранить</button></div></div></div></div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
let currentId = null;

function escapeHtml(value = '') {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function showMessage(text, type = 'danger') {
    apiMessage.innerHTML = `<div class="alert alert-${type}">${escapeHtml(text)}</div>`;
}

async function requestJson(url, options = {}) {
    const res = await fetch(url, options);
    const payload = await res.json();
    if (!res.ok || payload.success === false) {
        const details = payload.fields ? ' ' + Object.values(payload.fields).join(' ') : '';
        throw new Error((payload.message || 'Ошибка запроса.') + details);
    }
    return payload;
}

async function loadJobTitles() {
    const payload = await requestJson('api/get_job_titles');
    const selects = [add_job_title, edit_job_title];
    selects.forEach(select => {
        select.innerHTML = '';
        payload.data.forEach(t => {
            select.innerHTML += `<option value="${t.id}">${escapeHtml(t.name)}</option>`;
        });
    });
}

function cardImage(path) {
    if (!path) {
        return '<div class="employee-img mb-2 d-flex align-items-center justify-content-center bg-secondary">Нет фото</div>';
    }
    return `<img src="catalogue_images/${encodeURIComponent(path)}" class="employee-img mb-2" alt="Фото">`;
}

async function loadEmployees() {
    const payload = await requestJson('api/read');
    employees.innerHTML = '';
    payload.data.forEach(e => {
        const card = document.createElement('div');
        card.className = 'col-md-4';
        card.innerHTML = `
        <div class="card p-3">
            ${cardImage(e.img_path)}
            <div class="card-body-fix">
                <h5>${escapeHtml(e.name)}</h5>
                <p>${escapeHtml(e.job_title_name || 'Не указана')}</p>
                <p>${escapeHtml(e.birth)}</p>
                <p>${escapeHtml(e.description)}</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-soft w-50 btn-edit">Редактировать</button>
                <button class="btn btn-soft w-50 btn-delete">Удалить</button>
            </div>
        </div>`;
        card.querySelector('.btn-edit').addEventListener('click', () => openEdit(e));
        card.querySelector('.btn-delete').addEventListener('click', () => removeEmployee(e.id));
        employees.appendChild(card);
    });
}

function openEdit(employee) {
    currentId = employee.id;
    edit_name.value = employee.name;
    edit_birth.value = employee.birth;
    edit_job_title.value = employee.job_title_id;
    edit_desc.value = employee.description;
    edit_img_path.value = employee.img_path || '';
    new bootstrap.Modal(editModal).show();
}

async function createEmployee() {
    try {
        await requestJson('api/create', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                name: add_name.value,
                birth: add_birth.value,
                job_title_id: add_job_title.value,
                description: add_desc.value,
                img_path: add_img_path.value
            })
        });
        bootstrap.Modal.getInstance(addModal).hide();
        [add_name, add_birth, add_img_path, add_desc].forEach(field => field.value = '');
        showMessage('Сотрудник добавлен.', 'success');
        loadEmployees();
    } catch (error) {
        showMessage(error.message);
    }
}

async function updateEmployee() {
    try {
        await requestJson('api/update', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id: currentId,
                name: edit_name.value,
                birth: edit_birth.value,
                job_title_id: edit_job_title.value,
                description: edit_desc.value,
                img_path: edit_img_path.value
            })
        });
        bootstrap.Modal.getInstance(editModal).hide();
        showMessage('Сотрудник обновлён.', 'success');
        loadEmployees();
    } catch (error) {
        showMessage(error.message);
    }
}

async function removeEmployee(id) {
    if (!confirm('Удалить сотрудника?')) return;
    try {
        await requestJson('api/delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        showMessage('Сотрудник удалён.', 'success');
        loadEmployees();
    } catch (error) {
        showMessage(error.message);
    }
}

Promise.all([loadJobTitles(), loadEmployees()]).catch(error => showMessage(error.message));
</script>
</body>
</html>
