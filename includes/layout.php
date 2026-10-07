<?php

declare(strict_types=1);

// Начало страницы: <head> с Bootstrap и открытый <body>
function pageHeader(string $title, string $bodyClass = 'd-flex flex-column min-vh-100'): void
{
    $title = htmlspecialchars($title);
    $bodyClass = htmlspecialchars($bodyClass);

    echo <<<HTML
        <!DOCTYPE html>
        <html lang="ru" data-bs-theme="dark">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{$title}</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="{$bodyClass}">

        HTML;
}

/**
 * Конец страницы: подвал (если нужен), скрипты и закрывающие теги.
 *
 * @param list<string> $scripts пути к JS-файлам относительно public/
 */
function pageFooter(array $scripts = [], bool $showFooter = true): void
{
    if ($showFooter) {
        $year = date('Y');
        echo <<<HTML
            <footer class="mt-5 py-3 bg-body-tertiary border-top text-center small">
                Архитектурное бюро {$year} &copy; Все права защищены
            </footer>

            HTML;
    }
    foreach ($scripts as $script) {
        echo '<script src="' . htmlspecialchars($script) . "\"></script>\n";
    }
    echo "</body>\n</html>\n";
}
