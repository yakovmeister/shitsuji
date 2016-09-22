<?php

namespace Yakovmeister\Weebo\Component;

use Symfony\Component\Finder\Finder;
use Yakovmeister\Weebo\Exception\FileNotFoundException;

class Speech
{
	protected $finder;

	protected $active = "normal";

	protected $dir;

	public function __construct(Finder $finder)
	{
		$this->dir = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR."speech";

		$this->finder = $finder;

		$this->finder->files()->ignoreUnreadableDirs()->in($this->dir);
	}

	public function setSpeechPreference($speechPreference = "normal")
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

		return @require("{$this->dir}/{$this->fetchSpeechPreference()}.php");
	}

}