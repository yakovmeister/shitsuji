<?php

use PHPUnit\Framework\TestCase;
use Yakovmeister\Weebo\Component\IO;
use Yakovmeister\Weebo\Application;

class IOTest extends TestCase
{

	protected $io;

	protected $fileCreationPath;

	protected $folderCreationPath;

	public function setUp()
	{
		$this->io = Application::getInstance()->make(IO::class);

		$this->fileCreationPath = "./tests/FileCreationTest";

		$this->folderCreationPath = "./tests/DirectoryCreationTest";

		if(!is_dir($this->fileCreationPath)) @mkdir($this->fileCreationPath);
		if(!is_dir($this->folderCreationPath)) @mkdir($this->folderCreationPath);
	}

	public function testFileCreation()
	{
		$this->io->makeFile("{$this->fileCreationPath}/file.txt","This is automatically created by test");

		$this->assertTrue(file_exists("{$this->fileCreationPath}/file.txt"));
	}

	public function testFolderCreation()
	{
		$this->assertTrue($this->io->makeDirectory($this->folderCreationPath."/".substr(md5(rand()), 0,5)));
	}

}