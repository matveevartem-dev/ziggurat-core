<?php

namespace App\Infrastructure\Repository\Editor;

use App\Domain\Editor\EditorRepositoryInterface;
use App\Infrastructure\Integration\LegacyApiGateway;

class LegacyEditorRepository implements EditorRepositoryInterface
{
    private LegacyApiGateway $gateway;

    public function __construct(LegacyApiGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function init(int $translationId, string $cookieHeader): array
    {
        // Просто дёргаем базовый шлюз по имени контракта
        return $this->gateway->sendRequest('editor-init', ['translationId' => $translationId], $cookieHeader);
    }

    public function search(array $queryParams, string $cookieHeader): array
    {
        return $this->gateway->sendRequest('editor-search', $queryParams, $cookieHeader);
    }
}
