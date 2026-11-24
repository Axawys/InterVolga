<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';

session_start();

$isLoggedIn = isset($_SESSION['user_id']);

pageHeader('Архитектурное бюро');
?>
<main class="flex-grow-1">
<div class="container my-5">
<div class="row justify-content-center">
<div class="col-lg-10 col-xl-8">

<?php if (!$isLoggedIn): ?>
    <!-- Гостевая страница -->
    <div class="bg-body-tertiary rounded-3 shadow p-4">
        <h2 class="text-center mb-3">Архитектурное бюро</h2>
        <p>
            Добро пожаловать на сайт нашего архитектурного бюро! Мы занимаемся проектированием
            жилых и коммерческих объектов, а также реализуем уникальные дизайнерские решения
            для интерьеров и экстерьеров.
        </p>
        <p>
            Наша команда состоит из опытных архитекторов, инженеров и дизайнеров,
            готовых воплотить ваши идеи в реальность.
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

    </div>

    <div id="catalogue">
        <p class="alert alert-info mt-4" role="status">Загрузка каталога…</p>
    </div>
    <noscript><p class="alert alert-warning mt-4">Для просмотра каталога включите JavaScript.</p></noscript>

    <template id="catalogue-template">
        <div class="bg-body-tertiary rounded-3 shadow p-4 mt-4">
            <h4 class="text-center">Фильтры</h4>
            <form class="mt-4" @submit.prevent="applyFilters">
                <div v-for="field in fields" :key="field.name" class="mb-3">
                    <label :for="field.name" class="form-label">{{ field.label }}</label>
                    <input :id="field.name" :name="field.name" :placeholder="field.placeholder"
                           v-model="filters[field.name]" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="job_title_id" class="form-label">По должности</label>
                    <select id="job_title_id" name="job_title_id" v-model="filters.job_title_id" class="form-select">
                        <option value="">Все</option>
                        <option v-for="job in jobTitles" :key="job.id" :value="String(job.id)">{{ job.name }}</option>
                    </select>
                    <p v-if="jobTitlesError" class="text-warning mt-2" role="alert">{{ jobTitlesError }}</p>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary me-2">Применить фильтр</button>
                    <button type="button" class="btn btn-secondary" @click="clearFilters">Очистить</button>
                </div>
            </form>
        </div>

        <div class="bg-body-tertiary rounded-3 shadow p-4 mt-4" :aria-busy="loading">
            <h4 class="text-center">Сотрудники</h4>
            <p v-if="loading" class="text-center" role="status">Загрузка…</p>
            <p v-else-if="error" class="alert alert-danger" role="alert">{{ error }}</p>
            <p v-else-if="!employees.length" class="alert alert-info text-center">
                Нет сотрудников, соответствующих критериям фильтра
            </p>
            <div v-else class="table-responsive">
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
                        <tr v-for="employee in employees" :key="employee.id">
                            <td>
                                <img v-if="employee.img_path && !employee.photoUnavailable"
                                     :src="'catalogue_images/' + encodeURIComponent(employee.img_path)"
                                     @error="employee.photoUnavailable = true" alt="Фото"
                                     width="100" height="100" class="rounded object-fit-cover">
                                <span v-else class="d-inline-block bg-secondary rounded px-2 py-4 small">Нет фото</span>
                            </td>
                            <td>{{ employee.name }}</td>
                            <td>{{ employee.birth }}</td>
                            <td>{{ employee.job_title_name ?? 'Не указана' }}</td>
                            <td>{{ employee.description }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>
<?php endif; ?>

</div>
</div>
</div>
</main>
<?php pageFooter($isLoggedIn ? ['js/vue.global.prod.js', 'js/catalogue.js'] : []); ?>
