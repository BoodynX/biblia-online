/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 40);
/******/ })
/************************************************************************/
/******/ ({

/***/ 40:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(8);


/***/ }),

/***/ 8:
/***/ (function(module, exports) {

$(document).ready(function () {
    /**
     * SWITCH FORM ACTION ATTR ON CLICK
     * Changes the forms action attr depending on which button was pushed
     * button determined by value attr
     */
    $('.navButton').on('click', function () {
        var elem = $(this);
        if (elem[0].value == 'the_end') {
            document.chapter_nav_buttons.action = '/last';
        } else {
            if (elem[0].value == 'next_step') {
                var book = elem[0].dataset.book;
                var chapter = elem[0].dataset.chapter;
            }
            if (elem[0].value == 'next_book') {
                var book = elem[0].dataset.book;
                var chapter = elem[0].dataset.chapter;
            }
            document.chapter_nav_buttons.action = '/ksiega/' + book + '/rozdzial/' + chapter;
        }
    });

    /**
     * SEND QUESTION - AJAX
     */
    $('#faq_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/chapter/send_question',
            data: $('#faq_form').serialize(),
            success: function success(msg) {
                $("#faq_form").collapse('hide');
                $("#ajaxResponse").text(msg);
                $("#ajaxResponse").collapse('show');
            }
        });
    });

    /**
     * Hide the "Thank You" message, after reopening the FAQ form, after a question was send
     */
    $('#faq_form_question').on('click', function () {
        $("#ajaxResponse").collapse('hide');
    });
});

/***/ })

/******/ });