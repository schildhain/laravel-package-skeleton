<?php


namespace Schildhain\PackageSkeleton;

//use Illuminate\Support\ServiceProvider;

use Schildhain\PackageSkeleton\Commands\PackageSkeletonCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider as ServiceProvider;

class PackageSkeletonServiceProvider extends ServiceProvider {
    public function configurePackage( Package $package ): void {
        $package->name( 'package_skeleton' )
            ->hasCommands( PackageSkeletonCommand::class )
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasViewComponents( "package-skeleton", /* ... $components */ )
            //->hasViewComposer($view, $viewComposer)
            //->sharesDataWithAllViews($name, $value)
            ->hasTranslations()
            ->hasRoutes( [ 'web' ] )
            ->hasMigrations( [ 'create_package_skeleton_table' ] );
    }

    public function register() {
        parent::register();

        $this->app->bind(
            PackageSkeleton::class,
            function ( $app ) {
                $config = config( 'package_skeleton', [] );

                return new PackageSkeleton( $app, $config );
            }
        );
    }

    public function boot() {
        parent::boot();
    }
}
