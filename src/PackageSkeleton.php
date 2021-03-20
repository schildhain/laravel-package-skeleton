<?php


namespace Schildhain\PackageSkeleton;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

class PackageSkeleton
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var Repository
     */
    protected $config;

    /**
     * PackageSkeleton constructor.
     *
     * @param Application $app
     * @param Repository  $config
     */
    public function __construct(Application $app, Repository $config)
    {
        $this->config = $config;
        $this->app = $app;
    }
}
