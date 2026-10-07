<?php

declare(strict_types=1);

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/layout.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        ['errors' => $errors, 'data' => $data] = validateRegistration($_POST);
        if ($errors === []) {
            registerUser($data);
            header('Location: autorisation');
            exit;
        }
    } catch (PDOException $e) {
        $errors[] = 'Ошибка базы данных, попробуйте позже.';
    }
}

pageHeader('Регистрация');
?>
<main class="flex-grow-1">
<div class="container my-5">
<div class="row justify-content-center">
<div class="col-md-8 col-lg-6">
<div class="bg-body-tertiary rounded-3 shadow p-4">
<h2 class="mb-4 text-center">Регистрация</h2>
<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>
<form method="POST" id="regForm">
    <div class="form-group">
        <label>ФИО</label>
        <input type="text" class="form-control" name="FIO" id="FIO" required>
        <small class="text-danger d-none">Введите ФИО</small>
    </div>

    <div class="form-group mt-3">
        <label>Дата рождения</label>
        <div class="d-flex gap-2">
            <select id="day" class="form-select" required></select>
            <select id="month" class="form-select" required></select>
            <select id="year" class="form-select" required></select>
        </div>
        <small id="dobError" class="text-danger d-none">Выберите корректную дату</small>
        <input type="hidden" id="dob" name="date">
    </div>

    <div class="form-group mt-3">
        <label>Адрес</label>
        <input type="text" class="form-control" name="address" id="address" required>
        <small class="text-danger d-none">Введите адрес</small>
    </div>

    <div class="form-group mt-3">
        <label>Пол</label>
        <select class="form-select" name="sex" id="sex" required>
            <option value="">Выберите пол</option>
            <option value="М">Мужской</option>
            <option value="Ж">Женский</option>
        </select>
        <small class="text-danger d-none" id="sexError">Выберите пол</small>
    </div>

    <div class="form-group mt-3">
        <label>Интересы</label>
        <textarea class="form-control" name="interes" id="interes" rows="3" required></textarea>
        <small class="text-danger d-none">Введите интересы</small>
    </div>

    <div class="form-group mt-3">
        <label>Ссылка VK</label>
        <input type="url" class="form-control" name="vk" id="vk" required>
        <small class="text-danger d-none">Введите корректную ссылку</small>
    </div>

    <div class="form-group mt-3">
        <label>Группа крови</label>
        <select class="form-select" name="blood" id="blood" required>
            <option value="">Выберите группу</option>
            <option value="I">I (0)</option>
            <option value="II">II (A)</option>
            <option value="III">III (B)</option>
            <option value="IV">IV (AB)</option>
        </select>
        <small class="text-danger d-none" id="bloodError">Выберите группу крови</small>
    </div>

    <div class="form-group mt-3">
        <label>Резус-фактор</label>
        <select class="form-select" name="resus" id="resus" required>
            <option value="">Выберите</option>
            <option value="+">Положительный (+)</option>
            <option value="-">Отрицательный (-)</option>
        </select>
        <small class="text-danger d-none" id="resusError">Выберите резус-фактор</small>
    </div>

    <div class="form-group mt-3">
        <label>Email</label>
        <input type="email" class="form-control" name="email" id="email" required>
        <small class="text-danger d-none" id="emailFormatError">Введите корректный email</small>
        <small class="text-danger d-none" id="emailError"></small>
    </div>

    <div class="form-group mt-3">
        <label>Пароль</label>
        <input type="password" class="form-control" name="password" id="password" required>
        <small class="text-danger d-none">Пароль минимум 7 символов</small>
    </div>

    <div class="form-group mt-3">
        <label>Подтвердите пароль</label>
        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" required>
        <small class="text-danger d-none">Пароли должны совпадать</small>
    </div>

    <div class="mt-3 d-flex justify-content-between">
        <button type="submit" class="btn btn-primary" id="submitBtn">Зарегистрироваться</button>
        <a href="/" class="btn btn-secondary"
           onclick="return confirm('Все заполненные данные будут потеряны. Вернуться на главную?')">
            Вернуться в главное меню
        </a>
    </div>
</form>

</div>
</div>
</div>
</div>
</main>
<?php pageFooter(['js/registration.js']); ?>
