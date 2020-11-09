<?php

namespace App\Services;
use InvalidArgumentException;
use GuzzleHttp\Client as Guzzle;

class Crawler
{
    const MAX_PAGES_TO_CRAWL = 5;
    protected $toCrawl = [];
    protected $crawled = [];
    protected $domain;
    protected $scheme;
    protected $images = [];
    protected $intLinks = [];
    protected $extLinks = [];
    protected $pageLoad = 0;
    protected $wordCount = 0;
    protected $titleLength = 0;

    /**
     * 
     * @param string $url The initial url to start the scraping 
     */
    public function __construct($url)
    {
        // save the scheme and domain so we can follow internal links
        $parsed = parse_url($url);
        if (!isset($parsed['scheme'], $parsed['host'])) {
            throw new InvalidArgumentException('Need to start with a valid scheme and host.');
        }
        $this->domain = $parsed['host'];
        $this->scheme = $parsed['scheme'];

        // start the scraping with $url. 
        // Trim domain and scheme off the beginning, would handle this better on a production app
        if (isset($parsed['path'])) {
            $url = $parsed['path'];
        } else {
            $url = '/';
        }
        if (isset($parsed['query'])) {
            $url .= '?' . $parsed['query'];
        }
        if (isset($parsed['fragment'])) {
            $url .= '' . $parsed['fragment'];
        }
        $this->toCrawl[$url] = true;
    }
    
    public function run()
    {
        while ($this->toCrawl && count($this->crawled) < self::MAX_PAGES_TO_CRAWL) { // while there are still pages to crawl, and we haven't gone over the limit
            
            // get a url to crawl. Storing them as ['url' => true, ...] to get Set properties i.e. uniqueness 
            // so popping from the array is a little bit complicated
            reset($this->toCrawl);
            $url = key($this->toCrawl);
            unset($this->toCrawl[$url]);

            // make sure we haven't already crawled this page
            if (array_key_exists($url, $this->crawled)) {
                continue;
            }

            // get the HTML and the load time
            $loaded = $this->load($url);
            $html = $loaded['html'];
            $status = $loaded['status'];
            $this->pageLoad += $loaded['pageLoad'];

            // get the stats and accumulate them in properties of $this

            $values = $this->parse($html);

            foreach($values['imgs'] as $img) {
                $this->images[$img] = true;
            }

            foreach($values['links'] as $href) {
                if ($this->isLinkInternal($href)) {
                    $this->intLinks[$href] = true;
                } else {
                    $this->extLinks[$href] = true;
                }
            }

            $this->titleLength += $values['titleLength'];

            $this->wordCount += $values['wordCount'];

            // record that we crawled this page
            $this->crawled[$url] = $status;

            // add the internal links to 'toCrawl' array
            $this->toCrawl = array_merge($this->toCrawl, $this->intLinks);
        }
    }

    /**
     * Load HTML from the given url
     * 
     * Also keep track of and return page load time
     * 
     * @param string $url
     * 
     * @return array
     */
    public function load($url)
    {
        $baseUrl = $this->scheme . '://' . $this->domain;
        $client = new Guzzle();
        $startTime = microtime(true);
        $response = $client->request('GET', $baseUrl . $url);
        $elapsed = microtime(true) - $startTime;
        $this->pageLoad += $elapsed;
        return ['html' => (string) $response->getBody(), 'pageLoad' => $elapsed, 'status' => $response->getStatusCode()];
    }

    /**
     * Parse the given HTML for desired statistics
     * 
     * @param string $html
     * 
     * @return void
     */
    public function parse($html)
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        // get the images
        $imgs = [];
        foreach($doc->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            if ($src) {
                $imgs[] = $src;
            }
        }

        // get the links
        $links = [];
        foreach($doc->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            if ($href) {
                $links[] = $href;
            }
        }

        // get the title length
        $titleLength = 0;
        $list = $doc->getElementsByTagName('title');
        if ($list->length > 0) {
            $titleLength = strlen($list->item(0)->textContent);
        }

        // get the total word count. Number of characters within the <body> divided by 5. Includes JavaScript and whitespace.
        $wordCount = 0;
        $list = $doc->getElementsByTagName('body');
        if ($list->length > 0) {
            $text = $list->item(0)->textContent;
            $wordCount = strlen($list->item(0)->textContent) / 5;
        }

        return compact('imgs', 'links', 'titleLength', 'wordCount');
    }

    /**
     * Is the link an internal link?
     * 
     * @return bool
     */
    public function isLinkInternal($href)
    {
        // urls starting with a single slash are internal
        if ($href == '/' || $href[0] == '/' && $href[1] != '/') {
            return true;
        }

        // trim the scheme off the beginning
        $href = preg_replace('/^((http(s?):)?\/\/)/', '', $href);
        // see if it starts with the local domain
        return substr($href, 0, strlen($this->domain)) == $this->domain;
    }

    /**
     * Report on the statistics
     * 
     * @return array
     */
    public function report()
    {
        $crawled = $this->crawled;
        $count = count($crawled);
        $img = count($this->images);
        $intLinks = count($this->intLinks);
        $extLinks = count($this->extLinks);
        $pageLoad = $this->pageLoad / $count;
        $wordCount = $this->wordCount / $count;
        $titleLength = $this->titleLength / $count;
        return compact('count', 'crawled', 'img', 'intLinks', 'extLinks', 'pageLoad', 'wordCount', 'titleLength');
    }
}