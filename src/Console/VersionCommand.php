<?php

namespace Salih\Composer\Console;

use Illuminate\Console\Command;

class VersionCommand extends Command
{
    use CreateFiles;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salih {--version : Get version}';


    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Get version';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        return $this->version();
    }

    public function version()
    {
        echo 'v1.1.19';
    }

}