<?php
require_once "logic.php";
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/footer.css" rel="stylesheet">
<title>Архитектурное бюро</title>
<style>
body {
    background: url('images/background.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
}
.container-blur {
    backdrop-filter: blur(10px);
    background-color: rgba(0,0,0,0.5);
    padding: 30px;
    border-radius: 10px;
    margin: 50px auto;
    max-width: 900px;
}
h2, h3, h4 {
    text-align: center;
}
img.office-img {
    display: block;
    margin: 20px auto;
    max-width: 100%;
    aspect-ratio: 4 / 3;
    border-radius: 10px;
}
table th, table td {
    vertical-align: middle !important;
    text-align: center;
}
.btn-home {
    display: inline-block;
    margin: 10px 5px 0 5px;
}
</style>
</head>
<body>

<main class="page-content">
<div class="container mt-5">

<?php if (!$isLoggedIn): ?>
    <!-- Гостевая страница -->
    <div class="container-blur">
        <h2>Архитектурное бюро Popoff Inc.</h2>
        <p>
            Добро пожаловать на сайт нашего архитектурного бюро! Мы занимаемся проектированием жилых и коммерческих объектов,
            а также реализуем уникальные дизайнерские решения для интерьеров и экстерьеров.
        </p>
        <p>
            Наша команда состоит из опытных архитекторов, инженеров и дизайнеров, готовых воплотить ваши идеи в реальность.
        </p>

        <h4>Главный офис</h4>
        <img src="images/office.jpg" alt="Главный офис" class="office-img">

        <div class="text-center">
            <a href="autorisation" class="btn btn-primary btn-home">Войти в аккаунт</a>
            <a href="registration" class="btn btn-secondary btn-home">Регистрация</a>
        </div>
    </div>
<?php else: ?>
    <!-- Главное меню авторизованного пользователя -->
    <div class="container-blur">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Главное меню</h3>
            <div>
                <a href="edit" class="btn btn-warning me-2">Админ панель</a>
                <a href="logout" class="btn btn-danger">Выйти</a>
            </div>
        </div>

        <h4>Фильтры</h4>
        <form method="get" action="./" class="text-start mt-4">
            <div class="mb-3">
                <label>По году рождения:</label>
                <input type="text" class="form-control" name="birth" placeholder="Введите год рождения" value="<?= htmlspecialchars($birth_filter) ?>">
            </div>

            <div class="mb-3">
                <label>По ФИО:</label>
                <input type="text" class="form-control" name="name" placeholder="Введите ФИО" value="<?= htmlspecialchars($name_filter) ?>">
            </div>

            <div class="mb-3">
                <label>По личной характеристике:</label>
                <input type="text" class="form-control" name="description" placeholder="Введите личную характеристику" value="<?= htmlspecialchars($description_filter) ?>">
            </div>

            <div class="mb-3">
                <label>По должности:</label>
                <select name="job_title_id" class="form-select">
                    <option value="" <?= ($job_title_filter == '' ? 'selected' : '') ?>>Все</option>
                    <?php foreach ($job_titles as $job): ?>
                        <option value="<?= $job['id'] ?>" <?= ($job_title_filter == $job['id'] ? 'selected' : '') ?>>
                            <?= htmlspecialchars($job['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary me-2">Применить фильтр</button>
                <a href="./" class="btn btn-secondary">Очистить</a>
            </div>
        </form>
    </div>

    <!-- Таблица сотрудников -->
    <div class="container-blur mt-4">
        <h4 class="text-center">Сотрудники</h4>
        <?php if (empty($employees)): ?>
            <div class="alert alert-info text-center">
                Нет сотрудников, соответствующих критериям фильтра
            </div>
        <?php else: ?>
            <table class="table table-dark table-striped mt-3">
                <thead>
                    <tr>
                        <th scope="col">Фото</th>
                        <th scope="col">ФИО</th>
                        <th scope="col">Год рождения</th>
                        <th scope="col">Должность</th>
                        <th scope="col">Личная характеристика</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td>
                                <div style="width:100px; height:100px; overflow:hidden; margin:auto; border-radius:5px;">
                                    <?php if (!empty($employee['img_path']) && file_exists('catalogue_images/' . $employee['img_path'])): ?>
                                        <img class="img-thumbnail" alt="Фото" src="catalogue_images/<?= htmlspecialchars($employee['img_path']) ?>" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="width:100%; height:100%;">
                                            Нет фото
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($employee['name']) ?></td>
                            <td><?= htmlspecialchars($employee['birth']) ?></td>
                            <td><?= htmlspecialchars($employee['job_title_name'] ?? 'Не указана') ?></td>
                            <td><?= htmlspecialchars($employee['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

</div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>