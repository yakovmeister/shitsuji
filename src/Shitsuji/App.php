<?php

namespace Yakovmeister\Shitsuji;

use Yakovmeister\Shitsuji\IO;
use Yakovmeister\Shitsuji\Scrapper;

class App
{
    /**
     * App version duh.
     * @access protected 
     * @var String
     */
	protected $version = "0.2.1";
	
    /**
     * Handles Http requests and response, and file management 
     * @access protected 
     * @var IO
     */
    protected $io;

    /**
     * Scrapes HTML document 
     * @access protected 
     * @var Scrapper
     */
    protected $scrapper;

    /**
     * Search keyword, anime you're trying to find
     * @access protected 
     * @var String
     */
    protected $searchKey;

    /**
     * Anime sources or URL. Contains various URL cuts
     * @access protected 
     * @var Array
     */
    protected $source = [
        "base"    => "http://rawranime.tv/",
        "search"  => "/index.php?ajax=anime&do=search&s=:searchKey",
        "watch"   => "/watch/:animid/episode-:animep/?l=:animlang&q=:animq"
    ];

    /**
     * Various details about anime, save it here for future use.
     * @access protected 
     * @var Array
     */
    protected $animeDetail = [];

    /**
     * Anime Title.
     * @var Array
     */
    protected $titleCache = [];

    /**
     * Episodes
     * @var Array
     */
    protected $episodeCache = [];

    /**
     * Video link mirrors
     * @var Array
     */
    protected $videoLinkCache = [];

    /**
     * Keeping sanity 
     * @access protected
     * @var Int $_SERVER['argc']
     */
    protected $argc;
	
    /**
     * Keeping sanity even more
     * @access protected 
     * @var Array $_SERVER['argv']
     */
    protected $argv;

    /**
     * Anime being selected by user
     * @access protected 
     * @var Int 0
     */
    protected $animeSelection = 0;

    /**
     * Episode/s user wants to download
     * by default, it'll download episode 1
     * @access protected 
     * @var Int 0
     */
    protected $episodeSelection = 0;

    /**
     * Language Preference (subbed or dubbed)
     * by default, it'll download subtitled anime
     * @access protected
     * @var String subbed
     */
    protected $languageSelection = "subbed";

    /**
     * Quality being selected by user 
     * by default, it'll download 720p quality
     * @access protected
     * @var String 720p
     */
    protected $qualitySelection = "720p";

	/**
 	 * Run you fools. 
 	 *
 	 * @access public
 	 */
    public function run()
    {
        if($this->argc <= 1) die("Please tell me what to do, my lord. Type {$this->argv[0]} --help\n");

        switch ($this->argv[1]) 
        {
            case "-s":
            case "--search":
            case "search":
            	$this->optionSearch();
            case "-h":
            case "--help":
            case "help":
            	$this->optionHelp();
            case "-v":
            case "--version":
                die($this->optionAbout());
    		default:
    			die("Pardon? I cannot understand you my lord. Type {$this->argv[0]} --help \n");
    	}
    }

 	/**
     * Search Anime
     *
     * @access public
     */
    public function optionSearch()
    {
        $this->io->n();
        
        $this->searchKey = empty($this->argv[2])
 	        ? $this->io->gets("What anime would you like to download senpai? ")
 	        : $this->argv[2];

        $this->searchTitleAndCache();
        $this->displaySearchResult();
        $this->searchEpisodeAndCache();
        $this->displayEpisodes();
        $this->displayLanguages();
        $this->displayQuality();

        $this->checkEpisodeCountAndDownload();

    }

