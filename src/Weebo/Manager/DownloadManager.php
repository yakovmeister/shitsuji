<?php

namespace Yakovmeister\Weebo\Manager;

use Yakovmeister\Weebo\Component\Net;
use Yakovmeister\Weebo\Component\IO;

class DownloadManager
{
	use \Yakovmeister\Weebo\Component\FileInformationTrait;

	protected $net;

	protected $io;

	protected $response;

	public function __construct(Net $net, IO $io)
	{
		$this->net = $net;

		$this->io = $io;
	}

	/**
	 * @access public
	 * @param  Array $metadata
	 * @return Yakovmeister\Weebo\Manager\DownloadManager
	 */
	public function fetchFile(array $metadata)
	{
		$this->set("name", $metadata["name"])
			 ->set("mirrors", $metadata["mirrors"])
			 ->set("downloadPath", $metadata["path"]);

		if(!$this->hasMirrors()) {
			$this->io->write("Too bad, {$this->getName()} may not available with your video preference or it's not been released yet.")->newLn();
			return $this;
		}

		$this->set("extension", pathinfo($metadata["mirrors"][$this->getCurrentMirrorIndex()])["extension"]);

		// it seems that hash from source may differ as always
		if(!$this->io->hashMismatched($this->getDownloadPathWithName(), $this->getCurrentMirror())) 
		{
			$this->io->newLn()->newLn();
			$this->io->write("Hash Matched for: {$this->getName()}, skipping file")->newLn()->newLn(); 
		}
		else
		{
			$loaded = $this->net->load($this->getCurrentMirror(), [$this, "progressCallback"]);

			if(($loaded->getResponseStatus() == Net::HTTP_NOT_FOUND) && ($this->getCurrentMirrorIndex() < count($this->getMirrors()))) {
				$this->set("mirrorIndex", $this->getCurrentMirrorIndex() + 1);
				return $this->fetchFile($metadata);
			}

			$this->response = $loaded;			
		}

		return $this;
	}

	/**
	 * @access public
	 */
	public function save()
	{
		if(empty($this->getName()) && empty($this->getDownloadPath())) throw new PathNotFoundExtension;
		if(empty($this->response)) return ;
		if($this->response->getResponseStatus() == Net::HTTP_NOT_FOUND) return ;

		if(!$this->io->directoryExists($this->getDownloadPath())) 
			$this->io->makeDirectory($this->getDownloadPath());

		$this->io->makeFile($this->getDownloadPathWithName(), $this->response->getResponse());

		$this->io->retLn()->write("Downloading {$this->getName()} Finished!")->newLn()->newLn();
	}

	/**
	 * @access public
	 * @param  String $notification_code
	 * @param  String $severity
	 * @param  String $message
	 * @param  Integer $message_code
	 * @param  Integer $bytes_transferred
	 * @param  Integer $bytes_max
	 */
	public function progressCallback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
	{
		$bytes_transferred = convertFileSize($bytes_transferred);
		$bytes_max = convertFileSize($bytes_max);

		switch ($notification_code) 
		{
			case STREAM_NOTIFY_CONNECT:
				$this->io->newLn()->newLn();
				$this->io->write([
					"Downloading: {$this->getName()}",
					"----------------"
				]);
				break;
			case STREAM_NOTIFY_FILE_SIZE_IS:
				$this->io->write("Size: {$bytes_max}")->newLn();
				break;
			case STREAM_NOTIFY_PROGRESS:
				$this->io->write("                            ")->retLn();
				$this->io->write("{$bytes_transferred}/{$bytes_max}")->retLn();
				break;
		}
	}
}