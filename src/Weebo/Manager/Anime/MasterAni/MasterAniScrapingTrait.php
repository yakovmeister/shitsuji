<?php

namespace Yakovmeister\Weebo\Manager\Anime\MasterAni;

trait MasterAniScrapingTrait
{
	/**
	 * [instance of Yakovmeister\Weebo\Component\Scraper]
	 * @var Object
	 */
	protected $scraper;

	/**
	 * [set scraping instance]
	 * @param \Yakovmeister\Weebo\Component\Scraper $scraper
	 * @return $this
	 */
	public function setScraper(\Yakovmeister\Weebo\Component\Scraper $scraper)
	{
		$this->scraper = $scraper;

		return $this;
	}

	/**
	 * [get scraper instance]
	 * @return $this->scraper
	 */
	public function getScraper()
	{
		return $this->scraper;
	}

	/**
	 * [get anime title as list]
	 * @param  String $html [html document that contains search results of anime]
	 * @return String
	 */
	public function scrapeAnimeTitle($html)
	{
		return $this->getScraper()->capture($html)
                    ->scrape('quicksearch-title')
                    ->getResults(); 
	}

	/**
	 * [get anime link as list]
	 * @param  String $html [html document that contains search results of anime]
	 * @return String
	 */
	public function scrapeAnimeLink($html)
	{
        return $this->getScraper()->capture($html)
                    ->scrape('quicksearch-result')->byAttribute('href')
                    ->getResults();
	}

	/**
	 * [get anime episodes title as list]
	 * @param  String $html [html document that contains search results of anime]
	 * @return String
	 */
	public function scrapeEpisodesList($html)
	{
		return $this->getScraper()->capture($html)
            		->scrape('epp')->byAttribute('data-episode')
                    ->getResults();
	}

	/**
	 * [get anime mirror link as list]
	 * @param  String $html [html document that contains search results of anime]
	 * @return String
	 */
	public function scrapeMirrorLink($html, $quality, $language)
	{
		return $this->getScraper()->capture($html) 
                    ->scrape($quality, 'data-quality')
                    ->scrape($language, 'data-lang')
                    ->byAttribute('data-src')->getResults();
	}
}