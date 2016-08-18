<?php

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
            case $int > 1073731824:
                $int = round(((($int / 1024) / 1024) / 1024), 1);
                return "{$int} GB";
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