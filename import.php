<?php
require_once 'db.php';
require_once 'includes/helpers.php';
requireAdminAccess();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: edit?backup=error');
    exit;
}

try {
    $sql = file_get_contents($_FILES['sql_file']['tmp_name']);
    $connect->exec($sql);
    header('Location: edit?backup=ok');
} catch (PDOException $e) {
    header('Location: edit?backup=error');
}
exit;
