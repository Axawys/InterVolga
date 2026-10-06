<?php

/** @var string $password_error задаётся в edit.php */
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
