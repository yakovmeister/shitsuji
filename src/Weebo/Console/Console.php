<?php

namespace Yakovmeister\Weebo\Console;

use Yakovmeister\Weebo\Component\IO;
use Yakovmeister\Weebo\Application;
use Yakovmeister\Weebo\Manager\Anime\RAWR\RAWRAnimeManager;
use Yakovmeister\Weebo\Manager\Anime\HentaiHaven\HHavenManager;
use Yakovmeister\Weebo\Manager\DownloadManager as Download;
use Yakovmeister\Weebo\Component\Speech;


class Console
{

	protected $init = false;

	protected $io;

	protected $speech;

	protected $anime = [
		"language" => "subbed",
		"quality" => "720p"
	];

	protected $animeManager;

	protected $downloadManager;

	public function __construct(IO $io, Download $download, Speech $speech)
	{
		$this->io = $io;

		$this->downloadManager = $download;

		$this->speech = $speech;

		$this->init = true;

		$this->setAnimeSource();

		$this->setLanguage()->setQuality();
	}

	public function boot()
	{
		while($this->init)
		{
			$input = $this->io->read("butler> ");

			switch(strtolower($input))
			{
				/* Language Selection Modes */
				case "language:sub":
				case "language:subbed":
					$this->setLanguage("subbed");
					$this->io->newLn()->newLn()->write(
						$this->speech->get("lang_pref_up", [":language" => "subtitled"])
					)->newLn()->newLn();
					break;
				case "language:dub":
				case "language:dubbed":
					$this->setLanguage("dubbed");
					$this->io->newLn()->newLn()->write(
						$this->speech->get("lang_pref_up", [":language" => "dubbed"])
					)->newLn()->newLn();
					break;
				case "language:raw":
					$this->setLanguage("raw");
					$this->io->newLn()->newLn()->write(
						$this->speech->get("lang_pref_up", [":language" => "raw"])
					)->newLn()->newLn();
					break;
				/* Language Selection Modes End */

				/* Quality Selection Modes */
				case "quality:1080":
				case "quality:1080p":
					$this->setQuality("1080p");
					$this->io->newLn()->newLn()->write(
						$this->speech->get("qlty_pref_up", [":quality" => "1080p"])
					)->newLn()->newLn();
					break;
				case "quality:720":
				case "quality:720p":
					$this->setQuality("720p");
					$this->io->newLn()->newLn()->write(
						$this->speech->get("qlty_pref_up", [":quality" => "720p"])
					)->newLn()->newLn();
					break;
				case "quality:480":
				case "quality:480p":
					$this->setQuality("480p");
					$this->io->newLn()->newLn()->write(
						$this->speech->get("qlty_pref_up", [":quality" => "480p"])
					)->newLn()->newLn();
					break;
				/* Quality Selection Modes End */

				/* Source Selection Modes */
				case "source:hentai-haven":
					$this->setAnimeSource("hentai-haven");
					break;
				case "source:rawr":
					$this->setAnimeSource("rawr");
					break;
				/* Source Selection Modes End */

				/* Console Commands */
				case "console:exit":
					$this->init = false;
					break;
				case "console:help":

					break;
				/* Console Commands End */
				
				default:
					if(empty($input)) return $this->io->newLn()->write( $this->speech->get("quit") );

					$this->commenceSearcher($input);

					break;
			}
		}
	}

