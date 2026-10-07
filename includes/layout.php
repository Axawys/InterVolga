<?php

declare(strict_types=1);

// Начало страницы: <head> с Bootstrap и открытый <body>
function pageHeader(string $title, string $bodyClass = 'd-flex flex-column min-vh-100'): void
{
    ?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title) ?></title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">
    <?php
}

/**
 * Конец страницы: подвал (если нужен), скрипты и закрывающие теги.
 *
 * @param list<string> $scripts пути к JS-файлам относительно public/
 */
function pageFooter(array $scripts = [], bool $showFooter = true): void
{
    if ($showFooter) {
        ?>
<footer class="mt-5 py-3 bg-body-tertiary border-top text-center small">
    Архитектурное бюро <?= date('Y') ?> &copy; Все права защищены
</footer>
        <?php
    }
    foreach ($scripts as $script) {
        ?>
<script src="<?= htmlspecialchars($script) ?>"></script>
        <?php
    }
    ?>
</body>
</html>
    <?php
}
