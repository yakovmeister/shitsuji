<?php

namespace Yakovmeister\Weebo\Component;

use \DOMDocument;
use \DOMXpath;

class Scraper
{

   /**
     * DOMDocument Object
     *
     * @access protected
     * @var \DOMDocument
     */
    protected $domDocObject;

    /**
     * the HTML codes saved as string
     *
     * @access protected
     * @var String $htmlString
     */
    protected $htmlString;

    /**
     * DOMXpath Object
     *
     * @access protected
     * @var \DOMXpath
     */
    protected $xpathObject;

    /**
     * xpath query
     *
     * @access protected
     * @var String $query
     */
    protected $query;

    /**
     * xpath query results
     *
     * @access protected
     * @var String $results
     */
    protected $results;
    
    /**
     * xpath query results cache
     *
     * @access protected
     * @var String $results
     */
    protected $resultCache = [];
    
    /**
     * xpath html attrib
     *
     * @access protected
     * @var String $attribute
     */
    protected $attribute;
    
    /**
     * HTML string you want to scrape
     *
     * @access public
     * @param String $htmlString
     * @return String Yakovmeister\Shitsuji\Scrapper
     */
    public function capture($htmlString)
    {
        $this->htmlString = normalizeHTML($htmlString);

        return $this;
    }
    
    /**
     * Add query for scrapping
     *
     * @access public
     * @param String $identifier, String $attribute = class
     * @return String Yakovmeister\Shitsuji\Scrapper
     */
    public function scrape($identifier, $attribute = 'class')
    {
        $this->query .= !empty($this->query) 
                        ? "and @{$attribute}='{$identifier}' "
                        : "@{$attribute}='{$identifier}'";

        return $this;

    }

    /**
     * Return scrapped results
     *
     * @access public
     * @return Array $cache
     */
    public function getResults()
    {
        $this->domDocObject = new DOMDocument;
 		
        @$this->domDocObject->loadHTML($this->htmlString);

        $this->xpathObject = new DOMXpath($this->domDocObject);

        $this->results = $this->xpathObject->query("//*[{$this->query}]");

        if(!empty($this->attribute))
        {
            foreach ($this->results as $result) 
            {
                array_push($this->resultCache, $result->getAttribute($this->attribute));
            }
        } 
        elseif(empty($this->resultCache)) 
         {
            foreach ($this->results as $result) 
            {
                array_push($this->resultCache, $result->textContent);
            }
        }

        $cache = $this->resultCache;

        $this->resultCache = [];
        $this->query = null;

        return $cache;
    }

    /**
     * Filter results by attribute
     *
     * @access public
     * @return Yakovmeister\Shitsuji\Scrapper
     */
    public function byAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }
 
}