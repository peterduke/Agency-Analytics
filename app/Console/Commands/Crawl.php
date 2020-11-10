<?php

namespace App\Console\Commands;

use App\Services\Crawler;
use Illuminate\Console\Command;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl web site and report some statistics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $url = $this->argument('url');
        $c = new Crawler($url);
        $c->run();
        print_r($c->report());
    }
}
