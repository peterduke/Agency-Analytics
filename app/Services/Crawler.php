<?php

namespace App\Services;

class Crawler
{
    const MAX_PAGES_TO_CRAWL = 5;
    protected $toCrawl = [];
    protected $crawled = [];
    protected $internalDomain = '';
    protected $images = [];
    protected $intLinks = [];
    protected $extLinks = [];
    protected $pageLoad = 0;
    protected $wordCount = 0;
    protected $titleLength = 0;

    public function setInternalDomain($domain)
    {
        $this->internalDomain = $domain;
    }

    public function addUrl($url)
    {
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

            // time page load
            $startTime = microtime(true);
            $html = $this->load($url);
            $elapsed = microtime(true) - $startTime;
            $this->pageLoad += $elapsed;

            // get the stats and accumulate them in properties of $this

            $values = $this->parse($html);

            foreach($values['imgs'] as $img) {
                $this->images[$this->normalize($img)] = true;
            }

            foreach($values['links'] as $href) {
                $href = $this->normalize($href);
                if ($this->isLinkInternal($href)) {
                    $this->intLinks[$href] = true;
                } else {
                    $this->extLinks[$href] = true;
                }
            }

            $this->titleLength += $values['titleLength'];

            $this->wordCount += $values['wordCount'];

            // record that we crawled this page
            $this->crawled[$url] = true;
        }
    }

    /**
     * Load HTML from the given url
     * 
     * @param string $url
     * 
     * @return string
     */
    public function load($url)
    {
        // TODO load the file with curl or guzzle
        return file_get_contents(base_path('docs/agencyanalytics.com.html'));
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
     */
    public function isLinkInternal($href)
    {
        // urls starting with a single slash are internal
        if ($href == '/' || $href[0] == '/' && $href[1] != '/') {
            return true;
        }

        // trim the schema off the beginning
        $href = preg_replace('/^((http(s?):)?\/\/)/', '', $href);
        // see if it starts with the local domain
        return substr($href, 0, strlen($this->internalDomain)) == $this->internalDomain;
    }

    /**
     * Normalize the url.
     * 
     * Currently only trims backslash from end but could be extended.
     */
    public function normalize($url)
    {
        if ($url != '\\') {
            return rtrim($url, '\\');
        }
    }

    /**
     * Report on the statistics
     * 
     * @return array
     */
    public function report()
    {

    }
}