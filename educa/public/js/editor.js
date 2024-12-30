/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************!*\
  !*** ./resources/js/editor.js ***!
  \********************************/
if (document.querySelector('.editor') != null) {
  window.editor = DecoupledDocumentEditor.create(document.querySelector('.editor'), {
    toolbar: {
      items: ['exportPdf', 'heading', '|', 'undo', 'redo', '|', 'fontSize', 'fontFamily', 'fontBackgroundColor', 'fontColor', '|', 'bold', 'italic', 'underline', 'strikethrough', 'highlight', '|', 'alignment', '|', 'numberedList', 'bulletedList', '|', 'indent', 'outdent', '|', 'todoList', 'link', 'blockQuote', 'imageUpload', 'insertTable', 'mediaEmbed', '|', 'MathType', 'ChemType']
    },
    language: 'de',
    image: {
      toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
    },
    table: {
      contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties']
    }
  }).then(function (editor) {
    window.editor = editor;

    // Set a custom container for the toolbar.
    document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
    document.querySelector('.ck-toolbar').classList.add('ck-reset_all');
  })["catch"](function (error) {
    console.error(error);
  });
}
/******/ })()
;