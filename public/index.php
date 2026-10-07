<?php
require_once __DIR__ . '/../logic.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/bootstrap.min.css" rel="stylesheet">
<title>Архитектурное бюро</title>
</head>
<body class="d-flex flex-column min-vh-100">

<main class="flex-grow-1">
<div class="container my-5">
<div class="row justify-content-center">
<div class="col-lg-10 col-xl-8">

<?php if (!$isLoggedIn): ?>
    <!-- Гостевая страница -->
    <div class="bg-body-tertiary rounded-3 shadow p-4">
        <h2 class="text-center mb-3">Архитектурное бюро</h2>
        <p>
            Добро пожаловать на сайт нашего архитектурного бюро! Мы занимаемся проектированием жилых и коммерческих объектов,
            а также реализуем уникальные дизайнерские решения для интерьеров и экстерьеров.
        </p>
        <p>
            Наша команда состоит из опытных архитекторов, инженеров и дизайнеров, готовых воплотить ваши идеи в реальность.
        </p>

        <h4 class="text-center">Главный офис</h4>
        <img src="images/office.jpg" alt="Главный офис" class="img-fluid d-block mx-auto rounded my-3">

        <div class="text-center">
            <a href="autorisation" class="btn btn-primary mx-1 mt-2">Войти в аккаунт</a>
            <a href="registration" class="btn btn-secondary mx-1 mt-2">Регистрация</a>
        </div>
    </div>
<?php else: ?>
    <!-- Главное меню авторизованного пользователя -->
    <div class="bg-body-tertiary rounded-3 shadow p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Главное меню</h3>
            <div>
                <a href="edit" class="btn btn-warning me-2">Админ панель</a>
                <a href="logout" class="btn btn-danger">Выйти</a>
            </div>
        </div>

        <h4 class="text-center">Фильтры</h4>
        <form method="get" action="./" class="text-start mt-4">
            <div class="mb-3">
                <label class="form-label">По году рождения:</label>
                <input type="text" class="form-control" name="birth" placeholder="Введите год рождения" value="<?= htmlspecialchars($birth_filter) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">По ФИО:</label>
                <input type="text" class="form-control" name="name" placeholder="Введите ФИО" value="<?= htmlspecialchars($name_filter) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">По личной характеристике:</label>
                <input type="text" class="form-control" name="description" placeholder="Введите личную характеристику" value="<?= htmlspecialchars($description_filter) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">По должности:</label>
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
    <div class="bg-body-tertiary rounded-3 shadow p-4 mt-4">
        <h4 class="text-center">Сотрудники</h4>
        <?php if (empty($employees)): ?>
            <div class="alert alert-info text-center">
                Нет сотрудников, соответствующих критериям фильтра
            </div>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-striped align-middle text-center mt-3 mb-0">
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
                                                                    <?php if (!empty($employee['img_path']) && file_exists(__DIR__ . '/catalogue_images/' . $employee['img_path'])): ?>
                                        <img src="catalogue_images/<?= htmlspecialchars($employee['img_path']) ?>" alt="Фото" width="100" height="100" class="rounded object-fit-cover">
                                    <?php else: ?>
                                        <span class="d-inline-block bg-secondary rounded px-2 py-4 small">Нет фото</span>
                                    <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($employee['name']) ?></td>
                            <td><?= htmlspecialchars($employee['birth']) ?></td>
                            <td><?= htmlspecialchars($employee['job_title_name'] ?? 'Не указана') ?></td>
                            <td><?= htmlspecialchars($employee['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

</div>
</div>
</div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