	public function commenceSearcher($keyword)
	{

		$this->io->newLn()->newLn()->write(
			$this->speech->get("search", [":search-keyword" => $keyword])
		)->newLn();
		
		$this->animeManager->searchAnime($keyword, $this->getQuality(), $this->getLanguage());

		$searchResultCount = count($this->animeManager->getAnime());

		if($searchResultCount > 0) 
		{
			//--------------------------------------------------------------------------------------
			// Search and display anime, after searching and displaying users can interact by
			// simply providing an input number of the anime the users wants to download
			//--------------------------------------------------------------------------------------
			$this->io->write(
				$this->speech->get("search_result_found", [
					":result-count"   => $searchResultCount,
					":search-keyword" => $keyword
				])
			)->newLn()->newLn();

			$this->display($this->animeManager->getAnime(), "title");

			$this->io->newLn();
			
			$animeSelection = $this->io->read($this->speech->get("anime_selection"));

			if($animeSelection > $searchResultCount || empty($animeSelection)) {
				
				$this->io->write(
					$this->speech->get("invalid_selection")
				)->newLn(); 
				
				return $this->init = false;
			}
			
			$this->animeManager->selectAnime($animeSelection);
			//--------------------------------------------------------------------------------------
			// after selecting the anime, it'll now then display the episodes of the anime
			// it'll compile the mirrors and anime episode numbers for you, users can also interact
			// by inputting a string, or an array
			// array example:
			// [1,2,3,4,5,6] -> which will download episodes from 1 to 6
			// user can also do range episode download
			// example:
			//  "5-13" -> which will download all episodes from 5 to 13
			//  "5,6,10" -> which will download episode 5,6 and 10 only
			//--------------------------------------------------------------------------------------
			$this->animeManager->captureEpisodes();
			
			$episodeCount = count($this->animeManager->getEpisodes());
			
			$this->io->newLn()->write(
				$this->speech->get("episodes", [
					":anime" => $this->animeManager->getAnime()[$this->animeManager->getAnimePreference()]["title"],
					":episode-count" => $episodeCount
				])
			)->newLn()->newLn();
			
			$this->display($this->animeManager->getEpisodes(), "episode");
			
			$this->io->write(
				$this->speech->get("all_episodes")
			)->newLn();
			
			$episodeSelection = $this->io->read($this->speech->get("episode_selection"));
			
			if(empty($episodeSelection) || $episodeSelection > $episodeCount) {
				
				$this->io->write(
					$this->speech->get("invalid_selection")
				)->newLn(); 

				return $this->init = false;
			}
			
			$this->animeManager->selectEpisodes($episodeSelection);

			$this->io->write($this->speech->get("compile_episode"))->newLn();
			
			$this->animeManager->makeDirectLinks();
			
			$this->io->write($this->speech->get("compile_episode_ok"));

			//---------------------------------------------------------------------------------------
			// Start downloading after gathering all the information needed
			//---------------------------------------------------------------------------------------
			$this->animeManager->startDownload();

			
		}

		$this->io->write(
			$this->speech->get("search_result_fail", [":search-keyword" => $keyword])
		)->newLn();

		// reinstantiate anime source object
		return $this->setAnimeSource();
	}

	public function display($dataToDisplay, $arrayParam = null)
	{
		foreach ($dataToDisplay as $key => $value) 
		{
			$key += 1;
			if(!is_null($arrayParam))
				$this->io->write("[{$key}] {$value[$arrayParam]}")->newLn();
			else
				$this->io->write("[{$key}] {$value}")->newLn();	
		}
	}

	/**
	 * [set anime source]
	 * @param String $animeSource [anime source]
	 * @return $this 
	 */
	public function setAnimeSource($animeSource = null)
	{
		switch ($animeSource) {
			case "hentai-haven":

			///	$this->animeManager = Application::getInstance()->make(HHavenManager::class);

				$this->io->newLn()->newLn()->write("[!] Hentai Haven soon to be added")->newLn()->newLn()->newLn();
			case "rawr":
				$this->animeManager = Application::getInstance()->make(RAWRAnimeManager::class);
				
				$this->io->newLn()->newLn()->write(
					$this->speech->get("source_switch", [":source" => "rawr anime"])
				)->newLn()->newLn()->newLn();
				break;
			default:
				$this->animeManager = Application::getInstance()->make(RAWRAnimeManager::class);
				break;
		}

		return $this;
	}

	/**
	 * [set language preference of the anime]
	 * @return $this
	 */
	public function setLanguage($animeLanguage = "subbed")
	{
		$this->anime["language"] = $animeLanguage;

		return $this;
	}

	/**
	 * [set language preference of the anime]
	 * @return $this
	 */
	public function setQuality($animeQuality = "720p")
	{
		$this->anime["quality"] = $animeQuality;

		return $this;
	}

	/**
	 * [get language preference of the anime]
	 * @return $this->anime["quality"]
	 */
	public function getLanguage()
	{
		return $this->anime["language"];
	}

	/**
	 * [get quality preference of the anime]
	 * @return $this->anime["quality"]
	 */
	public function getQuality()
	{
		return $this->anime["quality"];
	}

}