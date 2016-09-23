<?php

namespace Yakovmeister\Weebo\Manager\Anime\HentaiHaven;

use Yakovmeister\Weebo\Component\Scraper;
use Yakovmeister\Weebo\Component\IO;
use Yakovmeister\Weebo\Manager\PageLoadManager;
use Yakovmeister\Weebo\Manager\DownloadManager;
use Yakovmeister\Weebo\Manager\Anime\AnimeInterface;

class HHavenManager implements AnimeInterface
{
	
	public function __construct(Scraper $scraper, PageLoadManager $pageLoader, DownloadManager $downloadManager)
	{

		$this->setScraper($scraper);

		$this->page = $pageLoader;

		$this->downloadManager = $downloadManager;

		$this->setSource([
 	        "base"    => "http://hentaihaven.org/",
    	    "search"  => "/?s=:search-key",
    	    "episode"   => ""
		]);
	}

}