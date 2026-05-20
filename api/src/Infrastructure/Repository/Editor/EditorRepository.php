<?php
namespace App\Infrastructure\Repository\Editor;

use App\Domain\Core\Dto\CoreRequestInterface;
use App\Domain\Editor\Dto\EditorSearchResponse;
use App\Domain\Editor\Dto\EditorUpdateResponse;
use App\Domain\Editor\Dto\EditorDuplicatesResponse;
use Override;

final class EditorRepository implements EditorRepositoryInterface
{
    #[Override]
    public function search(CoreRequestInterface $request): EditorSearchResponse
    {
        throw new \Exception('Not implemented');
    }

    #[Override]
    public function update(CoreRequestInterface $request): EditorUpdateResponse
    {
        throw new \Exception('Not implemented');
    }

    #[Override]
    public function duplicates(CoreRequestInterface $request): EditorDuplicatesResponse
    {
        throw new \Exception('Not implemented');
    }
}
