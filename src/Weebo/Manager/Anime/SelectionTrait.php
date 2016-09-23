<?php

namespace Yakovmeister\Weebo\Manager\Anime;

trait SelectionTrait
{
	/**
	 * [set video quality preference]
	 * @param  string $quality = 720p [video quality of your choice, supports 480 and 720 for RAWRAnime]
	 * @return String
	 */
	public function selectQuality($quality = "720p")
	{
		switch($quality) 
		{
			case "1080":
			case 1080:
			case "1080p":
			case "1080 p":
			case "10":
				$quality = "1080p";
				break;
			case "720":
			case 720:
			case "720p":
			case "720 p":
			case 7:
			case "7":
				$quality = "720p";
				break;
			case "480":
			case 480:
			case "480p":
			case "480 p":
			case 4:
			case "4":
				$quality = "480p";
				break;
		}

		return $quality;
	}

	/**
	 * [set video language preference]
	 * @param  string $language=subbed [video language, supports subtitled, and dubbed with rare appearance of raw]
	 * @return String
	 */
	public function selectLanguage($language = "subbed")
	{
		switch ($language) {
			case "sub":
			case "subbed":
				$language = "subbed";
				break;
			case "dub":
			case "dubbed":
				$language = "dubbed";
				break;
			case "raw":
				$language = "raw";
				break;
		}

		return $language;
	}

}