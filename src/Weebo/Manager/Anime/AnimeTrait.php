<?php

namespace Yakovmeister\Weebo\Manager\Anime;

use Yakovmeister\Weebo\Exception\UnrecognizedDataTypeException;
use Yakovmeister\Weebo\Exception\OutOfBoundsException;

trait AnimeTrait
{
	/**
	 * [associative array that contains base url, search url, and watch link url may
	 *  also contain direct video link if available]
	 * @var Array
	 */
	protected $source = [];

	/**
	 * [associative array that contains title and link list of anime]
	 * @var Array
	 */
	protected $anime = [];

	/**
	 * [associative array that contains episode number and link list of anime]
	 * @var Array
	 */
	protected $episodes = [];

	/**
	 * [selected anime index]
	 * @var Integer
	 */
	protected $animePreference;

	/**
	 * [can be an array or string or integer, selected episode/s index]
	 * @var Mixed
	 */
	protected $episodesPreference = [];

	/**
	 * [selected anime language]
	 * @var String "subbed"
	 */
	protected $languagePreference = "subbed";

	/**
	 * [selected anime quality]
	 * @var String "720p"
	 */
	protected $qualityPreference = "720p";

	/**
	 * [direct download link for episodes]
	 * @var Array
	 */
	protected $directVideoMirrors = [];

	/**
	 * [set property value]
	 * @param String $id
	 * @param Mixed $value
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait 
	 */
	public function set($id, $value)
	{
		$this->$id = $value;

		return $this;
	}

	/**
	 * [set source property value]
	 * @param Array $value
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait
	 */
	public function setSource(array $value)
	{
		$this->set("source", $value);

		return $this;
	}

	/**
	 * [push anime link and title on anime property]
	 * @param Array $value SHOULD contain "title" and "link" as assoc array
	 */
	public function setAnime(array $value)
	{
		array_push($this->anime, $value);

		return $this;
	}

	/**
	 * [push episodes and link on episodes property]
	 * @param Mixed $value
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait 
	 */
	public function setEpisodes($value)
	{
		array_push($this->episodes, $value);

		return $this;
	}

	/**
	 * [set animePreference value]
	 * @param Mixed $value
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait 
	 */
	public function setAnimePreference($value)
	{
		return $this->set("animePreference", $value);
	}

	/**
	 * [set languagePreference value]
	 * @param Mixed $value
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait 
	 */
	public function setLanguagePreference($value)
	{
		return $this->set("languagePreference", $value);
	}

	/**
	 * [set qualityPreference value]
	 * @param Mixed $value
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait 
	 */
	public function setQualityPreference($value)
	{
		return $this->set("qualityPreference", $value);
	}

	/**
	 * [set episodesPreference value]
	 * @param Mixed $value
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait 
	 */
	public function setEpisodesPreference($value = 1)
	{
		$episodesCount = count($this->getEpisodes());

		if($value > $episodesCount || $value < 0) throw new OutOfBoundsException;

		switch ($value) {
			case "*":
				for ($index=1; $index <= $episodesCount; $index++) { 
					array_push($this->episodesPreference, $index-1);
				}
				break;
			case is_array($value):
				foreach($value as $episode) {
					$this->setEpisodesPreference($episode--);
				}
				break;
			case is_integer($value):
				array_push($this->episodesPreference, $value--);
				break;
			case is_string($value):
				foreach(separateList($value) as $episode) {
					$this->setEpisodesPreference($episode);
				}
				break;
			default:
				throw new UnrecognizedDataTypeException;
				break;
		}
	}

	/**
	 * [push value to directVideoMirrors]
	 * @param Array $value
	 * return Yakovmeister\Weebo\Manager\Anime\RAWRTrait
	 */
	public function setDirectVideoMirrors(array $value)
	{
		array_push($this->directVideoMirrors, $value);

		return $this;
	}

	/**
	 * [get property value using property name]
	 * @param String $id
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::$id
	 */
	public function get($id)
	{
		return $this->$id;
	}

	/**
	 * [get source property]
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::source
	 */
	public function getSource()
	{
		return $this->get("source");
	}

	/**
	 * [get anime property]
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::anime
	 */
	public function getAnime()
	{
		return $this->get("anime");
	}

	/**
	 * [get episodes property]
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::animeEpisodes
	 */
	public function getEpisodes()
	{
		return $this->get("episodes");
	}

	/**
	 * [get animePreference property]
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::animePreference
	 */
	public function getAnimePreference()
	{
		return $this->get("animePreference");
	}

	/**
	 * [get episodesPreference property]
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::episodesPreference
	 */
	public function getEpisodesPreference()
	{
		return $this->get("episodesPreference");
	}

	/**
	 * [get languagePreference property]
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::languagePreference
	 */
	public function getLanguagePreference()
	{
		return $this->get("languagePreference");
	}

	/**
	 * [get qualityPreference property]
	 * @return Yakovmeister\Weebo\Manager\Anime\RAWRTrait::qualityPreference
	 */
	public function getQualityPreference()
	{
		return $this->get("qualityPreference");
	}

	/**
	 * [direct download mirrors of the video]
	 * @return Yakovmeister\Weebo\Manager\Anime|RAWRTrait::directVideoMirrors
	 */
	public function getDirectVideoMirrors()
	{
		return $this->get("directVideoMirrors");
	}

	/**
	 * [get base url from source]
	 * @return String
	 */
	public function getBaseURL()
	{
		return trim(trim($this->getSource()["base"], " "), "/");
	}
	
	/**
	 * [get search url from source]
	 * @return String
	 */
	public function getSearchURL()
	{
		$searchURL = trim(trim($this->getSource()["search"], " "), "/");

		$searchURL = "{$this->getBaseURL()}/{$searchURL}";

		return trim(trim($searchURL, " "), "/");
	}

	/**
	 * [get episode url from soure]
	 * @return String
	 */
	public function getEpisodeURL()
	{
		$watchURL = trim(trim($this->getSource()["episode"], " "), "/");

		$watchURL = "{$this->getBaseURL()}/{$watchURL}";

		return trim(trim($watchURL, " "), "/");	
	}
	
}