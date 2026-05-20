<?php

namespace App;

use App\Service\Editor\TranslationService;

class Application
{
    public function __construct(
        private readonly TranslationService $translationService
    ) {}

    /**
     * Тут роутинг, но плохо что он общий для всего приложения сразу и им будет сложно потом управлять
     */
    public function handleRequest(array $rawQuery, array $rawBody, string $cookieHeader): array
    {
        $action = $rawQuery['action'] ?? $rawQuery['r'] ?? 'default';

        return match ($action) {
            'editor-search' => $this->translationService->search($rawQuery, $cookieHeader),
            'editor-init'   => $this->translationService->init($rawQuery, $cookieHeader),
            // Сюда будем добавлять новые экшены: update, merge и т.д.
            default => [
                'status' => 'error', 
                'message' => "Unknown route: $action"
            ]
        };
    }
}
