<?php

namespace Yakovmeister\Shitsuji;

use \DOMDocument;
use \DOMXpath;

class Scrapper
{

    protected $htmlDocument;

    protected $htmlString;

    protected $xpathObject;

    protected $query;

    protected $results;

    protected $resultCache = [];

    protected $attribute;

    public function capture($htmlString)
    {
        $this->htmlString = normalizeHTML($htmlString);

        return $this;
    }
 	
    public function scrape($identifier, $attribute = 'class')
    {
        $this->query .= !empty($this->query) 
                        ? "and @{$attribute}='{$identifier}' "
                        : "@{$attribute}='{$identifier}'";

        return $this;

    }

    public function getResults()
    {
        $this->htmlDocument = new DOMDocument;
 		
        @$this->htmlDocument->loadHTML($this->htmlString);

        $this->xpathObject = new DOMXpath($this->htmlDocument);

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

    public function byAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }
 
}