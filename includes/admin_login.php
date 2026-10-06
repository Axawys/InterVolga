<?php

/** @var string $password_error задаётся в edit.php */
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

<form method="POST" class="p-4 bg-body-tertiary rounded-3 shadow">
    <h4>Доступ</h4>
    <input type="password" name="password" class="form-control mb-2" placeholder="Пароль" required>
    <button name="password_check" class="btn btn-outline-light w-100">Войти</button>
    <div class="text-danger mt-2"><?= htmlspecialchars($password_error) ?></div>
</form>

</body>
</html>
