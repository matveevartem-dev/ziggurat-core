<?php
namespace App\Domain\Editor\Dto;

use Override;

readonly class EditorDuplicatesResponse implements EditorDtoInterface
{
    #[Override]
    public function toArray(): array
    {
        throw new \Exception('Not implemented');
    }
}
