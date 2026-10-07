<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/employee_repository.php';
require_once __DIR__ . '/includes/helpers.php';

/**
 * Данные для главной страницы: фильтры из запроса, отфильтрованные сотрудники и список должностей.
 *
 * @return array{filters: array{birth: string, name: string, description: string, job_title_id: string}, employees: array, job_titles: array}
 */
function indexPageData(array $query): array
{
    $filters = [
        'birth' => normalizeString($query['birth'] ?? ''),
        'name' => normalizeString($query['name'] ?? ''),
        'description' => normalizeString($query['description'] ?? ''),
        'job_title_id' => normalizeString($query['job_title_id'] ?? ''),
    ];

    return [
        'filters' => $filters,
        'employees' => getEmployees(db(), $filters),
        'job_titles' => getAllJobTitles(db()),
    ];
}
