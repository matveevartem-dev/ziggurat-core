<?php
namespace App\Domain\Editor\Dto;

use Override;

readonly class EditorSearchResponse implements EditorDtoInterface
{
    #[Override]
    public function toArray(): array
    {
        throw new \Exception('Not implemented');
    }
}
