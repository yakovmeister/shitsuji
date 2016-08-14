# Shitsuji #
---
Mighty butler is here to serve you! Shitsuji is a simple Command Line Interface based Anime Downloader.

##Requirements##
* PHP 7 or higher
* Terminal or Command Prompt
* php_curl extension

##Basic Usage##
* open cmd and type ```php shitsuji --search```
* you can also try ```php shitsuji --search anime title``` example: `php weebo --search Ansatsu Kyoshitsu`

##Notes to Self##
* this is yet to be tested on Linux
* ~~batch downloading is not yet available~~ Latest commit should work but not yet tested
* ~~it should allow users to customize download path~~ nonsense, not necessary
* ~~downloading may be broken (not yet tested due to slow internet connection)~~
* ~~As of now, downloaded videos are named video.mp4. It will overwrite existing video of same name. Fix will be implemented soon.~~
* 480p quality is downloaded but 720p is displayed as default
* Scrapper class not documented.
* Hoping someone could help me test this.

##Future Features##
* Allow Download Resume
* Allow download from different mirror (for now, shitsuji can only download from mp4upload)
* Mirror Download Selection
* Anything I might find cool