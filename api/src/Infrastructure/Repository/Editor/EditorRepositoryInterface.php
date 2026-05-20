<?php
namespace App\Infrastructure\Repository\Editor;

use App\Domain\Core\Dto\CoreRequestInterface;
use App\Domain\Editor\Dto\EditorSearchResponse;
use App\Domain\Editor\Dto\EditorUpdateResponse;
use App\Domain\Editor\Dto\EditorDuplicatesResponse;

interface EditorRepositoryInterface
{
    /**
     * Возвращает список сегментов, упакованных в DTO/массивы
     */
    public function search(CoreRequestInterface $request): EditorSearchResponse;

    /**
     * Обновление сегмента
     */
    public function update(CoreRequestInterface $request): EditorUpdateResponse;


    /**
     * Возвращает список всех segment_id, которые являются повторениями текущего сегмента
     */
    public function duplicates(CoreRequestInterface $request): EditorDuplicatesResponse;
}
