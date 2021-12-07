/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/blocks/article-teasers/edit.js":
/*!***************************************************!*\
  !*** ./assets/src/blocks/article-teasers/edit.js ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ edit; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./editor.scss */ "./assets/src/blocks/article-teasers/editor.scss");








/**
 * Editor styles.
 */


/**
 * Edit function.
 *
 * @return {WPElement} Element to render.
 */

function edit(_ref) {
  let {
    attributes,
    setAttributes
  } = _ref;
  const {
    featuredArticleId
  } = attributes;

  const blockProps = () => {
    let classNames = []; // This is here to force the 'dirty' state.

    classNames.push('featured-article-id-' + featuredArticleId);
    return (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.useBlockProps)({
      className: classNames
    });
  };

  const {
    articles
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useSelect)(select => {
    const articles = select('ramp').getArticles();
    return {
      articles
    };
  });
  let articlesOptions = articles.map(article => {
    return {
      label: article.title.rendered,
      value: article.id
    };
  });
  articlesOptions.unshift({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select a Featured Article', 'ramp'),
    value: 0
  });
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.InspectorControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Panel, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Featured Article', 'ramp')
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select an Article for the Featured area', 'ramp'),
    value: featuredArticleId,
    options: articlesOptions,
    onChange: featuredArticleId => setAttributes({
      featuredArticleId
    })
  })))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", blockProps(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default()), {
    attributes: attributes,
    block: "ramp/article-teasers",
    httpMethod: "GET"
  })));
}

/***/ }),

/***/ "./assets/src/blocks/article-teasers/index.js":
/*!****************************************************!*\
  !*** ./assets/src/blocks/article-teasers/index.js ***!
  \****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./assets/src/blocks/article-teasers/edit.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./assets/src/blocks/article-teasers/block.json");
/**
 * Research Topics block.
 */


/**
 * Internal dependencies
 */



/**
 * Block definition.
 */

(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__, {
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],

  /**
   * Rendered in PHP.
   */
  save: () => {
    return null;
  }
});

/***/ }),

/***/ "./assets/src/blocks/research-topic-teasers/edit.js":
/*!**********************************************************!*\
  !*** ./assets/src/blocks/research-topic-teasers/edit.js ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ edit; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./editor.scss */ "./assets/src/blocks/research-topic-teasers/editor.scss");








/**
 * Editor styles.
 */


/**
 * Edit function.
 *
 * @return {WPElement} Element to render.
 */

function edit(_ref) {
  let {
    attributes,
    setAttributes
  } = _ref;
  const {
    numberOfItems,
    selectionType,
    slot1,
    slot2,
    slot3
  } = attributes;

  const blockProps = () => {
    let classNames = []; // This is here to force the 'dirty' state.

    classNames.push('number-of-items-' + numberOfItems);
    classNames.push('selection-type-' + selectionType);
    classNames.push('slots-' + slot1 + '-' + slot2 + '-' + slot3);
    return (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.useBlockProps)({
      className: classNames
    });
  };

  const spinner = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Spinner, null);
  const {
    researchTopics
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useSelect)(select => {
    const researchTopics = select('ramp').getResearchTopics();
    return {
      researchTopics
    };
  });
  let researchTopicsOptions = researchTopics.map(topic => {
    return {
      label: topic.title.rendered,
      value: topic.id
    };
  });
  researchTopicsOptions.unshift({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select a Research Topic', 'ramp'),
    value: 0
  });
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.InspectorControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Panel, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Number of Items', 'ramp')
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalNumberControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Number of Research Topics to show', 'ramp'),
    value: numberOfItems,
    min: 0,
    max: 5,
    step: 1,
    onChange: numberOfItems => setAttributes({
      numberOfItems
    })
  }))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Panel, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Content Settings', 'ramp')
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Research Topics to show', 'ramp'),
    value: selectionType,
    options: [{
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Alphabetical', 'ramp'),
      value: 'alphabetical'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Recently Added', 'ramp'),
      value: 'latest'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Random', 'ramp'),
      value: 'random'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Specific', 'ramp'),
      value: 'specific'
    }],
    onChange: selectionType => setAttributes({
      selectionType
    })
  }), 'specific' === selectionType && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("fieldset", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("legend", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select a Research Topic for each slot.', 'ramp')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Slot 1', 'ramp'),
    labelPosition: "side",
    value: slot1,
    options: researchTopicsOptions,
    onChange: slot1 => setAttributes({
      slot1
    })
  })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Slot 2', 'ramp'),
    labelPosition: "side",
    value: slot2,
    options: researchTopicsOptions,
    onChange: slot2 => setAttributes({
      slot2
    })
  })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Slot 3', 'ramp'),
    labelPosition: "side",
    value: slot3,
    options: researchTopicsOptions,
    onChange: slot3 => setAttributes({
      slot3
    })
  }))))))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", blockProps(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default()), {
    attributes: attributes,
    block: "ramp/research-topic-teasers",
    httpMethod: "GET",
    LoadingResponsePlaceholder: _wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Spinner
  })));
}

