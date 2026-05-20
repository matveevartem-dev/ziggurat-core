<?php

namespace App\Infrastructure\Integration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Единый системный шлюз к Legacy-инфраструктуре
 */
class LegacyApiGateway
{
    private HttpClientInterface $httpClient;
    private array $mapping;

    public function __construct(HttpClientInterface $httpClient, string $mappingFilePath)
    {
        $this->httpClient = $httpClient;
        // Загружаем нашу сквозную JSON-матрицу контрактов из корня ziggurat-translation-os
        $this->mapping = json_decode(file_get_contents($mappingFilePath), true) ?? [];
    }

    /**
     * Универсальный метод отправки конвейера запросов
     */
    public function sendRequest(string $routeName, array $dynamicParams, string $cookieHeader): array
    {
        $contract = $this->mapping[$routeName] ?? null;
        if (!$contract) {
            throw new \RuntimeException("Contract for route '{$routeName}' not found.");
        }

        // Тут разворачивается твой конвейер { INTERFACE_METHOD: [{url, request, response}] }
        // Он последовательно делает запросы, подставляет плейсхолдеры и возвращает чистый массив
        try {
            $response = $this->httpClient->request('POST', $contract['url'], [
                'query' => $dynamicParams,
                'headers' => ['Cookie' => $cookieHeader, 'Accept' => 'application/json'],
                'json' => $contract['request'] // Шаблон тела запроса
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => "Gateway Error: " . $e->getMessage()];
        }
    }
}
