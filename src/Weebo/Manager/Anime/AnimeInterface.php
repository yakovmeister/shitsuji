<?php

namespace Yakovmeister\Weebo\Manager\Anime;

interface AnimeInterface
{
	/**
	 * [search anime and cache result (both title and link)]
	 * @param  String $searchKey       [the anime you want to find]
	 * @param  String $quality=720p    [video quality]
	 * @param  String $language=subbed [video language]
	 * @return $this
	 */
	public function searchAnime($searchKey, $quality = "720p", $language = "subbed");

	/**
	 * [selected anime index]
	 * @param  Integer $index=1 [index of the anime you want to download]
	 * @return $this
	 */
	public function selectAnime($index = 1);

	/**
	 * [fetch episodes and cache result (both episode number and link)]
	 * @return $this
	 */
	public function captureEpisodes();

	/**
	 * [selected episode index, can be array of integer or string that contain ranges]
	 * @param  Mixed  $index  [index/es of the episode/s you want to download]
	 * @return $this
	 */
	public function selectEpisodes($index = 1);

	/**
	 * [capture all mirrors of all episodes, make it read for download]
	 * @return $this
	 */
	public function makeDirectLinks();

	/**
	 * [as the method name suggest, it'll start downloading your files]
	 * @return $this
	 */
	public function startDownload();

}