/***/ }),

/***/ "./assets/src/blocks/research-topic-teasers/index.js":
/*!***********************************************************!*\
  !*** ./assets/src/blocks/research-topic-teasers/index.js ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./assets/src/blocks/research-topic-teasers/edit.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./assets/src/blocks/research-topic-teasers/block.json");
/**
 * Research Topics block.
 */


/**
 * Internal dependencies
 */



/**
 * Block definition.
 */

(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__, {
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],

  /**
   * Rendered in PHP.
   */
  save: () => {
    return null;
  }
});

/***/ }),

/***/ "./assets/src/blocks/zotero-library-info-help/edit.js":
/*!************************************************************!*\
  !*** ./assets/src/blocks/zotero-library-info-help/edit.js ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ edit; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./editor.scss */ "./assets/src/blocks/zotero-library-info-help/editor.scss");



/**
 * Editor styles.
 */


/**
 * Edit function.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

function edit(props) {
  const blockProps = () => {
    const classNames = ['ramp-zotero-library-info-help'];
    return (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)({
      className: classNames
    });
  };

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", blockProps(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enter the name of your Zotero Library above. This is for your reference only.', 'ramp')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('In the sidebar, find the "Library Settings" section under the "Zotero Library" tab.', 'ramp')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enter your Zotero group URL, group ID, and API key in the provided fields.', 'ramp')));
}

/***/ }),

/***/ "./assets/src/blocks/zotero-library-info-help/index.js":
/*!*************************************************************!*\
  !*** ./assets/src/blocks/zotero-library-info-help/index.js ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./assets/src/blocks/zotero-library-info-help/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./save */ "./assets/src/blocks/zotero-library-info-help/save.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./block.json */ "./assets/src/blocks/zotero-library-info-help/block.json");


/**
 * Internal dependencies
 */




/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_4__, {
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],

  /**
   * @see ./save.js
   */
  save: _save__WEBPACK_IMPORTED_MODULE_3__["default"]
  /**
   * @see ./deprecated.js
   */
  //deprecated: deprecated,

});

/***/ }),

/***/ "./assets/src/blocks/zotero-library-info-help/save.js":
/*!************************************************************!*\
  !*** ./assets/src/blocks/zotero-library-info-help/save.js ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ save; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);



/**
 * Save function.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */

function save(_ref) {
  let {
    attributes,
    className
  } = _ref;
  const {
    content,
    year
  } = attributes;
  const blockProps = {
    className: 'ginger-timeline-year'
  };
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", blockProps, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ginger-timeline-year-year"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.RichText.Content, {
    tagName: "h3",
    value: year
  })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ginger-timeline-year-content"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.RichText.Content, {
    tagName: "ul",
    value: content
  })));
}

/***/ }),

