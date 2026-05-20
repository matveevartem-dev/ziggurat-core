<?php
namespace App\Domain\Editor\Dto;

readonly class EditorSegmentDto implements EditorDtoInterface
{
    public function __construct(
        public int $id,
        public string $source,
        public string $target,
        public string $author = 'Ai',
        public bool $isVerified = false,
        public ?int $memoryId = null,
        public array $styles = []
    ) {}

    // Метод для превращения DTO обратно в массив для Nuxt
    public function toArray(): array 
    {
        return [
            'id' => $this->id,
            'source_text' => $this->source,
            'target_text' => $this->target,
            'author' => $this->author,
            'is_verified' => $this->isVerified,
            'memory_id' => $this->memoryId,
            'styles' => $this->styles
        ];
    }
}