    /**
     * Check if user wants to do a batch download
     * or just download a single file.
     *
     * @access public
     * @return Yakovmeister\Shitsuji\App
     */
    public function checkEpisodeCountAndDownload()
    {

    	if(!$this->io->directory($this->animeDetail["title"])) $this->io->makeDirectory($this->animeDetail["title"]);

    	$this->io->makeFile("{$this->animeDetail["title"]}/anime.json", json_encode($this->animeDetail));

    	/// Triggers our batch download if the episode selection is
    	///  greater than total anime count.

    	if($this->episodeSelection >= $this->animeDetail["total-episodes"])
    	{
    	    $this->batchDownloadAnime();
    	}

        $this->searchVideoLinkAndCache();
        $this->fetchVideo();

        die();
    }
   
    /**
     * Let us download all episodes in one go
     *
     * @access public
     * @return Yakovmeister\Shitsuji\App
     */
    public function batchDownloadAnime()
    {
        for ($episode=1; $episode <= $this->animeDetail["total-episodes"]; $episode++) 
    	{ 
            $this->searchVideoLinkAndCache($episode);
            $this->fetchVideo();
        }

    	die();
    }

    /**
     * Replace placeholders from watch URL with real value
     *
     * @access public
     * @param String $id, String $episode, String $language, String $quality
     * @return String
     */
    public function compileWatchLink($id, $episode, $language, $quality)
    {
        $watchLink = $this->source['watch'];
 
        $watchLink = str_replace(":animid", $id, $watchLink);
        $watchLink = str_replace(":animep", $episode, $watchLink);
        $watchLink = str_replace(":animlang", $language, $watchLink);
        $watchLink = str_replace(":animq", $quality, $watchLink);

        return $watchLink;
    }

    /**
     * Extract video link from HTML document
     *
     * @access public
     * @param int $episodeNumber
     * @return String
     */
    public function generateWatchLink($episodeNumber)
    {
        $animid = $this->extractAnimeTitleID();
 		
        $this->episodeSelection = empty($episodeNumber) ? $this->episodeSelection + 1 : $episodeNumber;
 		
        $this->languageSelection = $this->languageSelection == 's' || 'subbed' || 'sub' ? 'subbed' : 'dubbed';
 		
        $this->qualitySelection = $this->qualitySelection == '720p' ||
                $this->qualitySelection == '720' ? '720p' : '480p';

        return $this->compileWatchLink($animid, $this->episodeSelection, $this->languageSelection, $this->qualitySelection);

 	}

    /**
     * Extract video link from HTML document
     *
     * @access public
     * @param String $mp4uploadHTML
     * @return String
     */
    public function extractMp4UploadLink($mp4uploadHTML)
    {
        $document = $this->io->loadURL($mp4uploadHTML)->getLoad();

        preg_match("/http:\/\/(.*?)video.mp4/", $document, $match);

        return $match[0];
    }

    /**
     * Select Video Mirror and download the video
     *
     * @access public
     * @return $this
     */
    public function fetchVideo()
    {
        foreach ($this->scrapeVideoLinks() as $video) 
        {	
            // let's stick to mp4upload for the mean time
            if(strrpos($video, 'mp4upload'))
            {
                $downloadLink = $this->extractMp4UploadLink($video);
           
                $this->io->loadURL($downloadLink, true, 
                    "{$this->animeDetail["title"]}/{$this->animeDetail["title"]}-episode-{$this->episodeSelection}")->store();

                break ;
            }
        }

        return $this;
    }

    /**
     * RAWRAnime is using id-anime-title as anime id. 
     * Example (For Assassination Classroom): 1407-assassination-classroom
     * We're extracting this from our link, it'll be useful for later use... trust me
     *
     * @access public
     * @return String
     */
    public function extractAnimeTitleID()
    {
        $pieces = explode("/", $this->scrapeLinkList()[$this->animeSelection]);

        return $pieces[count($pieces) - 1];
    }

    /**
     * Display halp for noobs
     * 
     * @access public
     */
    public function optionHelp()
    {
    	$this->optionAbout();
        $this->io->console("Usage:")->n();
        $this->io->console("   --search [anime title]     Start Anime Search")->n();
        $this->io->console("   --search                                     ")->n();
        $this->io->console("         -s [anime title]                       ")->n();
        $this->io->console("         -s                                     ")->n()->n();
        $this->io->console("   --help                     Display help      ")->n();
        $this->io->console("   --h                                          ")->n()->n();
        $this->io->console("   --version                  Display version   ")->n();
        $this->io->console("   --v                                          ")->n()->n();

 		die();
    }

