<?php

namespace Yakovmeister\Weebo\Component\Traits;

trait File
{

    /**
     * [the name of the file (without extension)]
     * @var string $name
     */
    protected $name;

    /**
     * [the file extension]
     * @var string $extension
     */
    protected $extension;

    /**
     * [directory on which the file will be saved]
     * @var string $downloadPath
     */
    protected $downloadPath;

    /**
     * [alternative links (if there's any, just encase the first link doesn't work)]
     * @var array $mirrors
     */
    protected $mirrors = [];

    /**
     * [mirror (alternative link) index]
     * @var integer
     */
    protected $mirrorIndex = 0;

    /**
     * @access public
     * @param String $id
     * @param Mixed $value
     * @return Yakovmeister\Weebo\Component\FileInformationTrait
     */
    public function set($id, $value)
    {
        $this->$id = $value;

        return $this;
    }

    /**
     * @access public
     * @param String $value
     * @return Yakovmeister\Weebo\Component\FileInformationTrait::set()
     */
    public function setName($value)
    {
        return $this->set("name", $value);
    }

    /**
     * @access public
     * @param String $id
     * @return Yakovmeister\Weebo\Component\FileInformationTrait::$id
     */
    public function get($id)
    {
        return $this->$id;
    }

    /**
     * @access public
     * @return Yakovmeister\Weebo\Component\FileInformationTrait::extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @access public
     * @return Yakovmeister\Weebo\Component\FileInfomationTrait::name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @access public
     * @return String 
     */
    public function getNameWithExtension()
    {
        return "{$this->getName()}.{$this->getExtension()}";
    }

    /**
     * @access public
     * @return Yakovmeister\Weebo\Component\FileInformationTrait::downloadPath
     */
    public function getDownloadPath()
    {
        return $this->downloadPath;
    }

    /**
     * @access public
     * @return String
     */
    public function getDownloadPathWithName()
    {
        $downloadPath = trim($this->getDownloadPath(), "/");
        
        return "{$downloadPath}/{$this->getNameWithExtension()}";
    }

    /**
     * @access public
     * @return Yakovmeister\Weebo\Component\FileInformationTrait::get()
     */
    public function getMirrors()
    {
        return $this->get("mirrors");
    }

    /**
     * @return boolean [check if mirrors are empty]
     */
    public function hasMirrors()
    {
        return !empty($this->getMirrors());
    }

    /**
     * @access public
     * @return Yakovmeister\Weebo\Component\FileInformationTrait::get()
     */
    public function getCurrentMirror()
    {
        return $this->getMirrors()[$this->getCurrentMirrorIndex()];
    }

    /**
     * @access public
     * @return Yakovmeister\Weebo\Component\FileInformationTrait::get()
     */
    public function getCurrentMirrorIndex()
    {
        return $this->get("mirrorIndex");
    }

    /**
     * @access public
     */
    public function flush()
    {
        $this->set("name", null);
        $this->set("extension", null);
        $this->set("downloadPath", null);
        $this->set("mirrors", []);
        $this->set("mirrorIndex", 0);   
    }
}