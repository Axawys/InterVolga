<?php

declare(strict_types=1);

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/layout.php';

session_start();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (loginUser(normalizeString($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''))) {
            header('Location: /');
            exit;
        }
        $errorMessage = 'Неправильный логин или пароль';
    } catch (PDOException $e) {
        $errorMessage = 'Ошибка базы данных, попробуйте позже.';
    }
}

pageHeader('Авторизация');
?>
<main class="flex-grow-1">
<div class="container my-5">
<div class="row justify-content-center">
<div class="col-md-6 col-lg-4">
<div class="bg-body-tertiary rounded-3 shadow p-4">
    <h2 class="mb-4 text-center">Авторизация</h2>

    <?php if ($errorMessage !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Введите email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Введите пароль" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Войти</button>
            <a href="/" class="btn btn-secondary"
               onclick="return confirm('Все введённые данные будут потеряны. Вернуться на главную?')">Главная</a>
        </div>
    </form>

    <p class="mt-3 text-center">
        Нет аккаунта?
        <a href="registration"
           class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Регистрация</a>
    </p>
</div>
</div>
</div>
</div>
</main>
<?php pageFooter(); ?>
