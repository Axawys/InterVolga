<?php

declare(strict_types=1);

require_once __DIR__ . '/layout.php';

/** @var string $password_error задаётся в edit.php */

pageHeader('Доступ', 'd-flex align-items-center justify-content-center vh-100');
?>
<form method="POST" class="p-4 bg-body-tertiary rounded-3 shadow">
    <h4>Доступ</h4>
    <input type="password" name="password" class="form-control mb-2" placeholder="Пароль" required>
    <button name="password_check" class="btn btn-outline-light w-100">Войти</button>
    <div class="text-danger mt-2"><?= htmlspecialchars($password_error) ?></div>
</form>
<?php pageFooter([], false); ?>