/***/ "./assets/src/components/ZoteroLibraryInfo.js":
/*!****************************************************!*\
  !*** ./assets/src/components/ZoteroLibraryInfo.js ***!
  \****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ZoteroLibraryInfo; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7__);











function ZoteroLibraryInfo(_ref) {
  let {
    attributes,
    setAttributes
  } = _ref;
  const postType = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.select)('core/editor').getCurrentPostType();

  if ('ssrc_zotero_library' !== postType) {
    return null;
  }

  const {
    apiKey,
    groupId,
    groupUrl,
    libraryInfo,
    postId
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(select => {
    const {
      zotero_api_key,
      zotero_group_id,
      zotero_group_url
    } = select('core/editor').getEditedPostAttribute('meta');
    const postId = select('core/editor').getCurrentPostId();
    const libraryInfo = select('ramp').getLibraryInfo(postId);
    return {
      apiKey: zotero_api_key,
      groupId: zotero_group_id,
      groupUrl: zotero_group_url,
      libraryInfo,
      postId
    };
  }, []);

  const triggerIngest = () => {
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_7___default()({
      path: (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_4__.addQueryArgs)('ramp/v1/zotero-library', {
        libraryId: postId
      }),
      method: 'POST'
    }).then(res => {
      console.log('I heard back');
    });
  };

  const setGroupId = value => {
    (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.dispatch)('core/editor').editPost({
      meta: {
        'zotero_group_id': value
      }
    });
  };

  const setGroupUrl = value => {
    (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.dispatch)('core/editor').editPost({
      meta: {
        'zotero_group_url': value
      }
    });
  };

  const setApiKey = value => {
    (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.dispatch)('core/editor').editPost({
      meta: {
        'zotero_api_key': value
      }
    });
  };

  let nextIngest;

  if (libraryInfo) {
    nextIngest = libraryInfo.nextIngest;
  }

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3__.PluginDocumentSettingPanel, {
    name: "ramp-zotero-library-info",
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Library Settings', 'ramp')
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Group ID', 'ramp'),
    value: groupId,
    onChange: value => setGroupId(value)
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Group URL', 'ramp'),
    value: groupUrl,
    onChange: value => setGroupUrl(value)
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('API Key', 'ramp'),
    value: apiKey,
    onChange: value => setApiKey(value)
  }), nextIngest, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "primary",
    onClick: triggerIngest
  }, "Trigger Zotero sync"));
}

/***/ }),

/***/ "./assets/src/store.js":
/*!*****************************!*\
  !*** ./assets/src/store.js ***!
  \*****************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "STORE_NAME": function() { return /* binding */ STORE_NAME; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);


const DEFAULT_STATE = {
  articles: [],
  libraries: {},
  researchTopics: []
};
const STORE_NAME = 'ramp';
const actions = {
  fetchFromAPI(path) {
    return {
      type: 'FETCH_FROM_API',
      path
    };
  },

  setLibraryInfo(libraryId, libraryInfo) {
    return {
      type: 'SET_LIBRARY_INFO',
      libraryId,
      libraryInfo
    };
  },

  setArticles(articles) {
    return {
      type: 'SET_ARTICLES',
      articles
    };
  },

  setResearchTopics(researchTopics) {
    return {
      type: 'SET_RESEARCH_TOPICS',
      researchTopics
    };
  }

};

const reducer = function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_LIBRARY_INFO':
      return { ...state,
        libraries: { ...state.libraries,
          [action.libraryId]: action.libraryInfo
        }
      };

    case 'SET_ARTICLES':
      return { ...state,
        articles: action.articles
      };

    case 'SET_RESEARCH_TOPICS':
      return { ...state,
        researchTopics: action.researchTopics
      };

    default:
      return state;
  }
};

const controls = {
  FETCH_FROM_API(action) {
    return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: action.path
    });
  }

};
const selectors = {
  getArticles(state) {
    const {
      articles
    } = state;
    return articles;
  },

  getLibraryInfo(state, libraryId) {
    const {
      libraries
    } = state;
    const libraryInfo = libraries[libraryId];
    return libraryInfo;
  },

  getResearchTopics(state) {
    const {
      researchTopics
    } = state;
    return researchTopics;
  }

};
const resolvers = {
  *getLibraryInfo(libraryId) {
    const path = '/ramp/v1/zotero-library/' + libraryId;
    const libraryInfo = yield actions.fetchFromAPI(path);
    return actions.setLibraryInfo(libraryId, libraryInfo);
  },

  *getResearchTopics() {
    const path = '/wp/v2/research-topics?per_page=50&orderby=title&order=asc';
    const researchTopics = yield actions.fetchFromAPI(path);
    return actions.setResearchTopics(researchTopics);
  },

  *getArticles() {
    const path = '/wp/v2/articles?per_page=100&orderby=title&order=asc';
    const articles = yield actions.fetchFromAPI(path);
    return actions.setArticles(articles);
  }

};
const storeConfig = {
  actions,
  reducer,
  controls,
  selectors,
  resolvers
};
(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.registerStore)(STORE_NAME, storeConfig);


