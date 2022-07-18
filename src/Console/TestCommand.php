<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class salih extends Command
{
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
     * @return int
     */
    public function handle()
    {
        if ($this->option('path')!='file') {
            echo 'ok';
            /*
             $this->>installLaraJson();
             * */
        }else {
            echo 'error please select file';
        }
        return 0;
    }
}
