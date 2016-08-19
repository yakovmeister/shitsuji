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
     * Download path, where files are saved
     *
     * @access protected
     * @var String $fileDownloadPath
     */
    protected $fileDownloadPath;

    /**
     * Download the file 
     *
     * @access public
     * @param mixed $url, bool $isFile = false, bool $useIncludePath = false
     * @return file
     */
    public function download($url, $isFile = false, $useIncludePath = false)
    {
        /// reverse toilet. we flush before we take a shit lulz 
        $this->flush();
        $ctx = null;
        /// assumes that the url given has our data 
        /// format, if it's an array, sets our file variable
        if(is_array($url))
        {

            $this->fileInfo = pathinfo($url["url"]);

            $this->fileName = !empty($url["file_name"]) ? $url["file_name"] : $this->fileInfo["filename"];

            $this->fileExtension = $this->fileInfo["extension"];  

            $this->fileDownloadPath = $url["download_path"];

            $url = $url["url"];
        }
        
        if($isFile)
        {
            $ctx = stream_context_create();

            stream_context_set_params($ctx, ["notification" => [$this, "downloadCallback"]]);   
        }

        return file_get_contents($url, $useIncludePath, $ctx);
    }

    /**
     * Download files from server
     *
     * @access public 
     * @param Array $links, Bool $progressFn = false
     * @return Yakovmeister\Shitsuji\IO
     */
    public function downloadFiles($urls)
    { 

        foreach ($urls as $key => $value) 
        {
            $file = $this->download($value, true);

            // done downloading? save the fuck up
            $this->makeFile($this->getFile(), $file);
        }

        return $this;
    }

    public function downloadCallback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        switch($notification_code) {
            case STREAM_NOTIFY_RESOLVE:
            case STREAM_NOTIFY_AUTH_REQUIRED:
            case STREAM_NOTIFY_COMPLETED:
            case STREAM_NOTIFY_FAILURE:
            case STREAM_NOTIFY_AUTH_RESULT:
 //               var_dump($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max);
                /* Ignore */
                break;

            case STREAM_NOTIFY_REDIRECTED:
                echo "Being redirected to: ", $message;
               break;

            case STREAM_NOTIFY_CONNECT:
   
               echo "\n\nDownloading {$this->getFileNameOnly()}\n";
               break;

            case STREAM_NOTIFY_FILE_SIZE_IS:
                echo "File Size: ", convertFileSize($bytes_max);
                echo "\n";
                break;

            case STREAM_NOTIFY_PROGRESS:
                echo "\r                                                       ";
                echo "\rDownloading ",convertFileSize($bytes_transferred), " / ", convertFileSize($bytes_max);
                break;
        }
    }

    /**
     * reset variables, some says it's good to have a fresh start
     *
     * @access public
     * @return Yakovmeister\Shitsuji\IO
     */
    public function flush()
    {
        $this->fileName = null;
        $this->fileExtension = null;
        $this->fileInfo = null;

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
     * Create a file. duh.
     *
     * @access public
     */
    public function makeFile($fileName, $content, $writeMode = "w+")
    {
        $fp = fopen($fileName, $writeMode);
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
        return "{$this->get("fileDownloadPath")}/{$this->get("fileName")}.{$this->get("fileExtension")}";
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

    /**
     * Convert string to a safe file name
     *
     * @param String $filename
     * @return String $filename
     */
    public function safe_filename($filename)
    {
        $avoid = ["/" => "", "\\" => "", "*" => ".", 
                  ":" => "", "|" => "", "?" => "", 
                  "\"" => "", "<" => "[", ">" => "]"];

        foreach ($avoid as $illegalCharacter => $subtitute) 
        {
            $filename = str_replace($illegalCharacter, $subtitute, $filename);
        }

        return $filename;
    }
}