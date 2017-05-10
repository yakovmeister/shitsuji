'use strict';

var _animeScraper = require('anime-scraper');

var _utility = require('./utility');

var _utility2 = _interopRequireDefault(_utility);

var _async = require('async');

var _async2 = _interopRequireDefault(_async);

var _co = require('co');

var _co2 = _interopRequireDefault(_co);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var detail = {};
var util = new _utility2.default();

(0, _co2.default)(regeneratorRuntime.mark(function _callee() {
    var input, animeResult, a, x;
    return regeneratorRuntime.wrap(function _callee$(_context) {
        while (1) {
            switch (_context.prev = _context.next) {
                case 0:
                    _context.next = 2;
                    return util.read("Search for an anime: ");

                case 2:
                    input = _context.sent;

                    console.log('[SEARCH] Looking for ' + input + '...');
                    _context.next = 6;
                    return _animeScraper.Anime.search(input);

                case 6:
                    animeResult = _context.sent;

                    console.log('[SEARCH] Complete. Returning results...\n');
                    console.log('------------------------------------------');

                    for (a in animeResult) {
                        console.log('[' + a + ']: ' + animeResult[a].name);
                    }

                    _context.next = 12;
                    return _animeScraper.Anime.fromUrl(animeResult[3].url);

                case 12:
                    x = _context.sent;

                    console.log(x);

                case 14:
                case 'end':
                    return _context.stop();
            }
        }
    }, _callee, this);
}));
//# sourceMappingURL=index.js.map