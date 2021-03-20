<?php


namespace Schildhain\PackageSkeleton;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Schildhain\PackageSkeleton\PackageSkeleton
 */
class PackageSkeletonFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PackageSkeleton';
    }
}
