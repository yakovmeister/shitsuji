<?php

use Yakovmeister\Weebo\Manager\PageLoadManager as Page;
use Yakovmeister\Weebo\Exception\ExceedingFileSizeException;
use Yakovmeister\Weebo\Exception\IllegalRangeFormatException;

/**
 * turn bytes to Megabytes or even Gigabytes.
 *
 * @param Int $int
 * @return String
 */
if(!function_exists("convertFileSize"))
{
    function convertFileSize($int)
    {
        switch ($int) {
            case $int > 0 && $int < 1073741825:
                $int = round((($int / 1024) / 1024), 1);
                return "{$int} MB";
            case $int > 1073731824 && $int < 1099511627776:
                $int = round(((($int / 1024) / 1024) / 1024), 1);
                return "{$int} GB";
            case $int > 1099511627776:
                throw new ExceedingFileSizeException;
            default:
                return $int;
                break;
        }
    }
}

/**
 * RAWRAnime would return a weird string of html document.
 * So we normalize them here..
 *
 * @param String $html
 * @return String $html
 */
if(!function_exists("normalizeHTML"))
{
    function normalizeHTML($html)
    {
        return str_replace('\/', '/',   /// Since we don't need a escaped characters, we replace \/ with forward slash
        	   str_replace('\n', '',    /// then we change \n to nothing. :P lulz. we don't need formatting duh.
        	   str_replace('\"', "'",   /// I feel like double-quotes are problem, so let there be single quote
        	   trim($html,"\""))));  /// Lastly trim excess fat... The excess double-quote
	}
}

/**
 * @param String $filename
 * @return $filename
 */
if(!function_exists("normalizeFileName"))
{
    function normalizeFileName($filename)
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

/**
 * 
 */
if(!function_exists("separateList"))
{
    function separateList($data)
    {
        $num = [];

        switch ($data) {
            case strpos($data, "-") > 0:
            
                $data = explode("-", $data);

                if(count($data) > 2) throw new IllegalRangeFormatException;

                for ($index = $data[0]; $index <= $data[1]; $index++) {
                    array_push($num, $index);
                }

                break;
            case strpos($data, ",") > 0:
                $data = explode(",", $data);

                return $data;
            default:
                if(ctype_digit($data))
                    array_push($num, $data);
                else
                    array_push($num, 0);

                return $num;
        }

        return $num;
    }
}

/**
 * Extract video link from HTML document
 *
 * @access public
 * @param String  $html  "HTML string from where you will extract the link"
 * @return String        "matched link"
 */
if(!function_exists("getmp4uploadLink"))
{
    function getmp4uploadLink($html)
    {
        preg_match("/http:\/\/(.*?)video.mp4/", $html, $match);

        return $match[0];
    }
}

if(!function_exists("mp4uploadLinkMultiple"))
{
    function mp4uploadLinkMultiple(array $mp4uploadLinks)
    {
        $returnLink = [];
        foreach($mp4uploadLinks as $link)
        {
            array_push($returnLink, getmp4uploadLink(Page::getInstance()->load($link)["message"]));
        }

        return $returnLink;
    }
}

/**
 * Dump and Die
 * @param Mixed $expression expression you want to dump
 */
if(!function_exists("dd"))
{
    function dd($expression)
    {
        die(var_dump($expression));
    }
}