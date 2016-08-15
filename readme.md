# Shitsuji #
---
Mighty butler is here to serve you! Shitsuji is a simple Command Line Interface (CLI) based Anime Downloader.

##Requirements##
* PHP 7 or higher
* Terminal or Command Prompt
* php_curl extension

##Basic Usage##
* open cmd and type ```php shitsuji --search```
* you can also try ```php shitsuji --search anime title``` example: `php shitsuji --search Ansatsu Kyoshitsu`

##Notes to Self##
* this is yet to be tested on Linux
* ~~batch downloading is not yet available~~ ~~Latest commit should work but not yet tested~~ Working but [Progress bar isn't working after first download.](https://github.com/yakovmeister/shitsuji/issues/1)
* ~~it should allow users to customize download path~~ nonsense, not necessary
* ~~downloading may be broken (not yet tested due to slow internet connection)~~
* ~~As of now, downloaded videos are named video.mp4. It will overwrite existing video of same name. Fix will be implemented soon.~~
* ~~480p quality is downloaded but 720p is displayed as default~~
* ~~Scrapper class not documented.~~
* ~~some anime title may cause error in creating file and folder~~
* Hoping someone could help me test this.

##Future Features##
* Allow Download Resume
* Allow download from different mirror (for now, shitsuji can only download from mp4upload)
* Mirror Download Selection
* Anything I might find cool
* Custom Episode Selections example (1-5 - to download 1 to 5), (1&5 - download only 1 and 5)
* File checking (skip downloading file if it exists)
* Anime episode as index