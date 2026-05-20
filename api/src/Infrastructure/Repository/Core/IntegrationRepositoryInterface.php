<?php
namespace App\Infrastructure\Repository\Core;

use App\Domain\Core\Dto\CoreRequestInterface;
use App\Domain\Core\Dto\CoreResponseInterface;

interface IntegrationRepositoryInterface
{
    /**
     * Прямой проброс запроса в Legacy-систему
     */
    public function proxy(string $method, CoreRequestInterface $request): CoreResponseInterface;

    /**
     * тут какой-то builder, что читает из нашей legacy-prozy капрты json и собирает запрос для Symfony\Contracts\HttpClient\HttpClientInterface
     */
    public function request(CoreRequestInterface $coreRequest): CoreRequestInterface;

    /**
     * тут какой-то builder, что читает ответ и собирает валидный объект CoreResponseInterface
     */
    public function response(CoreResponseInterface $coreRequest): CoreResponseInterface;
}
