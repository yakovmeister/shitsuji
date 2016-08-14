#!/usr/bin/env php
<?php

/**
 * Shitsuji - Command Line Interface (CLI) based Anime downloader for everyone.
 *
 * @package  Shitsuji
 */
 
require("vendor/autoload.php");


/// We are expecting a lot of memory usage since we're downloading a whole bunch 
/// of anime from source, so it's important to remove the limit or else it'll
/// trigger an error and won't let you continue to download the file.
ini_set("memory_limit", "-1");

$app = new \Yakovmeister\Shitsuji\App;
$app->run();
