<?php

namespace Schildhain\PackageSkeleton\Commands;

use Illuminate\Console\Command;

class PackageSkeletonCommand extends Command
{
    public $signature = 'package-skeleton';

    public $description = 'PackageSkeleton command';

    public function handle()
    {
        $this->comment('All done');
    }
}
