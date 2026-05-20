<?php

declare(strict_types=1);

use App\Domain\Editor\EditorRepositoryInterface;
use App\Infrastructure\Repository\Editor\LegacyEditorRepository;
use PDO;

return [
    // ЧИСТЫЙ ENTERPRISE: Прямой, ультрабыстрый сокет к Postgres 15 через PDO
    PDO::class => static function () {
        // Подтягиваем продовые переменные, которые ты настроил для CentOS 9
        $host = $_ENV['DB_HOST'] ?? 'postgres';
        $port = $_ENV['DB_PORT'] ?? '5432';
        $db   = $_ENV['DB_NAME'] ?? 'ziggurat';
        $user = $_ENV['DB_USER'] ?? 'postgres';
        $pass = $_ENV['DB_PASSWORD'] ?? 'secret';

        $dsn = "pgsql:host={$host};port={$port};dbname={$db};";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Выключаем эмуляцию препарированных запросов, пусть Postgres сам оптимизирует JSONB-дерево
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return $pdo;
    },

    // Напрямую связываем интерфейс с нашей обновленной базовой реализацией
    EditorRepositoryInterface::class => static function (\Psr\Container\ContainerInterface $container) {
        return new LegacyEditorRepository(
            $container->get(\Symfony\Contracts\HttpClient\HttpClientInterface::class),
            $container->get(PDO::class)
        );
    },
];
