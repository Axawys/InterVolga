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

if (empty($_SESSION['edit_page_access'])) {
    require __DIR__ . '/../includes/admin_login.php';
    exit;
}

$backupMessages = [
    'ok' => ['success', 'Бэкап успешно загружен.'],
    'error' => ['danger', 'Не удалось загрузить бэкап.'],
];
$backupMessage = $backupMessages[$_GET['backup'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Сотрудники</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
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
<div class="alert alert-<?= $backupMessage[0] ?>"><?= $backupMessage[1] ?></div>
<?php endif; ?>
<div id="apiMessage" class="mb-3"></div>
<div class="row g-3" id="employees"></div>
</div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>

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
    const content = path
        ? `<img src="catalogue_images/${encodeURIComponent(path)}" class="rounded border object-fit-cover" alt="Фото">`
        : '<div class="rounded bg-secondary d-flex align-items-center justify-content-center small">Нет фото</div>';
    return `<div class="col-6 col-md-4 mx-auto mb-2"><div class="ratio ratio-1x1">${content}</div></div>`;
}

async function loadEmployees() {
    const payload = await requestJson('api/read');
    employees.innerHTML = '';
    payload.data.forEach(e => {
        const card = document.createElement('div');
        card.className = 'col-md-4';
        card.innerHTML = `
        <div class="card h-100 p-3 shadow">
            ${cardImage(e.img_path)}
            <div class="flex-grow-1 text-center px-1 small">
                <h5 class="fw-semibold mb-1">${escapeHtml(e.name)}</h5>
                <p class="mb-1">${escapeHtml(e.job_title_name || 'Не указана')}</p>
                <p class="mb-1">${escapeHtml(e.birth)}</p>
                <p class="mb-2">${escapeHtml(e.description)}</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-light w-50 btn-edit">Редактировать</button>
                <button class="btn btn-outline-light w-50 btn-delete">Удалить</button>
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
    edit_photo.value = '';
    new bootstrap.Modal(editModal).show();
}

// Фото загружается отдельным запросом после сохранения сотрудника: имя файла строится из его id
async function uploadPhoto(id, input, successText) {
    const file = input.files[0];
    input.value = '';
    try {
        if (file) {
            const form = new FormData();
            form.append('id', id);
            form.append('photo', file);
            await requestJson('api/upload_photo', {method: 'POST', body: form});
        }
        showMessage(successText, 'success');
    } catch (error) {
        showMessage(successText + ' Но фото не загружено: ' + error.message);
    }
    loadEmployees();
}

async function createEmployee() {
    let payload;
    try {
        payload = await requestJson('api/create', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                name: add_name.value,
                birth: add_birth.value,
                job_title_id: add_job_title.value,
                description: add_desc.value
            })
        });
    } catch (error) {
        showMessage(error.message);
        return;
    }
    bootstrap.Modal.getInstance(addModal).hide();
    [add_name, add_birth, add_desc].forEach(field => field.value = '');
    await uploadPhoto(payload.data.id, add_photo, 'Сотрудник добавлен.');
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
                description: edit_desc.value
            })
        });
    } catch (error) {
        showMessage(error.message);
        return;
    }
    bootstrap.Modal.getInstance(editModal).hide();
    await uploadPhoto(currentId, edit_photo, 'Сотрудник обновлён.');
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
