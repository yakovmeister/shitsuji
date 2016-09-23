<?php

use PHPUnit\Framework\TestCase;
use Yakovmeister\Weebo\Component\Speech;
use Yakovmeister\Weebo\Application;

class SpeechTest extends TestCase
{
	protected $speech;

	public function setUp()
	{
		$this->speech = Application::getInstance()->make(Speech::class);
	}

	public function testGetActiveSpeech()
	{
		$this->assertTrue(!empty($this->speech->getSpeech()));
	}
}