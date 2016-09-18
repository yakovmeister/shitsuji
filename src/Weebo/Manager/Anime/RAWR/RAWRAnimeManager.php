<?php

namespace Yakovmeister\Weebo\Manager\Anime\RAWR;

use Yakovmeister\Weebo\Component\Scraper;
use Yakovmeister\Weebo\Component\IO;
use Yakovmeister\Weebo\Manager\PageLoadManager;
use Yakovmeister\Weebo\Manager\DownloadManager;
use Yakovmeister\Weebo\Manager\Anime\AnimeInterface;

class RAWRAnimeManager implements AnimeInterface
{
	use \Yakovmeister\Weebo\Manager\Anime\AnimeTrait,
	    \Yakovmeister\Weebo\Manager\Anime\RAWR\RAWRScrapingTrait,
	    \Yakovmeister\Weebo\Manager\Anime\SelectionTrait;

	protected $page;

	protected $downloadManager;

	protected $loaded = [];

	protected $downloadLink = [];

	public function __construct(Scraper $scraper, PageLoadManager $pageLoader, DownloadManager $downloadManager)
	{

		$this->setScraper($scraper);

		$this->page = $pageLoader;

		$this->downloadManager = $downloadManager;

		$this->setSource([
 	        "base"    => "http://rawranime.tv/",
    	    "search"  => "/index.php?ajax=anime&do=search&s=:search-key",
    	    "episode"   => "/watch/:anime-id/episode-:anime-episode/?l=:anime-language&q=:anime-quality"
		]);
	}

	/**
	 * [search anime and cache result (both title and link)]
	 * @param  String $searchKey       [the anime you want to find]
	 * @param  String $quality=720p    [video quality]
	 * @param  String $language=subbed [video language]
	 * @return $this
	 */
	public function searchAnime($animeTitle, $quality = "720p", $language = "subbed")
	{

		$this->setQualityPreference($this->selectQuality($quality));

		$this->setLanguagePreference($this->selectLanguage($language));

		$this->loaded["anime"] = $this->page->load($this->search($animeTitle));

		if($this->loaded["anime"]["status"] == 404) return ;

		$animeTitle = $this->scrapeAnimeTitle($this->loaded["anime"]["message"]);
		$animeLink  = $this->scrapeAnimeLink($this->loaded["anime"]["message"]);

		for($index = 0; $index < count($animeLink); $index++)
		{
			$this->setAnime([
				"title" => $animeTitle[$index],
				"link"  => $animeLink[$index]
			]);
		}

		return $this;
	}

	/**
	 * [selected anime index]
	 * @param  Integer $index=1 [index of the anime you want to download]
	 * @return $this
	 */
	public function selectAnime($index = 1)
	{
		$this->setAnimePreference(($index - 1));

		return $this;
	}

	/**
	 * [fetch episodes and cache result (both episode number and link)]
	 * @return $this
	 */
	public function captureEpisodes()
	{
		$this->loaded["episode"] = $this->page->load("{$this->getBaseURL()}/{$this->anime[$this->getAnimePreference()]["link"]}");

		if($this->loaded["episode"]["status"] == 404) return ;

		$episodeTitle = $this->scrapeEpisodesList($this->loaded["episode"]["message"]);

		for ($index = 0; $index < count($episodeTitle); $index++) 
		{ 
			$this->setEpisodes([
				"episode" => $episodeTitle[$index],
				"link"    => $this->episode($this->extractAnimeID(), ($index + 1), $this->getLanguagePreference(), $this->getQualityPreference())
			]);
		}

		return $this;
	}

	/**
	 * [selected episode index, can be array of integer or string that contain ranges]
	 * @param  Mixed  $index  [index/es of the episode/s you want to download]
	 * @return $this
	 */
	public function selectEpisodes($index = 1)
	{
		$this->setEpisodesPreference($index);

		return $this;
	}

	/**
	 * [capture all mirrors of all episodes, make it read for download]
	 * @return $this
	 */
	public function makeDirectLinks()
	{
		foreach($this->getEpisodes() as $key => $episode)
		{
			$episodeNo = $key + 1;
			$links = [];

			$this->loaded["download"][$key] = $this->page->load($episode["link"]);

			if($this->loaded["download"][$key]["status"] == 404) return ;

			$mirrors = $this->scrapeMirrorLink($this->loaded["download"][$key]["message"], 
				$this->getQualityPreference(), $this->getLanguagePreference());

			foreach ($mirrors as $mirror) 
			{
				if(strrpos($mirror, "mp4upload"))
				{
					array_push($links, $mirror);
				}
			}

			$this->setDirectVideoMirrors([
				"folder"  => "{$this->getAnime()[$this->getAnimePreference()]["title"]}",
				"file"    => "{$this->getAnime()[$this->getAnimePreference()]["title"]}-episode-{$episodeNo}",
				"mirrors" => $links
			]);


            $extractLinkProgress = round($key / count($this->getEpisodes()) * 100);
            IO::getInstance()->write("Compiling: {$extractLinkProgress}%")->retLn();
		}

		return $this;
	}

	/**
	 * [change :anime-id with real anime ID, :anime-episode with real episode number, :anime-language
	 *  with real language preference, :anime-quality with real quality preference from Episode URL]
	 * @param  String $id       [anime ID]
	 * @param  String $episode  [Episode Number]
	 * @param  String $language [Language Preference]
	 * @param  String $quality  [Quality Preference]
	 * @return String [episodeURL with real data]
	 */
	public function episode($id, $episode, $language, $quality)
	{
	 	return str_replace(":anime-id", $id, 
	 		   str_replace(":anime-episode", $episode, 
	 		   str_replace(":anime-language", $language, 
	 		   str_replace(":anime-quality", $quality, $this->getEpisodeURL()))));
	}

	/**
	 * [change :search-key from search URL with real anime search keyword]
	 * @param String $searchKey [anime you want to search]
	 * @return String
	 */
	public function search($searchKey)
	{
		return str_replace(":search-key", $searchKey, $this->getSearchURL());
	}

	/**
	 * [this will extract anime ID from RAWR anime link]
	 * @return String [anime ID]
	 */
	public function extractAnimeID()
	{
		$pieces = explode("/", $this->getAnime()[$this->getAnimePreference()]["link"]);
        
        return $pieces[count($pieces) - 1];
	}

	/**
	 * [start your download]
	 * @return $this
	 */
	public function startDownload()
	{
		foreach ($this->getEpisodesPreference() as $key => $value) 
		{
			$mp4mirror = [];

			foreach ($this->getDirectVideoMirrors()[$value]["mirrors"] as $key2 => $value2) 
			{
				array_push($mp4mirror, getmp4uploadLink($this->page->load($value2)["message"]));
			}

			$this->downloadManager->fetchFile([
				"path" 			=> $this->getDirectVideoMirrors()[$value]["folder"],
				"name"   		=> $this->getDirectVideoMirrors()[$value]["file"],
				"mirrors" 		=> $mp4mirror
			])->save();
		}
		
		return $this;
	}

}