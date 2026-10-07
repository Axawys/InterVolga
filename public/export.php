<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdminAccess();

$sqlDump = "-- Бэкап базы данных от " . date('d.m.Y H:i') . "\n\n";
$sqlDump .= "SET NAMES utf8mb4;\n\n";

$tables = $connect->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    // Структура таблицы
    $createTable = $connect->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
    $sqlDump .= $createTable['Create Table'] . ";\n\n";

    // Данные таблицы
    $rows = $connect->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $values = array_map(fn($value) => $value === null ? 'NULL' : $connect->quote($value), $row);
        $sqlDump .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
    }
    $sqlDump .= "\n";
}

$filename = 'backup_' . date('Y-m-d_H-i') . '.sql';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $sqlDump;
