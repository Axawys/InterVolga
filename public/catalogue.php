<?php

declare(strict_types=1);

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

require_once getenv('COMPOSER_AUTOLOAD') ?: __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/api/bootstrap.php';
require_once __DIR__ . '/../logic.php';

$app = new Application();
$json = static function (array $data, int $status = 200, array $headers = []): JsonResponse {
    $response = new JsonResponse(null, $status, $headers);
    $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $response->setData($data);
};

$app->before(function (Request $request) {
    if ($request->isMethod('HEAD')) {
        throw new MethodNotAllowedHttpException(['GET']);
    }
});

$app->get('/api/read{extension}', function (Request $request) use ($json) {
    try {
        return $json([
            'success' => true,
            'data' => getEmployees(db(), employeeFilters($request->query->all())),
        ]);
    } catch (Throwable $e) {
        return $json([
            'success' => false,
            'error' => 'SERVER_ERROR',
            'message' => 'Не удалось получить список сотрудников.',
        ], 500);
    }
})->value('extension', '')->assert('extension', '(?:\\.php)?');

$app->get('/api/get_job_titles{extension}', function () use ($json) {
    try {
        return $json(['success' => true, 'data' => getAllJobTitles(db())]);
    } catch (Throwable $e) {
        return $json([
            'success' => false,
            'error' => 'SERVER_ERROR',
            'message' => 'Не удалось получить список должностей.',
        ], 500);
    }
})->value('extension', '')->assert('extension', '(?:\\.php)?');

$app->on(KernelEvents::EXCEPTION, function (ExceptionEvent $event) use ($json) {
    $exception = $event->getThrowable();
    if ($exception instanceof MethodNotAllowedHttpException) {
        $event->setResponse($json([
            'success' => false,
            'error' => 'METHOD_NOT_ALLOWED',
            'message' => 'Используйте GET.',
        ], 405, $exception->getHeaders()));
    }
});

$app->run();
