<?php

namespace Yakovmeister\Weebo\Component;

use Yakovmeister\Weebo\Application;

class IO
{
	/**
	 * [Singleton Instance for IO]
	 * @var [type]
	 */
	protected static $instance;
	
	/**
	 * @param  String $fileName
	 * @param  Mixed $content = null
	 * @param  String $writeMode = w+
	 */
	public function makeFile($fileName, $content = null, $writeMode = "w+")
	{
        $fp = fopen($fileName, $writeMode);
            fwrite($fp, $content);
        fclose($fp);    
	}

	/**
	 * @param  String $directoryName
	 * @return Boolean
	 */
	public function makeDirectory($directoryName)
	{
		return @mkdir($directoryName);
	}

	/**
	 * @param  String $directoryName
	 * @return Boolean
	 */
	public function removeDirectory($directoryName)
	{
		return @rmdir($directoryName);
	}

	/**
	 * @param  String $directoryName
	 * @return Boolean
	 */
	public function directoryExists($directoryName)
	{
		return is_dir($directoryName);
	}

	/**
	 * @param  Mixed 
	 * @return Yakovmeister\Weebo\Component\IO
	 */
	public function write($message)
	{
		if(is_array($message)) {
			foreach ($message as $value) {
				$this->write($value)->newLn();
			}
		} else {
			echo $message;
		}

		return $this;
	}

    /**
     *
     * @access public
     * @param String $prompt
     * @return String
     */
    public function read($prompt)
    {
        if($prompt) echo $prompt;

        $stream = fopen ("php://stdin","r");

        return trim(fgets($stream));
    }

	/**
	 *
	 * @access public
	 * @return Yakovmeister\Weebo\Component\IO
	 */
	public function newLn()
	{
		$this->write("\n");

		return $this;
	}

	/**
	 *
	 * @access public
	 * @return Yakovmeister\Weebo\Component\IO
	 */
	public function retLn()
	{
		$this->write("\r");

		return $this;
	}

    /**
     * @access public
     * @param String $file1
     * @param String $file2
     * @return Bool
     */
    public function hashMismatched($file1, $file2)
    {
    	if(!file_exists($file1)) return true;

        return $this->makeHash($file1) !== $this->makeHash($file2);
    }

    /**
     * @access public
     * @param String $file
     * @return String md5hashed
     */
    public function makeHash($file)
    {
        return @md5_file($file);
    }

    public static function getInstance()
    {
    	return empty(static::$instance) 
    			? Application::getInstance()->make(IO::class)
    			: static::$instance;
    }

}