    /**
     * Display version
     * 
     * @access public
     */
    public function optionAbout()
    {
        $this->io->n()->console("Shitsuji - Simple CLI based Anime Downloader.")->n();
        $this->io->console("version: {$this->version}");
        $this->io->n()->n();
    }

    
    /**
     * Doesn't actually do anything except saving anime selection
     * and displaying the anime list
     *
     * @access public
     * @return Yakovmeister\Shitsuji\App
     */
 	public function displaySearchResult()
    {
        $listCount = count($this->scrapeTitleList());

        if($listCount > 0)
        {
            $this->io->console("My lord, I found {$listCount} total result/s for Anime {$this->searchKey}")->n()->n();
 			
            foreach($this->scrapeTitleList() as $key => $title)
            {
                $this->io->console("[{$key}] => {$title}")->n();
            }

            $selection = $this->io->n()->gets("Demo... that's a lot my lord, please pick one: (Default: 0) ");

            if(empty($selection)) $this->animeSelection = $this->animeSelection;
            elseif($selection <= $listCount && $selection >= 0) $this->animeSelection = $selection;
            else die("That's not a valid number my lord. Gomen.");

            $this->animeDetail["title"] = $this->scrapeTitleList()[$this->animeSelection];
        }
        else 
        {
            die("Gomenasai my lord... I failed to find {$this->searchKey}, please try different anime");
        }

        return $this;
    }

    /**
     * Doesn't actually do anything except saving episode selection
     * and displaying the episode list
     *
     * @access public
     * @return $this
     */
    public function displayEpisodes()
    {
    	$this->io->n()->n()->console("You Selected {$this->animeDetail["title"]}, my lord. Here's the episodes: ")->n();

        $this->animeDetail["total-episodes"] = $listCount = count($this->scrapeEpisodeList());

        if($listCount > 0)
        {	
            foreach ($this->scrapeEpisodeList() as $key => $value) 
            {
                $this->io->n()->console("[{$key}] => {$this->animeDetail["title"]} episode {$value}");
            }

            $this->io->n()->n();

            $this->io->n()->console("[{$listCount}] => {$this->animeDetail["title"]} download all episodes")->n()->n();	

            $selection = $this->io->gets("Anou... Which one of this should I download? (Default: 0) ");

           if(empty($selection)) $this->episodeSelection = $this->episodeSelection;
           elseif($selection >= 0) $this->episodeSelection = $selection;
           else die("That's not a valid number my lord. Gomen.");

        }
        else
        {
            die("Gomenasai my lord, that anime is not yet aired.");
        }

        return $this;
    }

    /**
     * Doesn't actually do anything except saving language preference
     * and displaying the available language preference
     *
     * @access public
     * @return $this
     */
    public function displayLanguages()
    {
        $this->io->n()->n()->console("[sub] or [subbed] or [s] => English Subtitled")->n();
        $this->io->console("[dub] or [dubbed] or [d] => English Dubbed")->n()->n();

        $this->languageSelection = $this->io->gets("What languange preference would you like my lord? (Default: sub) ")
                                   ?? $this->languageSelection;

        return $this;
    }

    /**
     * Doesn't actually do anything except saving anime quality
     * and displaying the anime quality
     *
     * @access public
     * @return $this
     */
    public function displayQuality()
    {
        $this->io->n()->n()->console("[480] => 480p");
        $this->io->n()->console("[720] => 720p")->n()->n();

        $this->qualitySelection = $this->io->gets("I suggest you download 720p for higher quality my lord... (Default: 720) ") 
                                  ?? $this->qualitySelection;

        return $this;
    } 

