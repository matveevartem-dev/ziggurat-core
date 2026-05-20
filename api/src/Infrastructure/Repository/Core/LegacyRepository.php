<?php
namespace App\Infrastructure\Repository\Core;

use App\Domain\Core\Dto\CoreRequestInterface;
use App\Domain\Core\Dto\CoreResponseInterface;
use Yiisoft\Di\Container;
use Override;

final class LegacyRepository implements IntegrationRepositoryInterface
{
    public function __construct(
        private Container $container
    ) {}

    #[Override]
    public function proxy(CoreRequestInterface $request): CoreResponseInterface
    {
        throw new \Exception('Not implemented');
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
