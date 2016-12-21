<?php

use PHPUnit\Framework\TestCase;

class CloudFlareTest extends TestCase
{

	public function testLoad()
	{			
		$httpProxy   = new httpProxy();
		$httpProxyUA = 'proxyFactory';

		$requestLink = 'https://kissanime.ru/';
			$requestPage = json_decode($httpProxy->performRequest($requestLink));

			// if page is protected by cloudflare
			if($requestPage->status->http_code == 503) {
				// Make this the same user agent you use for other cURL requests in your app
				cloudflare::useUserAgent($httpProxyUA);
				
				// attempt to get clearance cookie	
				if($clearanceCookie = cloudflare::bypass($requestLink)) {
					// use clearance cookie to bypass page
					$requestPage = $httpProxy->performRequest($requestLink, 'GET', null, array(
						'cookies' => $clearanceCookie
					));
					// return real page content for site
					$requestPage = json_decode($requestPage);
					$this->assertTrue(true);
				} else {
					$this->assertTrue(false);
				}	
			}
	}

}