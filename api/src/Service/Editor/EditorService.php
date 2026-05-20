<?php

namespace App\Service\Editor;

use App\Infrastructure\Repository\Core\IntegrationRepositoryInterface;
use Yiisoft\Hydrator\Hydrator;

class TranslationService
{
    // Yii3 сам принесет и Репозиторий, и Гидратор из контейнера
    public function __construct(
        private readonly IntegrationRepositoryInterface $repo,
        private readonly Hydrator $hydrator
    ) {}

    /**
     * ПОИСК И ЗАГРУЗКА (Теперь без передачи hydrator в аргументах)
     */
    public function search(array $rawQuery, string $cookie = ''): array
    {
        $tid = (int)($rawQuery['translationId'] ?? throw new \InvalidArgumentException("Missing translationId"));
        $page = (int)($rawQuery['page'] ?? 1);
        
        $filters = [
            'orig'  => $rawQuery['q_orig'] ?? '',
            'trans' => $rawQuery['q_trans'] ?? '',
            'id'    => $rawQuery['q_id'] ?? ''
        ];

        $items = $this->repo->getSegments($tid, $page, $filters, $cookie);
        $stats = $this->repo->getFileStats($tid);
        
        return [
            'status' => 'success',
            'data' => [
                'items' => $items,
                'total_pages' => 550, // TODO: вычислять на основе count
                'stats' => [
                    'total' => $stats['total'],
                    'verified' => $stats['verified']
                ]
            ]
        ];
    }

    /**
     * Инициализация метаданных (v290)
     */
    public function init(array $rawQuery, string $cookie = ''): array
    {
        $tid = (int)($rawQuery['translationId'] ?? throw new \InvalidArgumentException("Missing translationId"));
        $page = (int)($rawQuery['page'] ?? 1);

        // Получаем "чистые" данные из репозитория
        $data = $this->repo->init($tid, $page, $cookie);

        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    /**
     * ОБНОВЛЕНИЕ
     */
    public function update(array $rawQuery, array $rawBody, string $cookie = ''): array
    {
        $id = (int)($rawQuery['id']);
        $tid = (int)($rawQuery['translationId']);
        
        $success = $this->repo->updateSegment(
            $id, $tid, 
            $rawBody['text'] ?? '', 
            $rawQuery['author'] ?? 'man', 
            $cookie, 
            $rawBody['orig'] ?? null
        );

        $stats = $this->repo->getFileStats($tid);

        return [
            'status' => $success ? 'success' : 'error',
            'stats' => [
                'total' => $stats['total'],
                'verified' => $stats['verified']
            ]
        ];
    }
}
