<?php

namespace Yakovmeister\Weebo\Component;

use \Closure;

class Net
{
	const HTTP_OK = 200;

	const HTTP_NO_CONTENT = 204;

	const HTTP_MULTIPLE_CHOICE = 300;

	const HTTP_MOVED_PERMANENTLY = 301;

	const HTTP_FOUND = 302;
	
	const HTTP_TEMP_REDIR = 307;

	const HTTP_BAD_REQUEST = 400;

	const HTTP_UNAUTHORIZED = 401;

	const HTTP_FORBIDDEN = 403;

	const HTTP_NOT_FOUND = 404;

	const HTTP_REQUEST_TIMEOUT  = 408;

	const HTTP_BAD_GATEWAY  = 502;

	const HTTP_SERVICE_UNAVAILABLE = 503;

	/**
	 * [$response Response]
	 * @var Mixed
	 */
	protected $response;

	/**
	 * [$header HTTP Response Header]
	 * @var Array
	 */
	protected $header;

  	/**
  	 * [$status HTTP Response Status]
  	 * @var String
  	 */
	protected $status;

	/**
	 * 
	 * @param  String $url
	 * @param  Closure $callback
	 * @return Yakovmeister\Weebo\Component\Net
	 */
	public function load($url, $callback = null)
	{
		$ctx = null;
			
		if($callback instanceof Closure || is_callable($callback))
		{
			$ctx = stream_context_create();

			stream_context_set_params($ctx, ["notification" => $callback]);
		}

		$this->setResponse(@file_get_contents($url, false, $ctx));		

		$this->setHeader($http_response_header);

		$this->setStatus((int)substr($this->getResponseHeader(0), 9, 3));

		return $this;
	}

	/**
	 * 
	 * @param Mixed $response
	 * @return Yakovmeister\Weebo\Component\Net
	 */
	public function setResponse($response)
	{
		$this->response = $response;

		return $this;
	}

	/**
	 * 
	 * @param Array $header
	 * @return Yakovmeister\Weebo\Component\Net
	 */
	public function setHeader($header)
	{
		$this->header = $header;

		return $this;
	}

	/**
	 *
	 * @access public
	 * @param String $status
	 * @return Yakovmeister\Weebo\Component\Net::status
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * 
	 * @access public
	 * @return Yakovmeister\Weebo\Component\Net::response
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * 
	 * @access public
	 * @param  Integer $index = null
	 * @return Yakovmeister\Weebo\Component\Net::header
	 */
	public function getResponseHeader($index = null)
	{
		if( !is_null($index))
			return $this->header[$index];

		return $this->header;
	}

	/**
	 *
	 * @access public
	 * @return return Yakovmeister\Weebo\Component\Net::status
	 */
	public function getResponseStatus()
	{
		return $this->status;
	}

}