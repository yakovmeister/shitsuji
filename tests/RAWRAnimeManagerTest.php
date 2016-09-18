<?php

use PHPUnit\Framework\TestCase;
use Yakovmeister\Weebo\Manager\Anime\RAWR\RAWRAnimeManager;
use Yakovmeister\Weebo\Application;

class RAWRAnimeManagerTest extends TestCase
{

	protected $rawr;

	public function setUp()
	{
		$this->rawr = Application::getInstance()->make(RAWRAnimeManager::class);
	}

	public function testSomething()
	{

		$this->rawr->searchAnime("Ansatsu Kyoushitsu")->selectAnime(1);

		$this->rawr->captureEpisodes();

		$this->rawr->makeDirectLinks();

		$this->rawr->selectEpisodes("*");

		dd($this->rawr->startDownload());
	}
	public function testSearchAnime()
	{
		$this->rawr->searchAnime("Ansatsu Kyoushitsu");

		$this->assertTrue(count($this->rawr->getAnime()) > 0);
	}

	public function testDumpSearchAnime()
	{
		$this->rawr->searchAnime("Ansatsu Kyoushitsu")->selectAnime(1);	
	
		return var_dump($this->rawr->getAnime());
	}

	public function testCaptureEpisodes()
	{
		$this->rawr->searchAnime("Ansatsu Kyoushitsu")->selectAnime(1);

		$this->rawr->captureEpisodes();

		$this->assertTrue(count($this->rawr->getEpisodes()) > 0);	
	}

	public function testDumpCaptureEpisodes()
	{
		$this->rawr->searchAnime("Ansatsu Kyoushitsu")->selectAnime(1);

		$this->rawr->captureEpisodes();

		return var_dump($this->rawr->getEpisodes());
	}

	public function testGettingVideoMirrors()
	{
		$this->rawr->searchAnime("Ansatsu Kyoushitsu")->selectAnime(1);

		$this->rawr->captureEpisodes()->selectEpisodes();

		$this->rawr->makeDirectLinks();

		$this->assertTrue($this->rawr->getDirectVideoMirrors() > 0);
	}

	public function testDumpGettingVideoMirrors()
	{
		$this->rawr->searchAnime("Ansatsu Kyoushitsu")->selectAnime(1);

		$this->rawr->captureEpisodes()->selectEpisodes();

		$this->rawr->makeDirectLinks();

		return var_dump($this->rawr->getDirectVideoMirrors());
	}


}