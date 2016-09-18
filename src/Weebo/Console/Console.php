<?php

namespace Yakovmeister\Weebo\Console;

use Yakovmeister\Weebo\Component\IO;
use Yakovmeister\Weebo\Application;
use Yakovmeister\Weebo\Manager\Anime\RAWR\RAWRAnimeManager;
use Yakovmeister\Weebo\Manager\DownloadManager as Download;


class Console
{

	protected $init = false;

	protected $io;

	protected $anime = [
		"language" => "subbed",
		"quality" => "720p"
	];

	protected $animeManager;

	protected $downloadManager;

	public function __construct(IO $io, Download $download)
	{
		$this->io = $io;

		$this->downloadManager = $download;

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
				case "language:sub":
				case "language:subbed":
					$this->setLanguage("subbed");
					$this->io->newLn()->newLn()->write("Language Preference Updated to: subtitled")->newLn()->newLn();
					break;
				case "language:dub":
				case "language:dubbed":
					$this->setLanguage("dubbed");
					$this->io->newLn()->newLn()->write("Language Preference Updated to: dubbed")->newLn()->newLn();
					break;
				case "language:raw":
					$this->setLanguage("raw");
					$this->io->newLn()->newLn()->write("Language Preference Updated to: raw")->newLn()->newLn();
					break;
				case "quality:720":
				case "quality:720p":
					$this->setQuality("720p");
					$this->io->newLn()->newLn()->write("Quality Preference Updated to: 720p")->newLn()->newLn();
					break;
				case "quality:480":
				case "quality:480p":
					$this->setQuality("480p");
					$this->io->newLn()->newLn()->write("Quality Preference Updated to: 480p")->newLn()->newLn();
					break;
				case "source:hentai-haven":
					$this->setAnimeSource("hentai-haven");
					break;
				case "source:rawr":
					$this->setAnimeSource("rawr");
					break;
				case "console:exit":
					$this->init = false;
					break;
				case "console:help":

					break;
				
				default:
					if(empty($input)) return $this->io->newLn()->write("Bye bye!");

					$this->commenceSearcher($input);

					break;
			}

			$input = null;
		}
	}

	public function commenceSearcher($keyword)
	{
		$this->io->newLn()->newLn()->write("Searching {$keyword}...")->newLn();
		$this->animeManager->searchAnime($keyword, $this->getQuality(), $this->getLanguage());

		$searchResultCount = count($this->animeManager->getAnime());

		if($searchResultCount > 0) 
		{
			//--------------------------------------------------------------------------------------
			// Search and display anime, after searching and displaying users can interact by
			// simply providing an input number of the anime the users wants to download
			//--------------------------------------------------------------------------------------
			$this->io->write("{$searchResultCount} result/s found for {$keyword}")->newLn()->newLn();

			$this->display($this->animeManager->getAnime(), "title");

			$this->io->newLn();
			
			$animeSelection = $this->io->read("Type the number of the anime you want to download: ");

			if($animeSelection > $searchResultCount || empty($animeSelection)) {
				$this->io->write("Invalid Selection, Exiting...")->newLn(); return $this->init = false;
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
			
			$this->io->newLn()
			->write("{$this->animeManager->getAnime()[$this->animeManager->getAnimePreference()]["title"]} has {$episodeCount} episodes")
			->newLn()->newLn();
			
			$this->display($this->animeManager->getEpisodes(), "episode");
			
			$this->io->write("[*] to download all episodes")->newLn();
			
			$episodeSelection = $this->io->read("Type the episode number you want to download: ");
			
			if(empty($episodeSelection) || $episodeSelection > $episodeCount) {
				$this->io->write("Invalid Selection, Exiting...")->newLn(); return $this->init = false;
			}
			
			$this->animeManager->selectEpisodes($episodeSelection);

			$this->io->write("Compiling Episodes and Mirrors... This may take a minute. Please Wait.")->newLn();
			
			$this->animeManager->makeDirectLinks();
			
			$this->io->write("Episodes and Mirrors Compiled!");

			//---------------------------------------------------------------------------------------
			// Start downloading after gathering all the information needed
			//---------------------------------------------------------------------------------------
			$this->animeManager->startDownload();

			
		}

		// reinstantiate anime source object
		return $this->setAnimeSource();
	}

	public function display($dataToDisplay, $arrayParam = null)
	{
		foreach ($dataToDisplay as $key => $value) 
		{
			$key += 1;
			if(!is_null($arrayParam))
			{
				$this->io->write("[{$key}] {$value[$arrayParam]}")->newLn();
			}
			else
			{
				$this->io->write("[{$key}] {$value}")->newLn();	
			}
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
				$this->io->newLn()->newLn()->write("[!] Hentai Haven soon to be added")->newLn()->newLn()->newLn();
			case "rawr":
				$this->animeManager = Application::getInstance()->make(RAWRAnimeManager::class);
				
				$this->io->newLn()->newLn()->write("[!] Source Switched to Rawr Anime")->newLn()->newLn()->newLn();
				break;
			default:
				$this->animeManager = Application::getInstance()->make(RAWRAnimeManager::class);
				break;
		}

		return $this;
	}

	public function setLanguage($animeLanguage = "subbed")
	{
		$this->anime["language"] = $animeLanguage;

		return $this;
	}

	public function setQuality($animeQuality = "720p")
	{
		$this->anime["quality"] = $animeQuality;

		return $this;
	}

	public function getLanguage()
	{
		return $this->anime["language"];
	}

	public function getQuality()
	{
		return $this->anime["quality"];
	}

}