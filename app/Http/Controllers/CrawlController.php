<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Crawler;

class CrawlController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): \Illuminate\Http\Response
    {
        return view('layout');
    }

    public function crawl(Request $request): \Illuminate\Http\Response
    {
        // $url = $request->input('url');
        $url = 'https://agencyanalytics.com/';
        $c = new Crawler($url);
        $c->run();
        return view('crawl', ['url' => $url], $c->report());
    }
}
