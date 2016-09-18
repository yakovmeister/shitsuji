
* anime => array that contains link and title of all available anime that matched the criteria
* animePreference => integer (value - 1) index of the anime you want to download
* episodes => array that contains episode number and links of the selected anime
* episodePreference => integer (value - 1) | string | array contains the index of the episode of the anime you want to download
* qualityPreference => string quality of the video
* languagePreference => language of the video (subbed or dubbed or raw)
* videoLinks => array direct mirrors of the video.



searchAnime($searchKey) => search and cache anime with link
selectAnime($index) => select anime preference
selectQuality($quality = "720p") select anime quality
selectLanguage($language = "subbed") select anime language
selectEpisodes($episodes = 1) select episode preference
captureEpisode() => fetch and cache episodes with link

