<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'db.php';
    try {
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: /");
            exit();
        } else {
            $errorMessage = "Неправильный логин или пароль";
        }
    } catch (PDOException $e) {
        $errorMessage = "Ошибка подключения: " . $e->getMessage();
    }
    $connect = null;
}
?>

<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-grow-1">
<div class="container my-5">
<div class="row justify-content-center">
<div class="col-md-6 col-lg-4">
<div class="bg-body-tertiary rounded-3 shadow p-4">
    <h2 class="mb-4 text-center">Авторизация</h2>

    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Введите email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Войти</button>
            <button type="button" class="btn btn-secondary" onclick="goHome()">Главная</button>
        </div>
    </form>

    <p class="mt-3 text-center">
        Нет аккаунта? 
        <a href="registration" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Регистрация</a>
    </p>
</div>

<script>
function goHome() {
    if(confirm("Все введённые данные будут потеряны. Вы уверены, что хотите вернуться на главную?")) {
        window.location.href = '/';
    }
}
</script>
</div>
</div>
</div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