/***/ }),

/***/ "./assets/css/blocks.css":
/*!*******************************!*\
  !*** ./assets/css/blocks.css ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/blocks/article-teasers/editor.scss":
/*!*******************************************************!*\
  !*** ./assets/src/blocks/article-teasers/editor.scss ***!
  \*******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/blocks/research-topic-teasers/editor.scss":
/*!**************************************************************!*\
  !*** ./assets/src/blocks/research-topic-teasers/editor.scss ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/blocks/zotero-library-info-help/editor.scss":
/*!****************************************************************!*\
  !*** ./assets/src/blocks/zotero-library-info-help/editor.scss ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ (function(module) {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/core-data":
/*!**********************************!*\
  !*** external ["wp","coreData"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["coreData"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/edit-post":
/*!**********************************!*\
  !*** external ["wp","editPost"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["editPost"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/plugins":
/*!*********************************!*\
  !*** external ["wp","plugins"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["plugins"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ (function(module) {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "@wordpress/url":
/*!*****************************!*\
  !*** external ["wp","url"] ***!
  \*****************************/
/***/ (function(module) {

module.exports = window["wp"]["url"];

/***/ }),

/***/ "./assets/src/blocks/article-teasers/block.json":
/*!******************************************************!*\
  !*** ./assets/src/blocks/article-teasers/block.json ***!
  \******************************************************/
/***/ (function(module) {

module.exports = JSON.parse('{"apiVersion":2,"name":"ramp/article-teasers","title":"Article Teasers","icon":"lightbulb","category":"ramp","style":"file:../../../../build/index.css","supports":{"anchor":true},"attributes":{"featuredArticleId":{"type":"integer","default":0}}}');

/***/ }),

/***/ "./assets/src/blocks/research-topic-teasers/block.json":
/*!*************************************************************!*\
  !*** ./assets/src/blocks/research-topic-teasers/block.json ***!
  \*************************************************************/
/***/ (function(module) {

module.exports = JSON.parse('{"apiVersion":2,"name":"ramp/research-topic-teasers","title":"Research Topic Teasers","icon":"lightbulb","category":"ramp","style":"file:../../../../build/index.css","supports":{"anchor":true},"attributes":{"numberOfItems":{"type":"integer","default":3},"selectionType":{"type":"string","default":"random"},"slot1":{"type":"integer","default":0},"slot2":{"type":"integer","default":0},"slot3":{"type":"integer","default":0}}}');

/***/ }),

/***/ "./assets/src/blocks/zotero-library-info-help/block.json":
/*!***************************************************************!*\
  !*** ./assets/src/blocks/zotero-library-info-help/block.json ***!
  \***************************************************************/
/***/ (function(module) {

module.exports = JSON.parse('{"apiVersion":2,"name":"ramp/zotero-library-info-help","title":"Zotero Library info help","icon":"book-alt","style":"file:../../../../build/index.css","category":"ramp","supports":{"anchor":true}}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!*****************************!*\
  !*** ./assets/src/index.js ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store */ "./assets/src/store.js");
/* harmony import */ var _blocks_research_topic_teasers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blocks/research-topic-teasers */ "./assets/src/blocks/research-topic-teasers/index.js");
/* harmony import */ var _blocks_article_teasers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./blocks/article-teasers */ "./assets/src/blocks/article-teasers/index.js");
/* harmony import */ var _blocks_zotero_library_info_help__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./blocks/zotero-library-info-help */ "./assets/src/blocks/zotero-library-info-help/index.js");
/* harmony import */ var _css_blocks_css__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../css/blocks.css */ "./assets/css/blocks.css");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _components_ZoteroLibraryInfo__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/ZoteroLibraryInfo */ "./assets/src/components/ZoteroLibraryInfo.js");
/**
 * Set up store
 */

/**
 * Import blocks
 */




/**
 * Shared block styles.
 */


/**
 * Components
 */



(0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_5__.registerPlugin)('zotero-library-info', {
  icon: 'book-alt',
  render: _components_ZoteroLibraryInfo__WEBPACK_IMPORTED_MODULE_6__["default"]
});
}();
/******/ })()
;
//# sourceMappingURL=index.js.map