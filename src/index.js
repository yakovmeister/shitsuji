import {Anime as anime} from 'anime-scraper';
import Utility from './utility';
import async from 'async';
import co from 'co';

let detail = {};
let util = new Utility();

co(function * () {
    let input = yield util.read("Search for an anime: ");
    console.log(`[SEARCH] Looking for ${input}...`);
    let animeResult = yield anime.search(input);
    console.log(`[SEARCH] Complete. Returning results...\n`);
    console.log(`------------------------------------------`);

    for(let a in animeResult) {
        console.log(`[${a}]: ${animeResult[a].name}`);
    }

    let x = yield anime.fromUrl(animeResult[3].url);
    console.log(x);
});
