<?php
namespace App\Infrastructure\Repository\Core;

use Yiisoft\Di\Container;

final class CoreRepository implements CoreRepositoryInterface
{
    public function __construct(
        private Container $container
    ) {}
}
