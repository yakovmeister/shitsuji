<?php

namespace Yakovmeister\Shitsuji;

class IO
{

    /**
     * File name, well.. you SHOULD set this one to avoid having
     * your downloads named "video".
     *
     * @access protected
     * @var String $fileName
     */
    protected $fileName;

    /**
     * File extension of the file you're trying to download
     *
     * @access protected
     * @var String $fileExtension
     */
    protected $fileExtension;

    /**
     * Page information, including the name of the file, and extension
     *
     * @access protected
     * @var Array $pageInfo
     */
    protected $pageInfo;

    /**
     * Page or File you're trying to load.
     *
     * @access protected
     * @var curl $load
     */
    protected $load;

    /**
     * Load the URL, create a curl request
     *
     * @access public 
     * @param String $url, Bool $progressFn = false, String $filename = null
     * @return Yakovmeister\Shitsuji\IO
     */
    public function loadURL($url, $progressFn = false, $filename = null)
    {
        $this->reset();

        $this->pageInfo = pathinfo($url);
        
        $handle         = curl_init($url);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        if($progressFn)
        {


            ob_start();
            ob_flush();
            flush();	
            	
            $this->fileName      = !empty($filename) ? $filename : $this->pageInfo['filename'];
            $this->fileExtension = $this->pageInfo['extension'];

            $this->n()->console("Downloading: {$this->getFileNameOnly()} ")->n();

            curl_setopt($handle, CURLOPT_PROGRESSFUNCTION, [$this, "progress"]);
            curl_setopt($handle, CURLOPT_NOPROGRESS, false);
        }

        $this->load = curl_exec($handle);
		
        curl_close($handle);

        if($progressFn) ob_flush(); flush();

        return $this;
	}

    /**
     * curl PROGRESSFUNCTION callback. display download progress
     *
     * @access public
     * @param $clientp, Double $dlnow, Double $dlnow, Double $ultotal, Double $ulnow
     */
    public function progress($clientp, $dltotal, $dlnow, $ultotal, $ulnow)
    {
        $downloadTotalSize   = convertFileSize($dltotal);
        $downloadedSize      = convertFileSize($dlnow);
		
        if($dltotal > 0){
            $this->console("Downloading: {$downloadedSize} / {$downloadTotalSize}")->r();
        }

        ob_flush();
        flush();
    }

    /**
     * Reset everything. Some says it good to have a fresh start
     *
     * @access public
     * @return Yakovmeister\Shitsuji\IO
     */
    public function reset()
    {
    	$this->fileName         = null;
    	$this->fileExtension    = null;
    	$this->pageInfo         = null;
    	$this->load             = null;

    	return $this;
    }

    /**
     * Prints new line in console... duh
     *
     * @access public
     * @return Yakovmeister\Shitsuji\IO
     */
    public function n()
    {
        $this->console("\n");

        return $this;
    }

    /**
     * Prints a carriage return in console... duh
     *
     * @access public
     * @return Yakovmeister\Shitsuji\IO
     */
    public function r()
    {
    	$this->console("\r");

    	return $this;
    }

    /**
     * Just to keep code clean. Prints the parameter given
     *
     * @access public
     * @param String $text
     * @return Yakovmeister\Shitsuji\IO
     */
    public function console($text)
    {
        echo $text;

        return $this;
    }

    /**
     * Receive users' input. Return that input mah nigguh.
     *
     * @access public
     * @param String $prompt
     * @return String
     */
    public function gets($prompt)
    {
        if($prompt) echo $prompt;

        $stream = fopen ("php://stdin","r");

        return trim(fgets($stream));
    }

    /**
     * Save/Write downloaded file. duh.
     *
     * @access public
     */
    public function store()
    {
        $this->makeFile($this->getFile(), $this->getLoad());
    }

    /**
     * Create a file. duh.
     *
     * @access public
     */
    public function makeFile($fileName, $content)
    {
        $fp = fopen($fileName, "w+");
            fwrite($fp, $content);
        fclose($fp);	
    }

    /**
     * Get property
     *
     * @access public
     * @return Yakovmeister\Shitsuji\IO::$propertyName
     */
    public function get($propertyName)
    {
        return $this->$propertyName;
    }

    /**
     * Get load property
     *
     * @access public
     * @return curl Yakovmeister\Shitsuji\IO::$load
     */
    public function getLoad()
    {
        return $this->get("load");
    }

    /**
     * Get file name and file extension property
     *
     * @access public
     * @return String Yakovmeister\Shitsuji\IO::$fileName.Yakovmeister\Shitsuji\IO::$fileExtension
     */
    public function getFile()
    {
        return "{$this->get("fileName")}.{$this->get("fileExtension")}";
    }

    /**
     * Instead of returning filename with path, return file name only
     *
     * @access public
     * @return String
     */
    public function getFileNameOnly()
    {
        $filenameArray = explode("/", $this->getFile());

        return $filenameArray[ count($filenameArray) - 1];   
    }

    /**
     * Make a folder duh
     *
     * @access public
     * @return Bool
     */
    public function makeDirectory($dirname)
    {
        return @mkdir($dirname);
    }

    /**
     * Check if the folder exist
     *
     * @access public
     * @return Bool
     */
    public function directory($dirname)
    {
        return is_dir($dirname);
    }
}