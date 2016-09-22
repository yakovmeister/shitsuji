<?php

namespace Yakovmeister\Weebo\Component;

use Symfony\Component\Finder\Finder;
use Yakovmeister\Weebo\Exception\FileNotFoundException;

class Speech
{
	protected $finder;

	protected $active = "butler";

	public function __construct(Finder $finder)
	{
		$this->finder = $finder;

		$this->finder->files()->ignoreUnreadableDirs()->in("./speech");
	}

	public function setSpeech($speechPreference = "butler")
	{
		$this->active = $speechPreference;

		return $this;
	}

	public function get($speech, $appendToMessage = [])
	{

		if(empty($appendToMessage)) return $this->fetchResult()[$speech];

		$message = $this->fetchResult()[$speech];

		foreach ($appendToMessage as $key => $value) {
			
			$message = str_replace($key, $value, $message);
		}
		
		return $message;
	}

	public function fetchSpeechPreference()
	{
		return $this->active;
	}

	public function fetchResult()
	{
		if(count($this->finder->name("{$this->fetchSpeechPreference()}.php")) <= 0) throw new FileNotFoundException;

		return @require("./speech/{$this->fetchSpeechPreference()}.php");
	}

}