<?php

declare(strict_types=1);

function getAllJobTitles(PDO $connect): array
{
    $stmt = $connect->query('SELECT id, name FROM job_title ORDER BY id');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function jobTitleExists(PDO $connect, int $id): bool
{
    $stmt = $connect->prepare('SELECT COUNT(*) FROM job_title WHERE id = ?');
    $stmt->execute([$id]);
    return (bool)$stmt->fetchColumn();
}

function findEmployeeById(PDO $connect, int $id): ?array
{
    $stmt = $connect->prepare('SELECT * FROM employees WHERE id = ?');
    $stmt->execute([$id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    return $employee ?: null;
}

function getEmployees(PDO $connect, array $filters = []): array
{
    $sql = "SELECT e.*, j.name AS job_title_name
            FROM employees e
            LEFT JOIN job_title j ON e.job_title_id = j.id";

    $where = [];
    $params = [];

    if (!empty($filters['birth'])) {
        $where[] = 'e.birth = :birth';
        $params[':birth'] = (int)$filters['birth'];
    }

    if (!empty($filters['name'])) {
        $where[] = 'e.name LIKE :name';
        $params[':name'] = '%' . $filters['name'] . '%';
    }

    if (!empty($filters['description'])) {
        $where[] = 'e.description LIKE :description';
        $params[':description'] = '%' . $filters['description'] . '%';
    }

    if (!empty($filters['job_title_id'])) {
        $where[] = 'e.job_title_id = :job_title_id';
        $params[':job_title_id'] = (int)$filters['job_title_id'];
    }

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY e.id DESC';

    $stmt = $connect->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
