<?php
namespace App\Domain\Core\Enum;

enum RepositoryMethodEnum: string {
    case getSegments = 'getSegments';
    case getFileStats = 'getFileStats';
}
