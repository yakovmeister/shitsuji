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

    protected $multipleHandle;

    protected $multipleLoad;

    protected $fp;

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

    public function loadMultipleURL(array $links, $progressFn = false)
    {
        $this->resetMultiple();

        $fp = [];

        $itemCount = count($links);

        $handle = curl_multi_init();

        foreach ($links as $key => $value) 
        {
            $this->multipleHandle[$key] = curl_init($value['url']);

            $this->pageInfo = pathinfo($value['url']);

            curl_setopt($this->multipleHandle[$key], CURLOPT_RETURNTRANSFER, true);

            if($progressFn)
            {
                ob_start();
                ob_flush();
                flush();    

                $this->fileName      = !empty($value['file_name']) && !empty($value['download_path']) 
                                     ? "{$value['download_path']}/{$value['file_name']}" : $this->pageInfo['filename'];
                $this->fileExtension = $this->pageInfo['extension'];

                $this->n()->console("Downloading: {$this->getFileNameOnly()} ")->n();

                $fp[$key] = fopen($this->getFile(), "w+");

                curl_setopt($this->multipleHandle[$key], CURLOPT_BINARYTRANSFER, true);
                curl_setopt($this->multipleHandle[$key], CURLOPT_FILE, $fp[$key]);
                curl_setopt($this->multipleHandle[$key], CURLOPT_PROGRESSFUNCTION, [$this, "progress"]);
                curl_setopt($this->multipleHandle[$key], CURLOPT_NOPROGRESS, false);
            }

            curl_multi_add_handle($handle, $this->multipleHandle[$key]);

        }

        do {
            $this->multipleLoad = curl_multi_exec($handle, $isActive);
        } while($isActive);

        foreach ($links as $key => $value) 
        {
            curl_multi_remove_handle($handle, $this->multipleHandle[$key]);
            curl_close($this->multipleHandle[$key]);
            fclose($fp[$key]);
        }

        curl_multi_close($handle);

        if($progressFn) ob_flush(); flush();

        return die("staph");
    }

    public function saveFile($clientp, $string)
    {
        
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
        $this->resetFileInfo();
        $this->load             = null;

        return $this;
    }

    public function resetFileInfo()
    {
        $this->fileName         = null;
        $this->fileExtension    = null;
        $this->pageInfo         = null;
    }

    public function resetMultiple()
    {
        $this->reset();
        /// but wait there's more...
        $this->multipleHandle = null;
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