"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _readline = require("readline");

var _readline2 = _interopRequireDefault(_readline);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Utility = function () {
    function Utility() {
        _classCallCheck(this, Utility);

        this.io = _readline2.default.createInterface({
            input: process.stdin,
            output: process.stdout
        });
    }

    /**
     * Read input from user
     * @param {string} message
     * @param {function} callback
     */


    _createClass(Utility, [{
        key: "read",
        value: function read(message) {
            var _this = this;

            return new Promise(function (resolve, reject) {
                if (typeof message != "string") reject(new Error("read: first argument is expecting a string " + (typeof message === "undefined" ? "undefined" : _typeof(message)) + " given."));

                _this.io.question(message, function (answer) {
                    resolve(answer);
                    _this.io.close();
                });
            });
        }

        /**
         * Scrape video link from string convert HTML.
         * @param {string} source
         * @return {string} matched video URL.
         */

    }, {
        key: "scrapeMP4",
        value: function scrapeMP4(source) {
            if (typeof source != "string") throw new Error("scrapeMP4: expecting a string " + (typeof source === "undefined" ? "undefined" : _typeof(source)) + " given.");

            var match = source.match(/http:\/\/(.*?).mp4/g);

            // if there's no video link found using http protocol 
            // try to match source with https instead.
            if (typeof match == "array" && match < 0) {
                match = source.match(/https:\/\/(.*?).mp4/g);
            }

            return match[0];
        }
    }]);

    return Utility;
}();

exports.default = Utility;
//# sourceMappingURL=utility.js.map