    /**
     * Return Episode List
     *
     * @access public
     * @return String
     */
    public function getEpisodes()
    {
        return $this->episodeCache;
    }

    /**
     * Return Title List
     *
     * @access public
     * @return Array
     */
    public function getSearchResult()
    {
        return $this->titleCache;
    }

    /**
     * Return Video Mirrors
     *
     * @access public
     * @return Array
     */
    public function getVideos()
    {
        return $this->videoLinkCache;
    }

    /**
     * Return trimmed Base URL
     *
     * @access public
     * @return String
     */
    public function getBaseURL()
    {
        return rtrim($this->source['base'], "/");
    }

    /**
     * Return trimmed Search URL (no base url yet)
     *
     * @access public
     * @return String
     */
    public function getSearchURL()
    {
        return ltrim($this->source['search'], "/");
    }

    /**
     * Combine partial URL with the base URL
     *
     * @access public
     * @return String
     */
    public function appendToBase($urlPartial)
    {
        return "{$this->getBaseURL()}/{$urlPartial}";
    }

    /**
     * Append Search URL with search key to Base URL 
     *
     * @access public
     * @return String
     */
    public function appendSearch()
    {
        return $this->appendToBase($this->compileSearchLink());
    }

    /**
     * Replace :searchKey with real search key value 
     *
     * @access public
     * @return String
     */
    public function compileSearchLink()
    {
    	return str_replace(":searchKey", $this->searchKey, $this->getSearchURL());
    }

    /**
     * Load Anime Title Lists and Cache it
     *
     * @access public
     * @return $this
     */
    public function searchTitleAndCache()
    {
        $this->titleCache = $this->io->loadURL($this->appendSearch())->getLoad();

        return $this;
    }

    /**
     * Load Video Mirrors and Cache it
     *
     * @access public
     * @param String $link
     * @return $this
     */
    public function searchVideoLinkAndCache($link = null)
    {   
        $this->videoLinkCache = $this->io->loadURL($this->appendToBase($this->generateWatchLink($link)))->getLoad();

        return $this;
    }

    /**
     * Load Episode Lists and Cache it
     *
     * @access public
     * @return $this
     */
    public function searchEpisodeAndCache()
    {
        $this->episodeCache = $this->io->loadURL($this->appendToBase($this->scrapeLinkList()[$this->animeSelection]))->getLoad();

        return $this;
    }


    /**
     * Scrape and stareeeee... lulz 
     * Scrape the title from HTML document, return Titles as Array
     *
     * @access public
     * @return Array
     */
    public function scrapeTitleList()
    {
        return $this->scrapper->capture($this->getSearchResult())
                    ->scrape('quicksearch-title')->getResults(); 		
 	}

    /**
     * Scrape the link from HTML document, return links as Array
     *
     * @access public
     * @return Array
     */
    public function scrapeLinkList()
    {
        return $this->scrapper->capture($this->getSearchResult())
                    ->scrape('quicksearch-result')->byAttribute('href')
                    ->getResults();
    }

    /**
     * We will capture all the mirrors that matches our quality and language preference
     * get the links so we can choose which link is better for download
     *
     * @access public
     * @return Array
     */
    public function scrapeVideoLinks()
    {
        return $this->scrapper->capture($this->getVideos()) 
                    ->scrape($this->qualitySelection,'data-quality')
                    ->scrape($this->languageSelection,'data-lang')
                    ->byAttribute('data-src')->getResults();
    }

    /**
     * Scrape the episode from HTML document, return Episode as Array
     *
     * @access public
     * @return Array
     */
    public function scrapeEpisodeList()
    {
        return $this->scrapper->capture($this->getEpisodes())
                    ->scrape('epp')->byAttribute('data-episode')
                    ->getResults();
    }

    public function __construct()
    {
    	$this->argc     = $_SERVER['argc'];
    	$this->argv     = $_SERVER['argv'];
    	$this->io       = new IO;
    	$this->scrapper = new Scrapper;
    }

}