<?php

namespace App\Domain\Editor;

interface EditorRepositoryInterface
{
    /**
     * Инициализация метаданных (editor-init)
     * Возвращает массив с именами файлов
     */
    public function init(int $translationId, string $cookieHeader): array;

    /**
     * Поиск сегментов (editor-search)
     */
    public function search(array $queryParams, string $cookieHeader): array;
}
