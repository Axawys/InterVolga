<?php

declare(strict_types=1);

function employeeFilters(array $query): array
{
    $filters = [];
    foreach (['birth', 'name', 'description', 'job_title_id'] as $name) {
        $value = $query[$name] ?? '';
        $filters[$name] = is_string($value) ? normalizeString($value) : '';
    }

    return $filters;
}
