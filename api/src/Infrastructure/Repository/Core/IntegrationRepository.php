<?php
namespace App\Infrastructure\Repository\Core;

use App\Domain\Core\Dto\CoreRequestInterface;
use App\Domain\Core\Dto\CoreResponseInterface;
use Yiisoft\Di\Container;
use Override;

final class IntegrationRepository implements IntegrationRepositoryInterface
{
    public function __construct(
        private Container $container,
        private CoreRepositoryInterface $coreRepo,
        private LegacyRepositoryInterface $legacyRepo
    ) {}

    #[Override]
    public function proxy(string $method, CoreRequestInterface $request): CoreResponseInterface
    {
        try {
            // пробуем забрать данные из postgres
            $response = $this->coreRepo->$method($request);
        } catch (\Throwable $e) {
            try {
                // пробуем забрать данные из legacy
                $response =$this->legacyRepo->$method($request);
            } catch (\Throwable $e) {
                // можно добавить логгирование и спокойно умереть )))
                throw $e;
            }
        }

        return $response;
    }

    #[Override]
    public function request(CoreRequestInterface $coreRequest): CoreRequestInterface
    {
        throw new \Exception('Not implemented');
    }

    #[Override]
    public function response(CoreResponseInterface $coreRequest): CoreResponseInterface
    {
        throw new \Exception('Not implemented');
    }
}
