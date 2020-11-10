<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Crawler;

class CrawlController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        return view('layout');
    }

    public function crawl(Request $request): \Illuminate\View\View
    {
        // $url = $request->input('url');
        $url = 'https://agencyanalytics.com/';
        $c = new Crawler($url);
        $c->run();
        return view('crawl', ['url' => $url], $c->report());
    }
}
