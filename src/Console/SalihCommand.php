<?php

namespace Salih\Composer\Console;

use Illuminate\Console\Command;

class SalihCommand extends Command
{
    use CreateFiles;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salih:install {--path=file : Json file path}';


    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Install required files from json file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('path')!='file') {

            echo 'Installing...';

            $this->createFiles($this->option('path'));

        }else {
            echo 'error please select file';
        }
        return 0;
    }

    public function install(string $filePath)
    {
        $this->createFiles($filePath);
    }

}