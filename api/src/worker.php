<?php

/**
 * ZIGGURAT v2: RoadRunner + Yii3 Worker
 * Статус: FULL YII3 CONTAINER INTEGRATION
 */

use Spiral\RoadRunner;
use Nyholm\Psr7;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Config\Config;
use Yiisoft\Config\ConfigPaths;
use App\Application;

require __DIR__ . '/../vendor/autoload.php';

// --- 1. ИНИЦИАЛИЗАЦИЯ YII3 (Вне цикла) ---

// Настраиваем пути конфигов Yii3
$config = new Config(
    new ConfigPaths(__DIR__, 'config'),
    null // environment
);

// Создаем контейнер на основе PHP-конфигов (директория config/di.php)
$containerConfig = ContainerConfig::create()
    ->withDefinitions($config->get('di'));

$container = new Container($containerConfig);
// Вытаскиваем наше приложение из контейнера
// Yii3 сам создаст HttpClient, Repository и Service, прокинув их в конструктор!
/** @var Application $app */
$app = $container->get(Application::class);

// Настройка RoadRunner
$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();
$psr7Worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

// --- 2. ЦИКЛ ОБРАБОТКИ ---

while ($request = $psr7Worker->waitRequest()) {
    try {
        $queryParams = $request->getQueryParams();
        $cookieHeader = $request->getHeaderLine('Cookie');
        $body = json_decode((string)$request->getBody(), true) ?? [];

        // Application теперь — это полноценный Yii3-объект
        $result = $app->handleRequest($queryParams, $body, $cookieHeader);

        $response = $psrFactory->createResponse(200)
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($result));

        $psr7Worker->respond($response);

    } catch (\Throwable $e) {
        $response = $psrFactory->createResponse(500);
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Yii3 Worker Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]));
        $psr7Worker->respond($response);
    }
}
