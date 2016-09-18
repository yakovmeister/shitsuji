<?php

namespace Yakovmeister\Weebo\Manager;

use Yakovmeister\Weebo\Component\Net;

class PageLoadManager
{
	protected $net;

	protected $page;

	public function __construct(Net $net)
	{
		$this->net = $net;
	}

	/**
	 * @param  String $url
	 * @return Yakovmeister\Weebo\Manager\PageLoadManager::page
	 */
	public function load($url)
	{
		$page = $this->net->load($url);

		switch ($page->getResponseStatus()) 
		{
			case Net::HTTP_NOT_FOUND:
				$this->page = [
					"status" => Net::HTTP_NOT_FOUND,
					"message" => "Page not found."
				];

				break;
			case Net::HTTP_OK:
			case Net::HTTP_FOUND:
				$this->page = [
					"status" => Net::HTTP_OK,
					"message" => $page->getResponse()
				];
	
				break;
		}

		return $this->page;
	}

	public function getStatus()
	{
		return $this->page["status"];
	}

	public function getContent()
	{
		return $this->page["message"];
	}
}