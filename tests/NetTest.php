<?php

use PHPUnit\Framework\TestCase;
use Yakovmeister\Weebo\Component\Net;
use Yakovmeister\Weebo\Application;

class NetTest extends TestCase
{
	protected $net;

	public function setUp()
	{
		$this->net = Application::getInstance()->make(Net::class);
	}

	public function testLoad()
	{
		$url = 'http://www.nyaa.se/?page=view&tid=849502';

		$this->net->load($url);

		$this->assertTrue($this->net->getResponseStatus() != 404);
	}

	public function testNotFoundLoad()
	{
		$url = 'https://www.google.com.ph/asdq2312';

		$this->net->load($url);

		$this->assertTrue($this->net->getResponseStatus() == 404);
	}
}