<?php

declare(strict_types=1);

// Соединение с базой: создаётся при первом вызове и переиспользуется до конца запроса
function db(): PDO
{
    static $connect = null;

    if ($connect === null) {
        $connect = new PDO('mysql:host=db;dbname=appdb;charset=utf8', 'user', 'password', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    return $connect;
}
