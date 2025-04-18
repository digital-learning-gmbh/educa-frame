<html lang="de"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Benjamin Ledel">
    <style>
        body {
            background-color: #fff !important;
            margin: 50px;
        }
        /* nunito-200normal - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-display: swap;
            font-weight: 200;
            src:
                local('Nunito Extra Light '),
                local('Nunito-Extra Light'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-200.woff2?2dbeb8d854e77c952cb636cb14a151c4) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-200.woff?e53a21a8bf1b97fb9900e8d5c5bfbc14) format('woff'); /* Modern Browsers */
        }

        /* nunito-200italic - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: italic;
            font-display: swap;
            font-weight: 200;
            src:
                local('Nunito Extra Light italic'),
                local('Nunito-Extra Lightitalic'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-200italic.woff2?bbdf1c37c56e1bfaf618b9a4ee46d508) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-200italic.woff?2bcb70df023a72cb0a5864c993d03bfc) format('woff'); /* Modern Browsers */
        }

        /* nunito-300normal - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-display: swap;
            font-weight: 300;
            src:
                local('Nunito Light '),
                local('Nunito-Light'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-300.woff2?98aabf9aea1a55e2390109ad1efddce3) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-300.woff?97e0d2fc0422400a6e7be8f1b5e4949e) format('woff'); /* Modern Browsers */
        }

        /* nunito-300italic - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: italic;
            font-display: swap;
            font-weight: 300;
            src:
                local('Nunito Light italic'),
                local('Nunito-Lightitalic'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-300italic.woff2?ac71e7f9cd408dd512f6a6bbbd64eb51) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-300italic.woff?8646bd832f738c016b56b146af6fcd46) format('woff'); /* Modern Browsers */
        }

        /* nunito-400normal - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-display: swap;
            font-weight: 400;
            src:
                local('Nunito Regular '),
                local('Nunito-Regular'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-400.woff2?508e414e3d3bc41666826fee46c7d881) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-400.woff?e5875b853d135f2a82ceae7ac537b6f4) format('woff'); /* Modern Browsers */
        }

        /* nunito-400italic - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: italic;
            font-display: swap;
            font-weight: 400;
            src:
                local('Nunito Regular italic'),
                local('Nunito-Regularitalic'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-400italic.woff2?28f555d9a5c83842f03988ed14f00ef5) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-400italic.woff?09ce3100c075ee04f293b9191c88f156) format('woff'); /* Modern Browsers */
        }

        /* nunito-600normal - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-display: swap;
            font-weight: 600;
            src:
                local('Nunito SemiBold '),
                local('Nunito-SemiBold'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-600.woff2?8b8871e482a76d7e9327b02131564af7) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-600.woff?f894e279899f0cda9d06e32b78783399) format('woff'); /* Modern Browsers */
        }

        /* nunito-600italic - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: italic;
            font-display: swap;
            font-weight: 600;
            src:
                local('Nunito SemiBold italic'),
                local('Nunito-SemiBolditalic'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-600italic.woff2?9f930640316b0b9e8b088958d8f7aa5c) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-600italic.woff?9d150271cf30aefb8609dff4c9ece978) format('woff'); /* Modern Browsers */
        }

        /* nunito-700normal - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-display: swap;
            font-weight: 700;
            src:
                local('Nunito Bold '),
                local('Nunito-Bold'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-700.woff2?a22acb48f45d03d672bf5b9389363a83) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-700.woff?7d90308f9bdf7321be5e28d017a5ade5) format('woff'); /* Modern Browsers */
        }

        /* nunito-700italic - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: italic;
            font-display: swap;
            font-weight: 700;
            src:
                local('Nunito Bold italic'),
                local('Nunito-Bolditalic'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-700italic.woff2?4507a1361620a66827e217855c9fff24) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-700italic.woff?26556875512fee1b823bc6117c076358) format('woff'); /* Modern Browsers */
        }

        /* nunito-800normal - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-display: swap;
            font-weight: 800;
            src:
                local('Nunito ExtraBold '),
                local('Nunito-ExtraBold'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-800.woff2?eaa946756e91563b4b7d766de2f5b7ed) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-800.woff?5fa815a069b40c78f26863eaabcd6d2f) format('woff'); /* Modern Browsers */
        }

        /* nunito-800italic - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: italic;
            font-display: swap;
            font-weight: 800;
            src:
                local('Nunito ExtraBold italic'),
                local('Nunito-ExtraBolditalic'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-800italic.woff2?b9cd56a25c86d089cc18bc92dbad167a) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-800italic.woff?2e1a4979ec23066532d157ab5fe4ccc5) format('woff'); /* Modern Browsers */
        }

        /* nunito-900normal - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-display: swap;
            font-weight: 900;
            src:
                local('Nunito Black '),
                local('Nunito-Black'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-900.woff2?f82c6fb49774f4e4caa570640e164320) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-900.woff?19de37363cebfe020de6d4abdbb38c2e) format('woff'); /* Modern Browsers */
        }

        /* nunito-900italic - latin */
        @font-face {
            font-family: 'Nunito';
            font-style: italic;
            font-display: swap;
            font-weight: 900;
            src:
                local('Nunito Black italic'),
                local('Nunito-Blackitalic'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-900italic.woff2?6f10930c04eec040ac4ffb4890191149) format('woff2'),
                url(/fonts/vendor/typeface-nunito/files/nunito-latin-900italic.woff?ca56cb53793f0f9084f26315012cefd8) format('woff'); /* Modern Browsers */
        }

        /*!
 * Bootstrap Colorpicker - Bootstrap Colorpicker is a modular color picker plugin for Bootstrap 4.
 * @package bootstrap-colorpicker
 * @version v3.2.0
 * @license MIT
 * @link https://itsjavi.com/bootstrap-colorpicker/
 * @link https://github.com/itsjavi/bootstrap-colorpicker.git
 */
        .colorpicker {
            position: relative;
            display: none;
            font-size: inherit;
            color: inherit;
            text-align: left;
            list-style: none;
            background-color: #ffffff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.2);
            padding: .75rem .75rem;
            width: 148px;
            border-radius: 4px;
            box-sizing: content-box; }

        .colorpicker.colorpicker-disabled,
        .colorpicker.colorpicker-disabled * {
            cursor: default !important; }

        .colorpicker div {
            position: relative; }

        .colorpicker-popup {
            position: absolute;
            top: 100%;
            left: 0;
            float: left;
            margin-top: 1px;
            z-index: 1060; }

        .colorpicker-popup.colorpicker-bs-popover-content {
            position: relative;
            top: auto;
            left: auto;
            float: none;
            margin: 0;
            z-index: initial;
            border: none;
            padding: 0.25rem 0;
            border-radius: 0;
            background: none;
            box-shadow: none; }

        .colorpicker:before,
        .colorpicker:after {
            content: "";
            display: table;
            clear: both;
            line-height: 0; }

        .colorpicker-clear {
            clear: both;
            display: block; }

        .colorpicker:before {
            content: '';
            display: inline-block;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid #ccc;
            border-bottom-color: rgba(0, 0, 0, 0.2);
            position: absolute;
            top: -7px;
            left: auto;
            right: 6px; }

        .colorpicker:after {
            content: '';
            display: inline-block;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #ffffff;
            position: absolute;
            top: -6px;
            left: auto;
            right: 7px; }

        .colorpicker.colorpicker-with-alpha {
            width: 170px; }

        .colorpicker.colorpicker-with-alpha .colorpicker-alpha {
            display: block; }

        .colorpicker-saturation {
            position: relative;
            width: 126px;
            height: 126px;
            /* FF3.6+ */
            /* Chrome,Safari4+ */
            /* Chrome10+,Safari5.1+ */
            /* Opera 11.10+ */
            /* IE10+ */
            background: linear-gradient(to bottom, transparent 0%, black 100%), linear-gradient(to right, white 0%, rgba(255, 255, 255, 0) 100%);
            /* W3C */
            cursor: crosshair;
            float: left;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2);
            margin-bottom: 6px; }
        .colorpicker-saturation .colorpicker-guide {
            display: block;
            height: 6px;
            width: 6px;
            border-radius: 6px;
            border: 1px solid #000;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.8);
            position: absolute;
            top: 0;
            left: 0;
            margin: -3px 0 0 -3px; }

        .colorpicker-hue,
        .colorpicker-alpha {
            position: relative;
            width: 16px;
            height: 126px;
            float: left;
            cursor: row-resize;
            margin-left: 6px;
            margin-bottom: 6px; }

        .colorpicker-alpha-color {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%; }

        .colorpicker-hue,
        .colorpicker-alpha-color {
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2); }

        .colorpicker-hue .colorpicker-guide,
        .colorpicker-alpha .colorpicker-guide {
            display: block;
            height: 4px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.4);
            position: absolute;
            top: 0;
            left: 0;
            margin-left: -2px;
            margin-top: -2px;
            right: -2px;
            z-index: 1; }

        .colorpicker-hue {
            /* FF3.6+ */
            /* Chrome,Safari4+ */
            /* Chrome10+,Safari5.1+ */
            /* Opera 11.10+ */
            /* IE10+ */
            background: linear-gradient(to top, red 0%, #ff8000 8%, yellow 17%, #80ff00 25%, lime 33%, #00ff80 42%, cyan 50%, #0080ff 58%, blue 67%, #8000ff 75%, magenta 83%, #ff0080 92%, red 100%);
            /* W3C */ }

        .colorpicker-alpha {
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), white;
            background-size: 10px 10px;
            background-position: 0 0, 5px 5px;
            display: none; }

        .colorpicker-bar {
            min-height: 16px;
            margin: 6px 0 0 0;
            clear: both;
            text-align: center;
            font-size: 10px;
            line-height: normal;
            max-width: 100%;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2); }
        .colorpicker-bar:before {
            content: "";
            display: table;
            clear: both; }

        .colorpicker-bar.colorpicker-bar-horizontal {
            height: 126px;
            width: 16px;
            margin: 0 0 6px 0;
            float: left; }

        .colorpicker-input-addon {
            position: relative; }

        .colorpicker-input-addon i {
            display: inline-block;
            cursor: pointer;
            vertical-align: text-top;
            height: 16px;
            width: 16px;
            position: relative; }

        .colorpicker-input-addon:before {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            display: inline-block;
            vertical-align: text-top;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), white;
            background-size: 10px 10px;
            background-position: 0 0, 5px 5px; }

        .colorpicker.colorpicker-inline {
            position: relative;
            display: inline-block;
            float: none;
            z-index: auto;
            vertical-align: text-bottom; }

        .colorpicker.colorpicker-horizontal {
            width: 126px;
            height: auto; }

        .colorpicker.colorpicker-horizontal .colorpicker-bar {
            width: 126px; }

        .colorpicker.colorpicker-horizontal .colorpicker-saturation {
            float: none;
            margin-bottom: 0; }

        .colorpicker.colorpicker-horizontal .colorpicker-hue,
        .colorpicker.colorpicker-horizontal .colorpicker-alpha {
            float: none;
            width: 126px;
            height: 16px;
            cursor: col-resize;
            margin-left: 0;
            margin-top: 6px;
            margin-bottom: 0; }

        .colorpicker.colorpicker-horizontal .colorpicker-hue .colorpicker-guide,
        .colorpicker.colorpicker-horizontal .colorpicker-alpha .colorpicker-guide {
            position: absolute;
            display: block;
            bottom: -2px;
            left: 0;
            right: auto;
            height: auto;
            width: 4px; }

        .colorpicker.colorpicker-horizontal .colorpicker-hue {
            /* FF3.6+ */
            /* Chrome,Safari4+ */
            /* Chrome10+,Safari5.1+ */
            /* Opera 11.10+ */
            /* IE10+ */
            background: linear-gradient(to left, red 0%, #ff8000 8%, yellow 17%, #80ff00 25%, lime 33%, #00ff80 42%, cyan 50%, #0080ff 58%, blue 67%, #8000ff 75%, magenta 83%, #ff0080 92%, red 100%);
            /* W3C */ }

        .colorpicker.colorpicker-horizontal .colorpicker-alpha {
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), white;
            background-size: 10px 10px;
            background-position: 0 0, 5px 5px; }

        .colorpicker-inline:before,
        .colorpicker-no-arrow:before,
        .colorpicker-popup.colorpicker-bs-popover-content:before {
            content: none;
            display: none; }

        .colorpicker-inline:after,
        .colorpicker-no-arrow:after,
        .colorpicker-popup.colorpicker-bs-popover-content:after {
            content: none;
            display: none; }

        .colorpicker-alpha,
        .colorpicker-saturation,
        .colorpicker-hue {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none; }

        .colorpicker.colorpicker-visible,
        .colorpicker-alpha.colorpicker-visible,
        .colorpicker-saturation.colorpicker-visible,
        .colorpicker-hue.colorpicker-visible,
        .colorpicker-bar.colorpicker-visible {
            display: block; }

        .colorpicker.colorpicker-hidden,
        .colorpicker-alpha.colorpicker-hidden,
        .colorpicker-saturation.colorpicker-hidden,
        .colorpicker-hue.colorpicker-hidden,
        .colorpicker-bar.colorpicker-hidden {
            display: none; }

        .colorpicker-inline.colorpicker-visible {
            display: inline-block; }

        .colorpicker.colorpicker-disabled:after {
            border: none;
            content: '';
            display: block;
            width: 100%;
            height: 100%;
            background: rgba(233, 236, 239, 0.33);
            top: 0;
            left: 0;
            right: auto;
            z-index: 2;
            position: absolute; }

        .colorpicker.colorpicker-disabled .colorpicker-guide {
            display: none; }

        /** EXTENSIONS **/
        .colorpicker-preview {
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), white;
            background-size: 10px 10px;
            background-position: 0 0, 5px 5px; }

        .colorpicker-preview > div {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%; }

        .colorpicker-bar.colorpicker-swatches {
            box-shadow: none;
            height: auto; }

        .colorpicker-swatches--inner {
            clear: both;
            margin-top: -6px; }

        .colorpicker-swatch {
            position: relative;
            cursor: pointer;
            float: left;
            height: 16px;
            width: 16px;
            margin-right: 6px;
            margin-top: 6px;
            margin-left: 0;
            display: block;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2);
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), linear-gradient(45deg, rgba(0, 0, 0, 0.1) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.1) 75%, rgba(0, 0, 0, 0.1) 0), white;
            background-size: 10px 10px;
            background-position: 0 0, 5px 5px; }

        .colorpicker-swatch--inner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%; }

        .colorpicker-swatch:nth-of-type(7n+0) {
            margin-right: 0; }

        .colorpicker-with-alpha .colorpicker-swatch:nth-of-type(7n+0) {
            margin-right: 6px; }

        .colorpicker-with-alpha .colorpicker-swatch:nth-of-type(8n+0) {
            margin-right: 0; }

        .colorpicker-horizontal .colorpicker-swatch:nth-of-type(6n+0) {
            margin-right: 0; }

        .colorpicker-horizontal .colorpicker-swatch:nth-of-type(7n+0) {
            margin-right: 6px; }

        .colorpicker-horizontal .colorpicker-swatch:nth-of-type(8n+0) {
            margin-right: 6px; }

        .colorpicker-swatch:last-of-type:after {
            content: "";
            display: table;
            clear: both; }

        *[dir='rtl'] .colorpicker-element input,
        .colorpicker-element[dir='rtl'] input,
        .colorpicker-element input[dir='rtl'] {
            direction: ltr;
            text-align: right; }
        /*
 * This combined file was created by the DataTables downloader builder:
 *   https://datatables.net/download
 *
 * To rebuild or modify this file with the latest versions of the included
 * software please visit:
 *   https://datatables.net/download/#bs4/jszip-2.5.0/pdfmake-0.1.36/dt-1.10.22/af-2.3.5/b-1.6.5/b-colvis-1.6.5/b-html5-1.6.5/b-print-1.6.5/cr-1.5.2/fc-3.3.1/kt-2.5.3/r-2.2.6/sb-1.0.0/sp-1.2.1/sl-1.3.1
 *
 * Included libraries:
 *   JSZip 2.5.0, pdfmake 0.1.36, DataTables 1.10.22, AutoFill 2.3.5, Buttons 1.6.5, Column visibility 1.6.5, HTML5 export 1.6.5, Print view 1.6.5, ColReorder 1.5.2, FixedColumns 3.3.1, KeyTable 2.5.3, Responsive 2.2.6, SearchBuilder 1.0.0, SearchPanes 1.2.1, Select 1.3.1
 */

        table.dataTable {
            clear: both;
            margin-top: 6px !important;
            margin-bottom: 6px !important;
            max-width: none !important;
            border-collapse: separate !important;
            border-spacing: 0;
        }
        table.dataTable td,
        table.dataTable th {
            box-sizing: content-box;
        }
        table.dataTable td.dataTables_empty,
        table.dataTable th.dataTables_empty {
            text-align: center;
        }
        table.dataTable.nowrap th,
        table.dataTable.nowrap td {
            white-space: nowrap;
        }

        div.dataTables_wrapper div.dataTables_length label {
            font-weight: normal;
            text-align: left;
            white-space: nowrap;
        }
        div.dataTables_wrapper div.dataTables_length select {
            width: auto;
            display: inline-block;
        }
        div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
        }
        div.dataTables_wrapper div.dataTables_filter label {
            font-weight: normal;
            white-space: nowrap;
            text-align: left;
        }
        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: auto;
        }
        div.dataTables_wrapper div.dataTables_info {
            padding-top: 0.85em;
        }
        div.dataTables_wrapper div.dataTables_paginate {
            margin: 0;
            white-space: nowrap;
            text-align: right;
        }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin: 2px 0;
            white-space: nowrap;
            justify-content: flex-end;
        }
        div.dataTables_wrapper div.dataTables_processing {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 200px;
            margin-left: -100px;
            margin-top: -26px;
            text-align: center;
            padding: 1em 0;
        }

        table.dataTable > thead > tr > th:active,
        table.dataTable > thead > tr > td:active {
            outline: none;
        }
        table.dataTable > thead > tr > th:not(.sorting_disabled),
        table.dataTable > thead > tr > td:not(.sorting_disabled) {
            padding-right: 30px;
        }
        table.dataTable > thead .sorting,
        table.dataTable > thead .sorting_asc,
        table.dataTable > thead .sorting_desc,
        table.dataTable > thead .sorting_asc_disabled,
        table.dataTable > thead .sorting_desc_disabled {
            cursor: pointer;
            position: relative;
        }
        table.dataTable > thead .sorting:before, table.dataTable > thead .sorting:after,
        table.dataTable > thead .sorting_asc:before,
        table.dataTable > thead .sorting_asc:after,
        table.dataTable > thead .sorting_desc:before,
        table.dataTable > thead .sorting_desc:after,
        table.dataTable > thead .sorting_asc_disabled:before,
        table.dataTable > thead .sorting_asc_disabled:after,
        table.dataTable > thead .sorting_desc_disabled:before,
        table.dataTable > thead .sorting_desc_disabled:after {
            position: absolute;
            bottom: 0.9em;
            display: block;
            opacity: 0.3;
        }
        table.dataTable > thead .sorting:before,
        table.dataTable > thead .sorting_asc:before,
        table.dataTable > thead .sorting_desc:before,
        table.dataTable > thead .sorting_asc_disabled:before,
        table.dataTable > thead .sorting_desc_disabled:before {
            right: 1em;
            content: "\2191";
        }
        table.dataTable > thead .sorting:after,
        table.dataTable > thead .sorting_asc:after,
        table.dataTable > thead .sorting_desc:after,
        table.dataTable > thead .sorting_asc_disabled:after,
        table.dataTable > thead .sorting_desc_disabled:after {
            right: 0.5em;
            content: "\2193";
        }
        table.dataTable > thead .sorting_asc:before,
        table.dataTable > thead .sorting_desc:after {
            opacity: 1;
        }
        table.dataTable > thead .sorting_asc_disabled:before,
        table.dataTable > thead .sorting_desc_disabled:after {
            opacity: 0;
        }

        div.dataTables_scrollHead table.dataTable {
            margin-bottom: 0 !important;
        }

        div.dataTables_scrollBody table {
            border-top: none;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        div.dataTables_scrollBody table thead .sorting:before,
        div.dataTables_scrollBody table thead .sorting_asc:before,
        div.dataTables_scrollBody table thead .sorting_desc:before,
        div.dataTables_scrollBody table thead .sorting:after,
        div.dataTables_scrollBody table thead .sorting_asc:after,
        div.dataTables_scrollBody table thead .sorting_desc:after {
            display: none;
        }
        div.dataTables_scrollBody table tbody tr:first-child th,
        div.dataTables_scrollBody table tbody tr:first-child td {
            border-top: none;
        }

        div.dataTables_scrollFoot > .dataTables_scrollFootInner {
            box-sizing: content-box;
        }
        div.dataTables_scrollFoot > .dataTables_scrollFootInner > table {
            margin-top: 0 !important;
            border-top: none;
        }

        @media screen and (max-width: 767px) {
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter,
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: center;
            }
            div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                justify-content: center !important;
            }
        }
        table.dataTable.table-sm > thead > tr > th:not(.sorting_disabled) {
            padding-right: 20px;
        }
        table.dataTable.table-sm .sorting:before,
        table.dataTable.table-sm .sorting_asc:before,
        table.dataTable.table-sm .sorting_desc:before {
            top: 5px;
            right: 0.85em;
        }
        table.dataTable.table-sm .sorting:after,
        table.dataTable.table-sm .sorting_asc:after,
        table.dataTable.table-sm .sorting_desc:after {
            top: 5px;
        }

        table.table-bordered.dataTable {
            border-right-width: 0;
        }
        table.table-bordered.dataTable th,
        table.table-bordered.dataTable td {
            border-left-width: 0;
        }
        table.table-bordered.dataTable th:last-child, table.table-bordered.dataTable th:last-child,
        table.table-bordered.dataTable td:last-child,
        table.table-bordered.dataTable td:last-child {
            border-right-width: 1px;
        }
        table.table-bordered.dataTable tbody th,
        table.table-bordered.dataTable tbody td {
            border-bottom-width: 0;
        }

        div.dataTables_scrollHead table.table-bordered {
            border-bottom-width: 0;
        }

        div.table-responsive > div.dataTables_wrapper > div.row {
            margin: 0;
        }
        div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:first-child {
            padding-left: 0;
        }
        div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:last-child {
            padding-right: 0;
        }


        div.dt-autofill-handle{position:absolute;height:8px;width:8px;z-index:102;box-sizing:border-box;background:#0275d8;cursor:pointer}div.dtk-focus-alt div.dt-autofill-handle{background:#ff8b33}div.dt-autofill-select{position:absolute;z-index:1001;background-color:#0275d8;background-image:repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,0.5) 5px, rgba(255,255,255,0.5) 10px)}div.dt-autofill-select.top,div.dt-autofill-select.bottom{height:3px;margin-top:-1px}div.dt-autofill-select.left,div.dt-autofill-select.right{width:3px;margin-left:-1px}div.dt-autofill-list{position:fixed;top:50%;left:50%;width:500px;margin-left:-250px;background-color:white;border-radius:6px;box-shadow:0 0 5px #555;border:2px solid #444;z-index:11;box-sizing:border-box;padding:1.5em 2em}div.dt-autofill-list ul{display:table;margin:0;padding:0;list-style:none;width:100%}div.dt-autofill-list ul li{display:table-row}div.dt-autofill-list ul li:last-child div.dt-autofill-question,div.dt-autofill-list ul li:last-child div.dt-autofill-button{border-bottom:none}div.dt-autofill-list ul li:hover{background-color:#f6f6f6}div.dt-autofill-list div.dt-autofill-question{display:table-cell;padding:0.5em 0;border-bottom:1px solid #ccc}div.dt-autofill-list div.dt-autofill-question input[type=number]{padding:6px;width:30px;margin:-2px 0}div.dt-autofill-list div.dt-autofill-button{display:table-cell;padding:0.5em 0;border-bottom:1px solid #ccc}div.dt-autofill-background{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);background:radial-gradient(ellipse farthest-corner at center, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);z-index:10}div.dt-autofill-list div.dt-autofill-question input[type=number]{padding:6px;width:60px;margin:-2px 0}


        @keyframes dtb-spinner {
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @-webkit-keyframes dtb-spinner {
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        div.dt-button-info {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 400px;
            margin-top: -100px;
            margin-left: -200px;
            background-color: white;
            border: 2px solid #111;
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
            border-radius: 3px;
            text-align: center;
            z-index: 21;
        }
        div.dt-button-info h2 {
            padding: 0.5em;
            margin: 0;
            font-weight: normal;
            border-bottom: 1px solid #ddd;
            background-color: #f3f3f3;
        }
        div.dt-button-info > div {
            padding: 1em;
        }

        div.dt-button-collection-title {
            text-align: center;
            padding: 0.3em 0 0.5em;
            font-size: 0.9em;
        }

        div.dt-button-collection-title:empty {
            display: none;
        }

        div.dt-button-collection {
            position: absolute;
            z-index: 2001;
        }
        div.dt-button-collection div.dropdown-menu {
            display: block;
            z-index: 2002;
            min-width: 100%;
        }
        div.dt-button-collection div.dt-button-collection-title {
            background-color: white;
            border: 1px solid rgba(0, 0, 0, 0.15);
        }
        div.dt-button-collection.fixed {
            position: fixed;
            top: 50%;
            left: 50%;
            margin-left: -75px;
            border-radius: 0;
        }
        div.dt-button-collection.fixed.two-column {
            margin-left: -200px;
        }
        div.dt-button-collection.fixed.three-column {
            margin-left: -225px;
        }
        div.dt-button-collection.fixed.four-column {
            margin-left: -300px;
        }
        div.dt-button-collection > :last-child {
            display: block !important;
            -webkit-column-gap: 8px;
            -ms-column-gap: 8px;
            -o-column-gap: 8px;
            column-gap: 8px;
        }
        div.dt-button-collection > :last-child > * {
            -webkit-column-break-inside: avoid;
            break-inside: avoid;
        }
        div.dt-button-collection.two-column {
            width: 400px;
        }
        div.dt-button-collection.two-column > :last-child {
            padding-bottom: 1px;
            -webkit-column-count: 2;
            -ms-column-count: 2;
            -o-column-count: 2;
            column-count: 2;
        }
        div.dt-button-collection.three-column {
            width: 450px;
        }
        div.dt-button-collection.three-column > :last-child {
            padding-bottom: 1px;
            -webkit-column-count: 3;
            -ms-column-count: 3;
            -o-column-count: 3;
            column-count: 3;
        }
        div.dt-button-collection.four-column {
            width: 600px;
        }
        div.dt-button-collection.four-column > :last-child {
            padding-bottom: 1px;
            -webkit-column-count: 4;
            -ms-column-count: 4;
            -o-column-count: 4;
            column-count: 4;
        }
        div.dt-button-collection .dt-button {
            border-radius: 0;
        }
        div.dt-button-collection.fixed {
            max-width: none;
        }
        div.dt-button-collection.fixed:before, div.dt-button-collection.fixed:after {
            display: none;
        }

        div.dt-button-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999;
        }

        @media screen and (max-width: 767px) {
            div.dt-buttons {
                float: none;
                width: 100%;
                text-align: center;
                margin-bottom: 0.5em;
            }
            div.dt-buttons a.btn {
                float: none;
            }
        }
        div.dt-buttons button.btn.processing,
        div.dt-buttons div.btn.processing,
        div.dt-buttons a.btn.processing {
            color: rgba(0, 0, 0, 0.2);
        }
        div.dt-buttons button.btn.processing:after,
        div.dt-buttons div.btn.processing:after,
        div.dt-buttons a.btn.processing:after {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            box-sizing: border-box;
            display: block;
            content: ' ';
            border: 2px solid #282828;
            border-radius: 50%;
            border-left-color: transparent;
            border-right-color: transparent;
            animation: dtb-spinner 1500ms infinite linear;
            -o-animation: dtb-spinner 1500ms infinite linear;
            -ms-animation: dtb-spinner 1500ms infinite linear;
            -webkit-animation: dtb-spinner 1500ms infinite linear;
            -moz-animation: dtb-spinner 1500ms infinite linear;
        }


        table.DTCR_clonedTable.dataTable {
            position: absolute !important;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 202;
        }

        div.DTCR_pointer {
            width: 1px;
            background-color: #0275d8;
            z-index: 201;
        }


        table.DTFC_Cloned tr {
            background-color: white;
            margin-bottom: 0;
        }

        div.DTFC_LeftHeadWrapper table,
        div.DTFC_RightHeadWrapper table {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            background-color: white;
        }

        div.DTFC_LeftBodyWrapper table,
        div.DTFC_RightBodyWrapper table {
            border-top: none;
            margin: 0 !important;
            z-index: 2;
        }
        div.DTFC_LeftBodyWrapper table thead .sorting:before, div.DTFC_LeftBodyWrapper table thead .sorting:after,
        div.DTFC_LeftBodyWrapper table thead .sorting_asc:before,
        div.DTFC_LeftBodyWrapper table thead .sorting_asc:after,
        div.DTFC_LeftBodyWrapper table thead .sorting_desc:before,
        div.DTFC_LeftBodyWrapper table thead .sorting_desc:after,
        div.DTFC_LeftBodyWrapper table thead .sorting:before,
        div.DTFC_LeftBodyWrapper table thead .sorting:after,
        div.DTFC_LeftBodyWrapper table thead .sorting_asc:before,
        div.DTFC_LeftBodyWrapper table thead .sorting_asc:after,
        div.DTFC_LeftBodyWrapper table thead .sorting_desc:before,
        div.DTFC_LeftBodyWrapper table thead .sorting_desc:after,
        div.DTFC_RightBodyWrapper table thead .sorting:before,
        div.DTFC_RightBodyWrapper table thead .sorting:after,
        div.DTFC_RightBodyWrapper table thead .sorting_asc:before,
        div.DTFC_RightBodyWrapper table thead .sorting_asc:after,
        div.DTFC_RightBodyWrapper table thead .sorting_desc:before,
        div.DTFC_RightBodyWrapper table thead .sorting_desc:after,
        div.DTFC_RightBodyWrapper table thead .sorting:before,
        div.DTFC_RightBodyWrapper table thead .sorting:after,
        div.DTFC_RightBodyWrapper table thead .sorting_asc:before,
        div.DTFC_RightBodyWrapper table thead .sorting_asc:after,
        div.DTFC_RightBodyWrapper table thead .sorting_desc:before,
        div.DTFC_RightBodyWrapper table thead .sorting_desc:after {
            display: none;
        }
        div.DTFC_LeftBodyWrapper table tbody tr:first-child th,
        div.DTFC_LeftBodyWrapper table tbody tr:first-child td,
        div.DTFC_RightBodyWrapper table tbody tr:first-child th,
        div.DTFC_RightBodyWrapper table tbody tr:first-child td {
            border-top: none;
        }

        div.DTFC_LeftFootWrapper table,
        div.DTFC_RightFootWrapper table {
            border-top: none;
            margin-top: 0 !important;
            background-color: white;
        }

        div.DTFC_Blocker {
            background-color: white;
        }

        table.dataTable.table-striped.DTFC_Cloned tbody {
            background-color: white;
        }


        table.dataTable tbody th.focus,
        table.dataTable tbody td.focus {
            box-shadow: inset 0 0 1px 2px #0275d8;
        }

        div.dtk-focus-alt table.dataTable tbody th.focus,
        div.dtk-focus-alt table.dataTable tbody td.focus {
            box-shadow: inset 0 0 1px 2px #ff8b33;
        }


        table.dataTable.dtr-inline.collapsed > tbody > tr > td.child,
        table.dataTable.dtr-inline.collapsed > tbody > tr > th.child,
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dataTables_empty {
            cursor: default !important;
        }
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.child:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr > th.child:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dataTables_empty:before {
            display: none !important;
        }
        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td.dtr-control,
        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > th.dtr-control {
            position: relative;
            padding-left: 30px;
            cursor: pointer;
        }
        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > th.dtr-control:before {
            top: 50%;
            left: 5px;
            height: 1em;
            width: 1em;
            margin-top: -9px;
            display: block;
            position: absolute;
            color: white;
            border: 0.15em solid white;
            border-radius: 1em;
            box-shadow: 0 0 0.2em #444;
            box-sizing: content-box;
            text-align: center;
            text-indent: 0 !important;
            font-family: 'Courier New', Courier, monospace;
            line-height: 1em;
            content: '+';
            background-color: #0275d8;
        }
        table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control:before {
            content: '-';
            background-color: #d33333;
        }
        table.dataTable.dtr-inline.collapsed.compact > tbody > tr > td.dtr-control,
        table.dataTable.dtr-inline.collapsed.compact > tbody > tr > th.dtr-control {
            padding-left: 27px;
        }
        table.dataTable.dtr-inline.collapsed.compact > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed.compact > tbody > tr > th.dtr-control:before {
            left: 4px;
            height: 14px;
            width: 14px;
            border-radius: 14px;
            line-height: 14px;
            text-indent: 3px;
        }
        table.dataTable.dtr-column > tbody > tr > td.dtr-control,
        table.dataTable.dtr-column > tbody > tr > th.dtr-control,
        table.dataTable.dtr-column > tbody > tr > td.control,
        table.dataTable.dtr-column > tbody > tr > th.control {
            position: relative;
            cursor: pointer;
        }
        table.dataTable.dtr-column > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-column > tbody > tr > th.dtr-control:before,
        table.dataTable.dtr-column > tbody > tr > td.control:before,
        table.dataTable.dtr-column > tbody > tr > th.control:before {
            top: 50%;
            left: 50%;
            height: 0.8em;
            width: 0.8em;
            margin-top: -0.5em;
            margin-left: -0.5em;
            display: block;
            position: absolute;
            color: white;
            border: 0.15em solid white;
            border-radius: 1em;
            box-shadow: 0 0 0.2em #444;
            box-sizing: content-box;
            text-align: center;
            text-indent: 0 !important;
            font-family: 'Courier New', Courier, monospace;
            line-height: 1em;
            content: '+';
            background-color: #0275d8;
        }
        table.dataTable.dtr-column > tbody > tr.parent td.dtr-control:before,
        table.dataTable.dtr-column > tbody > tr.parent th.dtr-control:before,
        table.dataTable.dtr-column > tbody > tr.parent td.control:before,
        table.dataTable.dtr-column > tbody > tr.parent th.control:before {
            content: '-';
            background-color: #d33333;
        }
        table.dataTable > tbody > tr.child {
            padding: 0.5em 1em;
        }
        table.dataTable > tbody > tr.child:hover {
            background: transparent !important;
        }
        table.dataTable > tbody > tr.child ul.dtr-details {
            display: inline-block;
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        table.dataTable > tbody > tr.child ul.dtr-details > li {
            border-bottom: 1px solid #efefef;
            padding: 0.5em 0;
        }
        table.dataTable > tbody > tr.child ul.dtr-details > li:first-child {
            padding-top: 0;
        }
        table.dataTable > tbody > tr.child ul.dtr-details > li:last-child {
            border-bottom: none;
        }
        table.dataTable > tbody > tr.child span.dtr-title {
            display: inline-block;
            min-width: 75px;
            font-weight: bold;
        }

        div.dtr-modal {
            position: fixed;
            box-sizing: border-box;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 100;
            padding: 10em 1em;
        }
        div.dtr-modal div.dtr-modal-display {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 50%;
            height: 50%;
            overflow: auto;
            margin: auto;
            z-index: 102;
            overflow: auto;
            background-color: #f5f5f7;
            border: 1px solid black;
            border-radius: 0.5em;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
        }
        div.dtr-modal div.dtr-modal-content {
            position: relative;
            padding: 1em;
        }
        div.dtr-modal div.dtr-modal-close {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 22px;
            height: 22px;
            border: 1px solid #eaeaea;
            background-color: #f9f9f9;
            text-align: center;
            border-radius: 3px;
            cursor: pointer;
            z-index: 12;
        }
        div.dtr-modal div.dtr-modal-close:hover {
            background-color: #eaeaea;
        }
        div.dtr-modal div.dtr-modal-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 101;
            background: rgba(0, 0, 0, 0.6);
        }

        @media screen and (max-width: 767px) {
            div.dtr-modal div.dtr-modal-display {
                width: 95%;
            }
        }
        div.dtr-bs-modal table.table tr:first-child td {
            border-top: none;
        }

        table.dataTable.dtr-inline.collapsed.table-sm > tbody > tr > td:first-child:before,
        table.dataTable.dtr-inline.collapsed.table-sm > tbody > tr > th:first-child:before {
            top: 5px;
        }


        div.dt-datetime {
            position: absolute;
            background-color: white;
            z-index: 2050;
            border: 1px solid #ccc;
            box-shadow: 0 5px 15px -5px rgba(0, 0, 0, 0.5);
            padding: 0 20px 6px 20px;
            width: 275px;
        }
        div.dt-datetime div.dt-datetime-title {
            text-align: center;
            padding: 5px 0px 3px;
        }
        div.dt-datetime table {
            border-spacing: 0;
            margin: 12px 0;
            width: 100%;
        }
        div.dt-datetime table.dt-datetime-table-nospace {
            margin-top: -12px;
        }
        div.dt-datetime table th {
            font-size: 0.8em;
            color: #777;
            font-weight: normal;
            width: 14.285714286%;
            padding: 0 0 4px 0;
            text-align: center;
        }
        div.dt-datetime table td {
            font-size: 0.9em;
            color: #444;
            padding: 0;
        }
        div.dt-datetime table td.selectable {
            text-align: center;
            background: #f5f5f5;
        }
        div.dt-datetime table td.selectable.disabled {
            color: #aaa;
            background: white;
        }
        div.dt-datetime table td.selectable.disabled button:hover {
            color: #aaa;
            background: white;
        }
        div.dt-datetime table td.selectable.now {
            background-color: #ddd;
        }
        div.dt-datetime table td.selectable.now button {
            font-weight: bold;
        }
        div.dt-datetime table td.selectable.selected button {
            background: #4E6CA3;
            color: white;
            border-radius: 2px;
        }
        div.dt-datetime table td.selectable button:hover {
            background: #ff8000;
            color: white;
            border-radius: 2px;
        }
        div.dt-datetime table td.dt-datetime-week {
            font-size: 0.7em;
        }
        div.dt-datetime table button {
            width: 100%;
            box-sizing: border-box;
            border: none;
            background: transparent;
            font-size: inherit;
            color: inherit;
            text-align: center;
            padding: 4px 0;
            cursor: pointer;
            margin: 0;
        }
        div.dt-datetime table button span {
            display: inline-block;
            min-width: 14px;
            text-align: right;
        }
        div.dt-datetime table.weekNumber th {
            width: 12.5%;
        }
        div.dt-datetime div.dt-datetime-calendar table {
            margin-top: 0;
        }
        div.dt-datetime div.dt-datetime-label {
            position: relative;
            display: inline-block;
            height: 30px;
            padding: 5px 6px;
            border: 1px solid transparent;
            box-sizing: border-box;
            cursor: pointer;
        }
        div.dt-datetime div.dt-datetime-label:hover {
            border: 1px solid #ddd;
            border-radius: 2px;
            background-color: #f5f5f5;
        }
        div.dt-datetime div.dt-datetime-label select {
            position: absolute;
            top: 6px;
            left: 0;
            cursor: pointer;
            opacity: 0;
        }
        div.dt-datetime.horizontal {
            width: 550px;
        }
        div.dt-datetime.horizontal div.dt-datetime-date,
        div.dt-datetime.horizontal div.dt-datetime-time {
            width: 48%;
        }
        div.dt-datetime.horizontal div.dt-datetime-time {
            margin-left: 4%;
        }
        div.dt-datetime div.dt-datetime-date {
            position: relative;
            float: left;
            width: 100%;
        }
        div.dt-datetime div.dt-datetime-time {
            position: relative;
            float: left;
            width: 100%;
            text-align: center;
        }
        div.dt-datetime div.dt-datetime-time > span {
            vertical-align: middle;
        }
        div.dt-datetime div.dt-datetime-time th {
            text-align: left;
        }
        div.dt-datetime div.dt-datetime-time div.dt-datetime-timeblock {
            display: inline-block;
            vertical-align: middle;
        }
        div.dt-datetime div.dt-datetime-iconLeft,
        div.dt-datetime div.dt-datetime-iconRight,
        div.dt-datetime div.dt-datetime-iconUp,
        div.dt-datetime div.dt-datetime-iconDown {
            width: 30px;
            height: 30px;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.3;
            overflow: hidden;
            box-sizing: border-box;
        }
        div.dt-datetime div.dt-datetime-iconLeft:hover,
        div.dt-datetime div.dt-datetime-iconRight:hover,
        div.dt-datetime div.dt-datetime-iconUp:hover,
        div.dt-datetime div.dt-datetime-iconDown:hover {
            border: 1px solid #ccc;
            border-radius: 2px;
            background-color: #f0f0f0;
            opacity: 0.6;
        }
        div.dt-datetime div.dt-datetime-iconLeft button,
        div.dt-datetime div.dt-datetime-iconRight button,
        div.dt-datetime div.dt-datetime-iconUp button,
        div.dt-datetime div.dt-datetime-iconDown button {
            border: none;
            background: transparent;
            text-indent: 30px;
            height: 100%;
            width: 100%;
            cursor: pointer;
        }
        div.dt-datetime div.dt-datetime-iconLeft {
            position: absolute;
            top: 5px;
            left: 5px;
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAeCAYAAAAsEj5rAAAAUklEQVR42u3VMQoAIBADQf8Pgj+OD9hG2CtONJB2ymQkKe0HbwAP0xucDiQWARITIDEBEnMgMQ8S8+AqBIl6kKgHiXqQqAeJepBo/z38J/U0uAHlaBkBl9I4GwAAAABJRU5ErkJggg==");
        }
        div.dt-datetime div.dt-datetime-iconRight {
            position: absolute;
            top: 5px;
            right: 5px;
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAeCAYAAAAsEj5rAAAAU0lEQVR42u3VOwoAMAgE0dwfAnNjU26bYkBCFGwfiL9VVWoO+BJ4Gf3gtsEKKoFBNTCoCAYVwaAiGNQGMUHMkjGbgjk2mIONuXo0nC8XnCf1JXgArVIZAQh5TKYAAAAASUVORK5CYII=");
        }
        div.dt-datetime div.dt-datetime-iconUp {
            height: 20px;
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAALCAMAAABf9c24AAAAFVBMVEX///99fX1+fn57e3t6enoAAAAAAAC73bqPAAAABnRSTlMAYmJkZt92bnysAAAAL0lEQVR4AWOgJmBhxCvLyopHnpmVjY2VCadeoCxIHrcsWJ4RlyxCHlMWCTBRJxwAjrIBDMWSiM0AAAAASUVORK5CYII=");
        }
        div.dt-datetime div.dt-datetime-iconDown {
            height: 20px;
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAALCAMAAABf9c24AAAAFVBMVEX///99fX1+fn57e3t6enoAAAAAAAC73bqPAAAABnRSTlMAYmJkZt92bnysAAAAMElEQVR4AWOgDmBiRQIsmPKMrGxQgJDFlEfIYpoPk8Utz8qM232MYFfhkQfKUg8AANefAQxecJ58AAAAAElFTkSuQmCC");
        }

        div.dt-datetime-error {
            clear: both;
            padding: 0 1em;
            max-width: 240px;
            font-size: 11px;
            line-height: 1.25em;
            text-align: center;
            color: #b11f1f;
        }

        div.dt-button-collection {
            overflow: visible !important;
        }

        div.dtsb-searchBuilder {
            justify-content: space-evenly;
            cursor: default;
            margin-bottom: 1em;
        }
        div.dtsb-searchBuilder button.dtsb-button,
        div.dtsb-searchBuilder select {
            font-size: 1em;
        }
        div.dtsb-searchBuilder div.dtsb-titleRow {
            justify-content: space-evenly;
            margin-bottom: 0.5em;
        }
        div.dtsb-searchBuilder div.dtsb-titleRow div.dtsb-title {
            display: inline-block;
            padding-top: 6px;
        }
        div.dtsb-searchBuilder div.dtsb-titleRow button.dtsb-clearAll {
            float: right;
        }
        div.dtsb-searchBuilder div.dtsb-vertical .dtsb-value, div.dtsb-searchBuilder div.dtsb-vertical .dtsb-data, div.dtsb-searchBuilder div.dtsb-vertical .dtsb-condition {
            display: block;
        }
        div.dtsb-searchBuilder div.dtsb-group {
            position: relative;
            clear: both;
            margin-bottom: 0.8em;
        }
        div.dtsb-searchBuilder div.dtsb-group button.dtsb-clearGroup {
            margin: 2px;
        }
        div.dtsb-searchBuilder div.dtsb-group button.dtsb-add {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-logicContainer {
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
            position: absolute;
            margin-top: 0.8em;
            margin-right: 0.8em;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria {
            margin-bottom: 0.8em;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria select.dtsb-dropDown, div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria input.dtsb-input {
            padding: 0.4em;
            margin-right: 0.8em;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria select.dtsb-dropDown option.dtsb-notItalic, div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria input.dtsb-input option.dtsb-notItalic {
            font-style: normal;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria select.dtsb-italic {
            font-style: italic;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria div.dtsb-buttonContainer {
            float: right;
            display: inline-block;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria div.dtsb-buttonContainer button.dtsb-delete, div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria div.dtsb-buttonContainer button.dtsb-right, div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria div.dtsb-buttonContainer button.dtsb-left {
            margin-right: 0.8em;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria div.dtsb-buttonContainer button.dtsb-delete:last-child, div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria div.dtsb-buttonContainer button.dtsb-right:last-child, div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria div.dtsb-buttonContainer button.dtsb-left:last-child {
            margin-right: 0;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria span.dtsp-joiner {
            margin-right: 0.8em;
        }

        div.dtsb-searchBuilder div.dtsb-titleRow {
            height: 40px;
        }
        div.dtsb-searchBuilder div.dtsb-titleRow div.dtsb-title {
            padding-top: 10px;
        }
        div.dtsb-searchBuilder div.dtsb-group button.dtsb-clearGroup {
            margin-right: 8px;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria .form-control {
            width: auto;
            display: inline-block;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria select.dtsb-condition {
            border-color: #28a745;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria select.dtsb-data {
            border-color: #dc3545;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria select.dtsb-value, div.dtsb-searchBuilder div.dtsb-group div.dtsb-criteria input.dtsb-value {
            border-color: #007bff;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-logicContainer {
            border-radius: 4px;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-content: flex-start;
            align-items: flex-start;
            margin-top: 10px;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-logicContainer button.dtsb-logic {
            border: none;
            border-radius: 0px;
            flex-grow: 1;
            flex-shrink: 0;
            flex-basis: 40px;
            margin: 0px;
        }
        div.dtsb-searchBuilder div.dtsb-group div.dtsb-logicContainer button.dtsb-clearGroup {
            border: none;
            border-radius: 0px;
            width: 30px;
            margin: 0px;
        }


        div.dtsp-topRow {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-around;
            align-content: flex-start;
            align-items: flex-start;
        }
        div.dtsp-topRow input.dtsp-search {
            text-overflow: ellipsis;
        }
        div.dtsp-topRow div.dtsp-subRow1 {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            flex-grow: 1;
            flex-shrink: 0;
            flex-basis: 0;
        }
        div.dtsp-topRow div.dtsp-searchCont {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            flex-grow: 1;
            flex-shrink: 0;
            flex-basis: 0;
        }
        div.dtsp-topRow button.dtsp-nameButton {
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAYAAAAe2bNZAAABcGlDQ1BpY2MAACiRdZHNSwJBGMYftTDS8FCHkA57sOigIAXRMQzyYh3UIKvL7rirwe66zK6IdA26dBA6RF36OvQf1DXoWhAERRAR9B/0dQnZ3nEFJXSG2ffHs/O8zDwD+DM6M+yBJGCYDs+mU9JaYV0KviNMM4QoEjKzreXcUh59x88jfKI+JESv/vt6jlBRtRngGyKeYxZ3iBeIMzXHErxHPMbKcpH4hDjO6YDEt0JXPH4TXPL4SzDPZxcBv+gplbpY6WJW5gbxNHHM0KusfR5xk7BqruaoRmlNwEYWaaQgQUEVW9DhIEHVpMx6+5It3woq5GH0tVAHJ0cJZfLGSa1SV5WqRrpKU0dd5P4/T1ubnfG6h1PA4Kvrfk4CwX2g2XDd31PXbZ4BgRfg2uz4K5TT/DfpjY4WOwYiO8DlTUdTDoCrXWD82ZK53JICtPyaBnxcACMFYPQeGN7wsmr/x/kTkN+mJ7oDDo+AKdof2fwDCBRoDkL8UccAAAAJcEhZcwAAD2EAAA9hAag/p2kAAAK2SURBVFgJ7ZY9j41BFICvryCExrJBQ6HyEYVEIREaUZDQIRoR2ViJKCioxV+gkVXYTVZEQiEUhG2EQnxUCh0FKolY4ut5XnM2cyfva3Pt5m7EPcmzZ2bemTNnzjkzd1utnvQi0IvAfxiBy5z5FoxO89kPY+8mbMjtzs47RXs5/WVpbAG6bWExt5PuIibvhVkwmC+ck3eK9ln6/fAddFojYzBVuYSBpcnIEvRaqOw2RcaN18FPuJH0JvRUxbT3wWf4ltiKPgfVidWlbGZgPozDFfgAC+EA/K2EI4cwcAJ+gPaeQ+VQU2SOMMGcPgPl/m/V2p50rrbRsRgt9Iv5h6xtpP22Bz7Ce1C+gFFxfKzOmShcU+Qmyh2w3w8rIJfddHTck66EukL/xPhj+JM8rHNmFys0Pg4v0up3aFNlwR9NYyodd3OL/C64zpsymcTFcf6ElM4YzjAWKYrJkaq8kE/yUYNP4BoYvS1QRo+hNtF5xfkTUjoTheukSFFMjlTFm6PjceOca/SMpKfeCR1L6Uzk/y2WIkVhNFJlJAZhP+hYns7b9D3IPuhY5mYrIv8OrQJvR5NYyNaW4jsU8pSGNySiVx4o5tXq3JkoXE/mg5R/M8dGJCJpKhaDcjBRdbI/Rm8g69c122om33BHmj2CHoV5qa9jUXBraJ+G1fAVjIBO1klc87ro1K4JZ/K35SWW3TwcyDd6TecqnAEd8cGq2+w84xvBm1n3vS0izKkkwh5XNC/GmFPqqAtPF89AOScKuemaNzoTV1SD5dtSbmLf1/RV+tC0WTgcj6R7HEtrVGWaqu/lYDZ/2pvxQ/kIyw/gFByHC9AHw910hv1aUUumyd8yy0QfhmEkfiNod0Xusct68J1qc8Tdux0Z97Q+hsDb+AYGYEbF/4Guw2Q/qDPqZG/zXgT+3Qj8AtKnfWhFwmuAAAAAAElFTkSuQmCC");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 23px;
            vertical-align: bottom;
        }
        div.dtsp-topRow button.dtsp-countButton {
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABcGlDQ1BpY2MAACiRdZHNSwJBGMYftTDS8FCHkA57sOigIAXRMQzyYh3UIKvL7rirwe66zK6IdA26dBA6RF36OvQf1DXoWhAERRAR9B/0dQnZ3nEFJXSG2ffHs/O8zDwD+DM6M+yBJGCYDs+mU9JaYV0KviNMM4QoEjKzreXcUh59x88jfKI+JESv/vt6jlBRtRngGyKeYxZ3iBeIMzXHErxHPMbKcpH4hDjO6YDEt0JXPH4TXPL4SzDPZxcBv+gplbpY6WJW5gbxNHHM0KusfR5xk7BqruaoRmlNwEYWaaQgQUEVW9DhIEHVpMx6+5It3woq5GH0tVAHJ0cJZfLGSa1SV5WqRrpKU0dd5P4/T1ubnfG6h1PA4Kvrfk4CwX2g2XDd31PXbZ4BgRfg2uz4K5TT/DfpjY4WOwYiO8DlTUdTDoCrXWD82ZK53JICtPyaBnxcACMFYPQeGN7wsmr/x/kTkN+mJ7oDDo+AKdof2fwDCBRoDkL8UccAAAAJcEhZcwAAD2EAAA9hAag/p2kAAAG5SURBVEgN3VU9LwVBFF0fiYhofUSlEQkKhU7z/oBCQkIiGr9BgUbhVzy9BAnhFyjV/AYFiU5ICM7ZN+c5Zud5dm3lJmfmzrkz9+7cu3c3y/6jjOBSF8CxXS7FmTkbwqIJjDpJvTcmsJ4K3KPZUpyZsx0sxoB9J6mnAkyC7wGuuCFIipNtEcpcWExgXpOBc78vgj6N+QO4NVsjwdFM59tUIDxDrHMBOeIQ34C5ZDregXuAQm4YcI68nN9B3wr2PcwPAIPkN2EqtJH6b+QZm1ajjTx7BqwAr26Lb+C2Kvpbt0Mb2HAJ7NrGFGfmXO3DeA4UshDfQAVmH0gaUFg852TTTDvlxwBlCtxy9zXyBhQFaq0wMmIdRebrfgosA3zb2hKnqG0oqchp4QbuR8X0TjzABhbdOT8jnQ/atcgqpnfwOA7yqZyTU587ZkIGdesLTt2EkynOnbreMUUKMI/dA4B/QVOcO13CQh+5wWCgDwo/75u59odB/wjmfhbgvACcAOyZPHihMWAoIwxyCLgf1oxfgjzVbgBXSTzIN+f0pg6s5DkcesLMRpsBrgE2XO3CN64JFP7JtUeKHX4CKtRRXFZ+7dEAAAAASUVORK5CYII=");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 18px;
            vertical-align: bottom;
        }
        div.dtsp-topRow button.dtsp-searchIcon {
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAABcGlDQ1BpY2MAACiRdZHNSwJBGMYftTDS8FCHkA57sOigIAXRMQzyYh3UIKvL7rirwe66zK6IdA26dBA6RF36OvQf1DXoWhAERRAR9B/0dQnZ3nEFJXSG2ffHs/O8zDwD+DM6M+yBJGCYDs+mU9JaYV0KviNMM4QoEjKzreXcUh59x88jfKI+JESv/vt6jlBRtRngGyKeYxZ3iBeIMzXHErxHPMbKcpH4hDjO6YDEt0JXPH4TXPL4SzDPZxcBv+gplbpY6WJW5gbxNHHM0KusfR5xk7BqruaoRmlNwEYWaaQgQUEVW9DhIEHVpMx6+5It3woq5GH0tVAHJ0cJZfLGSa1SV5WqRrpKU0dd5P4/T1ubnfG6h1PA4Kvrfk4CwX2g2XDd31PXbZ4BgRfg2uz4K5TT/DfpjY4WOwYiO8DlTUdTDoCrXWD82ZK53JICtPyaBnxcACMFYPQeGN7wsmr/x/kTkN+mJ7oDDo+AKdof2fwDCBRoDkL8UccAAAAJcEhZcwAAD2EAAA9hAag/p2kAAAEnSURBVCgVpdG7SgNBFIDh1RhJsBBEsDIgIhaWFjZa2GtpKb6AnU0MprKOWEjK2IuFFxCxS2lhZyOWXh5AQVER/X+zuwwywoIHvp3dM3Nm55Ik/4i+P2or5FewiBIe0cEt8ogVz9LbhEVf+cgkcew1tvAZ5PPXGm9HOMEanMAYQhunaCAazuqA1UjvILl9HGPc/n4fabjPGbzjMM2FjfkDuPw5O8JilzgA9/OKWDynyWnbsPiF7yc4SRWxmEyTN7ZhsSd7gTLW8TuGSSzBcZd2hsV+n+MNC9jGCNzjPDwsz8XCO/x02Bqeptcxhg+4gjD8YxetLOkBGRbuwcIr+NdRLMPl3uMM2YHx2gsLd+D97qKEQuGe65jCAzbgVRWOCUZuovAfs5m/AdVxL0R1AIsLAAAAAElFTkSuQmCC");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 12px;
        }

        div.dt-button-collection {
            z-index: 2002;
        }

        div.dataTables_scrollBody {
            background: white !important;
        }

        div.dtsp-columns-1 {
            min-width: 98%;
            max-width: 98%;
            padding-left: 1%;
            padding-right: 1%;
            margin: 0px !important;
        }

        div.dtsp-columns-2 {
            min-width: 48%;
            max-width: 48%;
            padding-left: 1%;
            padding-right: 1%;
            margin: 0px !important;
        }

        div.dtsp-columns-3 {
            min-width: 30.333%;
            max-width: 30.333%;
            padding-left: 1%;
            padding-right: 1%;
            margin: 0px !important;
        }

        div.dtsp-columns-4 {
            min-width: 23%;
            max-width: 23%;
            padding-left: 1%;
            padding-right: 1%;
            margin: 0px !important;
        }

        div.dtsp-columns-5 {
            min-width: 18%;
            max-width: 18%;
            padding-left: 1%;
            padding-right: 1%;
            margin: 0px !important;
        }

        div.dtsp-columns-6 {
            min-width: 15.666%;
            max-width: 15.666%;
            padding-left: 0.5%;
            padding-right: 0.5%;
            margin: 0px !important;
        }

        div.dtsp-columns-7 {
            min-width: 13.28%;
            max-width: 13.28%;
            padding-left: 0.5%;
            padding-right: 0.5%;
            margin: 0px !important;
        }

        div.dtsp-columns-8 {
            min-width: 11.5%;
            max-width: 11.5%;
            padding-left: 0.5%;
            padding-right: 0.5%;
            margin: 0px !important;
        }

        div.dtsp-columns-9 {
            min-width: 11.111%;
            max-width: 11.111%;
            padding-left: 0.5%;
            padding-right: 0.5%;
            margin: 0px !important;
        }

        div.dt-button-collection {
            float: none;
        }

        div.dtsp-panesContainer {
            width: 100%;
        }

        div.dtsp-searchPanes {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-evenly;
            align-content: flex-start;
            align-items: stretch;
            clear: both;
        }
        div.dtsp-searchPanes button.btn {
            margin: 0;
        }
        div.dtsp-searchPanes button.dtsp-clearAll {
            max-width: 50px;
        }

        div.dtsp-columns-1,
        div.dtsp-columns-2,
        div.dtsp-columns-3,
        div.dtsp-columns-4,
        div.dtsp-columns-5,
        div.dtsp-columns-6,
        div.dtsp-columns-7,
        div.dtsp-columns-8,
        div.dtsp-columns-9 {
            padding-left: 0px;
            padding-right: 0px;
        }

        div.dtsp-searchPane {
            flex-direction: row;
            flex-wrap: nowrap;
            flex-grow: 1;
            flex-shrink: 0;
            flex-basis: 280px;
            justify-content: space-around;
            align-content: flex-start;
            align-items: stretch;
            padding-top: 0px;
            padding-bottom: 5px;
            margin: 5px 0;
            margin-top: 0px;
            margin-bottom: 0px;
            font-size: 0.9em;
            margin: 5px;
        }
        div.dtsp-searchPane div.dataTables_wrapper {
            flex: 1;
        }
        div.dtsp-searchPane div.dataTables_wrapper div.dataTables_filter {
            display: none;
        }
        div.dtsp-searchPane div.dataTables_wrapper div.row div.col-sm-12:empty {
            display: none;
        }
        div.dtsp-searchPane div.dataTables_wrapper div.row div.dataTables_filter {
            display: none;
        }
        div.dtsp-searchPane div.btn-group {
            padding: 0px;
        }
        div.dtsp-searchPane div.dtsp-topRow {
            padding: 0px !important;
            margin: 0px;
            margin-bottom: 0.5rem;
        }
        div.dtsp-searchPane div.dtsp-topRow div.dtsp-subRows {
            padding: 0px !important;
            text-align: right;
        }
        div.dtsp-searchPane div.dtsp-topRow div.row {
            width: 100%;
        }
        div.dtsp-searchPane div.dtsp-topRow button {
            min-width: 35px;
            max-width: 35px;
            border: 1px solid #ced4da;
        }
        div.dtsp-searchPane div.dtsp-topRow div.dtsp-subRow2 {
            margin-left: 5px;
        }
        div.dtsp-searchPane div.dtsp-topRow button.clearButton {
            padding-left: 10px;
        }
        div.dtsp-searchPane thead {
            display: none;
        }
        div.dtsp-searchPane .mb-3 {
            margin-bottom: none !important;
        }
        div.dtsp-searchPane .col-sm-12 {
            padding: 5px;
        }
        div.dtsp-searchPane .input-group {
            padding: 0px !important;
        }
        div.dtsp-searchPane .input-group .input-group-append {
            display: inline-block;
        }
        div.dtsp-searchPane div.dataTables_scrollHead {
            display: none;
        }
        div.dtsp-searchPane div.dataTables_scrollBody {
            padding: 2px;
            border: 2px #f0f0f0 solid;
            border-radius: 4px;
        }
        div.dtsp-searchPane div.dataTables_scrollBody:hover {
            border: 2px solid #cfcfcf !important;
        }
        div.dtsp-searchPane div.dataTables_scrollBody table {
            table-layout: fixed;
        }
        div.dtsp-searchPane div.dataTables_scrollBody table tbody tr td.dtsp-nameColumn {
            width: 100% !important;
        }
        div.dtsp-searchPane div.dataTables_scrollBody table tbody tr div.dtsp-nameCont {
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-content: flex-start;
            align-items: flex-start;
        }
        div.dtsp-searchPane div.dataTables_scrollBody table tbody tr div.dtsp-nameCont span.dtsp-name {
            text-overflow: ellipsis;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            white-space: nowrap;
            flex-grow: 1;
        }
        div.dtsp-searchPane div.dataTables_scrollBody table tbody tr div.dtsp-nameCont span.badge {
            min-width: 30px;
            display: inline-block;
            line-height: 1;
            margin-top: 3.5px;
        }
        div.dtsp-searchPane div.dataTables_scrollBody table tbody tr td.dtsp-countColumn {
            text-align: right;
        }
        div.dtsp-searchPane .dtsp-searchIcon {
            display: block;
            position: relative;
            padding: 18px 13px;
            border: 1px solid #ced4da;
        }
        div.dtsp-searchPane div.dataTables_wrapper div.dataTables_filter {
            display: none;
        }
        div.dtsp-searchPane div.dataTables_wrapper div.row {
            margin-left: -7px;
            margin-right: -7px;
        }
        div.dtsp-searchPane div.badge {
            min-width: 30px;
        }

        div.dtsp-panes {
            padding: 5px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            margin: 5px;
            clear: both;
        }
        div.dtsp-panes div.dtsp-titleRow {
            padding-bottom: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }
        div.dtsp-panes div.dtsp-title {
            float: left;
            margin-bottom: 0px;
            margin-top: 10px;
            padding-left: 0;
            padding-right: 0;
        }
        div.dtsp-panes button.dtsp-clearAll {
            float: right;
        }

        div.dtsp-hidden {
            display: none !important;
        }

        @media screen and (max-width: 767px) {
            div.dtsp-columns-4,
            div.dtsp-columns-5,
            div.dtsp-columns-6 {
                max-width: 31% !important;
                min-width: 31% !important;
            }
        }
        @media screen and (max-width: 640px) {
            div.dtsp-searchPanes {
                flex-direction: column !important;
            }

            div.dtsp-searchPane {
                max-width: 98% !important;
                min-width: 98% !important;
            }
        }


        table.dataTable tbody > tr.selected,
        table.dataTable tbody > tr > .selected {
            background-color: #0275d8;
        }
        table.dataTable.stripe tbody > tr.odd.selected,
        table.dataTable.stripe tbody > tr.odd > .selected, table.dataTable.display tbody > tr.odd.selected,
        table.dataTable.display tbody > tr.odd > .selected {
            background-color: #0272d3;
        }
        table.dataTable.hover tbody > tr.selected:hover,
        table.dataTable.hover tbody > tr > .selected:hover, table.dataTable.display tbody > tr.selected:hover,
        table.dataTable.display tbody > tr > .selected:hover {
            background-color: #0271d0;
        }
        table.dataTable.order-column tbody > tr.selected > .sorting_1,
        table.dataTable.order-column tbody > tr.selected > .sorting_2,
        table.dataTable.order-column tbody > tr.selected > .sorting_3,
        table.dataTable.order-column tbody > tr > .selected, table.dataTable.display tbody > tr.selected > .sorting_1,
        table.dataTable.display tbody > tr.selected > .sorting_2,
        table.dataTable.display tbody > tr.selected > .sorting_3,
        table.dataTable.display tbody > tr > .selected {
            background-color: #0273d4;
        }
        table.dataTable.display tbody > tr.odd.selected > .sorting_1, table.dataTable.order-column.stripe tbody > tr.odd.selected > .sorting_1 {
            background-color: #026fcc;
        }
        table.dataTable.display tbody > tr.odd.selected > .sorting_2, table.dataTable.order-column.stripe tbody > tr.odd.selected > .sorting_2 {
            background-color: #0270ce;
        }
        table.dataTable.display tbody > tr.odd.selected > .sorting_3, table.dataTable.order-column.stripe tbody > tr.odd.selected > .sorting_3 {
            background-color: #0270d0;
        }
        table.dataTable.display tbody > tr.even.selected > .sorting_1, table.dataTable.order-column.stripe tbody > tr.even.selected > .sorting_1 {
            background-color: #0273d4;
        }
        table.dataTable.display tbody > tr.even.selected > .sorting_2, table.dataTable.order-column.stripe tbody > tr.even.selected > .sorting_2 {
            background-color: #0274d5;
        }
        table.dataTable.display tbody > tr.even.selected > .sorting_3, table.dataTable.order-column.stripe tbody > tr.even.selected > .sorting_3 {
            background-color: #0275d7;
        }
        table.dataTable.display tbody > tr.odd > .selected, table.dataTable.order-column.stripe tbody > tr.odd > .selected {
            background-color: #026fcc;
        }
        table.dataTable.display tbody > tr.even > .selected, table.dataTable.order-column.stripe tbody > tr.even > .selected {
            background-color: #0273d4;
        }
        table.dataTable.display tbody > tr.selected:hover > .sorting_1, table.dataTable.order-column.hover tbody > tr.selected:hover > .sorting_1 {
            background-color: #026bc6;
        }
        table.dataTable.display tbody > tr.selected:hover > .sorting_2, table.dataTable.order-column.hover tbody > tr.selected:hover > .sorting_2 {
            background-color: #026cc8;
        }
        table.dataTable.display tbody > tr.selected:hover > .sorting_3, table.dataTable.order-column.hover tbody > tr.selected:hover > .sorting_3 {
            background-color: #026eca;
        }
        table.dataTable.display tbody > tr:hover > .selected,
        table.dataTable.display tbody > tr > .selected:hover, table.dataTable.order-column.hover tbody > tr:hover > .selected,
        table.dataTable.order-column.hover tbody > tr > .selected:hover {
            background-color: #026bc6;
        }
        table.dataTable tbody td.select-checkbox,
        table.dataTable tbody th.select-checkbox {
            position: relative;
        }
        table.dataTable tbody td.select-checkbox:before, table.dataTable tbody td.select-checkbox:after,
        table.dataTable tbody th.select-checkbox:before,
        table.dataTable tbody th.select-checkbox:after {
            display: block;
            position: absolute;
            top: 1.2em;
            left: 50%;
            width: 12px;
            height: 12px;
            box-sizing: border-box;
        }
        table.dataTable tbody td.select-checkbox:before,
        table.dataTable tbody th.select-checkbox:before {
            content: ' ';
            margin-top: -6px;
            margin-left: -6px;
            border: 1px solid black;
            border-radius: 3px;
        }
        table.dataTable tr.selected td.select-checkbox:after,
        table.dataTable tr.selected th.select-checkbox:after {
            content: '\2714';
            margin-top: -11px;
            margin-left: -4px;
            text-align: center;
            text-shadow: 1px 1px #B0BED9, -1px -1px #B0BED9, 1px -1px #B0BED9, -1px 1px #B0BED9;
        }

        div.dataTables_wrapper span.select-info,
        div.dataTables_wrapper span.select-item {
            margin-left: 0.5em;
        }

        @media screen and (max-width: 640px) {
            div.dataTables_wrapper span.select-info,
            div.dataTables_wrapper span.select-item {
                margin-left: 0;
                display: block;
            }
        }
        table.dataTable tbody tr.selected,
        table.dataTable tbody th.selected,
        table.dataTable tbody td.selected {
            color: white;
        }
        table.dataTable tbody tr.selected a,
        table.dataTable tbody th.selected a,
        table.dataTable tbody td.selected a {
            color: #a2d4ed;
        }


        /*!
 * required gridstack CSS for default 12 column size
 * https://gridstackjs.com/
 * (c) 2014-2019 Dylan Weiss, Alain Dumesny, Pavel Reznikov
 * gridstack.js may be freely distributed under the MIT license.
*/
        :root .grid-stack-item > .ui-resizable-handle {
            -webkit-filter: none;
            filter: none; }

        .grid-stack {
            position: relative; }
        .grid-stack.grid-stack-rtl {
            direction: ltr; }
        .grid-stack.grid-stack-rtl > .grid-stack-item {
            direction: rtl; }
        .grid-stack .grid-stack-placeholder > .placeholder-content {
            border: 1px dashed lightgray;
            margin: 0;
            position: absolute;
            top: 0;
            left: 10px;
            right: 10px;
            bottom: 0;
            width: auto;
            z-index: 0 !important;
            text-align: center; }
        .grid-stack > .grid-stack-item {
            min-width: 8.3333333333%;
            position: absolute;
            padding: 0; }
        .grid-stack > .grid-stack-item > .grid-stack-item-content {
            margin: 0;
            position: absolute;
            top: 0;
            left: 10px;
            right: 10px;
            bottom: 0;
            width: auto;
            overflow-x: hidden;
            overflow-y: auto; }
        .grid-stack > .grid-stack-item > .ui-resizable-handle {
            position: absolute;
            font-size: 0.1px;
            display: block;
            touch-action: none; }
        .grid-stack > .grid-stack-item.ui-resizable-disabled > .ui-resizable-handle,
        .grid-stack > .grid-stack-item.ui-resizable-autohide > .ui-resizable-handle {
            display: none; }
        .grid-stack > .grid-stack-item.ui-draggable-dragging, .grid-stack > .grid-stack-item.ui-resizable-resizing {
            z-index: 100; }
        .grid-stack > .grid-stack-item.ui-draggable-dragging > .grid-stack-item-content,
        .grid-stack > .grid-stack-item.ui-draggable-dragging > .grid-stack-item-content, .grid-stack > .grid-stack-item.ui-resizable-resizing > .grid-stack-item-content,
        .grid-stack > .grid-stack-item.ui-resizable-resizing > .grid-stack-item-content {
            box-shadow: 1px 4px 6px rgba(0, 0, 0, 0.2);
            opacity: 0.8; }
        .grid-stack > .grid-stack-item > .ui-resizable-se,
        .grid-stack > .grid-stack-item > .ui-resizable-sw {
            background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDUxMS42MjYgNTExLjYyNyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTExLjYyNiA1MTEuNjI3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTMyOC45MDYsNDAxLjk5NGgtMzYuNTUzVjEwOS42MzZoMzYuNTUzYzQuOTQ4LDAsOS4yMzYtMS44MDksMTIuODQ3LTUuNDI2YzMuNjEzLTMuNjE1LDUuNDIxLTcuODk4LDUuNDIxLTEyLjg0NSAgIGMwLTQuOTQ5LTEuODAxLTkuMjMxLTUuNDI4LTEyLjg1MWwtNzMuMDg3LTczLjA5QzI2NS4wNDQsMS44MDksMjYwLjc2LDAsMjU1LjgxMywwYy00Ljk0OCwwLTkuMjI5LDEuODA5LTEyLjg0Nyw1LjQyNCAgIGwtNzMuMDg4LDczLjA5Yy0zLjYxOCwzLjYxOS01LjQyNCw3LjkwMi01LjQyNCwxMi44NTFjMCw0Ljk0NiwxLjgwNyw5LjIyOSw1LjQyNCwxMi44NDVjMy42MTksMy42MTcsNy45MDEsNS40MjYsMTIuODUsNS40MjYgICBoMzYuNTQ1djI5Mi4zNThoLTM2LjU0MmMtNC45NTIsMC05LjIzNSwxLjgwOC0xMi44NSw1LjQyMWMtMy42MTcsMy42MjEtNS40MjQsNy45MDUtNS40MjQsMTIuODU0ICAgYzAsNC45NDUsMS44MDcsOS4yMjcsNS40MjQsMTIuODQ3bDczLjA4OSw3My4wODhjMy42MTcsMy42MTcsNy44OTgsNS40MjQsMTIuODQ3LDUuNDI0YzQuOTUsMCw5LjIzNC0xLjgwNywxMi44NDktNS40MjQgICBsNzMuMDg3LTczLjA4OGMzLjYxMy0zLjYyLDUuNDIxLTcuOTAxLDUuNDIxLTEyLjg0N2MwLTQuOTQ4LTEuODA4LTkuMjMyLTUuNDIxLTEyLjg1NCAgIEMzMzguMTQyLDQwMy44MDIsMzMzLjg1Nyw0MDEuOTk0LDMyOC45MDYsNDAxLjk5NHoiIGZpbGw9IiM2NjY2NjYiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K);
            background-repeat: no-repeat;
            background-position: center;
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg); }
        .grid-stack > .grid-stack-item > .ui-resizable-se {
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg); }
        .grid-stack > .grid-stack-item > .ui-resizable-nw {
            cursor: nw-resize;
            width: 20px;
            height: 20px;
            left: 10px;
            top: 0; }
        .grid-stack > .grid-stack-item > .ui-resizable-n {
            cursor: n-resize;
            height: 10px;
            top: 0;
            left: 25px;
            right: 25px; }
        .grid-stack > .grid-stack-item > .ui-resizable-ne {
            cursor: ne-resize;
            width: 20px;
            height: 20px;
            right: 10px;
            top: 0; }
        .grid-stack > .grid-stack-item > .ui-resizable-e {
            cursor: e-resize;
            width: 10px;
            right: 10px;
            top: 15px;
            bottom: 15px; }
        .grid-stack > .grid-stack-item > .ui-resizable-se {
            cursor: se-resize;
            width: 20px;
            height: 20px;
            right: 10px;
            bottom: 0; }
        .grid-stack > .grid-stack-item > .ui-resizable-s {
            cursor: s-resize;
            height: 10px;
            left: 25px;
            bottom: 0;
            right: 25px; }
        .grid-stack > .grid-stack-item > .ui-resizable-sw {
            cursor: sw-resize;
            width: 20px;
            height: 20px;
            left: 10px;
            bottom: 0; }
        .grid-stack > .grid-stack-item > .ui-resizable-w {
            cursor: w-resize;
            width: 10px;
            left: 10px;
            top: 15px;
            bottom: 15px; }
        .grid-stack > .grid-stack-item.ui-draggable-dragging > .ui-resizable-handle {
            display: none !important; }
        .grid-stack > .grid-stack-item[data-gs-width='1'] {
            width: 8.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-x='1'] {
            left: 8.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='1'] {
            min-width: 8.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='1'] {
            max-width: 8.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-width='2'] {
            width: 16.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-x='2'] {
            left: 16.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='2'] {
            min-width: 16.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='2'] {
            max-width: 16.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-width='3'] {
            width: 25%; }
        .grid-stack > .grid-stack-item[data-gs-x='3'] {
            left: 25%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='3'] {
            min-width: 25%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='3'] {
            max-width: 25%; }
        .grid-stack > .grid-stack-item[data-gs-width='4'] {
            width: 33.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-x='4'] {
            left: 33.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='4'] {
            min-width: 33.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='4'] {
            max-width: 33.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-width='5'] {
            width: 41.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-x='5'] {
            left: 41.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='5'] {
            min-width: 41.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='5'] {
            max-width: 41.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-width='6'] {
            width: 50%; }
        .grid-stack > .grid-stack-item[data-gs-x='6'] {
            left: 50%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='6'] {
            min-width: 50%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='6'] {
            max-width: 50%; }
        .grid-stack > .grid-stack-item[data-gs-width='7'] {
            width: 58.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-x='7'] {
            left: 58.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='7'] {
            min-width: 58.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='7'] {
            max-width: 58.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-width='8'] {
            width: 66.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-x='8'] {
            left: 66.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='8'] {
            min-width: 66.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='8'] {
            max-width: 66.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-width='9'] {
            width: 75%; }
        .grid-stack > .grid-stack-item[data-gs-x='9'] {
            left: 75%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='9'] {
            min-width: 75%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='9'] {
            max-width: 75%; }
        .grid-stack > .grid-stack-item[data-gs-width='10'] {
            width: 83.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-x='10'] {
            left: 83.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='10'] {
            min-width: 83.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='10'] {
            max-width: 83.3333333333%; }
        .grid-stack > .grid-stack-item[data-gs-width='11'] {
            width: 91.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-x='11'] {
            left: 91.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='11'] {
            min-width: 91.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='11'] {
            max-width: 91.6666666667%; }
        .grid-stack > .grid-stack-item[data-gs-width='12'] {
            width: 100%; }
        .grid-stack > .grid-stack-item[data-gs-x='12'] {
            left: 100%; }
        .grid-stack > .grid-stack-item[data-gs-min-width='12'] {
            min-width: 100%; }
        .grid-stack > .grid-stack-item[data-gs-max-width='12'] {
            max-width: 100%; }
        .grid-stack.grid-stack-animate,
        .grid-stack.grid-stack-animate .grid-stack-item {
            transition: left 0.3s, top 0.3s, height 0.3s, width 0.3s; }
        .grid-stack.grid-stack-animate .grid-stack-item.ui-draggable-dragging,
        .grid-stack.grid-stack-animate .grid-stack-item.ui-resizable-resizing,
        .grid-stack.grid-stack-animate .grid-stack-item.grid-stack-placeholder {
            transition: left 0s, top 0s, height 0s, width 0s; }
        .treegrid-indent {width:16px; height: 16px; display: inline-block; position: relative;}

        .treegrid-expander {width:16px; height: 16px; display: inline-block; position: relative; cursor: pointer;}

        .treegrid-expander-expanded{background-image: url(/images/vendor/jquery-treegrid/collapse.png?2ecde1c93da79055aa976858931e5587); }
        .treegrid-expander-collapsed{background-image: url(/images/vendor/jquery-treegrid/expand.png?24433bff45a957a671bb0290b659f6c9);}
        .select2-container{box-sizing:border-box;display:inline-block;margin:0;position:relative;vertical-align:middle}.select2-container .select2-selection--single{box-sizing:border-box;cursor:pointer;display:block;height:28px;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-user-select:none}.select2-container .select2-selection--single .select2-selection__rendered{display:block;padding-left:8px;padding-right:20px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.select2-container .select2-selection--single .select2-selection__clear{position:relative}.select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered{padding-right:8px;padding-left:20px}.select2-container .select2-selection--multiple{box-sizing:border-box;cursor:pointer;display:block;min-height:32px;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-user-select:none}.select2-container .select2-selection--multiple .select2-selection__rendered{display:inline-block;overflow:hidden;padding-left:8px;text-overflow:ellipsis;white-space:nowrap}.select2-container .select2-search--inline{float:left}.select2-container .select2-search--inline .select2-search__field{box-sizing:border-box;border:none;font-size:100%;margin-top:5px;padding:0}.select2-container .select2-search--inline .select2-search__field::-webkit-search-cancel-button{-webkit-appearance:none}.select2-dropdown{background-color:white;border:1px solid #aaa;border-radius:4px;box-sizing:border-box;display:block;position:absolute;left:-100000px;width:100%;z-index:1051}.select2-results{display:block}.select2-results__options{list-style:none;margin:0;padding:0}.select2-results__option{padding:6px;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-user-select:none}.select2-results__option[aria-selected]{cursor:pointer}.select2-container--open .select2-dropdown{left:0}.select2-container--open .select2-dropdown--above{border-bottom:none;border-bottom-left-radius:0;border-bottom-right-radius:0}.select2-container--open .select2-dropdown--below{border-top:none;border-top-left-radius:0;border-top-right-radius:0}.select2-search--dropdown{display:block;padding:4px}.select2-search--dropdown .select2-search__field{padding:4px;width:100%;box-sizing:border-box}.select2-search--dropdown .select2-search__field::-webkit-search-cancel-button{-webkit-appearance:none}.select2-search--dropdown.select2-search--hide{display:none}.select2-close-mask{border:0;margin:0;padding:0;display:block;position:fixed;left:0;top:0;min-height:100%;min-width:100%;height:auto;width:auto;opacity:0;z-index:99;background-color:#fff;filter:alpha(opacity=0)}.select2-hidden-accessible{border:0 !important;clip:rect(0 0 0 0) !important;-webkit-clip-path:inset(50%) !important;clip-path:inset(50%) !important;height:1px !important;overflow:hidden !important;padding:0 !important;position:absolute !important;width:1px !important;white-space:nowrap !important}.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:28px}.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}.select2-container--default .select2-selection--single .select2-selection__arrow{height:26px;position:absolute;top:1px;right:1px;width:20px}.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}.select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear{float:left}.select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow{left:1px;right:auto}.select2-container--default.select2-container--disabled .select2-selection--single{background-color:#eee;cursor:default}.select2-container--default.select2-container--disabled .select2-selection--single .select2-selection__clear{display:none}.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b{border-color:transparent transparent #888 transparent;border-width:0 4px 5px 4px}.select2-container--default .select2-selection--multiple{background-color:white;border:1px solid #aaa;border-radius:4px;cursor:text}.select2-container--default .select2-selection--multiple .select2-selection__rendered{box-sizing:border-box;list-style:none;margin:0;padding:0 5px;width:100%}.select2-container--default .select2-selection--multiple .select2-selection__rendered li{list-style:none}.select2-container--default .select2-selection--multiple .select2-selection__clear{cursor:pointer;float:right;font-weight:bold;margin-top:5px;margin-right:10px;padding:1px}.select2-container--default .select2-selection--multiple .select2-selection__choice{background-color:#e4e4e4;border:1px solid #aaa;border-radius:4px;cursor:default;float:left;margin-right:5px;margin-top:5px;padding:0 5px}.select2-container--default .select2-selection--multiple .select2-selection__choice__remove{color:#999;cursor:pointer;display:inline-block;font-weight:bold;margin-right:2px}.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{color:#333}.select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__choice,.select2-container--default[dir="rtl"] .select2-selection--multiple .select2-search--inline{float:right}.select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__choice{margin-left:5px;margin-right:auto}.select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__choice__remove{margin-left:2px;margin-right:auto}.select2-container--default.select2-container--focus .select2-selection--multiple{border:solid black 1px;outline:0}.select2-container--default.select2-container--disabled .select2-selection--multiple{background-color:#eee;cursor:default}.select2-container--default.select2-container--disabled .select2-selection__choice__remove{display:none}.select2-container--default.select2-container--open.select2-container--above .select2-selection--single,.select2-container--default.select2-container--open.select2-container--above .select2-selection--multiple{border-top-left-radius:0;border-top-right-radius:0}.select2-container--default.select2-container--open.select2-container--below .select2-selection--single,.select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple{border-bottom-left-radius:0;border-bottom-right-radius:0}.select2-container--default .select2-search--dropdown .select2-search__field{border:1px solid #aaa}.select2-container--default .select2-search--inline .select2-search__field{background:transparent;border:none;outline:0;box-shadow:none;-webkit-appearance:textfield}.select2-container--default .select2-results>.select2-results__options{max-height:200px;overflow-y:auto}.select2-container--default .select2-results__option[role=group]{padding:0}.select2-container--default .select2-results__option[aria-disabled=true]{color:#999}.select2-container--default .select2-results__option[aria-selected=true]{background-color:#ddd}.select2-container--default .select2-results__option .select2-results__option{padding-left:1em}.select2-container--default .select2-results__option .select2-results__option .select2-results__group{padding-left:0}.select2-container--default .select2-results__option .select2-results__option .select2-results__option{margin-left:-1em;padding-left:2em}.select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option{margin-left:-2em;padding-left:3em}.select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option{margin-left:-3em;padding-left:4em}.select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option{margin-left:-4em;padding-left:5em}.select2-container--default .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option .select2-results__option{margin-left:-5em;padding-left:6em}.select2-container--default .select2-results__option--highlighted[aria-selected]{background-color:#5897fb;color:white}.select2-container--default .select2-results__group{cursor:default;display:block;padding:6px}.select2-container--classic .select2-selection--single{background-color:#f7f7f7;border:1px solid #aaa;border-radius:4px;outline:0;background-image:linear-gradient(to bottom, #fff 50%, #eee 100%);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFFFF', endColorstr='#FFEEEEEE', GradientType=0)}.select2-container--classic .select2-selection--single:focus{border:1px solid #5897fb}.select2-container--classic .select2-selection--single .select2-selection__rendered{color:#444;line-height:28px}.select2-container--classic .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold;margin-right:10px}.select2-container--classic .select2-selection--single .select2-selection__placeholder{color:#999}.select2-container--classic .select2-selection--single .select2-selection__arrow{background-color:#ddd;border:none;border-left:1px solid #aaa;border-top-right-radius:4px;border-bottom-right-radius:4px;height:26px;position:absolute;top:1px;right:1px;width:20px;background-image:linear-gradient(to bottom, #eee 50%, #ccc 100%);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFEEEEEE', endColorstr='#FFCCCCCC', GradientType=0)}.select2-container--classic .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}.select2-container--classic[dir="rtl"] .select2-selection--single .select2-selection__clear{float:left}.select2-container--classic[dir="rtl"] .select2-selection--single .select2-selection__arrow{border:none;border-right:1px solid #aaa;border-radius:0;border-top-left-radius:4px;border-bottom-left-radius:4px;left:1px;right:auto}.select2-container--classic.select2-container--open .select2-selection--single{border:1px solid #5897fb}.select2-container--classic.select2-container--open .select2-selection--single .select2-selection__arrow{background:transparent;border:none}.select2-container--classic.select2-container--open .select2-selection--single .select2-selection__arrow b{border-color:transparent transparent #888 transparent;border-width:0 4px 5px 4px}.select2-container--classic.select2-container--open.select2-container--above .select2-selection--single{border-top:none;border-top-left-radius:0;border-top-right-radius:0;background-image:linear-gradient(to bottom, #fff 0%, #eee 50%);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFFFF', endColorstr='#FFEEEEEE', GradientType=0)}.select2-container--classic.select2-container--open.select2-container--below .select2-selection--single{border-bottom:none;border-bottom-left-radius:0;border-bottom-right-radius:0;background-image:linear-gradient(to bottom, #eee 50%, #fff 100%);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFEEEEEE', endColorstr='#FFFFFFFF', GradientType=0)}.select2-container--classic .select2-selection--multiple{background-color:white;border:1px solid #aaa;border-radius:4px;cursor:text;outline:0}.select2-container--classic .select2-selection--multiple:focus{border:1px solid #5897fb}.select2-container--classic .select2-selection--multiple .select2-selection__rendered{list-style:none;margin:0;padding:0 5px}.select2-container--classic .select2-selection--multiple .select2-selection__clear{display:none}.select2-container--classic .select2-selection--multiple .select2-selection__choice{background-color:#e4e4e4;border:1px solid #aaa;border-radius:4px;cursor:default;float:left;margin-right:5px;margin-top:5px;padding:0 5px}.select2-container--classic .select2-selection--multiple .select2-selection__choice__remove{color:#888;cursor:pointer;display:inline-block;font-weight:bold;margin-right:2px}.select2-container--classic .select2-selection--multiple .select2-selection__choice__remove:hover{color:#555}.select2-container--classic[dir="rtl"] .select2-selection--multiple .select2-selection__choice{float:right;margin-left:5px;margin-right:auto}.select2-container--classic[dir="rtl"] .select2-selection--multiple .select2-selection__choice__remove{margin-left:2px;margin-right:auto}.select2-container--classic.select2-container--open .select2-selection--multiple{border:1px solid #5897fb}.select2-container--classic.select2-container--open.select2-container--above .select2-selection--multiple{border-top:none;border-top-left-radius:0;border-top-right-radius:0}.select2-container--classic.select2-container--open.select2-container--below .select2-selection--multiple{border-bottom:none;border-bottom-left-radius:0;border-bottom-right-radius:0}.select2-container--classic .select2-search--dropdown .select2-search__field{border:1px solid #aaa;outline:0}.select2-container--classic .select2-search--inline .select2-search__field{outline:0;box-shadow:none}.select2-container--classic .select2-dropdown{background-color:#fff;border:1px solid transparent}.select2-container--classic .select2-dropdown--above{border-bottom:none}.select2-container--classic .select2-dropdown--below{border-top:none}.select2-container--classic .select2-results>.select2-results__options{max-height:200px;overflow-y:auto}.select2-container--classic .select2-results__option[role=group]{padding:0}.select2-container--classic .select2-results__option[aria-disabled=true]{color:grey}.select2-container--classic .select2-results__option--highlighted[aria-selected]{background-color:#3875d7;color:#fff}.select2-container--classic .select2-results__group{cursor:default;display:block;padding:6px}.select2-container--classic.select2-container--open .select2-dropdown{border-color:#5897fb}
        /* override some bootstrap styles screwing up the timelines css */

        .vis [class*="span"] {
            min-height: 0;
            width: auto;
        }

        .vis-timeline {
            /*
  -webkit-transition: height .4s ease-in-out;
  transition:         height .4s ease-in-out;
  */
        }

        .vis-panel {
            /*
  -webkit-transition: height .4s ease-in-out, top .4s ease-in-out;
  transition:         height .4s ease-in-out, top .4s ease-in-out;
  */
        }

        .vis-axis {
            /*
  -webkit-transition: top .4s ease-in-out;
  transition:         top .4s ease-in-out;
  */
        }

        /* TODO: get animation working nicely

.vis-item {
  -webkit-transition: top .4s ease-in-out;
  transition:         top .4s ease-in-out;
}

.vis-item.line {
  -webkit-transition: height .4s ease-in-out, top .4s ease-in-out;
  transition:         height .4s ease-in-out, top .4s ease-in-out;
}
/**/
        .vis-current-time {
            background-color: #FF7F6E;
            width: 2px;
            z-index: 1;
            pointer-events: none;
        }

        .vis-rolling-mode-btn {
            height: 40px;
            width: 40px;
            position: absolute;
            top: 7px;
            right: 20px;
            border-radius: 50%;
            font-size: 28px;
            cursor: pointer;
            opacity: 0.8;
            color: white;
            font-weight: bold;
            text-align: center;
            background: #3876c2;
        }
        .vis-rolling-mode-btn:before {
            content: "\26F6";
        }

        .vis-rolling-mode-btn:hover {
            opacity: 1;
        }
        .vis-panel {
            position: absolute;

            padding: 0;
            margin: 0;

            box-sizing: border-box;
        }

        .vis-panel.vis-center,
        .vis-panel.vis-left,
        .vis-panel.vis-right,
        .vis-panel.vis-top,
        .vis-panel.vis-bottom {
            border: 1px #bfbfbf;
        }

        .vis-panel.vis-center,
        .vis-panel.vis-left,
        .vis-panel.vis-right {
            border-top-style: solid;
            border-bottom-style: solid;
            overflow: hidden;
        }

        .vis-left.vis-panel.vis-vertical-scroll, .vis-right.vis-panel.vis-vertical-scroll {
            height: 100%;
            overflow-x: hidden;
            overflow-y: scroll;
        }

        .vis-left.vis-panel.vis-vertical-scroll {
            direction: rtl;
        }

        .vis-left.vis-panel.vis-vertical-scroll .vis-content {
            direction: ltr;
        }

        .vis-right.vis-panel.vis-vertical-scroll {
            direction: ltr;
        }

        .vis-right.vis-panel.vis-vertical-scroll .vis-content {
            direction: rtl;
        }

        .vis-panel.vis-center,
        .vis-panel.vis-top,
        .vis-panel.vis-bottom {
            border-left-style: solid;
            border-right-style: solid;
        }

        .vis-background {
            overflow: hidden;
        }

        .vis-panel > .vis-content {
            position: relative;
        }

        .vis-panel .vis-shadow {
            position: absolute;
            width: 100%;
            height: 1px;
            box-shadow: 0 0 10px rgba(0,0,0,0.8);
            /* TODO: find a nice way to ensure vis-shadows are drawn on top of items
  z-index: 1;
  */
        }

        .vis-panel .vis-shadow.vis-top {
            top: -1px;
            left: 0;
        }

        .vis-panel .vis-shadow.vis-bottom {
            bottom: -1px;
            left: 0;
        }

        .vis-timeline {
            position: relative;
            border: 1px solid #bfbfbf;
            overflow: hidden;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .vis-loading-screen {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }
        .vis-graph-group0 {
            fill:#4f81bd;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #4f81bd;
        }

        .vis-graph-group1 {
            fill:#f79646;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #f79646;
        }

        .vis-graph-group2 {
            fill: #8c51cf;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #8c51cf;
        }

        .vis-graph-group3 {
            fill: #75c841;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #75c841;
        }

        .vis-graph-group4 {
            fill: #ff0100;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #ff0100;
        }

        .vis-graph-group5 {
            fill: #37d8e6;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #37d8e6;
        }

        .vis-graph-group6 {
            fill: #042662;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #042662;
        }

        .vis-graph-group7 {
            fill:#00ff26;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #00ff26;
        }

        .vis-graph-group8 {
            fill:#ff00ff;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #ff00ff;
        }

        .vis-graph-group9 {
            fill: #8f3938;
            fill-opacity:0;
            stroke-width:2px;
            stroke: #8f3938;
        }

        .vis-timeline .vis-fill {
            fill-opacity:0.1;
            stroke: none;
        }


        .vis-timeline .vis-bar {
            fill-opacity:0.5;
            stroke-width:1px;
        }

        .vis-timeline .vis-point {
            stroke-width:2px;
            fill-opacity:1.0;
        }


        .vis-timeline .vis-legend-background {
            stroke-width:1px;
            fill-opacity:0.9;
            fill: #ffffff;
            stroke: #c2c2c2;
        }


        .vis-timeline .vis-outline {
            stroke-width:1px;
            fill-opacity:1;
            fill: #ffffff;
            stroke: #e5e5e5;
        }

        .vis-timeline .vis-icon-fill {
            fill-opacity:0.3;
            stroke: none;
        }

        .vis-custom-time {
            background-color: #6E94FF;
            width: 2px;
            cursor: move;
            z-index: 1;
        }

        .vis-custom-time > .vis-custom-time-marker {
            background-color: inherit;
            color: white;
            font-size: 12px;
            white-space: nowrap;
            padding: 3px 5px;
            top: 0px;
            cursor: initial;
            z-index: inherit;
        }

        .vis-panel.vis-background.vis-horizontal .vis-grid.vis-horizontal {
            position: absolute;
            width: 100%;
            height: 0;
            border-bottom: 1px solid;
        }

        .vis-panel.vis-background.vis-horizontal .vis-grid.vis-minor {
            border-color: #e5e5e5;
        }

        .vis-panel.vis-background.vis-horizontal .vis-grid.vis-major {
            border-color: #bfbfbf;
        }


        .vis-data-axis .vis-y-axis.vis-major {
            width: 100%;
            position: absolute;
            color: #4d4d4d;
            white-space: nowrap;
        }

        .vis-data-axis .vis-y-axis.vis-major.vis-measure {
            padding: 0;
            margin: 0;
            border: 0;
            visibility: hidden;
            width: auto;
        }


        .vis-data-axis .vis-y-axis.vis-minor {
            position: absolute;
            width: 100%;
            color: #bebebe;
            white-space: nowrap;
        }

        .vis-data-axis .vis-y-axis.vis-minor.vis-measure {
            padding: 0;
            margin: 0;
            border: 0;
            visibility: hidden;
            width: auto;
        }

        .vis-data-axis .vis-y-axis.vis-title {
            position: absolute;
            color: #4d4d4d;
            white-space: nowrap;
            bottom: 20px;
            text-align: center;
        }

        .vis-data-axis .vis-y-axis.vis-title.vis-measure {
            padding: 0;
            margin: 0;
            visibility: hidden;
            width: auto;
        }

        .vis-data-axis .vis-y-axis.vis-title.vis-left {
            bottom: 0;
            -webkit-transform-origin: left top;
            transform-origin: left bottom;
            -webkit-transform: rotate(-90deg);
            transform: rotate(-90deg);
        }

        .vis-data-axis .vis-y-axis.vis-title.vis-right {
            bottom: 0;
            -webkit-transform-origin: right bottom;
            transform-origin: right bottom;
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
        }

        .vis-legend {
            background-color: rgba(247, 252, 255, 0.65);
            padding: 5px;
            border: 1px solid #b3b3b3;
            box-shadow: 2px 2px 10px rgba(154, 154, 154, 0.55);
        }

        .vis-legend-text {
            /*font-size: 10px;*/
            white-space: nowrap;
            display: inline-block
        }

        .vis-labelset {
            position: relative;

            overflow: hidden;

            box-sizing: border-box;
        }

        .vis-labelset .vis-label {
            position: relative;
            left: 0;
            top: 0;
            width: 100%;
            color: #4d4d4d;

            box-sizing: border-box;
        }

        .vis-labelset .vis-label {
            border-bottom: 1px solid #bfbfbf;
        }

        .vis-labelset .vis-label.draggable {
            cursor: pointer;
        }

        .vis-group-is-dragging {
            background: rgba(0, 0, 0, .1);
        }

        .vis-labelset .vis-label:last-child {
            border-bottom: none;
        }

        .vis-labelset .vis-label .vis-inner {
            display: inline-block;
            padding: 5px;
        }

        .vis-labelset .vis-label .vis-inner.vis-hidden {
            padding: 0;
        }


        .vis-itemset {
            position: relative;
            padding: 0;
            margin: 0;

            box-sizing: border-box;
        }

        .vis-itemset .vis-background,
        .vis-itemset .vis-foreground {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .vis-axis {
            position: absolute;
            width: 100%;
            height: 0;
            left: 0;
            z-index: 1;
        }

        .vis-foreground .vis-group {
            position: relative;
            box-sizing: border-box;
            border-bottom: 1px solid #bfbfbf;
        }

        .vis-foreground .vis-group:last-child {
            border-bottom: none;
        }

        .vis-nesting-group {
            cursor: pointer;
        }

        .vis-label.vis-nested-group.vis-group-level-unknown-but-gte1 {
            background: #f5f5f5;
        }
        .vis-label.vis-nested-group.vis-group-level-0 {
            background-color: #ffffff;
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-0 .vis-inner {
            padding-left: 0;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-0 .vis-inner {
            padding-right: 0;
        }
        .vis-label.vis-nested-group.vis-group-level-1 {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-1 .vis-inner {
            padding-left: 15px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-1 .vis-inner {
            padding-right: 15px;
        }
        .vis-label.vis-nested-group.vis-group-level-2 {
            background-color: rgba(0, 0, 0, 0.1);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-2 .vis-inner {
            padding-left: 30px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-2 .vis-inner {
            padding-right: 30px;
        }
        .vis-label.vis-nested-group.vis-group-level-3 {
            background-color: rgba(0, 0, 0, 0.15);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-3 .vis-inner {
            padding-left: 45px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-3 .vis-inner {
            padding-right: 45px;
        }
        .vis-label.vis-nested-group.vis-group-level-4 {
            background-color: rgba(0, 0, 0, 0.2);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-4 .vis-inner {
            padding-left: 60px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-4 .vis-inner {
            padding-right: 60px;
        }
        .vis-label.vis-nested-group.vis-group-level-5 {
            background-color: rgba(0, 0, 0, 0.25);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-5 .vis-inner {
            padding-left: 75px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-5 .vis-inner {
            padding-right: 75px;
        }
        .vis-label.vis-nested-group.vis-group-level-6 {
            background-color: rgba(0, 0, 0, 0.3);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-6 .vis-inner {
            padding-left: 90px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-6 .vis-inner {
            padding-right: 90px;
        }
        .vis-label.vis-nested-group.vis-group-level-7 {
            background-color: rgba(0, 0, 0, 0.35);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-7 .vis-inner {
            padding-left: 105px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-7 .vis-inner {
            padding-right: 105px;
        }
        .vis-label.vis-nested-group.vis-group-level-8 {
            background-color: rgba(0, 0, 0, 0.4);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-8 .vis-inner {
            padding-left: 120px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-8 .vis-inner {
            padding-right: 120px;
        }
        .vis-label.vis-nested-group.vis-group-level-9 {
            background-color: rgba(0, 0, 0, 0.45);
        }
        .vis-ltr .vis-label.vis-nested-group.vis-group-level-9 .vis-inner {
            padding-left: 135px;
        }
        .vis-rtl .vis-label.vis-nested-group.vis-group-level-9 .vis-inner {
            padding-right: 135px;
        }
        /* default takes over beginning with level-10 (thats why we add .vis-nested-group
  to the selectors above, to have higher specifity than these rules for the defaults) */
        .vis-label.vis-nested-group {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .vis-ltr .vis-label.vis-nested-group .vis-inner {
            padding-left: 150px;
        }
        .vis-rtl .vis-label.vis-nested-group .vis-inner {
            padding-right: 150px;
        }

        .vis-group-level-unknown-but-gte1 {
            border: 1px solid red;
        }

        /* expanded/collapsed indicators */
        .vis-label.vis-nesting-group:before,
        .vis-label.vis-nesting-group:before {
            display: inline-block;
            width: 15px;
        }
        .vis-label.vis-nesting-group.expanded:before {
            content: "\25BC";
        }
        .vis-label.vis-nesting-group.collapsed:before {
            content: "\25B6";
        }
        .vis-rtl .vis-label.vis-nesting-group.collapsed:before {
            content: "\25C0";
        }
        /* compensate missing expanded/collapsed indicator, but only at levels > 0 */
        .vis-ltr .vis-label:not(.vis-nesting-group):not(.vis-group-level-0) {
            padding-left: 15px;
        }
        .vis-rtl .vis-label:not(.vis-nesting-group):not(.vis-group-level-0) {
            padding-right: 15px;
        }

        .vis-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
        }
        .vis-time-axis {
            position: relative;
            overflow: hidden;
        }

        .vis-time-axis.vis-foreground {
            top: 0;
            left: 0;
            width: 100%;
        }

        .vis-time-axis.vis-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .vis-time-axis .vis-text {
            position: absolute;
            color: #4d4d4d;
            padding: 3px;
            overflow: hidden;
            box-sizing: border-box;

            white-space: nowrap;
        }

        .vis-time-axis .vis-text.vis-measure {
            position: absolute;
            padding-left: 0;
            padding-right: 0;
            margin-left: 0;
            margin-right: 0;
            visibility: hidden;
        }

        .vis-time-axis .vis-grid.vis-vertical {
            position: absolute;
            border-left: 1px solid;
        }

        .vis-time-axis .vis-grid.vis-vertical-rtl {
            position: absolute;
            border-right: 1px solid;
        }

        .vis-time-axis .vis-grid.vis-minor {
            border-color: #e5e5e5;
        }

        .vis-time-axis .vis-grid.vis-major {
            border-color: #bfbfbf;
        }


        .vis-item {
            position: absolute;
            color: #1A1A1A;
            border-color: #97B0F8;
            border-width: 1px;
            background-color: #D5DDF6;
            display: inline-block;
            z-index: 1;
            /*overflow: hidden;*/
        }

        .vis-item.vis-selected {
            border-color: #FFC200;
            background-color: #FFF785;

            /* z-index must be higher than the z-index of custom time bar and current time bar */
            z-index: 2;
        }

        .vis-editable.vis-selected {
            cursor: move;
        }

        .vis-item.vis-point.vis-selected {
            background-color: #FFF785;
        }

        .vis-item.vis-box {
            text-align: center;
            border-style: solid;
            border-radius: 2px;
        }

        .vis-item.vis-point {
            background: none;
        }

        .vis-item.vis-dot {
            position: absolute;
            padding: 0;
            border-width: 4px;
            border-style: solid;
            border-radius: 4px;
        }

        .vis-item.vis-range {
            border-style: solid;
            border-radius: 2px;
            box-sizing: border-box;
        }

        .vis-item.vis-background {
            border: none;
            background-color: rgba(213, 221, 246, 0.4);
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        .vis-item .vis-item-overflow {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .vis-item-visible-frame {
            white-space: nowrap;
        }

        .vis-item.vis-range .vis-item-content {
            position: relative;
            display: inline-block;
        }

        .vis-item.vis-background .vis-item-content {
            position: absolute;
            display: inline-block;
        }

        .vis-item.vis-line {
            padding: 0;
            position: absolute;
            width: 0;
            border-left-width: 1px;
            border-left-style: solid;
        }

        .vis-item .vis-item-content {
            white-space: nowrap;
            box-sizing: border-box;
            padding: 5px;
        }

        .vis-item .vis-onUpdateTime-tooltip {
            position: absolute;
            background: #4f81bd;
            color: white;
            width: 200px;
            text-align: center;
            white-space: nowrap;
            padding: 5px;
            border-radius: 1px;
            transition: 0.4s;
            -o-transition: 0.4s;
            -moz-transition: 0.4s;
            -webkit-transition: 0.4s;
        }

        .vis-item .vis-delete, .vis-item .vis-delete-rtl {
            position: absolute;
            top: 0px;
            width: 24px;
            height: 24px;
            box-sizing: border-box;
            padding: 0px 5px;
            cursor: pointer;
            transition: background 0.2s linear;
        }

        .vis-item .vis-delete {
            right: -24px;
        }

        .vis-item .vis-delete-rtl {
            left: -24px;
        }

        .vis-item .vis-delete:after, .vis-item .vis-delete-rtl:after {
            content: "\D7"; /* MULTIPLICATION SIGN */
            color: red;
            font-family: arial, sans-serif;
            font-size: 22px;
            font-weight: bold;
            transition: color 0.2s linear;
        }

        .vis-item .vis-delete:hover, .vis-item .vis-delete-rtl:hover {
            background: red;
        }

        .vis-item .vis-delete:hover:after, .vis-item .vis-delete-rtl:hover:after {
            color: white;
        }

        .vis-item .vis-drag-center {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0px;
            cursor: move;
        }

        .vis-item.vis-range .vis-drag-left {
            position: absolute;
            width: 24px;
            max-width: 20%;
            min-width: 2px;
            height: 100%;
            top: 0;
            left: -4px;

            cursor: w-resize;
        }

        .vis-item.vis-range .vis-drag-right {
            position: absolute;
            width: 24px;
            max-width: 20%;
            min-width: 2px;
            height: 100%;
            top: 0;
            right: -4px;

            cursor: e-resize;
        }

        .vis-range.vis-item.vis-readonly .vis-drag-left,
        .vis-range.vis-item.vis-readonly .vis-drag-right {
            cursor: auto;
        }

        .vis-item.vis-cluster {
            vertical-align: center;
            text-align: center;
            border-style: solid;
            border-radius: 2px;
        }

        .vis-item.vis-cluster-line {
            padding: 0;
            position: absolute;
            width: 0;
            border-left-width: 1px;
            border-left-style: solid;
        }

        .vis-item.vis-cluster-dot {
            position: absolute;
            padding: 0;
            border-width: 4px;
            border-style: solid;
            border-radius: 4px;
        }
        .vis .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            /* Must be displayed above for example selected Timeline items */
            z-index: 10;
        }

        .vis-active {
            box-shadow: 0 0 10px #86d5f8;
        }

        div.vis-configuration {
            position:relative;
            display:block;
            float:left;
            font-size:12px;
        }

        div.vis-configuration-wrapper {
            display:block;
            width:700px;
        }

        div.vis-configuration-wrapper::after {
            clear: both;
            content: "";
            display: block;
        }

        div.vis-configuration.vis-config-option-container{
            display:block;
            width:495px;
            background-color: #ffffff;
            border:2px solid #f7f8fa;
            border-radius:4px;
            margin-top:20px;
            left:10px;
            padding-left:5px;
        }

        div.vis-configuration.vis-config-button{
            display:block;
            width:495px;
            height:25px;
            vertical-align: middle;
            line-height:25px;
            background-color: #f7f8fa;
            border:2px solid #ceced0;
            border-radius:4px;
            margin-top:20px;
            left:10px;
            padding-left:5px;
            cursor: pointer;
            margin-bottom:30px;
        }

        div.vis-configuration.vis-config-button.hover{
            background-color: #4588e6;
            border:2px solid #214373;
            color:#ffffff;
        }

        div.vis-configuration.vis-config-item{
            display:block;
            float:left;
            width:495px;
            height:25px;
            vertical-align: middle;
            line-height:25px;
        }


        div.vis-configuration.vis-config-item.vis-config-s2{
            left:10px;
            background-color: #f7f8fa;
            padding-left:5px;
            border-radius:3px;
        }
        div.vis-configuration.vis-config-item.vis-config-s3{
            left:20px;
            background-color: #e4e9f0;
            padding-left:5px;
            border-radius:3px;
        }
        div.vis-configuration.vis-config-item.vis-config-s4{
            left:30px;
            background-color: #cfd8e6;
            padding-left:5px;
            border-radius:3px;
        }

        div.vis-configuration.vis-config-header{
            font-size:18px;
            font-weight: bold;
        }

        div.vis-configuration.vis-config-label{
            width:120px;
            height:25px;
            line-height: 25px;
        }

        div.vis-configuration.vis-config-label.vis-config-s3{
            width:110px;
        }
        div.vis-configuration.vis-config-label.vis-config-s4{
            width:100px;
        }

        div.vis-configuration.vis-config-colorBlock{
            top:1px;
            width:30px;
            height:19px;
            border:1px solid #444444;
            border-radius:2px;
            padding:0px;
            margin:0px;
            cursor:pointer;
        }

        input.vis-configuration.vis-config-checkbox {
            left:-5px;
        }


        input.vis-configuration.vis-config-rangeinput{
            position:relative;
            top:-5px;
            width:60px;
            /*height:13px;*/
            padding:1px;
            margin:0;
            pointer-events:none;
        }

        input.vis-configuration.vis-config-range{
            /*removes default webkit styles*/
            -webkit-appearance: none;

            /*fix for FF unable to apply focus style bug */
            border: 0px solid white;
            background-color:rgba(0,0,0,0);

            /*required for proper track sizing in FF*/
            width: 300px;
            height:20px;
        }
        input.vis-configuration.vis-config-range::-webkit-slider-runnable-track {
            width: 300px;
            height: 5px;
            background: #dedede; /* Old browsers */ /* FF3.6+ */ /* Chrome,Safari4+ */ /* Chrome10+,Safari5.1+ */ /* Opera 11.10+ */ /* IE10+ */
            background: linear-gradient(to bottom,  #dedede 0%,#c8c8c8 99%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#dedede', endColorstr='#c8c8c8',GradientType=0 ); /* IE6-9 */

            border: 1px solid #999999;
            box-shadow: #aaaaaa 0px 0px 3px 0px;
            border-radius: 3px;
        }
        input.vis-configuration.vis-config-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            border: 1px solid #14334b;
            height: 17px;
            width: 17px;
            border-radius: 50%;
            background: #3876c2; /* Old browsers */ /* FF3.6+ */ /* Chrome,Safari4+ */ /* Chrome10+,Safari5.1+ */ /* Opera 11.10+ */ /* IE10+ */
            background: linear-gradient(to bottom,  #3876c2 0%,#385380 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3876c2', endColorstr='#385380',GradientType=0 ); /* IE6-9 */
            box-shadow: #111927 0px 0px 1px 0px;
            margin-top: -7px;
        }
        input.vis-configuration.vis-config-range:focus {
            outline: none;
        }
        input.vis-configuration.vis-config-range:focus::-webkit-slider-runnable-track {
            background: #9d9d9d; /* Old browsers */ /* FF3.6+ */ /* Chrome,Safari4+ */ /* Chrome10+,Safari5.1+ */ /* Opera 11.10+ */ /* IE10+ */
            background: linear-gradient(to bottom,  #9d9d9d 0%,#c8c8c8 99%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#9d9d9d', endColorstr='#c8c8c8',GradientType=0 ); /* IE6-9 */
        }

        input.vis-configuration.vis-config-range::-moz-range-track {
            width: 300px;
            height: 10px;
            background: #dedede; /* Old browsers */ /* FF3.6+ */ /* Chrome,Safari4+ */ /* Chrome10+,Safari5.1+ */ /* Opera 11.10+ */ /* IE10+ */
            background: linear-gradient(to bottom,  #dedede 0%,#c8c8c8 99%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#dedede', endColorstr='#c8c8c8',GradientType=0 ); /* IE6-9 */

            border: 1px solid #999999;
            box-shadow: #aaaaaa 0px 0px 3px 0px;
            border-radius: 3px;
        }
        input.vis-configuration.vis-config-range::-moz-range-thumb {
            border: none;
            height: 16px;
            width: 16px;

            border-radius: 50%;
            background:  #385380;
        }

        /*hide the outline behind the border*/
        input.vis-configuration.vis-config-range:-moz-focusring{
            outline: 1px solid white;
            outline-offset: -1px;
        }

        input.vis-configuration.vis-config-range::-ms-track {
            width: 300px;
            height: 5px;

            /*remove bg colour from the track, we'll use ms-fill-lower and ms-fill-upper instead */
            background: transparent;

            /*leave room for the larger thumb to overflow with a transparent border */
            border-color: transparent;
            border-width: 6px 0;

            /*remove default tick marks*/
            color: transparent;
        }
        input.vis-configuration.vis-config-range::-ms-fill-lower {
            background: #777;
            border-radius: 10px;
        }
        input.vis-configuration.vis-config-range::-ms-fill-upper {
            background: #ddd;
            border-radius: 10px;
        }
        input.vis-configuration.vis-config-range::-ms-thumb {
            border: none;
            height: 16px;
            width: 16px;
            border-radius: 50%;
            background:  #385380;
        }
        input.vis-configuration.vis-config-range:focus::-ms-fill-lower {
            background: #888;
        }
        input.vis-configuration.vis-config-range:focus::-ms-fill-upper {
            background: #ccc;
        }

        .vis-configuration-popup {
            position: absolute;
            background: rgba(57, 76, 89, 0.85);
            border: 2px solid #f2faff;
            line-height:30px;
            height:30px;
            width:150px;
            text-align:center;
            color: #ffffff;
            font-size:14px;
            border-radius:4px;
            transition: opacity 0.3s ease-in-out;
        }
        .vis-configuration-popup:after, .vis-configuration-popup:before {
            left: 100%;
            top: 50%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }

        .vis-configuration-popup:after {
            border-color: rgba(136, 183, 213, 0);
            border-left-color: rgba(57, 76, 89, 0.85);
            border-width: 8px;
            margin-top: -8px;
        }
        .vis-configuration-popup:before {
            border-color: rgba(194, 225, 245, 0);
            border-left-color: #f2faff;
            border-width: 12px;
            margin-top: -12px;
        }
        div.vis-tooltip {
            position: absolute;
            visibility: hidden;
            padding: 5px;
            white-space: nowrap;

            font-family: verdana;
            font-size:14px;
            color:#000000;
            background-color: #f5f4ed;
            border-radius: 3px;
            border: 1px solid #808074;

            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
            pointer-events: none;

            z-index: 5;
        }
        .daterangepicker {
            position: absolute;
            color: inherit;
            background-color: #fff;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 278px;
            max-width: none;
            padding: 0;
            margin-top: 7px;
            top: 100px;
            left: 20px;
            z-index: 3001;
            display: none;
            font-family: arial;
            font-size: 15px;
            line-height: 1em;
        }

        .daterangepicker:before, .daterangepicker:after {
            position: absolute;
            display: inline-block;
            border-bottom-color: rgba(0, 0, 0, 0.2);
            content: '';
        }

        .daterangepicker:before {
            top: -7px;
            border-right: 7px solid transparent;
            border-left: 7px solid transparent;
            border-bottom: 7px solid #ccc;
        }

        .daterangepicker:after {
            top: -6px;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #fff;
            border-left: 6px solid transparent;
        }

        .daterangepicker.opensleft:before {
            right: 9px;
        }

        .daterangepicker.opensleft:after {
            right: 10px;
        }

        .daterangepicker.openscenter:before {
            left: 0;
            right: 0;
            width: 0;
            margin-left: auto;
            margin-right: auto;
        }

        .daterangepicker.openscenter:after {
            left: 0;
            right: 0;
            width: 0;
            margin-left: auto;
            margin-right: auto;
        }

        .daterangepicker.opensright:before {
            left: 9px;
        }

        .daterangepicker.opensright:after {
            left: 10px;
        }

        .daterangepicker.drop-up {
            margin-top: -7px;
        }

        .daterangepicker.drop-up:before {
            top: initial;
            bottom: -7px;
            border-bottom: initial;
            border-top: 7px solid #ccc;
        }

        .daterangepicker.drop-up:after {
            top: initial;
            bottom: -6px;
            border-bottom: initial;
            border-top: 6px solid #fff;
        }

        .daterangepicker.single .daterangepicker .ranges, .daterangepicker.single .drp-calendar {
            float: none;
        }

        .daterangepicker.single .drp-selected {
            display: none;
        }

        .daterangepicker.show-calendar .drp-calendar {
            display: block;
        }

        .daterangepicker.show-calendar .drp-buttons {
            display: block;
        }

        .daterangepicker.auto-apply .drp-buttons {
            display: none;
        }

        .daterangepicker .drp-calendar {
            display: none;
            max-width: 270px;
        }

        .daterangepicker .drp-calendar.left {
            padding: 8px 0 8px 8px;
        }

        .daterangepicker .drp-calendar.right {
            padding: 8px;
        }

        .daterangepicker .drp-calendar.single .calendar-table {
            border: none;
        }

        .daterangepicker .calendar-table .next span, .daterangepicker .calendar-table .prev span {
            color: #fff;
            border: solid black;
            border-width: 0 2px 2px 0;
            border-radius: 0;
            display: inline-block;
            padding: 3px;
        }

        .daterangepicker .calendar-table .next span {
            transform: rotate(-45deg);
            -webkit-transform: rotate(-45deg);
        }

        .daterangepicker .calendar-table .prev span {
            transform: rotate(135deg);
            -webkit-transform: rotate(135deg);
        }

        .daterangepicker .calendar-table th, .daterangepicker .calendar-table td {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            min-width: 32px;
            width: 32px;
            height: 24px;
            line-height: 24px;
            font-size: 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            white-space: nowrap;
            cursor: pointer;
        }

        .daterangepicker .calendar-table {
            border: 1px solid #fff;
            border-radius: 4px;
            background-color: #fff;
        }

        .daterangepicker .calendar-table table {
            width: 100%;
            margin: 0;
            border-spacing: 0;
            border-collapse: collapse;
        }

        .daterangepicker td.available:hover, .daterangepicker th.available:hover {
            background-color: #eee;
            border-color: transparent;
            color: inherit;
        }

        .daterangepicker td.week, .daterangepicker th.week {
            font-size: 80%;
            color: #ccc;
        }

        .daterangepicker td.off, .daterangepicker td.off.in-range, .daterangepicker td.off.start-date, .daterangepicker td.off.end-date {
            background-color: #fff;
            border-color: transparent;
            color: #999;
        }

        .daterangepicker td.in-range {
            background-color: #ebf4f8;
            border-color: transparent;
            color: #000;
            border-radius: 0;
        }

        .daterangepicker td.start-date {
            border-radius: 4px 0 0 4px;
        }

        .daterangepicker td.end-date {
            border-radius: 0 4px 4px 0;
        }

        .daterangepicker td.start-date.end-date {
            border-radius: 4px;
        }

        .daterangepicker td.active, .daterangepicker td.active:hover {
            background-color: #357ebd;
            border-color: transparent;
            color: #fff;
        }

        .daterangepicker th.month {
            width: auto;
        }

        .daterangepicker td.disabled, .daterangepicker option.disabled {
            color: #999;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .daterangepicker select.monthselect, .daterangepicker select.yearselect {
            font-size: 12px;
            padding: 1px;
            height: auto;
            margin: 0;
            cursor: default;
        }

        .daterangepicker select.monthselect {
            margin-right: 2%;
            width: 56%;
        }

        .daterangepicker select.yearselect {
            width: 40%;
        }

        .daterangepicker select.hourselect, .daterangepicker select.minuteselect, .daterangepicker select.secondselect, .daterangepicker select.ampmselect {
            width: 50px;
            margin: 0 auto;
            background: #eee;
            border: 1px solid #eee;
            padding: 2px;
            outline: 0;
            font-size: 12px;
        }

        .daterangepicker .calendar-time {
            text-align: center;
            margin: 4px auto 0 auto;
            line-height: 30px;
            position: relative;
        }

        .daterangepicker .calendar-time select.disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .daterangepicker .drp-buttons {
            clear: both;
            text-align: right;
            padding: 8px;
            border-top: 1px solid #ddd;
            display: none;
            line-height: 12px;
            vertical-align: middle;
        }

        .daterangepicker .drp-selected {
            display: inline-block;
            font-size: 12px;
            padding-right: 8px;
        }

        .daterangepicker .drp-buttons .btn {
            margin-left: 8px;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 8px;
        }

        .daterangepicker.show-ranges.single.rtl .drp-calendar.left {
            border-right: 1px solid #ddd;
        }

        .daterangepicker.show-ranges.single.ltr .drp-calendar.left {
            border-left: 1px solid #ddd;
        }

        .daterangepicker.show-ranges.rtl .drp-calendar.right {
            border-right: 1px solid #ddd;
        }

        .daterangepicker.show-ranges.ltr .drp-calendar.left {
            border-left: 1px solid #ddd;
        }

        .daterangepicker .ranges {
            float: none;
            text-align: left;
            margin: 0;
        }

        .daterangepicker.show-calendar .ranges {
            margin-top: 8px;
        }

        .daterangepicker .ranges ul {
            list-style: none;
            margin: 0 auto;
            padding: 0;
            width: 100%;
        }

        .daterangepicker .ranges li {
            font-size: 12px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .daterangepicker .ranges li:hover {
            background-color: #eee;
        }

        .daterangepicker .ranges li.active {
            background-color: #08c;
            color: #fff;
        }

        /*  Larger Screen Styling */
        @media (min-width: 564px) {
            .daterangepicker {
                width: auto;
            }

            .daterangepicker .ranges ul {
                width: 140px;
            }

            .daterangepicker.single .ranges ul {
                width: 100%;
            }

            .daterangepicker.single .drp-calendar.left {
                clear: none;
            }

            .daterangepicker.single .ranges, .daterangepicker.single .drp-calendar {
                float: left;
            }

            .daterangepicker {
                direction: ltr;
                text-align: left;
            }

            .daterangepicker .drp-calendar.left {
                clear: left;
                margin-right: 0;
            }

            .daterangepicker .drp-calendar.left .calendar-table {
                border-right: none;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
            }

            .daterangepicker .drp-calendar.right {
                margin-left: 0;
            }

            .daterangepicker .drp-calendar.right .calendar-table {
                border-left: none;
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }

            .daterangepicker .drp-calendar.left .calendar-table {
                padding-right: 8px;
            }

            .daterangepicker .ranges, .daterangepicker .drp-calendar {
                float: left;
            }
        }

        @media (min-width: 730px) {
            .daterangepicker .ranges {
                width: auto;
            }

            .daterangepicker .ranges {
                float: left;
            }

            .daterangepicker.rtl .ranges {
                float: right;
            }

            .daterangepicker .drp-calendar.left {
                clear: none !important;
            }
        }
        /*! jQuery UI - v1.12.1 - 2016-09-14
* http://jqueryui.com
* Includes: core.css, accordion.css, autocomplete.css, menu.css, button.css, controlgroup.css, checkboxradio.css, datepicker.css, dialog.css, draggable.css, resizable.css, progressbar.css, selectable.css, selectmenu.css, slider.css, sortable.css, spinner.css, tabs.css, tooltip.css, theme.css
* To view and modify this theme, visit http://jqueryui.com/themeroller/?bgShadowXPos=&bgOverlayXPos=&bgErrorXPos=&bgHighlightXPos=&bgContentXPos=&bgHeaderXPos=&bgActiveXPos=&bgHoverXPos=&bgDefaultXPos=&bgShadowYPos=&bgOverlayYPos=&bgErrorYPos=&bgHighlightYPos=&bgContentYPos=&bgHeaderYPos=&bgActiveYPos=&bgHoverYPos=&bgDefaultYPos=&bgShadowRepeat=&bgOverlayRepeat=&bgErrorRepeat=&bgHighlightRepeat=&bgContentRepeat=&bgHeaderRepeat=&bgActiveRepeat=&bgHoverRepeat=&bgDefaultRepeat=&iconsHover=url(%22images%2Fui-icons_555555_256x240.png%22)&iconsHighlight=url(%22images%2Fui-icons_777620_256x240.png%22)&iconsHeader=url(%22images%2Fui-icons_444444_256x240.png%22)&iconsError=url(%22images%2Fui-icons_cc0000_256x240.png%22)&iconsDefault=url(%22images%2Fui-icons_777777_256x240.png%22)&iconsContent=url(%22images%2Fui-icons_444444_256x240.png%22)&iconsActive=url(%22images%2Fui-icons_ffffff_256x240.png%22)&bgImgUrlShadow=&bgImgUrlOverlay=&bgImgUrlHover=&bgImgUrlHighlight=&bgImgUrlHeader=&bgImgUrlError=&bgImgUrlDefault=&bgImgUrlContent=&bgImgUrlActive=&opacityFilterShadow=Alpha(Opacity%3D30)&opacityFilterOverlay=Alpha(Opacity%3D30)&opacityShadowPerc=30&opacityOverlayPerc=30&iconColorHover=%23555555&iconColorHighlight=%23777620&iconColorHeader=%23444444&iconColorError=%23cc0000&iconColorDefault=%23777777&iconColorContent=%23444444&iconColorActive=%23ffffff&bgImgOpacityShadow=0&bgImgOpacityOverlay=0&bgImgOpacityError=95&bgImgOpacityHighlight=55&bgImgOpacityContent=75&bgImgOpacityHeader=75&bgImgOpacityActive=65&bgImgOpacityHover=75&bgImgOpacityDefault=75&bgTextureShadow=flat&bgTextureOverlay=flat&bgTextureError=flat&bgTextureHighlight=flat&bgTextureContent=flat&bgTextureHeader=flat&bgTextureActive=flat&bgTextureHover=flat&bgTextureDefault=flat&cornerRadius=3px&fwDefault=normal&ffDefault=Arial%2CHelvetica%2Csans-serif&fsDefault=1em&cornerRadiusShadow=8px&thicknessShadow=5px&offsetLeftShadow=0px&offsetTopShadow=0px&opacityShadow=.3&bgColorShadow=%23666666&opacityOverlay=.3&bgColorOverlay=%23aaaaaa&fcError=%235f3f3f&borderColorError=%23f1a899&bgColorError=%23fddfdf&fcHighlight=%23777620&borderColorHighlight=%23dad55e&bgColorHighlight=%23fffa90&fcContent=%23333333&borderColorContent=%23dddddd&bgColorContent=%23ffffff&fcHeader=%23333333&borderColorHeader=%23dddddd&bgColorHeader=%23e9e9e9&fcActive=%23ffffff&borderColorActive=%23003eff&bgColorActive=%23007fff&fcHover=%232b2b2b&borderColorHover=%23cccccc&bgColorHover=%23ededed&fcDefault=%23454545&borderColorDefault=%23c5c5c5&bgColorDefault=%23f6f6f6
* Copyright jQuery Foundation and other contributors; Licensed MIT */

        /* Layout helpers
----------------------------------*/
        .ui-helper-hidden {
            display: none;
        }
        .ui-helper-hidden-accessible {
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }
        .ui-helper-reset {
            margin: 0;
            padding: 0;
            border: 0;
            outline: 0;
            line-height: 1.3;
            text-decoration: none;
            font-size: 100%;
            list-style: none;
        }
        .ui-helper-clearfix:before,
        .ui-helper-clearfix:after {
            content: "";
            display: table;
            border-collapse: collapse;
        }
        .ui-helper-clearfix:after {
            clear: both;
        }
        .ui-helper-zfix {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: absolute;
            opacity: 0;
            filter:Alpha(Opacity=0); /* support: IE8 */
        }

        .ui-front {
            z-index: 100;
        }


        /* Interaction Cues
----------------------------------*/
        .ui-state-disabled {
            cursor: default !important;
            pointer-events: none;
        }


        /* Icons
----------------------------------*/
        .ui-icon {
            display: inline-block;
            vertical-align: middle;
            margin-top: -.25em;
            position: relative;
            text-indent: -99999px;
            overflow: hidden;
            background-repeat: no-repeat;
        }

        .ui-widget-icon-block {
            left: 50%;
            margin-left: -8px;
            display: block;
        }

        /* Misc visuals
----------------------------------*/

        /* Overlays */
        .ui-widget-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .ui-accordion .ui-accordion-header {
            display: block;
            cursor: pointer;
            position: relative;
            margin: 2px 0 0 0;
            padding: .5em .5em .5em .7em;
            font-size: 100%;
        }
        .ui-accordion .ui-accordion-content {
            padding: 1em 2.2em;
            border-top: 0;
            overflow: auto;
        }
        .ui-autocomplete {
            position: absolute;
            top: 0;
            left: 0;
            cursor: default;
        }
        .ui-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: block;
            outline: 0;
        }
        .ui-menu .ui-menu {
            position: absolute;
        }
        .ui-menu .ui-menu-item {
            margin: 0;
            cursor: pointer;
            /* support: IE10, see #8844 */
            list-style-image: url("data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");
        }
        .ui-menu .ui-menu-item-wrapper {
            position: relative;
            padding: 3px 1em 3px .4em;
        }
        .ui-menu .ui-menu-divider {
            margin: 5px 0;
            height: 0;
            font-size: 0;
            line-height: 0;
            border-width: 1px 0 0 0;
        }
        .ui-menu .ui-state-focus,
        .ui-menu .ui-state-active {
            margin: -1px;
        }

        /* icon support */
        .ui-menu-icons {
            position: relative;
        }
        .ui-menu-icons .ui-menu-item-wrapper {
            padding-left: 2em;
        }

        /* left-aligned */
        .ui-menu .ui-icon {
            position: absolute;
            top: 0;
            bottom: 0;
            left: .2em;
            margin: auto 0;
        }

        /* right-aligned */
        .ui-menu .ui-menu-icon {
            left: auto;
            right: 0;
        }
        .ui-button {
            padding: .4em 1em;
            display: inline-block;
            position: relative;
            line-height: normal;
            margin-right: .1em;
            cursor: pointer;
            vertical-align: middle;
            text-align: center;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;

            /* Support: IE <= 11 */
            overflow: visible;
        }

        .ui-button,
        .ui-button:link,
        .ui-button:visited,
        .ui-button:hover,
        .ui-button:active {
            text-decoration: none;
        }

        /* to make room for the icon, a width needs to be set here */
        .ui-button-icon-only {
            width: 2em;
            box-sizing: border-box;
            text-indent: -9999px;
            white-space: nowrap;
        }

        /* no icon support for input elements */
        input.ui-button.ui-button-icon-only {
            text-indent: 0;
        }

        /* button icon element(s) */
        .ui-button-icon-only .ui-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -8px;
            margin-left: -8px;
        }

        .ui-button.ui-icon-notext .ui-icon {
            padding: 0;
            width: 2.1em;
            height: 2.1em;
            text-indent: -9999px;
            white-space: nowrap;

        }

        input.ui-button.ui-icon-notext .ui-icon {
            width: auto;
            height: auto;
            text-indent: 0;
            white-space: normal;
            padding: .4em 1em;
        }

        /* workarounds */
        /* Support: Firefox 5 - 40 */
        input.ui-button::-moz-focus-inner,
        button.ui-button::-moz-focus-inner {
            border: 0;
            padding: 0;
        }
        .ui-controlgroup {
            vertical-align: middle;
            display: inline-block;
        }
        .ui-controlgroup > .ui-controlgroup-item {
            float: left;
            margin-left: 0;
            margin-right: 0;
        }
        .ui-controlgroup > .ui-controlgroup-item:focus,
        .ui-controlgroup > .ui-controlgroup-item.ui-visual-focus {
            z-index: 9999;
        }
        .ui-controlgroup-vertical > .ui-controlgroup-item {
            display: block;
            float: none;
            width: 100%;
            margin-top: 0;
            margin-bottom: 0;
            text-align: left;
        }
        .ui-controlgroup-vertical .ui-controlgroup-item {
            box-sizing: border-box;
        }
        .ui-controlgroup .ui-controlgroup-label {
            padding: .4em 1em;
        }
        .ui-controlgroup .ui-controlgroup-label span {
            font-size: 80%;
        }
        .ui-controlgroup-horizontal .ui-controlgroup-label + .ui-controlgroup-item {
            border-left: none;
        }
        .ui-controlgroup-vertical .ui-controlgroup-label + .ui-controlgroup-item {
            border-top: none;
        }
        .ui-controlgroup-horizontal .ui-controlgroup-label.ui-widget-content {
            border-right: none;
        }
        .ui-controlgroup-vertical .ui-controlgroup-label.ui-widget-content {
            border-bottom: none;
        }

        /* Spinner specific style fixes */
        .ui-controlgroup-vertical .ui-spinner-input {

            /* Support: IE8 only, Android < 4.4 only */
            width: 75%;
            width: calc( 100% - 2.4em );
        }
        .ui-controlgroup-vertical .ui-spinner .ui-spinner-up {
            border-top-style: solid;
        }

        .ui-checkboxradio-label .ui-icon-background {
            box-shadow: inset 1px 1px 1px #ccc;
            border-radius: .12em;
            border: none;
        }
        .ui-checkboxradio-radio-label .ui-icon-background {
            width: 16px;
            height: 16px;
            border-radius: 1em;
            overflow: visible;
            border: none;
        }
        .ui-checkboxradio-radio-label.ui-checkboxradio-checked .ui-icon,
        .ui-checkboxradio-radio-label.ui-checkboxradio-checked:hover .ui-icon {
            background-image: none;
            width: 8px;
            height: 8px;
            border-width: 4px;
            border-style: solid;
        }
        .ui-checkboxradio-disabled {
            pointer-events: none;
        }
        .ui-datepicker {
            width: 17em;
            padding: .2em .2em 0;
            display: none;
        }
        .ui-datepicker .ui-datepicker-header {
            position: relative;
            padding: .2em 0;
        }
        .ui-datepicker .ui-datepicker-prev,
        .ui-datepicker .ui-datepicker-next {
            position: absolute;
            top: 2px;
            width: 1.8em;
            height: 1.8em;
        }
        .ui-datepicker .ui-datepicker-prev-hover,
        .ui-datepicker .ui-datepicker-next-hover {
            top: 1px;
        }
        .ui-datepicker .ui-datepicker-prev {
            left: 2px;
        }
        .ui-datepicker .ui-datepicker-next {
            right: 2px;
        }
        .ui-datepicker .ui-datepicker-prev-hover {
            left: 1px;
        }
        .ui-datepicker .ui-datepicker-next-hover {
            right: 1px;
        }
        .ui-datepicker .ui-datepicker-prev span,
        .ui-datepicker .ui-datepicker-next span {
            display: block;
            position: absolute;
            left: 50%;
            margin-left: -8px;
            top: 50%;
            margin-top: -8px;
        }
        .ui-datepicker .ui-datepicker-title {
            margin: 0 2.3em;
            line-height: 1.8em;
            text-align: center;
        }
        .ui-datepicker .ui-datepicker-title select {
            font-size: 1em;
            margin: 1px 0;
        }
        .ui-datepicker select.ui-datepicker-month,
        .ui-datepicker select.ui-datepicker-year {
            width: 45%;
        }
        .ui-datepicker table {
            width: 100%;
            font-size: .9em;
            border-collapse: collapse;
            margin: 0 0 .4em;
        }
        .ui-datepicker th {
            padding: .7em .3em;
            text-align: center;
            font-weight: bold;
            border: 0;
        }
        .ui-datepicker td {
            border: 0;
            padding: 1px;
        }
        .ui-datepicker td span,
        .ui-datepicker td a {
            display: block;
            padding: .2em;
            text-align: right;
            text-decoration: none;
        }
        .ui-datepicker .ui-datepicker-buttonpane {
            background-image: none;
            margin: .7em 0 0 0;
            padding: 0 .2em;
            border-left: 0;
            border-right: 0;
            border-bottom: 0;
        }
        .ui-datepicker .ui-datepicker-buttonpane button {
            float: right;
            margin: .5em .2em .4em;
            cursor: pointer;
            padding: .2em .6em .3em .6em;
            width: auto;
            overflow: visible;
        }
        .ui-datepicker .ui-datepicker-buttonpane button.ui-datepicker-current {
            float: left;
        }

        /* with multiple calendars */
        .ui-datepicker.ui-datepicker-multi {
            width: auto;
        }
        .ui-datepicker-multi .ui-datepicker-group {
            float: left;
        }
        .ui-datepicker-multi .ui-datepicker-group table {
            width: 95%;
            margin: 0 auto .4em;
        }
        .ui-datepicker-multi-2 .ui-datepicker-group {
            width: 50%;
        }
        .ui-datepicker-multi-3 .ui-datepicker-group {
            width: 33.3%;
        }
        .ui-datepicker-multi-4 .ui-datepicker-group {
            width: 25%;
        }
        .ui-datepicker-multi .ui-datepicker-group-last .ui-datepicker-header,
        .ui-datepicker-multi .ui-datepicker-group-middle .ui-datepicker-header {
            border-left-width: 0;
        }
        .ui-datepicker-multi .ui-datepicker-buttonpane {
            clear: left;
        }
        .ui-datepicker-row-break {
            clear: both;
            width: 100%;
            font-size: 0;
        }

        /* RTL support */
        .ui-datepicker-rtl {
            direction: rtl;
        }
        .ui-datepicker-rtl .ui-datepicker-prev {
            right: 2px;
            left: auto;
        }
        .ui-datepicker-rtl .ui-datepicker-next {
            left: 2px;
            right: auto;
        }
        .ui-datepicker-rtl .ui-datepicker-prev:hover {
            right: 1px;
            left: auto;
        }
        .ui-datepicker-rtl .ui-datepicker-next:hover {
            left: 1px;
            right: auto;
        }
        .ui-datepicker-rtl .ui-datepicker-buttonpane {
            clear: right;
        }
        .ui-datepicker-rtl .ui-datepicker-buttonpane button {
            float: left;
        }
        .ui-datepicker-rtl .ui-datepicker-buttonpane button.ui-datepicker-current,
        .ui-datepicker-rtl .ui-datepicker-group {
            float: right;
        }
        .ui-datepicker-rtl .ui-datepicker-group-last .ui-datepicker-header,
        .ui-datepicker-rtl .ui-datepicker-group-middle .ui-datepicker-header {
            border-right-width: 0;
            border-left-width: 1px;
        }

        /* Icons */
        .ui-datepicker .ui-icon {
            display: block;
            text-indent: -99999px;
            overflow: hidden;
            background-repeat: no-repeat;
            left: .5em;
            top: .3em;
        }
        .ui-dialog {
            position: absolute;
            top: 0;
            left: 0;
            padding: .2em;
            outline: 0;
        }
        .ui-dialog .ui-dialog-titlebar {
            padding: .4em 1em;
            position: relative;
        }
        .ui-dialog .ui-dialog-title {
            float: left;
            margin: .1em 0;
            white-space: nowrap;
            width: 90%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ui-dialog .ui-dialog-titlebar-close {
            position: absolute;
            right: .3em;
            top: 50%;
            width: 20px;
            margin: -10px 0 0 0;
            padding: 1px;
            height: 20px;
        }
        .ui-dialog .ui-dialog-content {
            position: relative;
            border: 0;
            padding: .5em 1em;
            background: none;
            overflow: auto;
        }
        .ui-dialog .ui-dialog-buttonpane {
            text-align: left;
            border-width: 1px 0 0 0;
            background-image: none;
            margin-top: .5em;
            padding: .3em 1em .5em .4em;
        }
        .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
            float: right;
        }
        .ui-dialog .ui-dialog-buttonpane button {
            margin: .5em .4em .5em 0;
            cursor: pointer;
        }
        .ui-dialog .ui-resizable-n {
            height: 2px;
            top: 0;
        }
        .ui-dialog .ui-resizable-e {
            width: 2px;
            right: 0;
        }
        .ui-dialog .ui-resizable-s {
            height: 2px;
            bottom: 0;
        }
        .ui-dialog .ui-resizable-w {
            width: 2px;
            left: 0;
        }
        .ui-dialog .ui-resizable-se,
        .ui-dialog .ui-resizable-sw,
        .ui-dialog .ui-resizable-ne,
        .ui-dialog .ui-resizable-nw {
            width: 7px;
            height: 7px;
        }
        .ui-dialog .ui-resizable-se {
            right: 0;
            bottom: 0;
        }
        .ui-dialog .ui-resizable-sw {
            left: 0;
            bottom: 0;
        }
        .ui-dialog .ui-resizable-ne {
            right: 0;
            top: 0;
        }
        .ui-dialog .ui-resizable-nw {
            left: 0;
            top: 0;
        }
        .ui-draggable .ui-dialog-titlebar {
            cursor: move;
        }
        .ui-draggable-handle {
            touch-action: none;
        }
        .ui-resizable {
            position: relative;
        }
        .ui-resizable-handle {
            position: absolute;
            font-size: 0.1px;
            display: block;
            touch-action: none;
        }
        .ui-resizable-disabled .ui-resizable-handle,
        .ui-resizable-autohide .ui-resizable-handle {
            display: none;
        }
        .ui-resizable-n {
            cursor: n-resize;
            height: 7px;
            width: 100%;
            top: -5px;
            left: 0;
        }
        .ui-resizable-s {
            cursor: s-resize;
            height: 7px;
            width: 100%;
            bottom: -5px;
            left: 0;
        }
        .ui-resizable-e {
            cursor: e-resize;
            width: 7px;
            right: -5px;
            top: 0;
            height: 100%;
        }
        .ui-resizable-w {
            cursor: w-resize;
            width: 7px;
            left: -5px;
            top: 0;
            height: 100%;
        }
        .ui-resizable-se {
            cursor: se-resize;
            width: 12px;
            height: 12px;
            right: 1px;
            bottom: 1px;
        }
        .ui-resizable-sw {
            cursor: sw-resize;
            width: 9px;
            height: 9px;
            left: -5px;
            bottom: -5px;
        }
        .ui-resizable-nw {
            cursor: nw-resize;
            width: 9px;
            height: 9px;
            left: -5px;
            top: -5px;
        }
        .ui-resizable-ne {
            cursor: ne-resize;
            width: 9px;
            height: 9px;
            right: -5px;
            top: -5px;
        }
        .ui-progressbar {
            height: 2em;
            text-align: left;
            overflow: hidden;
        }
        .ui-progressbar .ui-progressbar-value {
            margin: -1px;
            height: 100%;
        }
        .ui-progressbar .ui-progressbar-overlay {
            background: url("data:image/gif;base64,R0lGODlhKAAoAIABAAAAAP///yH/C05FVFNDQVBFMi4wAwEAAAAh+QQJAQABACwAAAAAKAAoAAACkYwNqXrdC52DS06a7MFZI+4FHBCKoDeWKXqymPqGqxvJrXZbMx7Ttc+w9XgU2FB3lOyQRWET2IFGiU9m1frDVpxZZc6bfHwv4c1YXP6k1Vdy292Fb6UkuvFtXpvWSzA+HycXJHUXiGYIiMg2R6W459gnWGfHNdjIqDWVqemH2ekpObkpOlppWUqZiqr6edqqWQAAIfkECQEAAQAsAAAAACgAKAAAApSMgZnGfaqcg1E2uuzDmmHUBR8Qil95hiPKqWn3aqtLsS18y7G1SzNeowWBENtQd+T1JktP05nzPTdJZlR6vUxNWWjV+vUWhWNkWFwxl9VpZRedYcflIOLafaa28XdsH/ynlcc1uPVDZxQIR0K25+cICCmoqCe5mGhZOfeYSUh5yJcJyrkZWWpaR8doJ2o4NYq62lAAACH5BAkBAAEALAAAAAAoACgAAAKVDI4Yy22ZnINRNqosw0Bv7i1gyHUkFj7oSaWlu3ovC8GxNso5fluz3qLVhBVeT/Lz7ZTHyxL5dDalQWPVOsQWtRnuwXaFTj9jVVh8pma9JjZ4zYSj5ZOyma7uuolffh+IR5aW97cHuBUXKGKXlKjn+DiHWMcYJah4N0lYCMlJOXipGRr5qdgoSTrqWSq6WFl2ypoaUAAAIfkECQEAAQAsAAAAACgAKAAAApaEb6HLgd/iO7FNWtcFWe+ufODGjRfoiJ2akShbueb0wtI50zm02pbvwfWEMWBQ1zKGlLIhskiEPm9R6vRXxV4ZzWT2yHOGpWMyorblKlNp8HmHEb/lCXjcW7bmtXP8Xt229OVWR1fod2eWqNfHuMjXCPkIGNileOiImVmCOEmoSfn3yXlJWmoHGhqp6ilYuWYpmTqKUgAAIfkECQEAAQAsAAAAACgAKAAAApiEH6kb58biQ3FNWtMFWW3eNVcojuFGfqnZqSebuS06w5V80/X02pKe8zFwP6EFWOT1lDFk8rGERh1TTNOocQ61Hm4Xm2VexUHpzjymViHrFbiELsefVrn6XKfnt2Q9G/+Xdie499XHd2g4h7ioOGhXGJboGAnXSBnoBwKYyfioubZJ2Hn0RuRZaflZOil56Zp6iioKSXpUAAAh+QQJAQABACwAAAAAKAAoAAACkoQRqRvnxuI7kU1a1UU5bd5tnSeOZXhmn5lWK3qNTWvRdQxP8qvaC+/yaYQzXO7BMvaUEmJRd3TsiMAgswmNYrSgZdYrTX6tSHGZO73ezuAw2uxuQ+BbeZfMxsexY35+/Qe4J1inV0g4x3WHuMhIl2jXOKT2Q+VU5fgoSUI52VfZyfkJGkha6jmY+aaYdirq+lQAACH5BAkBAAEALAAAAAAoACgAAAKWBIKpYe0L3YNKToqswUlvznigd4wiR4KhZrKt9Upqip61i9E3vMvxRdHlbEFiEXfk9YARYxOZZD6VQ2pUunBmtRXo1Lf8hMVVcNl8JafV38aM2/Fu5V16Bn63r6xt97j09+MXSFi4BniGFae3hzbH9+hYBzkpuUh5aZmHuanZOZgIuvbGiNeomCnaxxap2upaCZsq+1kAACH5BAkBAAEALAAAAAAoACgAAAKXjI8By5zf4kOxTVrXNVlv1X0d8IGZGKLnNpYtm8Lr9cqVeuOSvfOW79D9aDHizNhDJidFZhNydEahOaDH6nomtJjp1tutKoNWkvA6JqfRVLHU/QUfau9l2x7G54d1fl995xcIGAdXqMfBNadoYrhH+Mg2KBlpVpbluCiXmMnZ2Sh4GBqJ+ckIOqqJ6LmKSllZmsoq6wpQAAAh+QQJAQABACwAAAAAKAAoAAAClYx/oLvoxuJDkU1a1YUZbJ59nSd2ZXhWqbRa2/gF8Gu2DY3iqs7yrq+xBYEkYvFSM8aSSObE+ZgRl1BHFZNr7pRCavZ5BW2142hY3AN/zWtsmf12p9XxxFl2lpLn1rseztfXZjdIWIf2s5dItwjYKBgo9yg5pHgzJXTEeGlZuenpyPmpGQoKOWkYmSpaSnqKileI2FAAACH5BAkBAAEALAAAAAAoACgAAAKVjB+gu+jG4kORTVrVhRlsnn2dJ3ZleFaptFrb+CXmO9OozeL5VfP99HvAWhpiUdcwkpBH3825AwYdU8xTqlLGhtCosArKMpvfa1mMRae9VvWZfeB2XfPkeLmm18lUcBj+p5dnN8jXZ3YIGEhYuOUn45aoCDkp16hl5IjYJvjWKcnoGQpqyPlpOhr3aElaqrq56Bq7VAAAOw==");
            height: 100%;
            filter: alpha(opacity=25); /* support: IE8 */
            opacity: 0.25;
        }
        .ui-progressbar-indeterminate .ui-progressbar-value {
            background-image: none;
        }
        .ui-selectable {
            touch-action: none;
        }
        .ui-selectable-helper {
            position: absolute;
            z-index: 100;
            border: 1px dotted black;
        }
        .ui-selectmenu-menu {
            padding: 0;
            margin: 0;
            position: absolute;
            top: 0;
            left: 0;
            display: none;
        }
        .ui-selectmenu-menu .ui-menu {
            overflow: auto;
            overflow-x: hidden;
            padding-bottom: 1px;
        }
        .ui-selectmenu-menu .ui-menu .ui-selectmenu-optgroup {
            font-size: 1em;
            font-weight: bold;
            line-height: 1.5;
            padding: 2px 0.4em;
            margin: 0.5em 0 0 0;
            height: auto;
            border: 0;
        }
        .ui-selectmenu-open {
            display: block;
        }
        .ui-selectmenu-text {
            display: block;
            margin-right: 20px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ui-selectmenu-button.ui-button {
            text-align: left;
            white-space: nowrap;
            width: 14em;
        }
        .ui-selectmenu-icon.ui-icon {
            float: right;
            margin-top: 0;
        }
        .ui-slider {
            position: relative;
            text-align: left;
        }
        .ui-slider .ui-slider-handle {
            position: absolute;
            z-index: 2;
            width: 1.2em;
            height: 1.2em;
            cursor: default;
            touch-action: none;
        }
        .ui-slider .ui-slider-range {
            position: absolute;
            z-index: 1;
            font-size: .7em;
            display: block;
            border: 0;
            background-position: 0 0;
        }

        /* support: IE8 - See #6727 */
        .ui-slider.ui-state-disabled .ui-slider-handle,
        .ui-slider.ui-state-disabled .ui-slider-range {
            -webkit-filter: inherit;
            filter: inherit;
        }

        .ui-slider-horizontal {
            height: .8em;
        }
        .ui-slider-horizontal .ui-slider-handle {
            top: -.3em;
            margin-left: -.6em;
        }
        .ui-slider-horizontal .ui-slider-range {
            top: 0;
            height: 100%;
        }
        .ui-slider-horizontal .ui-slider-range-min {
            left: 0;
        }
        .ui-slider-horizontal .ui-slider-range-max {
            right: 0;
        }

        .ui-slider-vertical {
            width: .8em;
            height: 100px;
        }
        .ui-slider-vertical .ui-slider-handle {
            left: -.3em;
            margin-left: 0;
            margin-bottom: -.6em;
        }
        .ui-slider-vertical .ui-slider-range {
            left: 0;
            width: 100%;
        }
        .ui-slider-vertical .ui-slider-range-min {
            bottom: 0;
        }
        .ui-slider-vertical .ui-slider-range-max {
            top: 0;
        }
        .ui-sortable-handle {
            touch-action: none;
        }
        .ui-spinner {
            position: relative;
            display: inline-block;
            overflow: hidden;
            padding: 0;
            vertical-align: middle;
        }
        .ui-spinner-input {
            border: none;
            background: none;
            color: inherit;
            padding: .222em 0;
            margin: .2em 0;
            vertical-align: middle;
            margin-left: .4em;
            margin-right: 2em;
        }
        .ui-spinner-button {
            width: 1.6em;
            height: 50%;
            font-size: .5em;
            padding: 0;
            margin: 0;
            text-align: center;
            position: absolute;
            cursor: default;
            display: block;
            overflow: hidden;
            right: 0;
        }
        /* more specificity required here to override default borders */
        .ui-spinner a.ui-spinner-button {
            border-top-style: none;
            border-bottom-style: none;
            border-right-style: none;
        }
        .ui-spinner-up {
            top: 0;
        }
        .ui-spinner-down {
            bottom: 0;
        }
        .ui-tabs {
            position: relative;/* position: relative prevents IE scroll bug (element with position: relative inside container with overflow: auto appear as "fixed") */
            padding: .2em;
        }
        .ui-tabs .ui-tabs-nav {
            margin: 0;
            padding: .2em .2em 0;
        }
        .ui-tabs .ui-tabs-nav li {
            list-style: none;
            float: left;
            position: relative;
            top: 0;
            margin: 1px .2em 0 0;
            border-bottom-width: 0;
            padding: 0;
            white-space: nowrap;
        }
        .ui-tabs .ui-tabs-nav .ui-tabs-anchor {
            float: left;
            padding: .5em 1em;
            text-decoration: none;
        }
        .ui-tabs .ui-tabs-nav li.ui-tabs-active {
            margin-bottom: -1px;
            padding-bottom: 1px;
        }
        .ui-tabs .ui-tabs-nav li.ui-tabs-active .ui-tabs-anchor,
        .ui-tabs .ui-tabs-nav li.ui-state-disabled .ui-tabs-anchor,
        .ui-tabs .ui-tabs-nav li.ui-tabs-loading .ui-tabs-anchor {
            cursor: text;
        }
        .ui-tabs-collapsible .ui-tabs-nav li.ui-tabs-active .ui-tabs-anchor {
            cursor: pointer;
        }
        .ui-tabs .ui-tabs-panel {
            display: block;
            border-width: 0;
            padding: 1em 1.4em;
            background: none;
        }
        .ui-tooltip {
            padding: 8px;
            position: absolute;
            z-index: 9999;
            max-width: 300px;
        }
        body .ui-tooltip {
            border-width: 2px;
        }

        /* Component containers
----------------------------------*/
        .ui-widget {
            font-family: Arial,Helvetica,sans-serif;
            font-size: 1em;
        }
        .ui-widget .ui-widget {
            font-size: 1em;
        }
        .ui-widget input,
        .ui-widget select,
        .ui-widget textarea,
        .ui-widget button {
            font-family: Arial,Helvetica,sans-serif;
            font-size: 1em;
        }
        .ui-widget.ui-widget-content {
            border: 1px solid #c5c5c5;
        }
        .ui-widget-content {
            border: 1px solid #dddddd;
            background: #ffffff;
            color: #333333;
        }
        .ui-widget-content a {
            color: #333333;
        }
        .ui-widget-header {
            border: 1px solid #dddddd;
            background: #e9e9e9;
            color: #333333;
            font-weight: bold;
        }
        .ui-widget-header a {
            color: #333333;
        }

        /* Interaction states
----------------------------------*/
        .ui-state-default,
        .ui-widget-content .ui-state-default,
        .ui-widget-header .ui-state-default,
        .ui-button,

            /* We use html here because we need a greater specificity to make sure disabled
works properly when clicked or hovered */
        html .ui-button.ui-state-disabled:hover,
        html .ui-button.ui-state-disabled:active {
            border: 1px solid #c5c5c5;
            background: #f6f6f6;
            font-weight: normal;
            color: #454545;
        }
        .ui-state-default a,
        .ui-state-default a:link,
        .ui-state-default a:visited,
        a.ui-button,
        a:link.ui-button,
        a:visited.ui-button,
        .ui-button {
            color: #454545;
            text-decoration: none;
        }
        .ui-state-hover,
        .ui-widget-content .ui-state-hover,
        .ui-widget-header .ui-state-hover,
        .ui-state-focus,
        .ui-widget-content .ui-state-focus,
        .ui-widget-header .ui-state-focus,
        .ui-button:hover,
        .ui-button:focus {
            border: 1px solid #cccccc;
            background: #ededed;
            font-weight: normal;
            color: #2b2b2b;
        }
        .ui-state-hover a,
        .ui-state-hover a:hover,
        .ui-state-hover a:link,
        .ui-state-hover a:visited,
        .ui-state-focus a,
        .ui-state-focus a:hover,
        .ui-state-focus a:link,
        .ui-state-focus a:visited,
        a.ui-button:hover,
        a.ui-button:focus {
            color: #2b2b2b;
            text-decoration: none;
        }

        .ui-visual-focus {
            box-shadow: 0 0 3px 1px rgb(94, 158, 214);
        }
        .ui-state-active,
        .ui-widget-content .ui-state-active,
        .ui-widget-header .ui-state-active,
        a.ui-button:active,
        .ui-button:active,
        .ui-button.ui-state-active:hover {
            border: 1px solid #003eff;
            background: #007fff;
            font-weight: normal;
            color: #ffffff;
        }
        .ui-icon-background,
        .ui-state-active .ui-icon-background {
            border: #003eff;
            background-color: #ffffff;
        }
        .ui-state-active a,
        .ui-state-active a:link,
        .ui-state-active a:visited {
            color: #ffffff;
            text-decoration: none;
        }

        /* Interaction Cues
----------------------------------*/
        .ui-state-highlight,
        .ui-widget-content .ui-state-highlight,
        .ui-widget-header .ui-state-highlight {
            border: 1px solid #dad55e;
            background: #fffa90;
            color: #777620;
        }
        .ui-state-checked {
            border: 1px solid #dad55e;
            background: #fffa90;
        }
        .ui-state-highlight a,
        .ui-widget-content .ui-state-highlight a,
        .ui-widget-header .ui-state-highlight a {
            color: #777620;
        }
        .ui-state-error,
        .ui-widget-content .ui-state-error,
        .ui-widget-header .ui-state-error {
            border: 1px solid #f1a899;
            background: #fddfdf;
            color: #5f3f3f;
        }
        .ui-state-error a,
        .ui-widget-content .ui-state-error a,
        .ui-widget-header .ui-state-error a {
            color: #5f3f3f;
        }
        .ui-state-error-text,
        .ui-widget-content .ui-state-error-text,
        .ui-widget-header .ui-state-error-text {
            color: #5f3f3f;
        }
        .ui-priority-primary,
        .ui-widget-content .ui-priority-primary,
        .ui-widget-header .ui-priority-primary {
            font-weight: bold;
        }
        .ui-priority-secondary,
        .ui-widget-content .ui-priority-secondary,
        .ui-widget-header .ui-priority-secondary {
            opacity: .7;
            filter:Alpha(Opacity=70); /* support: IE8 */
            font-weight: normal;
        }
        .ui-state-disabled,
        .ui-widget-content .ui-state-disabled,
        .ui-widget-header .ui-state-disabled {
            opacity: .35;
            filter:Alpha(Opacity=35); /* support: IE8 */
            background-image: none;
        }
        .ui-state-disabled .ui-icon {
            filter:Alpha(Opacity=35); /* support: IE8 - See #6059 */
        }

        /* Icons
----------------------------------*/

        /* states and images */
        .ui-icon {
            width: 16px;
            height: 16px;
        }
        .ui-icon,
        .ui-widget-content .ui-icon {
            background-image: url(/images/vendor/jquery-ui-dist/ui-icons_444444_256x240.png?d10bc07005bb2d604f4905183690ac04);
        }
        .ui-widget-header .ui-icon {
            background-image: url(/images/vendor/jquery-ui-dist/ui-icons_444444_256x240.png?d10bc07005bb2d604f4905183690ac04);
        }
        .ui-state-hover .ui-icon,
        .ui-state-focus .ui-icon,
        .ui-button:hover .ui-icon,
        .ui-button:focus .ui-icon {
            background-image: url(/images/vendor/jquery-ui-dist/ui-icons_555555_256x240.png?00dd0ec0a16a1085e714c7906ff8fb06);
        }
        .ui-state-active .ui-icon,
        .ui-button:active .ui-icon {
            background-image: url(/images/vendor/jquery-ui-dist/ui-icons_ffffff_256x240.png?ea4ebe072be75fbbea002631916836de);
        }
        .ui-state-highlight .ui-icon,
        .ui-button .ui-state-highlight.ui-icon {
            background-image: url(/images/vendor/jquery-ui-dist/ui-icons_777620_256x240.png?4e7e3e142f3939883cd0a7e00cabdaef);
        }
        .ui-state-error .ui-icon,
        .ui-state-error-text .ui-icon {
            background-image: url(/images/vendor/jquery-ui-dist/ui-icons_cc0000_256x240.png?093a819138276b446611d1d2a45b98a2);
        }
        .ui-button .ui-icon {
            background-image: url(/images/vendor/jquery-ui-dist/ui-icons_777777_256x240.png?40bf25799e4fec8079c7775083de09df);
        }

        /* positioning */
        .ui-icon-blank { background-position: 16px 16px; }
        .ui-icon-caret-1-n { background-position: 0 0; }
        .ui-icon-caret-1-ne { background-position: -16px 0; }
        .ui-icon-caret-1-e { background-position: -32px 0; }
        .ui-icon-caret-1-se { background-position: -48px 0; }
        .ui-icon-caret-1-s { background-position: -65px 0; }
        .ui-icon-caret-1-sw { background-position: -80px 0; }
        .ui-icon-caret-1-w { background-position: -96px 0; }
        .ui-icon-caret-1-nw { background-position: -112px 0; }
        .ui-icon-caret-2-n-s { background-position: -128px 0; }
        .ui-icon-caret-2-e-w { background-position: -144px 0; }
        .ui-icon-triangle-1-n { background-position: 0 -16px; }
        .ui-icon-triangle-1-ne { background-position: -16px -16px; }
        .ui-icon-triangle-1-e { background-position: -32px -16px; }
        .ui-icon-triangle-1-se { background-position: -48px -16px; }
        .ui-icon-triangle-1-s { background-position: -65px -16px; }
        .ui-icon-triangle-1-sw { background-position: -80px -16px; }
        .ui-icon-triangle-1-w { background-position: -96px -16px; }
        .ui-icon-triangle-1-nw { background-position: -112px -16px; }
        .ui-icon-triangle-2-n-s { background-position: -128px -16px; }
        .ui-icon-triangle-2-e-w { background-position: -144px -16px; }
        .ui-icon-arrow-1-n { background-position: 0 -32px; }
        .ui-icon-arrow-1-ne { background-position: -16px -32px; }
        .ui-icon-arrow-1-e { background-position: -32px -32px; }
        .ui-icon-arrow-1-se { background-position: -48px -32px; }
        .ui-icon-arrow-1-s { background-position: -65px -32px; }
        .ui-icon-arrow-1-sw { background-position: -80px -32px; }
        .ui-icon-arrow-1-w { background-position: -96px -32px; }
        .ui-icon-arrow-1-nw { background-position: -112px -32px; }
        .ui-icon-arrow-2-n-s { background-position: -128px -32px; }
        .ui-icon-arrow-2-ne-sw { background-position: -144px -32px; }
        .ui-icon-arrow-2-e-w { background-position: -160px -32px; }
        .ui-icon-arrow-2-se-nw { background-position: -176px -32px; }
        .ui-icon-arrowstop-1-n { background-position: -192px -32px; }
        .ui-icon-arrowstop-1-e { background-position: -208px -32px; }
        .ui-icon-arrowstop-1-s { background-position: -224px -32px; }
        .ui-icon-arrowstop-1-w { background-position: -240px -32px; }
        .ui-icon-arrowthick-1-n { background-position: 1px -48px; }
        .ui-icon-arrowthick-1-ne { background-position: -16px -48px; }
        .ui-icon-arrowthick-1-e { background-position: -32px -48px; }
        .ui-icon-arrowthick-1-se { background-position: -48px -48px; }
        .ui-icon-arrowthick-1-s { background-position: -64px -48px; }
        .ui-icon-arrowthick-1-sw { background-position: -80px -48px; }
        .ui-icon-arrowthick-1-w { background-position: -96px -48px; }
        .ui-icon-arrowthick-1-nw { background-position: -112px -48px; }
        .ui-icon-arrowthick-2-n-s { background-position: -128px -48px; }
        .ui-icon-arrowthick-2-ne-sw { background-position: -144px -48px; }
        .ui-icon-arrowthick-2-e-w { background-position: -160px -48px; }
        .ui-icon-arrowthick-2-se-nw { background-position: -176px -48px; }
        .ui-icon-arrowthickstop-1-n { background-position: -192px -48px; }
        .ui-icon-arrowthickstop-1-e { background-position: -208px -48px; }
        .ui-icon-arrowthickstop-1-s { background-position: -224px -48px; }
        .ui-icon-arrowthickstop-1-w { background-position: -240px -48px; }
        .ui-icon-arrowreturnthick-1-w { background-position: 0 -64px; }
        .ui-icon-arrowreturnthick-1-n { background-position: -16px -64px; }
        .ui-icon-arrowreturnthick-1-e { background-position: -32px -64px; }
        .ui-icon-arrowreturnthick-1-s { background-position: -48px -64px; }
        .ui-icon-arrowreturn-1-w { background-position: -64px -64px; }
        .ui-icon-arrowreturn-1-n { background-position: -80px -64px; }
        .ui-icon-arrowreturn-1-e { background-position: -96px -64px; }
        .ui-icon-arrowreturn-1-s { background-position: -112px -64px; }
        .ui-icon-arrowrefresh-1-w { background-position: -128px -64px; }
        .ui-icon-arrowrefresh-1-n { background-position: -144px -64px; }
        .ui-icon-arrowrefresh-1-e { background-position: -160px -64px; }
        .ui-icon-arrowrefresh-1-s { background-position: -176px -64px; }
        .ui-icon-arrow-4 { background-position: 0 -80px; }
        .ui-icon-arrow-4-diag { background-position: -16px -80px; }
        .ui-icon-extlink { background-position: -32px -80px; }
        .ui-icon-newwin { background-position: -48px -80px; }
        .ui-icon-refresh { background-position: -64px -80px; }
        .ui-icon-shuffle { background-position: -80px -80px; }
        .ui-icon-transfer-e-w { background-position: -96px -80px; }
        .ui-icon-transferthick-e-w { background-position: -112px -80px; }
        .ui-icon-folder-collapsed { background-position: 0 -96px; }
        .ui-icon-folder-open { background-position: -16px -96px; }
        .ui-icon-document { background-position: -32px -96px; }
        .ui-icon-document-b { background-position: -48px -96px; }
        .ui-icon-note { background-position: -64px -96px; }
        .ui-icon-mail-closed { background-position: -80px -96px; }
        .ui-icon-mail-open { background-position: -96px -96px; }
        .ui-icon-suitcase { background-position: -112px -96px; }
        .ui-icon-comment { background-position: -128px -96px; }
        .ui-icon-person { background-position: -144px -96px; }
        .ui-icon-print { background-position: -160px -96px; }
        .ui-icon-trash { background-position: -176px -96px; }
        .ui-icon-locked { background-position: -192px -96px; }
        .ui-icon-unlocked { background-position: -208px -96px; }
        .ui-icon-bookmark { background-position: -224px -96px; }
        .ui-icon-tag { background-position: -240px -96px; }
        .ui-icon-home { background-position: 0 -112px; }
        .ui-icon-flag { background-position: -16px -112px; }
        .ui-icon-calendar { background-position: -32px -112px; }
        .ui-icon-cart { background-position: -48px -112px; }
        .ui-icon-pencil { background-position: -64px -112px; }
        .ui-icon-clock { background-position: -80px -112px; }
        .ui-icon-disk { background-position: -96px -112px; }
        .ui-icon-calculator { background-position: -112px -112px; }
        .ui-icon-zoomin { background-position: -128px -112px; }
        .ui-icon-zoomout { background-position: -144px -112px; }
        .ui-icon-search { background-position: -160px -112px; }
        .ui-icon-wrench { background-position: -176px -112px; }
        .ui-icon-gear { background-position: -192px -112px; }
        .ui-icon-heart { background-position: -208px -112px; }
        .ui-icon-star { background-position: -224px -112px; }
        .ui-icon-link { background-position: -240px -112px; }
        .ui-icon-cancel { background-position: 0 -128px; }
        .ui-icon-plus { background-position: -16px -128px; }
        .ui-icon-plusthick { background-position: -32px -128px; }
        .ui-icon-minus { background-position: -48px -128px; }
        .ui-icon-minusthick { background-position: -64px -128px; }
        .ui-icon-close { background-position: -80px -128px; }
        .ui-icon-closethick { background-position: -96px -128px; }
        .ui-icon-key { background-position: -112px -128px; }
        .ui-icon-lightbulb { background-position: -128px -128px; }
        .ui-icon-scissors { background-position: -144px -128px; }
        .ui-icon-clipboard { background-position: -160px -128px; }
        .ui-icon-copy { background-position: -176px -128px; }
        .ui-icon-contact { background-position: -192px -128px; }
        .ui-icon-image { background-position: -208px -128px; }
        .ui-icon-video { background-position: -224px -128px; }
        .ui-icon-script { background-position: -240px -128px; }
        .ui-icon-alert { background-position: 0 -144px; }
        .ui-icon-info { background-position: -16px -144px; }
        .ui-icon-notice { background-position: -32px -144px; }
        .ui-icon-help { background-position: -48px -144px; }
        .ui-icon-check { background-position: -64px -144px; }
        .ui-icon-bullet { background-position: -80px -144px; }
        .ui-icon-radio-on { background-position: -96px -144px; }
        .ui-icon-radio-off { background-position: -112px -144px; }
        .ui-icon-pin-w { background-position: -128px -144px; }
        .ui-icon-pin-s { background-position: -144px -144px; }
        .ui-icon-play { background-position: 0 -160px; }
        .ui-icon-pause { background-position: -16px -160px; }
        .ui-icon-seek-next { background-position: -32px -160px; }
        .ui-icon-seek-prev { background-position: -48px -160px; }
        .ui-icon-seek-end { background-position: -64px -160px; }
        .ui-icon-seek-start { background-position: -80px -160px; }
        /* ui-icon-seek-first is deprecated, use ui-icon-seek-start instead */
        .ui-icon-seek-first { background-position: -80px -160px; }
        .ui-icon-stop { background-position: -96px -160px; }
        .ui-icon-eject { background-position: -112px -160px; }
        .ui-icon-volume-off { background-position: -128px -160px; }
        .ui-icon-volume-on { background-position: -144px -160px; }
        .ui-icon-power { background-position: 0 -176px; }
        .ui-icon-signal-diag { background-position: -16px -176px; }
        .ui-icon-signal { background-position: -32px -176px; }
        .ui-icon-battery-0 { background-position: -48px -176px; }
        .ui-icon-battery-1 { background-position: -64px -176px; }
        .ui-icon-battery-2 { background-position: -80px -176px; }
        .ui-icon-battery-3 { background-position: -96px -176px; }
        .ui-icon-circle-plus { background-position: 0 -192px; }
        .ui-icon-circle-minus { background-position: -16px -192px; }
        .ui-icon-circle-close { background-position: -32px -192px; }
        .ui-icon-circle-triangle-e { background-position: -48px -192px; }
        .ui-icon-circle-triangle-s { background-position: -64px -192px; }
        .ui-icon-circle-triangle-w { background-position: -80px -192px; }
        .ui-icon-circle-triangle-n { background-position: -96px -192px; }
        .ui-icon-circle-arrow-e { background-position: -112px -192px; }
        .ui-icon-circle-arrow-s { background-position: -128px -192px; }
        .ui-icon-circle-arrow-w { background-position: -144px -192px; }
        .ui-icon-circle-arrow-n { background-position: -160px -192px; }
        .ui-icon-circle-zoomin { background-position: -176px -192px; }
        .ui-icon-circle-zoomout { background-position: -192px -192px; }
        .ui-icon-circle-check { background-position: -208px -192px; }
        .ui-icon-circlesmall-plus { background-position: 0 -208px; }
        .ui-icon-circlesmall-minus { background-position: -16px -208px; }
        .ui-icon-circlesmall-close { background-position: -32px -208px; }
        .ui-icon-squaresmall-plus { background-position: -48px -208px; }
        .ui-icon-squaresmall-minus { background-position: -64px -208px; }
        .ui-icon-squaresmall-close { background-position: -80px -208px; }
        .ui-icon-grip-dotted-vertical { background-position: 0 -224px; }
        .ui-icon-grip-dotted-horizontal { background-position: -16px -224px; }
        .ui-icon-grip-solid-vertical { background-position: -32px -224px; }
        .ui-icon-grip-solid-horizontal { background-position: -48px -224px; }
        .ui-icon-gripsmall-diagonal-se { background-position: -64px -224px; }
        .ui-icon-grip-diagonal-se { background-position: -80px -224px; }


        /* Misc visuals
----------------------------------*/

        /* Corner radius */
        .ui-corner-all,
        .ui-corner-top,
        .ui-corner-left,
        .ui-corner-tl {
            border-top-left-radius: 3px;
        }
        .ui-corner-all,
        .ui-corner-top,
        .ui-corner-right,
        .ui-corner-tr {
            border-top-right-radius: 3px;
        }
        .ui-corner-all,
        .ui-corner-bottom,
        .ui-corner-left,
        .ui-corner-bl {
            border-bottom-left-radius: 3px;
        }
        .ui-corner-all,
        .ui-corner-bottom,
        .ui-corner-right,
        .ui-corner-br {
            border-bottom-right-radius: 3px;
        }

        /* Overlays */
        .ui-widget-overlay {
            background: #aaaaaa;
            opacity: .003;
            filter: Alpha(Opacity=.3); /* support: IE8 */
        }
        .ui-widget-shadow {
            box-shadow: 0px 0px 5px #666666;
        }
        @charset "UTF-8";

        /*!
 * Bootstrap v4.4.1 (https://getbootstrap.com/)
 * Copyright 2011-2019 The Bootstrap Authors
 * Copyright 2011-2019 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

        :root {
            --blue: #3490dc;
            --indigo: #6574cd;
            --purple: #9561e2;
            --pink: #f66D9b;
            --red: #e3342f;
            --orange: #f6993f;
            --yellow: #ffed4a;
            --green: #38c172;
            --teal: #4dc0b5;
            --cyan: #6cb2eb;
            --white: #fff;
            --gray: #6c757d;
            --gray-dark: #343a40;
            --primary: #3490dc;
            --secondary: #6c757d;
            --success: #38c172;
            --info: #6cb2eb;
            --warning: #ffed4a;
            --danger: #e3342f;
            --light: #f8f9fa;
            --dark: #343a40;
            --breakpoint-xs: 0;
            --breakpoint-sm: 576px;
            --breakpoint-md: 768px;
            --breakpoint-lg: 992px;
            --breakpoint-xl: 1200px;
            --font-family-sans-serif: "Nunito", sans-serif;
            --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            font-family: sans-serif;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        article,
        aside,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        main,
        nav,
        section {
            display: block;
        }

        body {
            margin: 0;
            font-family: "Nunito", sans-serif;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #212529;
            text-align: left;
            background-color: #fff;
        }

        [tabindex="-1"]:focus:not(:focus-visible) {
            outline: 0 !important;
        }

        hr {
            box-sizing: content-box;
            height: 0;
            overflow: visible;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        abbr[title],
        abbr[data-original-title] {
            text-decoration: underline;
            -webkit-text-decoration: underline dotted;
            text-decoration: underline dotted;
            cursor: help;
            border-bottom: 0;
            -webkit-text-decoration-skip-ink: none;
            text-decoration-skip-ink: none;
        }

        address {
            margin-bottom: 1rem;
            font-style: normal;
            line-height: inherit;
        }

        ol,
        ul,
        dl {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        ol ol,
        ul ul,
        ol ul,
        ul ol {
            margin-bottom: 0;
        }

        dt {
            font-weight: 700;
        }

        dd {
            margin-bottom: 0.5rem;
            margin-left: 0;
        }

        blockquote {
            margin: 0 0 1rem;
        }

        b,
        strong {
            font-weight: bolder;
        }

        small {
            font-size: 80%;
        }

        sub,
        sup {
            position: relative;
            font-size: 75%;
            line-height: 0;
            vertical-align: baseline;
        }

        sub {
            bottom: -0.25em;
        }

        sup {
            top: -0.5em;
        }

        a {
            color: #3490dc;
            text-decoration: none;
            background-color: transparent;
        }

        a:hover {
            color: #1d68a7;
            text-decoration: underline;
        }

        a:not([href]) {
            color: inherit;
            text-decoration: none;
        }

        a:not([href]):hover {
            color: inherit;
            text-decoration: none;
        }

        pre,
        code,
        kbd,
        samp {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 1em;
        }

        pre {
            margin-top: 0;
            margin-bottom: 1rem;
            overflow: auto;
        }

        figure {
            margin: 0 0 1rem;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        svg {
            overflow: hidden;
            vertical-align: middle;
        }

        table {
            border-collapse: collapse;
        }

        caption {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            color: #6c757d;
            text-align: left;
            caption-side: bottom;
        }

        th {
            text-align: inherit;
        }

        label {
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        button {
            border-radius: 0;
        }

        button:focus {
            outline: 1px dotted;
            outline: 5px auto -webkit-focus-ring-color;
        }

        input,
        button,
        select,
        optgroup,
        textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        button,
        input {
            overflow: visible;
        }

        button,
        select {
            text-transform: none;
        }

        select {
            word-wrap: normal;
        }

        button,
        [type=button],
        [type=reset],
        [type=submit] {
            -webkit-appearance: button;
        }

        button:not(:disabled),
        [type=button]:not(:disabled),
        [type=reset]:not(:disabled),
        [type=submit]:not(:disabled) {
            cursor: pointer;
        }

        button::-moz-focus-inner,
        [type=button]::-moz-focus-inner,
        [type=reset]::-moz-focus-inner,
        [type=submit]::-moz-focus-inner {
            padding: 0;
            border-style: none;
        }

        input[type=radio],
        input[type=checkbox] {
            box-sizing: border-box;
            padding: 0;
        }

        input[type=date],
        input[type=time],
        input[type=datetime-local],
        input[type=month] {
            -webkit-appearance: listbox;
        }

        textarea {
            overflow: auto;
            resize: vertical;
        }

        fieldset {
            min-width: 0;
            padding: 0;
            margin: 0;
            border: 0;
        }

        legend {
            display: block;
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
            line-height: inherit;
            color: inherit;
            white-space: normal;
        }

        progress {
            vertical-align: baseline;
        }

        [type=number]::-webkit-inner-spin-button,
        [type=number]::-webkit-outer-spin-button {
            height: auto;
        }

        [type=search] {
            outline-offset: -2px;
            -webkit-appearance: none;
        }

        [type=search]::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        ::-webkit-file-upload-button {
            font: inherit;
            -webkit-appearance: button;
        }

        output {
            display: inline-block;
        }

        summary {
            display: list-item;
            cursor: pointer;
        }

        template {
            display: none;
        }

        [hidden] {
            display: none !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h1,
        .h1 {
            font-size: 2.25rem;
        }

        h2,
        .h2 {
            font-size: 1.8rem;
        }

        h3,
        .h3 {
            font-size: 1.575rem;
        }

        h4,
        .h4 {
            font-size: 1.35rem;
        }

        h5,
        .h5 {
            font-size: 1.125rem;
        }

        h6,
        .h6 {
            font-size: 0.9rem;
        }

        .lead {
            font-size: 1.125rem;
            font-weight: 300;
        }

        .display-1 {
            font-size: 6rem;
            font-weight: 300;
            line-height: 1.2;
        }

        .display-2 {
            font-size: 5.5rem;
            font-weight: 300;
            line-height: 1.2;
        }

        .display-3 {
            font-size: 4.5rem;
            font-weight: 300;
            line-height: 1.2;
        }

        .display-4 {
            font-size: 3.5rem;
            font-weight: 300;
            line-height: 1.2;
        }

        hr {
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        small,
        .small {
            font-size: 80%;
            font-weight: 400;
        }

        mark,
        .mark {
            padding: 0.2em;
            background-color: #fcf8e3;
        }

        .list-unstyled {
            padding-left: 0;
            list-style: none;
        }

        .list-inline {
            padding-left: 0;
            list-style: none;
        }

        .list-inline-item {
            display: inline-block;
        }

        .list-inline-item:not(:last-child) {
            margin-right: 0.5rem;
        }

        .initialism {
            font-size: 90%;
            text-transform: uppercase;
        }

        .blockquote {
            margin-bottom: 1rem;
            font-size: 1.125rem;
        }

        .blockquote-footer {
            display: block;
            font-size: 80%;
            color: #6c757d;
        }

        .blockquote-footer::before {
            content: "\2014\A0";
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .img-thumbnail {
            padding: 0.25rem;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            max-width: 100%;
            height: auto;
        }

        .figure {
            display: inline-block;
        }

        .figure-img {
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .figure-caption {
            font-size: 90%;
            color: #6c757d;
        }

        code {
            font-size: 87.5%;
            color: #f66D9b;
            word-wrap: break-word;
        }

        a > code {
            color: inherit;
        }

        kbd {
            padding: 0.2rem 0.4rem;
            font-size: 87.5%;
            color: #fff;
            background-color: #212529;
            border-radius: 0.2rem;
        }

        kbd kbd {
            padding: 0;
            font-size: 100%;
            font-weight: 700;
        }

        pre {
            display: block;
            font-size: 87.5%;
            color: #212529;
        }

        pre code {
            font-size: inherit;
            color: inherit;
            word-break: normal;
        }

        .pre-scrollable {
            max-height: 340px;
            overflow-y: scroll;
        }

        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        @media (min-width: 576px) {
            .container {
                max-width: 540px;
            }
        }

        @media (min-width: 768px) {
            .container {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .container {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }

        .container-fluid,
        .container-xl,
        .container-lg,
        .container-md,
        .container-sm {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        @media (min-width: 576px) {
            .container-sm,
            .container {
                max-width: 540px;
            }
        }

        @media (min-width: 768px) {
            .container-md,
            .container-sm,
            .container {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .container-lg,
            .container-md,
            .container-sm,
            .container {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .container-xl,
            .container-lg,
            .container-md,
            .container-sm,
            .container {
                max-width: 1140px;
            }
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .no-gutters > .col,
        .no-gutters > [class*=col-] {
            padding-right: 0;
            padding-left: 0;
        }

        .col-xl,
        .col-xl-auto,
        .col-xl-12,
        .col-xl-11,
        .col-xl-10,
        .col-xl-9,
        .col-xl-8,
        .col-xl-7,
        .col-xl-6,
        .col-xl-5,
        .col-xl-4,
        .col-xl-3,
        .col-xl-2,
        .col-xl-1,
        .col-lg,
        .col-lg-auto,
        .col-lg-12,
        .col-lg-11,
        .col-lg-10,
        .col-lg-9,
        .col-lg-8,
        .col-lg-7,
        .col-lg-6,
        .col-lg-5,
        .col-lg-4,
        .col-lg-3,
        .col-lg-2,
        .col-lg-1,
        .col-md,
        .col-md-auto,
        .col-md-12,
        .col-md-11,
        .col-md-10,
        .col-md-9,
        .col-md-8,
        .col-md-7,
        .col-md-6,
        .col-md-5,
        .col-md-4,
        .col-md-3,
        .col-md-2,
        .col-md-1,
        .col-sm,
        .col-sm-auto,
        .col-sm-12,
        .col-sm-11,
        .col-sm-10,
        .col-sm-9,
        .col-sm-8,
        .col-sm-7,
        .col-sm-6,
        .col-sm-5,
        .col-sm-4,
        .col-sm-3,
        .col-sm-2,
        .col-sm-1,
        .col,
        .col-auto,
        .col-12,
        .col-11,
        .col-10,
        .col-9,
        .col-8,
        .col-7,
        .col-6,
        .col-5,
        .col-4,
        .col-3,
        .col-2,
        .col-1 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col {
            flex-basis: 0;
            flex-grow: 1;
            max-width: 100%;
        }

        .row-cols-1 > * {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .row-cols-2 > * {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .row-cols-3 > * {
            flex: 0 0 33.3333333333%;
            max-width: 33.3333333333%;
        }

        .row-cols-4 > * {
            flex: 0 0 25%;
            max-width: 25%;
        }

        .row-cols-5 > * {
            flex: 0 0 20%;
            max-width: 20%;
        }

        .row-cols-6 > * {
            flex: 0 0 16.6666666667%;
            max-width: 16.6666666667%;
        }

        .col-auto {
            flex: 0 0 auto;
            width: auto;
            max-width: 100%;
        }

        .col-1 {
            flex: 0 0 8.3333333333%;
            max-width: 8.3333333333%;
        }

        .col-2 {
            flex: 0 0 16.6666666667%;
            max-width: 16.6666666667%;
        }

        .col-3 {
            flex: 0 0 25%;
            max-width: 25%;
        }

        .col-4 {
            flex: 0 0 33.3333333333%;
            max-width: 33.3333333333%;
        }

        .col-5 {
            flex: 0 0 41.6666666667%;
            max-width: 41.6666666667%;
        }

        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .col-7 {
            flex: 0 0 58.3333333333%;
            max-width: 58.3333333333%;
        }

        .col-8 {
            flex: 0 0 66.6666666667%;
            max-width: 66.6666666667%;
        }

        .col-9 {
            flex: 0 0 75%;
            max-width: 75%;
        }

        .col-10 {
            flex: 0 0 83.3333333333%;
            max-width: 83.3333333333%;
        }

        .col-11 {
            flex: 0 0 91.6666666667%;
            max-width: 91.6666666667%;
        }

        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .order-first {
            order: -1;
        }

        .order-last {
            order: 13;
        }

        .order-0 {
            order: 0;
        }

        .order-1 {
            order: 1;
        }

        .order-2 {
            order: 2;
        }

        .order-3 {
            order: 3;
        }

        .order-4 {
            order: 4;
        }

        .order-5 {
            order: 5;
        }

        .order-6 {
            order: 6;
        }

        .order-7 {
            order: 7;
        }

        .order-8 {
            order: 8;
        }

        .order-9 {
            order: 9;
        }

        .order-10 {
            order: 10;
        }

        .order-11 {
            order: 11;
        }

        .order-12 {
            order: 12;
        }

        .offset-1 {
            margin-left: 8.3333333333%;
        }

        .offset-2 {
            margin-left: 16.6666666667%;
        }

        .offset-3 {
            margin-left: 25%;
        }

        .offset-4 {
            margin-left: 33.3333333333%;
        }

        .offset-5 {
            margin-left: 41.6666666667%;
        }

        .offset-6 {
            margin-left: 50%;
        }

        .offset-7 {
            margin-left: 58.3333333333%;
        }

        .offset-8 {
            margin-left: 66.6666666667%;
        }

        .offset-9 {
            margin-left: 75%;
        }

        .offset-10 {
            margin-left: 83.3333333333%;
        }

        .offset-11 {
            margin-left: 91.6666666667%;
        }

        @media (min-width: 576px) {
            .col-sm {
                flex-basis: 0;
                flex-grow: 1;
                max-width: 100%;
            }

            .row-cols-sm-1 > * {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .row-cols-sm-2 > * {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .row-cols-sm-3 > * {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .row-cols-sm-4 > * {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .row-cols-sm-5 > * {
                flex: 0 0 20%;
                max-width: 20%;
            }

            .row-cols-sm-6 > * {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-sm-auto {
                flex: 0 0 auto;
                width: auto;
                max-width: 100%;
            }

            .col-sm-1 {
                flex: 0 0 8.3333333333%;
                max-width: 8.3333333333%;
            }

            .col-sm-2 {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-sm-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .col-sm-4 {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .col-sm-5 {
                flex: 0 0 41.6666666667%;
                max-width: 41.6666666667%;
            }

            .col-sm-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .col-sm-7 {
                flex: 0 0 58.3333333333%;
                max-width: 58.3333333333%;
            }

            .col-sm-8 {
                flex: 0 0 66.6666666667%;
                max-width: 66.6666666667%;
            }

            .col-sm-9 {
                flex: 0 0 75%;
                max-width: 75%;
            }

            .col-sm-10 {
                flex: 0 0 83.3333333333%;
                max-width: 83.3333333333%;
            }

            .col-sm-11 {
                flex: 0 0 91.6666666667%;
                max-width: 91.6666666667%;
            }

            .col-sm-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .order-sm-first {
                order: -1;
            }

            .order-sm-last {
                order: 13;
            }

            .order-sm-0 {
                order: 0;
            }

            .order-sm-1 {
                order: 1;
            }

            .order-sm-2 {
                order: 2;
            }

            .order-sm-3 {
                order: 3;
            }

            .order-sm-4 {
                order: 4;
            }

            .order-sm-5 {
                order: 5;
            }

            .order-sm-6 {
                order: 6;
            }

            .order-sm-7 {
                order: 7;
            }

            .order-sm-8 {
                order: 8;
            }

            .order-sm-9 {
                order: 9;
            }

            .order-sm-10 {
                order: 10;
            }

            .order-sm-11 {
                order: 11;
            }

            .order-sm-12 {
                order: 12;
            }

            .offset-sm-0 {
                margin-left: 0;
            }

            .offset-sm-1 {
                margin-left: 8.3333333333%;
            }

            .offset-sm-2 {
                margin-left: 16.6666666667%;
            }

            .offset-sm-3 {
                margin-left: 25%;
            }

            .offset-sm-4 {
                margin-left: 33.3333333333%;
            }

            .offset-sm-5 {
                margin-left: 41.6666666667%;
            }

            .offset-sm-6 {
                margin-left: 50%;
            }

            .offset-sm-7 {
                margin-left: 58.3333333333%;
            }

            .offset-sm-8 {
                margin-left: 66.6666666667%;
            }

            .offset-sm-9 {
                margin-left: 75%;
            }

            .offset-sm-10 {
                margin-left: 83.3333333333%;
            }

            .offset-sm-11 {
                margin-left: 91.6666666667%;
            }
        }

        @media (min-width: 768px) {
            .col-md {
                flex-basis: 0;
                flex-grow: 1;
                max-width: 100%;
            }

            .row-cols-md-1 > * {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .row-cols-md-2 > * {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .row-cols-md-3 > * {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .row-cols-md-4 > * {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .row-cols-md-5 > * {
                flex: 0 0 20%;
                max-width: 20%;
            }

            .row-cols-md-6 > * {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-md-auto {
                flex: 0 0 auto;
                width: auto;
                max-width: 100%;
            }

            .col-md-1 {
                flex: 0 0 8.3333333333%;
                max-width: 8.3333333333%;
            }

            .col-md-2 {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-md-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .col-md-4 {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .col-md-5 {
                flex: 0 0 41.6666666667%;
                max-width: 41.6666666667%;
            }

            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .col-md-7 {
                flex: 0 0 58.3333333333%;
                max-width: 58.3333333333%;
            }

            .col-md-8 {
                flex: 0 0 66.6666666667%;
                max-width: 66.6666666667%;
            }

            .col-md-9 {
                flex: 0 0 75%;
                max-width: 75%;
            }

            .col-md-10 {
                flex: 0 0 83.3333333333%;
                max-width: 83.3333333333%;
            }

            .col-md-11 {
                flex: 0 0 91.6666666667%;
                max-width: 91.6666666667%;
            }

            .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .order-md-first {
                order: -1;
            }

            .order-md-last {
                order: 13;
            }

            .order-md-0 {
                order: 0;
            }

            .order-md-1 {
                order: 1;
            }

            .order-md-2 {
                order: 2;
            }

            .order-md-3 {
                order: 3;
            }

            .order-md-4 {
                order: 4;
            }

            .order-md-5 {
                order: 5;
            }

            .order-md-6 {
                order: 6;
            }

            .order-md-7 {
                order: 7;
            }

            .order-md-8 {
                order: 8;
            }

            .order-md-9 {
                order: 9;
            }

            .order-md-10 {
                order: 10;
            }

            .order-md-11 {
                order: 11;
            }

            .order-md-12 {
                order: 12;
            }

            .offset-md-0 {
                margin-left: 0;
            }

            .offset-md-1 {
                margin-left: 8.3333333333%;
            }

            .offset-md-2 {
                margin-left: 16.6666666667%;
            }

            .offset-md-3 {
                margin-left: 25%;
            }

            .offset-md-4 {
                margin-left: 33.3333333333%;
            }

            .offset-md-5 {
                margin-left: 41.6666666667%;
            }

            .offset-md-6 {
                margin-left: 50%;
            }

            .offset-md-7 {
                margin-left: 58.3333333333%;
            }

            .offset-md-8 {
                margin-left: 66.6666666667%;
            }

            .offset-md-9 {
                margin-left: 75%;
            }

            .offset-md-10 {
                margin-left: 83.3333333333%;
            }

            .offset-md-11 {
                margin-left: 91.6666666667%;
            }
        }

        @media (min-width: 992px) {
            .col-lg {
                flex-basis: 0;
                flex-grow: 1;
                max-width: 100%;
            }

            .row-cols-lg-1 > * {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .row-cols-lg-2 > * {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .row-cols-lg-3 > * {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .row-cols-lg-4 > * {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .row-cols-lg-5 > * {
                flex: 0 0 20%;
                max-width: 20%;
            }

            .row-cols-lg-6 > * {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-lg-auto {
                flex: 0 0 auto;
                width: auto;
                max-width: 100%;
            }

            .col-lg-1 {
                flex: 0 0 8.3333333333%;
                max-width: 8.3333333333%;
            }

            .col-lg-2 {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-lg-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .col-lg-4 {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .col-lg-5 {
                flex: 0 0 41.6666666667%;
                max-width: 41.6666666667%;
            }

            .col-lg-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .col-lg-7 {
                flex: 0 0 58.3333333333%;
                max-width: 58.3333333333%;
            }

            .col-lg-8 {
                flex: 0 0 66.6666666667%;
                max-width: 66.6666666667%;
            }

            .col-lg-9 {
                flex: 0 0 75%;
                max-width: 75%;
            }

            .col-lg-10 {
                flex: 0 0 83.3333333333%;
                max-width: 83.3333333333%;
            }

            .col-lg-11 {
                flex: 0 0 91.6666666667%;
                max-width: 91.6666666667%;
            }

            .col-lg-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .order-lg-first {
                order: -1;
            }

            .order-lg-last {
                order: 13;
            }

            .order-lg-0 {
                order: 0;
            }

            .order-lg-1 {
                order: 1;
            }

            .order-lg-2 {
                order: 2;
            }

            .order-lg-3 {
                order: 3;
            }

            .order-lg-4 {
                order: 4;
            }

            .order-lg-5 {
                order: 5;
            }

            .order-lg-6 {
                order: 6;
            }

            .order-lg-7 {
                order: 7;
            }

            .order-lg-8 {
                order: 8;
            }

            .order-lg-9 {
                order: 9;
            }

            .order-lg-10 {
                order: 10;
            }

            .order-lg-11 {
                order: 11;
            }

            .order-lg-12 {
                order: 12;
            }

            .offset-lg-0 {
                margin-left: 0;
            }

            .offset-lg-1 {
                margin-left: 8.3333333333%;
            }

            .offset-lg-2 {
                margin-left: 16.6666666667%;
            }

            .offset-lg-3 {
                margin-left: 25%;
            }

            .offset-lg-4 {
                margin-left: 33.3333333333%;
            }

            .offset-lg-5 {
                margin-left: 41.6666666667%;
            }

            .offset-lg-6 {
                margin-left: 50%;
            }

            .offset-lg-7 {
                margin-left: 58.3333333333%;
            }

            .offset-lg-8 {
                margin-left: 66.6666666667%;
            }

            .offset-lg-9 {
                margin-left: 75%;
            }

            .offset-lg-10 {
                margin-left: 83.3333333333%;
            }

            .offset-lg-11 {
                margin-left: 91.6666666667%;
            }
        }

        @media (min-width: 1200px) {
            .col-xl {
                flex-basis: 0;
                flex-grow: 1;
                max-width: 100%;
            }

            .row-cols-xl-1 > * {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .row-cols-xl-2 > * {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .row-cols-xl-3 > * {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .row-cols-xl-4 > * {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .row-cols-xl-5 > * {
                flex: 0 0 20%;
                max-width: 20%;
            }

            .row-cols-xl-6 > * {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-xl-auto {
                flex: 0 0 auto;
                width: auto;
                max-width: 100%;
            }

            .col-xl-1 {
                flex: 0 0 8.3333333333%;
                max-width: 8.3333333333%;
            }

            .col-xl-2 {
                flex: 0 0 16.6666666667%;
                max-width: 16.6666666667%;
            }

            .col-xl-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .col-xl-4 {
                flex: 0 0 33.3333333333%;
                max-width: 33.3333333333%;
            }

            .col-xl-5 {
                flex: 0 0 41.6666666667%;
                max-width: 41.6666666667%;
            }

            .col-xl-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .col-xl-7 {
                flex: 0 0 58.3333333333%;
                max-width: 58.3333333333%;
            }

            .col-xl-8 {
                flex: 0 0 66.6666666667%;
                max-width: 66.6666666667%;
            }

            .col-xl-9 {
                flex: 0 0 75%;
                max-width: 75%;
            }

            .col-xl-10 {
                flex: 0 0 83.3333333333%;
                max-width: 83.3333333333%;
            }

            .col-xl-11 {
                flex: 0 0 91.6666666667%;
                max-width: 91.6666666667%;
            }

            .col-xl-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .order-xl-first {
                order: -1;
            }

            .order-xl-last {
                order: 13;
            }

            .order-xl-0 {
                order: 0;
            }

            .order-xl-1 {
                order: 1;
            }

            .order-xl-2 {
                order: 2;
            }

            .order-xl-3 {
                order: 3;
            }

            .order-xl-4 {
                order: 4;
            }

            .order-xl-5 {
                order: 5;
            }

            .order-xl-6 {
                order: 6;
            }

            .order-xl-7 {
                order: 7;
            }

            .order-xl-8 {
                order: 8;
            }

            .order-xl-9 {
                order: 9;
            }

            .order-xl-10 {
                order: 10;
            }

            .order-xl-11 {
                order: 11;
            }

            .order-xl-12 {
                order: 12;
            }

            .offset-xl-0 {
                margin-left: 0;
            }

            .offset-xl-1 {
                margin-left: 8.3333333333%;
            }

            .offset-xl-2 {
                margin-left: 16.6666666667%;
            }

            .offset-xl-3 {
                margin-left: 25%;
            }

            .offset-xl-4 {
                margin-left: 33.3333333333%;
            }

            .offset-xl-5 {
                margin-left: 41.6666666667%;
            }

            .offset-xl-6 {
                margin-left: 50%;
            }

            .offset-xl-7 {
                margin-left: 58.3333333333%;
            }

            .offset-xl-8 {
                margin-left: 66.6666666667%;
            }

            .offset-xl-9 {
                margin-left: 75%;
            }

            .offset-xl-10 {
                margin-left: 83.3333333333%;
            }

            .offset-xl-11 {
                margin-left: 91.6666666667%;
            }
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px;
        }

        .table-borderless th,
        .table-borderless td,
        .table-borderless thead th,
        .table-borderless tbody + tbody {
            border: 0;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            color: #212529;
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-primary,
        .table-primary > th,
        .table-primary > td {
            background-color: #c6e0f5;
        }

        .table-primary th,
        .table-primary td,
        .table-primary thead th,
        .table-primary tbody + tbody {
            border-color: #95c5ed;
        }

        .table-hover .table-primary:hover {
            background-color: #b0d4f1;
        }

        .table-hover .table-primary:hover > td,
        .table-hover .table-primary:hover > th {
            background-color: #b0d4f1;
        }

        .table-secondary,
        .table-secondary > th,
        .table-secondary > td {
            background-color: #d6d8db;
        }

        .table-secondary th,
        .table-secondary td,
        .table-secondary thead th,
        .table-secondary tbody + tbody {
            border-color: #b3b7bb;
        }

        .table-hover .table-secondary:hover {
            background-color: #c8cbcf;
        }

        .table-hover .table-secondary:hover > td,
        .table-hover .table-secondary:hover > th {
            background-color: #c8cbcf;
        }

        .table-success,
        .table-success > th,
        .table-success > td {
            background-color: #c7eed8;
        }

        .table-success th,
        .table-success td,
        .table-success thead th,
        .table-success tbody + tbody {
            border-color: #98dfb6;
        }

        .table-hover .table-success:hover {
            background-color: #b3e8ca;
        }

        .table-hover .table-success:hover > td,
        .table-hover .table-success:hover > th {
            background-color: #b3e8ca;
        }

        .table-info,
        .table-info > th,
        .table-info > td {
            background-color: #d6e9f9;
        }

        .table-info th,
        .table-info td,
        .table-info thead th,
        .table-info tbody + tbody {
            border-color: #b3d7f5;
        }

        .table-hover .table-info:hover {
            background-color: #c0ddf6;
        }

        .table-hover .table-info:hover > td,
        .table-hover .table-info:hover > th {
            background-color: #c0ddf6;
        }

        .table-warning,
        .table-warning > th,
        .table-warning > td {
            background-color: #fffacc;
        }

        .table-warning th,
        .table-warning td,
        .table-warning thead th,
        .table-warning tbody + tbody {
            border-color: #fff6a1;
        }

        .table-hover .table-warning:hover {
            background-color: #fff8b3;
        }

        .table-hover .table-warning:hover > td,
        .table-hover .table-warning:hover > th {
            background-color: #fff8b3;
        }

        .table-danger,
        .table-danger > th,
        .table-danger > td {
            background-color: #f7c6c5;
        }

        .table-danger th,
        .table-danger td,
        .table-danger thead th,
        .table-danger tbody + tbody {
            border-color: #f09593;
        }

        .table-hover .table-danger:hover {
            background-color: #f4b0af;
        }

        .table-hover .table-danger:hover > td,
        .table-hover .table-danger:hover > th {
            background-color: #f4b0af;
        }

        .table-light,
        .table-light > th,
        .table-light > td {
            background-color: #fdfdfe;
        }

        .table-light th,
        .table-light td,
        .table-light thead th,
        .table-light tbody + tbody {
            border-color: #fbfcfc;
        }

        .table-hover .table-light:hover {
            background-color: #ececf6;
        }

        .table-hover .table-light:hover > td,
        .table-hover .table-light:hover > th {
            background-color: #ececf6;
        }

        .table-dark,
        .table-dark > th,
        .table-dark > td {
            background-color: #c6c8ca;
        }

        .table-dark th,
        .table-dark td,
        .table-dark thead th,
        .table-dark tbody + tbody {
            border-color: #95999c;
        }

        .table-hover .table-dark:hover {
            background-color: #b9bbbe;
        }

        .table-hover .table-dark:hover > td,
        .table-hover .table-dark:hover > th {
            background-color: #b9bbbe;
        }

        .table-active,
        .table-active > th,
        .table-active > td {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-hover .table-active:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-hover .table-active:hover > td,
        .table-hover .table-active:hover > th {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table .thead-dark th {
            color: #fff;
            background-color: #343a40;
            border-color: #454d55;
        }

        .table .thead-light th {
            color: #495057;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .table-dark {
            color: #fff;
            background-color: #343a40;
        }

        .table-dark th,
        .table-dark td,
        .table-dark thead th {
            border-color: #454d55;
        }

        .table-dark.table-bordered {
            border: 0;
        }

        .table-dark.table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .table-dark.table-hover tbody tr:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.075);
        }

        @media (max-width: 575.98px) {
            .table-responsive-sm {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive-sm > .table-bordered {
                border: 0;
            }
        }

        @media (max-width: 767.98px) {
            .table-responsive-md {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive-md > .table-bordered {
                border: 0;
            }
        }

        @media (max-width: 991.98px) {
            .table-responsive-lg {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive-lg > .table-bordered {
                border: 0;
            }
        }

        @media (max-width: 1199.98px) {
            .table-responsive-xl {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive-xl > .table-bordered {
                border: 0;
            }
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive > .table-bordered {
            border: 0;
        }

        .form-control {
            display: block;
            width: 100%;
            height: calc(1.6em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {
            .form-control {
                transition: none;
            }
        }

        .form-control::-ms-expand {
            background-color: transparent;
            border: 0;
        }

        .form-control:-moz-focusring {
            color: transparent;
            text-shadow: 0 0 0 #495057;
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #a1cbef;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .form-control::-webkit-input-placeholder {
            color: #6c757d;
            opacity: 1;
        }

        .form-control:-ms-input-placeholder {
            color: #6c757d;
            opacity: 1;
        }

        .form-control::-ms-input-placeholder {
            color: #6c757d;
            opacity: 1;
        }

        .form-control::placeholder {
            color: #6c757d;
            opacity: 1;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }

        select.form-control:focus::-ms-value {
            color: #495057;
            background-color: #fff;
        }

        .form-control-file,
        .form-control-range {
            display: block;
            width: 100%;
        }

        .col-form-label {
            padding-top: calc(0.375rem + 1px);
            padding-bottom: calc(0.375rem + 1px);
            margin-bottom: 0;
            font-size: inherit;
            line-height: 1.6;
        }

        .col-form-label-lg {
            padding-top: calc(0.5rem + 1px);
            padding-bottom: calc(0.5rem + 1px);
            font-size: 1.125rem;
            line-height: 1.5;
        }

        .col-form-label-sm {
            padding-top: calc(0.25rem + 1px);
            padding-bottom: calc(0.25rem + 1px);
            font-size: 0.7875rem;
            line-height: 1.5;
        }

        .form-control-plaintext {
            display: block;
            width: 100%;
            padding: 0.375rem 0;
            margin-bottom: 0;
            font-size: 0.9rem;
            line-height: 1.6;
            color: #212529;
            background-color: transparent;
            border: solid transparent;
            border-width: 1px 0;
        }

        .form-control-plaintext.form-control-sm,
        .form-control-plaintext.form-control-lg {
            padding-right: 0;
            padding-left: 0;
        }

        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.7875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .form-control-lg {
            height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1.125rem;
            line-height: 1.5;
            border-radius: 0.3rem;
        }

        select.form-control[size],
        select.form-control[multiple] {
            height: auto;
        }

        textarea.form-control {
            height: auto;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-text {
            display: block;
            margin-top: 0.25rem;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -5px;
            margin-left: -5px;
        }

        .form-row > .col,
        .form-row > [class*=col-] {
            padding-right: 5px;
            padding-left: 5px;
        }

        .form-check {
            position: relative;
            display: block;
            padding-left: 1.25rem;
        }

        .form-check-input {
            position: absolute;
            margin-top: 0.3rem;
            margin-left: -1.25rem;
        }

        .form-check-input[disabled] ~ .form-check-label,
        .form-check-input:disabled ~ .form-check-label {
            color: #6c757d;
        }

        .form-check-label {
            margin-bottom: 0;
        }

        .form-check-inline {
            display: inline-flex;
            align-items: center;
            padding-left: 0;
            margin-right: 0.75rem;
        }

        .form-check-inline .form-check-input {
            position: static;
            margin-top: 0;
            margin-right: 0.3125rem;
            margin-left: 0;
        }

        .valid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #38c172;
        }

        .valid-tooltip {
            position: absolute;
            top: 100%;
            z-index: 5;
            display: none;
            max-width: 100%;
            padding: 0.25rem 0.5rem;
            margin-top: 0.1rem;
            font-size: 0.7875rem;
            line-height: 1.6;
            color: #fff;
            background-color: rgba(56, 193, 114, 0.9);
            border-radius: 0.25rem;
        }

        .was-validated :valid ~ .valid-feedback,
        .was-validated :valid ~ .valid-tooltip,
        .is-valid ~ .valid-feedback,
        .is-valid ~ .valid-tooltip {
            display: block;
        }

        .was-validated .form-control:valid,
        .form-control.is-valid {
            border-color: #38c172;
            padding-right: calc(1.6em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2338c172' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.4em + 0.1875rem) center;
            background-size: calc(0.8em + 0.375rem) calc(0.8em + 0.375rem);
        }

        .was-validated .form-control:valid:focus,
        .form-control.is-valid:focus {
            border-color: #38c172;
            box-shadow: 0 0 0 0.2rem rgba(56, 193, 114, 0.25);
        }

        .was-validated textarea.form-control:valid,
        textarea.form-control.is-valid {
            padding-right: calc(1.6em + 0.75rem);
            background-position: top calc(0.4em + 0.1875rem) right calc(0.4em + 0.1875rem);
        }

        .was-validated .custom-select:valid,
        .custom-select.is-valid {
            border-color: #38c172;
            padding-right: calc(0.75em + 2.3125rem);
            background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='4' height='5' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e") no-repeat right 0.75rem center/8px 10px, url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2338c172' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") #fff no-repeat center right 1.75rem/calc(0.8em + 0.375rem) calc(0.8em + 0.375rem);
        }

        .was-validated .custom-select:valid:focus,
        .custom-select.is-valid:focus {
            border-color: #38c172;
            box-shadow: 0 0 0 0.2rem rgba(56, 193, 114, 0.25);
        }

        .was-validated .form-check-input:valid ~ .form-check-label,
        .form-check-input.is-valid ~ .form-check-label {
            color: #38c172;
        }

        .was-validated .form-check-input:valid ~ .valid-feedback,
        .was-validated .form-check-input:valid ~ .valid-tooltip,
        .form-check-input.is-valid ~ .valid-feedback,
        .form-check-input.is-valid ~ .valid-tooltip {
            display: block;
        }

        .was-validated .custom-control-input:valid ~ .custom-control-label,
        .custom-control-input.is-valid ~ .custom-control-label {
            color: #38c172;
        }

        .was-validated .custom-control-input:valid ~ .custom-control-label::before,
        .custom-control-input.is-valid ~ .custom-control-label::before {
            border-color: #38c172;
        }

        .was-validated .custom-control-input:valid:checked ~ .custom-control-label::before,
        .custom-control-input.is-valid:checked ~ .custom-control-label::before {
            border-color: #5cd08d;
            background-color: #5cd08d;
        }

        .was-validated .custom-control-input:valid:focus ~ .custom-control-label::before,
        .custom-control-input.is-valid:focus ~ .custom-control-label::before {
            box-shadow: 0 0 0 0.2rem rgba(56, 193, 114, 0.25);
        }

        .was-validated .custom-control-input:valid:focus:not(:checked) ~ .custom-control-label::before,
        .custom-control-input.is-valid:focus:not(:checked) ~ .custom-control-label::before {
            border-color: #38c172;
        }

        .was-validated .custom-file-input:valid ~ .custom-file-label,
        .custom-file-input.is-valid ~ .custom-file-label {
            border-color: #38c172;
        }

        .was-validated .custom-file-input:valid:focus ~ .custom-file-label,
        .custom-file-input.is-valid:focus ~ .custom-file-label {
            border-color: #38c172;
            box-shadow: 0 0 0 0.2rem rgba(56, 193, 114, 0.25);
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #e3342f;
        }

        .invalid-tooltip {
            position: absolute;
            top: 100%;
            z-index: 5;
            display: none;
            max-width: 100%;
            padding: 0.25rem 0.5rem;
            margin-top: 0.1rem;
            font-size: 0.7875rem;
            line-height: 1.6;
            color: #fff;
            background-color: rgba(227, 52, 47, 0.9);
            border-radius: 0.25rem;
        }

        .was-validated :invalid ~ .invalid-feedback,
        .was-validated :invalid ~ .invalid-tooltip,
        .is-invalid ~ .invalid-feedback,
        .is-invalid ~ .invalid-tooltip {
            display: block;
        }

        .was-validated .form-control:invalid,
        .form-control.is-invalid {
            border-color: #e3342f;
            padding-right: calc(1.6em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23e3342f' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23e3342f' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.4em + 0.1875rem) center;
            background-size: calc(0.8em + 0.375rem) calc(0.8em + 0.375rem);
        }

        .was-validated .form-control:invalid:focus,
        .form-control.is-invalid:focus {
            border-color: #e3342f;
            box-shadow: 0 0 0 0.2rem rgba(227, 52, 47, 0.25);
        }

        .was-validated textarea.form-control:invalid,
        textarea.form-control.is-invalid {
            padding-right: calc(1.6em + 0.75rem);
            background-position: top calc(0.4em + 0.1875rem) right calc(0.4em + 0.1875rem);
        }

        .was-validated .custom-select:invalid,
        .custom-select.is-invalid {
            border-color: #e3342f;
            padding-right: calc(0.75em + 2.3125rem);
            background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='4' height='5' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e") no-repeat right 0.75rem center/8px 10px, url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23e3342f' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23e3342f' stroke='none'/%3e%3c/svg%3e") #fff no-repeat center right 1.75rem/calc(0.8em + 0.375rem) calc(0.8em + 0.375rem);
        }

        .was-validated .custom-select:invalid:focus,
        .custom-select.is-invalid:focus {
            border-color: #e3342f;
            box-shadow: 0 0 0 0.2rem rgba(227, 52, 47, 0.25);
        }

        .was-validated .form-check-input:invalid ~ .form-check-label,
        .form-check-input.is-invalid ~ .form-check-label {
            color: #e3342f;
        }

        .was-validated .form-check-input:invalid ~ .invalid-feedback,
        .was-validated .form-check-input:invalid ~ .invalid-tooltip,
        .form-check-input.is-invalid ~ .invalid-feedback,
        .form-check-input.is-invalid ~ .invalid-tooltip {
            display: block;
        }

        .was-validated .custom-control-input:invalid ~ .custom-control-label,
        .custom-control-input.is-invalid ~ .custom-control-label {
            color: #e3342f;
        }

        .was-validated .custom-control-input:invalid ~ .custom-control-label::before,
        .custom-control-input.is-invalid ~ .custom-control-label::before {
            border-color: #e3342f;
        }

        .was-validated .custom-control-input:invalid:checked ~ .custom-control-label::before,
        .custom-control-input.is-invalid:checked ~ .custom-control-label::before {
            border-color: #e9605c;
            background-color: #e9605c;
        }

        .was-validated .custom-control-input:invalid:focus ~ .custom-control-label::before,
        .custom-control-input.is-invalid:focus ~ .custom-control-label::before {
            box-shadow: 0 0 0 0.2rem rgba(227, 52, 47, 0.25);
        }

        .was-validated .custom-control-input:invalid:focus:not(:checked) ~ .custom-control-label::before,
        .custom-control-input.is-invalid:focus:not(:checked) ~ .custom-control-label::before {
            border-color: #e3342f;
        }

        .was-validated .custom-file-input:invalid ~ .custom-file-label,
        .custom-file-input.is-invalid ~ .custom-file-label {
            border-color: #e3342f;
        }

        .was-validated .custom-file-input:invalid:focus ~ .custom-file-label,
        .custom-file-input.is-invalid:focus ~ .custom-file-label {
            border-color: #e3342f;
            box-shadow: 0 0 0 0.2rem rgba(227, 52, 47, 0.25);
        }

        .form-inline {
            display: flex;
            flex-flow: row wrap;
            align-items: center;
        }

        .form-inline .form-check {
            width: 100%;
        }

        @media (min-width: 576px) {
            .form-inline label {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 0;
            }

            .form-inline .form-group {
                display: flex;
                flex: 0 0 auto;
                flex-flow: row wrap;
                align-items: center;
                margin-bottom: 0;
            }

            .form-inline .form-control {
                display: inline-block;
                width: auto;
                vertical-align: middle;
            }

            .form-inline .form-control-plaintext {
                display: inline-block;
            }

            .form-inline .input-group,
            .form-inline .custom-select {
                width: auto;
            }

            .form-inline .form-check {
                display: flex;
                align-items: center;
                justify-content: center;
                width: auto;
                padding-left: 0;
            }

            .form-inline .form-check-input {
                position: relative;
                flex-shrink: 0;
                margin-top: 0;
                margin-right: 0.25rem;
                margin-left: 0;
            }

            .form-inline .custom-control {
                align-items: center;
                justify-content: center;
            }

            .form-inline .custom-control-label {
                margin-bottom: 0;
            }
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            color: #212529;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            line-height: 1.6;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {
            .btn {
                transition: none;
            }
        }

        .btn:hover {
            color: #212529;
            text-decoration: none;
        }

        .btn:focus,
        .btn.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .btn.disabled,
        .btn:disabled {
            opacity: 0.65;
        }

        a.btn.disabled,
        fieldset:disabled a.btn {
            pointer-events: none;
        }

        .btn-primary {
            color: #fff;
            background-color: #3490dc;
            border-color: #3490dc;
        }

        .btn-primary:hover {
            color: #fff;
            background-color: #227dc7;
            border-color: #2176bd;
        }

        .btn-primary:focus,
        .btn-primary.focus {
            color: #fff;
            background-color: #227dc7;
            border-color: #2176bd;
            box-shadow: 0 0 0 0.2rem rgba(82, 161, 225, 0.5);
        }

        .btn-primary.disabled,
        .btn-primary:disabled {
            color: #fff;
            background-color: #3490dc;
            border-color: #3490dc;
        }

        .btn-primary:not(:disabled):not(.disabled):active,
        .btn-primary:not(:disabled):not(.disabled).active,
        .show > .btn-primary.dropdown-toggle {
            color: #fff;
            background-color: #2176bd;
            border-color: #1f6fb2;
        }

        .btn-primary:not(:disabled):not(.disabled):active:focus,
        .btn-primary:not(:disabled):not(.disabled).active:focus,
        .show > .btn-primary.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(82, 161, 225, 0.5);
        }

        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            color: #fff;
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-secondary:focus,
        .btn-secondary.focus {
            color: #fff;
            background-color: #5a6268;
            border-color: #545b62;
            box-shadow: 0 0 0 0.2rem rgba(130, 138, 145, 0.5);
        }

        .btn-secondary.disabled,
        .btn-secondary:disabled {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:not(:disabled):not(.disabled):active,
        .btn-secondary:not(:disabled):not(.disabled).active,
        .show > .btn-secondary.dropdown-toggle {
            color: #fff;
            background-color: #545b62;
            border-color: #4e555b;
        }

        .btn-secondary:not(:disabled):not(.disabled):active:focus,
        .btn-secondary:not(:disabled):not(.disabled).active:focus,
        .show > .btn-secondary.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(130, 138, 145, 0.5);
        }

        .btn-success {
            color: #fff;
            background-color: #38c172;
            border-color: #38c172;
        }

        .btn-success:hover {
            color: #fff;
            background-color: #2fa360;
            border-color: #2d995b;
        }

        .btn-success:focus,
        .btn-success.focus {
            color: #fff;
            background-color: #2fa360;
            border-color: #2d995b;
            box-shadow: 0 0 0 0.2rem rgba(86, 202, 135, 0.5);
        }

        .btn-success.disabled,
        .btn-success:disabled {
            color: #fff;
            background-color: #38c172;
            border-color: #38c172;
        }

        .btn-success:not(:disabled):not(.disabled):active,
        .btn-success:not(:disabled):not(.disabled).active,
        .show > .btn-success.dropdown-toggle {
            color: #fff;
            background-color: #2d995b;
            border-color: #2a9055;
        }

        .btn-success:not(:disabled):not(.disabled):active:focus,
        .btn-success:not(:disabled):not(.disabled).active:focus,
        .show > .btn-success.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(86, 202, 135, 0.5);
        }

        .btn-info {
            color: #212529;
            background-color: #6cb2eb;
            border-color: #6cb2eb;
        }

        .btn-info:hover {
            color: #fff;
            background-color: #4aa0e6;
            border-color: #3f9ae5;
        }

        .btn-info:focus,
        .btn-info.focus {
            color: #fff;
            background-color: #4aa0e6;
            border-color: #3f9ae5;
            box-shadow: 0 0 0 0.2rem rgba(97, 157, 206, 0.5);
        }

        .btn-info.disabled,
        .btn-info:disabled {
            color: #212529;
            background-color: #6cb2eb;
            border-color: #6cb2eb;
        }

        .btn-info:not(:disabled):not(.disabled):active,
        .btn-info:not(:disabled):not(.disabled).active,
        .show > .btn-info.dropdown-toggle {
            color: #fff;
            background-color: #3f9ae5;
            border-color: #3495e3;
        }

        .btn-info:not(:disabled):not(.disabled):active:focus,
        .btn-info:not(:disabled):not(.disabled).active:focus,
        .show > .btn-info.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(97, 157, 206, 0.5);
        }

        .btn-warning {
            color: #212529;
            background-color: #ffed4a;
            border-color: #ffed4a;
        }

        .btn-warning:hover {
            color: #212529;
            background-color: #ffe924;
            border-color: #ffe817;
        }

        .btn-warning:focus,
        .btn-warning.focus {
            color: #212529;
            background-color: #ffe924;
            border-color: #ffe817;
            box-shadow: 0 0 0 0.2rem rgba(222, 207, 69, 0.5);
        }

        .btn-warning.disabled,
        .btn-warning:disabled {
            color: #212529;
            background-color: #ffed4a;
            border-color: #ffed4a;
        }

        .btn-warning:not(:disabled):not(.disabled):active,
        .btn-warning:not(:disabled):not(.disabled).active,
        .show > .btn-warning.dropdown-toggle {
            color: #212529;
            background-color: #ffe817;
            border-color: #ffe70a;
        }

        .btn-warning:not(:disabled):not(.disabled):active:focus,
        .btn-warning:not(:disabled):not(.disabled).active:focus,
        .show > .btn-warning.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(222, 207, 69, 0.5);
        }

        .btn-danger {
            color: #fff;
            background-color: #e3342f;
            border-color: #e3342f;
        }

        .btn-danger:hover {
            color: #fff;
            background-color: #d0211c;
            border-color: #c51f1a;
        }

        .btn-danger:focus,
        .btn-danger.focus {
            color: #fff;
            background-color: #d0211c;
            border-color: #c51f1a;
            box-shadow: 0 0 0 0.2rem rgba(231, 82, 78, 0.5);
        }

        .btn-danger.disabled,
        .btn-danger:disabled {
            color: #fff;
            background-color: #e3342f;
            border-color: #e3342f;
        }

        .btn-danger:not(:disabled):not(.disabled):active,
        .btn-danger:not(:disabled):not(.disabled).active,
        .show > .btn-danger.dropdown-toggle {
            color: #fff;
            background-color: #c51f1a;
            border-color: #b91d19;
        }

        .btn-danger:not(:disabled):not(.disabled):active:focus,
        .btn-danger:not(:disabled):not(.disabled).active:focus,
        .show > .btn-danger.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(231, 82, 78, 0.5);
        }

        .btn-light {
            color: #212529;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .btn-light:hover {
            color: #212529;
            background-color: #e2e6ea;
            border-color: #dae0e5;
        }

        .btn-light:focus,
        .btn-light.focus {
            color: #212529;
            background-color: #e2e6ea;
            border-color: #dae0e5;
            box-shadow: 0 0 0 0.2rem rgba(216, 217, 219, 0.5);
        }

        .btn-light.disabled,
        .btn-light:disabled {
            color: #212529;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .btn-light:not(:disabled):not(.disabled):active,
        .btn-light:not(:disabled):not(.disabled).active,
        .show > .btn-light.dropdown-toggle {
            color: #212529;
            background-color: #dae0e5;
            border-color: #d3d9df;
        }

        .btn-light:not(:disabled):not(.disabled):active:focus,
        .btn-light:not(:disabled):not(.disabled).active:focus,
        .show > .btn-light.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(216, 217, 219, 0.5);
        }

        .btn-dark {
            color: #fff;
            background-color: #343a40;
            border-color: #343a40;
        }

        .btn-dark:hover {
            color: #fff;
            background-color: #23272b;
            border-color: #1d2124;
        }

        .btn-dark:focus,
        .btn-dark.focus {
            color: #fff;
            background-color: #23272b;
            border-color: #1d2124;
            box-shadow: 0 0 0 0.2rem rgba(82, 88, 93, 0.5);
        }

        .btn-dark.disabled,
        .btn-dark:disabled {
            color: #fff;
            background-color: #343a40;
            border-color: #343a40;
        }

        .btn-dark:not(:disabled):not(.disabled):active,
        .btn-dark:not(:disabled):not(.disabled).active,
        .show > .btn-dark.dropdown-toggle {
            color: #fff;
            background-color: #1d2124;
            border-color: #171a1d;
        }

        .btn-dark:not(:disabled):not(.disabled):active:focus,
        .btn-dark:not(:disabled):not(.disabled).active:focus,
        .show > .btn-dark.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(82, 88, 93, 0.5);
        }

        .btn-outline-primary {
            color: #3490dc;
            border-color: #3490dc;
        }

        .btn-outline-primary:hover {
            color: #fff;
            background-color: #3490dc;
            border-color: #3490dc;
        }

        .btn-outline-primary:focus,
        .btn-outline-primary.focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.5);
        }

        .btn-outline-primary.disabled,
        .btn-outline-primary:disabled {
            color: #3490dc;
            background-color: transparent;
        }

        .btn-outline-primary:not(:disabled):not(.disabled):active,
        .btn-outline-primary:not(:disabled):not(.disabled).active,
        .show > .btn-outline-primary.dropdown-toggle {
            color: #fff;
            background-color: #3490dc;
            border-color: #3490dc;
        }

        .btn-outline-primary:not(:disabled):not(.disabled):active:focus,
        .btn-outline-primary:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-primary.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.5);
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }

        .btn-outline-secondary:hover {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-outline-secondary:focus,
        .btn-outline-secondary.focus {
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }

        .btn-outline-secondary.disabled,
        .btn-outline-secondary:disabled {
            color: #6c757d;
            background-color: transparent;
        }

        .btn-outline-secondary:not(:disabled):not(.disabled):active,
        .btn-outline-secondary:not(:disabled):not(.disabled).active,
        .show > .btn-outline-secondary.dropdown-toggle {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-outline-secondary:not(:disabled):not(.disabled):active:focus,
        .btn-outline-secondary:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-secondary.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }

        .btn-outline-success {
            color: #38c172;
            border-color: #38c172;
        }

        .btn-outline-success:hover {
            color: #fff;
            background-color: #38c172;
            border-color: #38c172;
        }

        .btn-outline-success:focus,
        .btn-outline-success.focus {
            box-shadow: 0 0 0 0.2rem rgba(56, 193, 114, 0.5);
        }

        .btn-outline-success.disabled,
        .btn-outline-success:disabled {
            color: #38c172;
            background-color: transparent;
        }

        .btn-outline-success:not(:disabled):not(.disabled):active,
        .btn-outline-success:not(:disabled):not(.disabled).active,
        .show > .btn-outline-success.dropdown-toggle {
            color: #fff;
            background-color: #38c172;
            border-color: #38c172;
        }

        .btn-outline-success:not(:disabled):not(.disabled):active:focus,
        .btn-outline-success:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-success.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(56, 193, 114, 0.5);
        }

        .btn-outline-info {
            color: #6cb2eb;
            border-color: #6cb2eb;
        }

        .btn-outline-info:hover {
            color: #212529;
            background-color: #6cb2eb;
            border-color: #6cb2eb;
        }

        .btn-outline-info:focus,
        .btn-outline-info.focus {
            box-shadow: 0 0 0 0.2rem rgba(108, 178, 235, 0.5);
        }

        .btn-outline-info.disabled,
        .btn-outline-info:disabled {
            color: #6cb2eb;
            background-color: transparent;
        }

        .btn-outline-info:not(:disabled):not(.disabled):active,
        .btn-outline-info:not(:disabled):not(.disabled).active,
        .show > .btn-outline-info.dropdown-toggle {
            color: #212529;
            background-color: #6cb2eb;
            border-color: #6cb2eb;
        }

        .btn-outline-info:not(:disabled):not(.disabled):active:focus,
        .btn-outline-info:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-info.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(108, 178, 235, 0.5);
        }

        .btn-outline-warning {
            color: #ffed4a;
            border-color: #ffed4a;
        }

        .btn-outline-warning:hover {
            color: #212529;
            background-color: #ffed4a;
            border-color: #ffed4a;
        }

        .btn-outline-warning:focus,
        .btn-outline-warning.focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 237, 74, 0.5);
        }

        .btn-outline-warning.disabled,
        .btn-outline-warning:disabled {
            color: #ffed4a;
            background-color: transparent;
        }

        .btn-outline-warning:not(:disabled):not(.disabled):active,
        .btn-outline-warning:not(:disabled):not(.disabled).active,
        .show > .btn-outline-warning.dropdown-toggle {
            color: #212529;
            background-color: #ffed4a;
            border-color: #ffed4a;
        }

        .btn-outline-warning:not(:disabled):not(.disabled):active:focus,
        .btn-outline-warning:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-warning.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 237, 74, 0.5);
        }

        .btn-outline-danger {
            color: #e3342f;
            border-color: #e3342f;
        }

        .btn-outline-danger:hover {
            color: #fff;
            background-color: #e3342f;
            border-color: #e3342f;
        }

        .btn-outline-danger:focus,
        .btn-outline-danger.focus {
            box-shadow: 0 0 0 0.2rem rgba(227, 52, 47, 0.5);
        }

        .btn-outline-danger.disabled,
        .btn-outline-danger:disabled {
            color: #e3342f;
            background-color: transparent;
        }

        .btn-outline-danger:not(:disabled):not(.disabled):active,
        .btn-outline-danger:not(:disabled):not(.disabled).active,
        .show > .btn-outline-danger.dropdown-toggle {
            color: #fff;
            background-color: #e3342f;
            border-color: #e3342f;
        }

        .btn-outline-danger:not(:disabled):not(.disabled):active:focus,
        .btn-outline-danger:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-danger.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(227, 52, 47, 0.5);
        }

        .btn-outline-light {
            color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .btn-outline-light:hover {
            color: #212529;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .btn-outline-light:focus,
        .btn-outline-light.focus {
            box-shadow: 0 0 0 0.2rem rgba(248, 249, 250, 0.5);
        }

        .btn-outline-light.disabled,
        .btn-outline-light:disabled {
            color: #f8f9fa;
            background-color: transparent;
        }

        .btn-outline-light:not(:disabled):not(.disabled):active,
        .btn-outline-light:not(:disabled):not(.disabled).active,
        .show > .btn-outline-light.dropdown-toggle {
            color: #212529;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .btn-outline-light:not(:disabled):not(.disabled):active:focus,
        .btn-outline-light:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-light.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(248, 249, 250, 0.5);
        }

        .btn-outline-dark {
            color: #343a40;
            border-color: #343a40;
        }

        .btn-outline-dark:hover {
            color: #fff;
            background-color: #343a40;
            border-color: #343a40;
        }

        .btn-outline-dark:focus,
        .btn-outline-dark.focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 58, 64, 0.5);
        }

        .btn-outline-dark.disabled,
        .btn-outline-dark:disabled {
            color: #343a40;
            background-color: transparent;
        }

        .btn-outline-dark:not(:disabled):not(.disabled):active,
        .btn-outline-dark:not(:disabled):not(.disabled).active,
        .show > .btn-outline-dark.dropdown-toggle {
            color: #fff;
            background-color: #343a40;
            border-color: #343a40;
        }

        .btn-outline-dark:not(:disabled):not(.disabled):active:focus,
        .btn-outline-dark:not(:disabled):not(.disabled).active:focus,
        .show > .btn-outline-dark.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 58, 64, 0.5);
        }

        .btn-link {
            font-weight: 400;
            color: #3490dc;
            text-decoration: none;
        }

        .btn-link:hover {
            color: #1d68a7;
            text-decoration: underline;
        }

        .btn-link:focus,
        .btn-link.focus {
            text-decoration: underline;
            box-shadow: none;
        }

        .btn-link:disabled,
        .btn-link.disabled {
            color: #6c757d;
            pointer-events: none;
        }

        .btn-lg,
        .btn-group-lg > .btn {
            padding: 0.5rem 1rem;
            font-size: 1.125rem;
            line-height: 1.5;
            border-radius: 0.3rem;
        }

        .btn-sm,
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.7875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .btn-block + .btn-block {
            margin-top: 0.5rem;
        }

        input[type=submit].btn-block,
        input[type=reset].btn-block,
        input[type=button].btn-block {
            width: 100%;
        }

        .fade {
            transition: opacity 0.15s linear;
        }

        @media (prefers-reduced-motion: reduce) {
            .fade {
                transition: none;
            }
        }

        .fade:not(.show) {
            opacity: 0;
        }

        .collapse:not(.show) {
            display: none;
        }

        .collapsing {
            position: relative;
            height: 0;
            overflow: hidden;
            transition: height 0.35s ease;
        }

        @media (prefers-reduced-motion: reduce) {
            .collapsing {
                transition: none;
            }
        }

        .dropup,
        .dropright,
        .dropdown,
        .dropleft {
            position: relative;
        }

        .dropdown-toggle {
            white-space: nowrap;
        }

        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }

        .dropdown-toggle:empty::after {
            margin-left: 0;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            font-size: 0.9rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.25rem;
        }

        .dropdown-menu-left {
            right: auto;
            left: 0;
        }

        .dropdown-menu-right {
            right: 0;
            left: auto;
        }

        @media (min-width: 576px) {
            .dropdown-menu-sm-left {
                right: auto;
                left: 0;
            }

            .dropdown-menu-sm-right {
                right: 0;
                left: auto;
            }
        }

        @media (min-width: 768px) {
            .dropdown-menu-md-left {
                right: auto;
                left: 0;
            }

            .dropdown-menu-md-right {
                right: 0;
                left: auto;
            }
        }

        @media (min-width: 992px) {
            .dropdown-menu-lg-left {
                right: auto;
                left: 0;
            }

            .dropdown-menu-lg-right {
                right: 0;
                left: auto;
            }
        }

        @media (min-width: 1200px) {
            .dropdown-menu-xl-left {
                right: auto;
                left: 0;
            }

            .dropdown-menu-xl-right {
                right: 0;
                left: auto;
            }
        }

        .dropup .dropdown-menu {
            top: auto;
            bottom: 100%;
            margin-top: 0;
            margin-bottom: 0.125rem;
        }

        .dropup .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0;
            border-right: 0.3em solid transparent;
            border-bottom: 0.3em solid;
            border-left: 0.3em solid transparent;
        }

        .dropup .dropdown-toggle:empty::after {
            margin-left: 0;
        }

        .dropright .dropdown-menu {
            top: 0;
            right: auto;
            left: 100%;
            margin-top: 0;
            margin-left: 0.125rem;
        }

        .dropright .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid transparent;
            border-right: 0;
            border-bottom: 0.3em solid transparent;
            border-left: 0.3em solid;
        }

        .dropright .dropdown-toggle:empty::after {
            margin-left: 0;
        }

        .dropright .dropdown-toggle::after {
            vertical-align: 0;
        }

        .dropleft .dropdown-menu {
            top: 0;
            right: 100%;
            left: auto;
            margin-top: 0;
            margin-right: 0.125rem;
        }

        .dropleft .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
        }

        .dropleft .dropdown-toggle::after {
            display: none;
        }

        .dropleft .dropdown-toggle::before {
            display: inline-block;
            margin-right: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid transparent;
            border-right: 0.3em solid;
            border-bottom: 0.3em solid transparent;
        }

        .dropleft .dropdown-toggle:empty::after {
            margin-left: 0;
        }

        .dropleft .dropdown-toggle::before {
            vertical-align: 0;
        }

        .dropdown-menu[x-placement^=top],
        .dropdown-menu[x-placement^=right],
        .dropdown-menu[x-placement^=bottom],
        .dropdown-menu[x-placement^=left] {
            right: auto;
            bottom: auto;
        }

        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid #e9ecef;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1.5rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            color: #16181b;
            text-decoration: none;
            background-color: #f8f9fa;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            color: #fff;
            text-decoration: none;
            background-color: #3490dc;
        }

        .dropdown-item.disabled,
        .dropdown-item:disabled {
            color: #6c757d;
            pointer-events: none;
            background-color: transparent;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            display: block;
            padding: 0.5rem 1.5rem;
            margin-bottom: 0;
            font-size: 0.7875rem;
            color: #6c757d;
            white-space: nowrap;
        }

        .dropdown-item-text {
            display: block;
            padding: 0.25rem 1.5rem;
            color: #212529;
        }

        .btn-group,
        .btn-group-vertical {
            position: relative;
            display: inline-flex;
            vertical-align: middle;
        }

        .btn-group > .btn,
        .btn-group-vertical > .btn {
            position: relative;
            flex: 1 1 auto;
        }

        .btn-group > .btn:hover,
        .btn-group-vertical > .btn:hover {
            z-index: 1;
        }

        .btn-group > .btn:focus,
        .btn-group > .btn:active,
        .btn-group > .btn.active,
        .btn-group-vertical > .btn:focus,
        .btn-group-vertical > .btn:active,
        .btn-group-vertical > .btn.active {
            z-index: 1;
        }

        .btn-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .btn-toolbar .input-group {
            width: auto;
        }

        .btn-group > .btn:not(:first-child),
        .btn-group > .btn-group:not(:first-child) {
            margin-left: -1px;
        }

        .btn-group > .btn:not(:last-child):not(.dropdown-toggle),
        .btn-group > .btn-group:not(:last-child) > .btn {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-group > .btn:not(:first-child),
        .btn-group > .btn-group:not(:first-child) > .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .dropdown-toggle-split {
            padding-right: 0.5625rem;
            padding-left: 0.5625rem;
        }

        .dropdown-toggle-split::after,
        .dropup .dropdown-toggle-split::after,
        .dropright .dropdown-toggle-split::after {
            margin-left: 0;
        }

        .dropleft .dropdown-toggle-split::before {
            margin-right: 0;
        }

        .btn-sm + .dropdown-toggle-split,
        .btn-group-sm > .btn + .dropdown-toggle-split {
            padding-right: 0.375rem;
            padding-left: 0.375rem;
        }

        .btn-lg + .dropdown-toggle-split,
        .btn-group-lg > .btn + .dropdown-toggle-split {
            padding-right: 0.75rem;
            padding-left: 0.75rem;
        }

        .btn-group-vertical {
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }

        .btn-group-vertical > .btn,
        .btn-group-vertical > .btn-group {
            width: 100%;
        }

        .btn-group-vertical > .btn:not(:first-child),
        .btn-group-vertical > .btn-group:not(:first-child) {
            margin-top: -1px;
        }

        .btn-group-vertical > .btn:not(:last-child):not(.dropdown-toggle),
        .btn-group-vertical > .btn-group:not(:last-child) > .btn {
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .btn-group-vertical > .btn:not(:first-child),
        .btn-group-vertical > .btn-group:not(:first-child) > .btn {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .btn-group-toggle > .btn,
        .btn-group-toggle > .btn-group > .btn {
            margin-bottom: 0;
        }

        .btn-group-toggle > .btn input[type=radio],
        .btn-group-toggle > .btn input[type=checkbox],
        .btn-group-toggle > .btn-group > .btn input[type=radio],
        .btn-group-toggle > .btn-group > .btn input[type=checkbox] {
            position: absolute;
            clip: rect(0, 0, 0, 0);
            pointer-events: none;
        }

        .input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
        }

        .input-group > .form-control,
        .input-group > .form-control-plaintext,
        .input-group > .custom-select,
        .input-group > .custom-file {
            position: relative;
            flex: 1 1 0%;
            min-width: 0;
            margin-bottom: 0;
        }

        .input-group > .form-control + .form-control,
        .input-group > .form-control + .custom-select,
        .input-group > .form-control + .custom-file,
        .input-group > .form-control-plaintext + .form-control,
        .input-group > .form-control-plaintext + .custom-select,
        .input-group > .form-control-plaintext + .custom-file,
        .input-group > .custom-select + .form-control,
        .input-group > .custom-select + .custom-select,
        .input-group > .custom-select + .custom-file,
        .input-group > .custom-file + .form-control,
        .input-group > .custom-file + .custom-select,
        .input-group > .custom-file + .custom-file {
            margin-left: -1px;
        }

        .input-group > .form-control:focus,
        .input-group > .custom-select:focus,
        .input-group > .custom-file .custom-file-input:focus ~ .custom-file-label {
            z-index: 3;
        }

        .input-group > .custom-file .custom-file-input:focus {
            z-index: 4;
        }

        .input-group > .form-control:not(:last-child),
        .input-group > .custom-select:not(:last-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group > .form-control:not(:first-child),
        .input-group > .custom-select:not(:first-child) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group > .custom-file {
            display: flex;
            align-items: center;
        }

        .input-group > .custom-file:not(:last-child) .custom-file-label,
        .input-group > .custom-file:not(:last-child) .custom-file-label::after {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group > .custom-file:not(:first-child) .custom-file-label {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group-prepend,
        .input-group-append {
            display: flex;
        }

        .input-group-prepend .btn,
        .input-group-append .btn {
            position: relative;
            z-index: 2;
        }

        .input-group-prepend .btn:focus,
        .input-group-append .btn:focus {
            z-index: 3;
        }

        .input-group-prepend .btn + .btn,
        .input-group-prepend .btn + .input-group-text,
        .input-group-prepend .input-group-text + .input-group-text,
        .input-group-prepend .input-group-text + .btn,
        .input-group-append .btn + .btn,
        .input-group-append .btn + .input-group-text,
        .input-group-append .input-group-text + .input-group-text,
        .input-group-append .input-group-text + .btn {
            margin-left: -1px;
        }

        .input-group-prepend {
            margin-right: -1px;
        }

        .input-group-append {
            margin-left: -1px;
        }

        .input-group-text {
            display: flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            margin-bottom: 0;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #495057;
            text-align: center;
            white-space: nowrap;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .input-group-text input[type=radio],
        .input-group-text input[type=checkbox] {
            margin-top: 0;
        }

        .input-group-lg > .form-control:not(textarea),
        .input-group-lg > .custom-select {
            height: calc(1.5em + 1rem + 2px);
        }

        .input-group-lg > .form-control,
        .input-group-lg > .custom-select,
        .input-group-lg > .input-group-prepend > .input-group-text,
        .input-group-lg > .input-group-append > .input-group-text,
        .input-group-lg > .input-group-prepend > .btn,
        .input-group-lg > .input-group-append > .btn {
            padding: 0.5rem 1rem;
            font-size: 1.125rem;
            line-height: 1.5;
            border-radius: 0.3rem;
        }

        .input-group-sm > .form-control:not(textarea),
        .input-group-sm > .custom-select {
            height: calc(1.5em + 0.5rem + 2px);
        }

        .input-group-sm > .form-control,
        .input-group-sm > .custom-select,
        .input-group-sm > .input-group-prepend > .input-group-text,
        .input-group-sm > .input-group-append > .input-group-text,
        .input-group-sm > .input-group-prepend > .btn,
        .input-group-sm > .input-group-append > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.7875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .input-group-lg > .custom-select,
        .input-group-sm > .custom-select {
            padding-right: 1.75rem;
        }

        .input-group > .input-group-prepend > .btn,
        .input-group > .input-group-prepend > .input-group-text,
        .input-group > .input-group-append:not(:last-child) > .btn,
        .input-group > .input-group-append:not(:last-child) > .input-group-text,
        .input-group > .input-group-append:last-child > .btn:not(:last-child):not(.dropdown-toggle),
        .input-group > .input-group-append:last-child > .input-group-text:not(:last-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group > .input-group-append > .btn,
        .input-group > .input-group-append > .input-group-text,
        .input-group > .input-group-prepend:not(:first-child) > .btn,
        .input-group > .input-group-prepend:not(:first-child) > .input-group-text,
        .input-group > .input-group-prepend:first-child > .btn:not(:first-child),
        .input-group > .input-group-prepend:first-child > .input-group-text:not(:first-child) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .custom-control {
            position: relative;
            display: block;
            min-height: 1.44rem;
            padding-left: 1.5rem;
        }

        .custom-control-inline {
            display: inline-flex;
            margin-right: 1rem;
        }

        .custom-control-input {
            position: absolute;
            left: 0;
            z-index: -1;
            width: 1rem;
            height: 1.22rem;
            opacity: 0;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            color: #fff;
            border-color: #3490dc;
            background-color: #3490dc;
        }

        .custom-control-input:focus ~ .custom-control-label::before {
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .custom-control-input:focus:not(:checked) ~ .custom-control-label::before {
            border-color: #a1cbef;
        }

        .custom-control-input:not(:disabled):active ~ .custom-control-label::before {
            color: #fff;
            background-color: #cce3f6;
            border-color: #cce3f6;
        }

        .custom-control-input[disabled] ~ .custom-control-label,
        .custom-control-input:disabled ~ .custom-control-label {
            color: #6c757d;
        }

        .custom-control-input[disabled] ~ .custom-control-label::before,
        .custom-control-input:disabled ~ .custom-control-label::before {
            background-color: #e9ecef;
        }

        .custom-control-label {
            position: relative;
            margin-bottom: 0;
            vertical-align: top;
        }

        .custom-control-label::before {
            position: absolute;
            top: 0.22rem;
            left: -1.5rem;
            display: block;
            width: 1rem;
            height: 1rem;
            pointer-events: none;
            content: "";
            background-color: #fff;
            border: #adb5bd solid 1px;
        }

        .custom-control-label::after {
            position: absolute;
            top: 0.22rem;
            left: -1.5rem;
            display: block;
            width: 1rem;
            height: 1rem;
            content: "";
            background: no-repeat 50%/50% 50%;
        }

        .custom-checkbox .custom-control-label::before {
            border-radius: 0.25rem;
        }

        .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26l2.974 2.99L8 2.193z'/%3e%3c/svg%3e");
        }

        .custom-checkbox .custom-control-input:indeterminate ~ .custom-control-label::before {
            border-color: #3490dc;
            background-color: #3490dc;
        }

        .custom-checkbox .custom-control-input:indeterminate ~ .custom-control-label::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4' viewBox='0 0 4 4'%3e%3cpath stroke='%23fff' d='M0 2h4'/%3e%3c/svg%3e");
        }

        .custom-checkbox .custom-control-input:disabled:checked ~ .custom-control-label::before {
            background-color: rgba(52, 144, 220, 0.5);
        }

        .custom-checkbox .custom-control-input:disabled:indeterminate ~ .custom-control-label::before {
            background-color: rgba(52, 144, 220, 0.5);
        }

        .custom-radio .custom-control-label::before {
            border-radius: 50%;
        }

        .custom-radio .custom-control-input:checked ~ .custom-control-label::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        }

        .custom-radio .custom-control-input:disabled:checked ~ .custom-control-label::before {
            background-color: rgba(52, 144, 220, 0.5);
        }

        .custom-switch {
            padding-left: 2.25rem;
        }

        .custom-switch .custom-control-label::before {
            left: -2.25rem;
            width: 1.75rem;
            pointer-events: all;
            border-radius: 0.5rem;
        }

        .custom-switch .custom-control-label::after {
            top: calc(0.22rem + 2px);
            left: calc(-2.25rem + 2px);
            width: calc(1rem - 4px);
            height: calc(1rem - 4px);
            background-color: #adb5bd;
            border-radius: 0.5rem;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-transform 0.15s ease-in-out;
            transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-transform 0.15s ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {
            .custom-switch .custom-control-label::after {
                transition: none;
            }
        }

        .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
            background-color: #fff;
            -webkit-transform: translateX(0.75rem);
            transform: translateX(0.75rem);
        }

        .custom-switch .custom-control-input:disabled:checked ~ .custom-control-label::before {
            background-color: rgba(52, 144, 220, 0.5);
        }

        .custom-select {
            display: inline-block;
            width: 100%;
            height: calc(1.6em + 0.75rem + 2px);
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #495057;
            vertical-align: middle;
            background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='4' height='5' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e") no-repeat right 0.75rem center/8px 10px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .custom-select:focus {
            border-color: #a1cbef;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .custom-select:focus::-ms-value {
            color: #495057;
            background-color: #fff;
        }

        .custom-select[multiple],
        .custom-select[size]:not([size="1"]) {
            height: auto;
            padding-right: 0.75rem;
            background-image: none;
        }

        .custom-select:disabled {
            color: #6c757d;
            background-color: #e9ecef;
        }

        .custom-select::-ms-expand {
            display: none;
        }

        .custom-select:-moz-focusring {
            color: transparent;
            text-shadow: 0 0 0 #495057;
        }

        .custom-select-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            padding-left: 0.5rem;
            font-size: 0.7875rem;
        }

        .custom-select-lg {
            height: calc(1.5em + 1rem + 2px);
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            padding-left: 1rem;
            font-size: 1.125rem;
        }

        .custom-file {
            position: relative;
            display: inline-block;
            width: 100%;
            height: calc(1.6em + 0.75rem + 2px);
            margin-bottom: 0;
        }

        .custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(1.6em + 0.75rem + 2px);
            margin: 0;
            opacity: 0;
        }

        .custom-file-input:focus ~ .custom-file-label {
            border-color: #a1cbef;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .custom-file-input[disabled] ~ .custom-file-label,
        .custom-file-input:disabled ~ .custom-file-label {
            background-color: #e9ecef;
        }

        .custom-file-input:lang(en) ~ .custom-file-label::after {
            content: "Browse";
        }

        .custom-file-input ~ .custom-file-label[data-browse]::after {
            content: attr(data-browse);
        }

        .custom-file-label {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1;
            height: calc(1.6em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-weight: 400;
            line-height: 1.6;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .custom-file-label::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 3;
            display: block;
            height: calc(1.6em + 0.75rem);
            padding: 0.375rem 0.75rem;
            line-height: 1.6;
            color: #495057;
            content: "Browse";
            background-color: #e9ecef;
            border-left: inherit;
            border-radius: 0 0.25rem 0.25rem 0;
        }

        .custom-range {
            width: 100%;
            height: 1.4rem;
            padding: 0;
            background-color: transparent;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .custom-range:focus {
            outline: none;
        }

        .custom-range:focus::-webkit-slider-thumb {
            box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .custom-range:focus::-moz-range-thumb {
            box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .custom-range:focus::-ms-thumb {
            box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .custom-range::-moz-focus-outer {
            border: 0;
        }

        .custom-range::-webkit-slider-thumb {
            width: 1rem;
            height: 1rem;
            margin-top: -0.25rem;
            background-color: #3490dc;
            border: 0;
            border-radius: 1rem;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            -webkit-appearance: none;
            appearance: none;
        }

        @media (prefers-reduced-motion: reduce) {
            .custom-range::-webkit-slider-thumb {
                transition: none;
            }
        }

        .custom-range::-webkit-slider-thumb:active {
            background-color: #cce3f6;
        }

        .custom-range::-webkit-slider-runnable-track {
            width: 100%;
            height: 0.5rem;
            color: transparent;
            cursor: pointer;
            background-color: #dee2e6;
            border-color: transparent;
            border-radius: 1rem;
        }

        .custom-range::-moz-range-thumb {
            width: 1rem;
            height: 1rem;
            background-color: #3490dc;
            border: 0;
            border-radius: 1rem;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            -moz-appearance: none;
            appearance: none;
        }

        @media (prefers-reduced-motion: reduce) {
            .custom-range::-moz-range-thumb {
                transition: none;
            }
        }

        .custom-range::-moz-range-thumb:active {
            background-color: #cce3f6;
        }

        .custom-range::-moz-range-track {
            width: 100%;
            height: 0.5rem;
            color: transparent;
            cursor: pointer;
            background-color: #dee2e6;
            border-color: transparent;
            border-radius: 1rem;
        }

        .custom-range::-ms-thumb {
            width: 1rem;
            height: 1rem;
            margin-top: 0;
            margin-right: 0.2rem;
            margin-left: 0.2rem;
            background-color: #3490dc;
            border: 0;
            border-radius: 1rem;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            appearance: none;
        }

        @media (prefers-reduced-motion: reduce) {
            .custom-range::-ms-thumb {
                transition: none;
            }
        }

        .custom-range::-ms-thumb:active {
            background-color: #cce3f6;
        }

        .custom-range::-ms-track {
            width: 100%;
            height: 0.5rem;
            color: transparent;
            cursor: pointer;
            background-color: transparent;
            border-color: transparent;
            border-width: 0.5rem;
        }

        .custom-range::-ms-fill-lower {
            background-color: #dee2e6;
            border-radius: 1rem;
        }

        .custom-range::-ms-fill-upper {
            margin-right: 15px;
            background-color: #dee2e6;
            border-radius: 1rem;
        }

        .custom-range:disabled::-webkit-slider-thumb {
            background-color: #adb5bd;
        }

        .custom-range:disabled::-webkit-slider-runnable-track {
            cursor: default;
        }

        .custom-range:disabled::-moz-range-thumb {
            background-color: #adb5bd;
        }

        .custom-range:disabled::-moz-range-track {
            cursor: default;
        }

        .custom-range:disabled::-ms-thumb {
            background-color: #adb5bd;
        }

        .custom-control-label::before,
        .custom-file-label,
        .custom-select {
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {
            .custom-control-label::before,
            .custom-file-label,
            .custom-select {
                transition: none;
            }
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }

        .nav-link {
            display: block;
            padding: 0.5rem 1rem;
        }

        .nav-link:hover,
        .nav-link:focus {
            text-decoration: none;
        }

        .nav-link.disabled {
            color: #6c757d;
            pointer-events: none;
            cursor: default;
        }

        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
        }

        .nav-tabs .nav-item {
            margin-bottom: -1px;
        }

        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        .nav-tabs .nav-link:hover,
        .nav-tabs .nav-link:focus {
            border-color: #e9ecef #e9ecef #dee2e6;
        }

        .nav-tabs .nav-link.disabled {
            color: #6c757d;
            background-color: transparent;
            border-color: transparent;
        }

        .nav-tabs .nav-link.active,
        .nav-tabs .nav-item.show .nav-link {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .nav-tabs .dropdown-menu {
            margin-top: -1px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .nav-pills .nav-link {
            border-radius: 0.25rem;
        }

        .nav-pills .nav-link.active,
        .nav-pills .show > .nav-link {
            color: #fff;
            background-color: #3490dc;
        }

        .nav-fill .nav-item {
            flex: 1 1 auto;
            text-align: center;
        }

        .nav-justified .nav-item {
            flex-basis: 0;
            flex-grow: 1;
            text-align: center;
        }

        .tab-content > .tab-pane {
            display: none;
        }

        .tab-content > .active {
            display: block;
        }

        .navbar {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem;
        }

        .navbar .container,
        .navbar .container-fluid,
        .navbar .container-sm,
        .navbar .container-md,
        .navbar .container-lg,
        .navbar .container-xl {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: inline-block;
            padding-top: 0.32rem;
            padding-bottom: 0.32rem;
            margin-right: 1rem;
            font-size: 1.125rem;
            line-height: inherit;
            white-space: nowrap;
        }

        .navbar-brand:hover,
        .navbar-brand:focus {
            text-decoration: none;
        }

        .navbar-nav {
            display: flex;
            flex-direction: column;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }

        .navbar-nav .nav-link {
            padding-right: 0;
            padding-left: 0;
        }

        .navbar-nav .dropdown-menu {
            position: static;
            float: none;
        }

        .navbar-text {
            display: inline-block;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .navbar-collapse {
            flex-basis: 100%;
            flex-grow: 1;
            align-items: center;
        }

        .navbar-toggler {
            padding: 0.25rem 0.75rem;
            font-size: 1.125rem;
            line-height: 1;
            background-color: transparent;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }

        .navbar-toggler:hover,
        .navbar-toggler:focus {
            text-decoration: none;
        }

        .navbar-toggler-icon {
            display: inline-block;
            width: 1.5em;
            height: 1.5em;
            vertical-align: middle;
            content: "";
            background: no-repeat center center;
            background-size: 100% 100%;
        }

        @media (max-width: 575.98px) {
            .navbar-expand-sm > .container,
            .navbar-expand-sm > .container-fluid,
            .navbar-expand-sm > .container-sm,
            .navbar-expand-sm > .container-md,
            .navbar-expand-sm > .container-lg,
            .navbar-expand-sm > .container-xl {
                padding-right: 0;
                padding-left: 0;
            }
        }

        @media (min-width: 576px) {
            .navbar-expand-sm {
                flex-flow: row nowrap;
                justify-content: flex-start;
            }

            .navbar-expand-sm .navbar-nav {
                flex-direction: row;
            }

            .navbar-expand-sm .navbar-nav .dropdown-menu {
                position: absolute;
            }

            .navbar-expand-sm .navbar-nav .nav-link {
                padding-right: 0.5rem;
                padding-left: 0.5rem;
            }

            .navbar-expand-sm > .container,
            .navbar-expand-sm > .container-fluid,
            .navbar-expand-sm > .container-sm,
            .navbar-expand-sm > .container-md,
            .navbar-expand-sm > .container-lg,
            .navbar-expand-sm > .container-xl {
                flex-wrap: nowrap;
            }

            .navbar-expand-sm .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }

            .navbar-expand-sm .navbar-toggler {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            .navbar-expand-md > .container,
            .navbar-expand-md > .container-fluid,
            .navbar-expand-md > .container-sm,
            .navbar-expand-md > .container-md,
            .navbar-expand-md > .container-lg,
            .navbar-expand-md > .container-xl {
                padding-right: 0;
                padding-left: 0;
            }
        }

        @media (min-width: 768px) {
            .navbar-expand-md {
                flex-flow: row nowrap;
                justify-content: flex-start;
            }

            .navbar-expand-md .navbar-nav {
                flex-direction: row;
            }

            .navbar-expand-md .navbar-nav .dropdown-menu {
                position: absolute;
            }

            .navbar-expand-md .navbar-nav .nav-link {
                padding-right: 0.5rem;
                padding-left: 0.5rem;
            }

            .navbar-expand-md > .container,
            .navbar-expand-md > .container-fluid,
            .navbar-expand-md > .container-sm,
            .navbar-expand-md > .container-md,
            .navbar-expand-md > .container-lg,
            .navbar-expand-md > .container-xl {
                flex-wrap: nowrap;
            }

            .navbar-expand-md .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }

            .navbar-expand-md .navbar-toggler {
                display: none;
            }
        }

        @media (max-width: 991.98px) {
            .navbar-expand-lg > .container,
            .navbar-expand-lg > .container-fluid,
            .navbar-expand-lg > .container-sm,
            .navbar-expand-lg > .container-md,
            .navbar-expand-lg > .container-lg,
            .navbar-expand-lg > .container-xl {
                padding-right: 0;
                padding-left: 0;
            }
        }

        @media (min-width: 992px) {
            .navbar-expand-lg {
                flex-flow: row nowrap;
                justify-content: flex-start;
            }

            .navbar-expand-lg .navbar-nav {
                flex-direction: row;
            }

            .navbar-expand-lg .navbar-nav .dropdown-menu {
                position: absolute;
            }

            .navbar-expand-lg .navbar-nav .nav-link {
                padding-right: 0.5rem;
                padding-left: 0.5rem;
            }

            .navbar-expand-lg > .container,
            .navbar-expand-lg > .container-fluid,
            .navbar-expand-lg > .container-sm,
            .navbar-expand-lg > .container-md,
            .navbar-expand-lg > .container-lg,
            .navbar-expand-lg > .container-xl {
                flex-wrap: nowrap;
            }

            .navbar-expand-lg .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }

            .navbar-expand-lg .navbar-toggler {
                display: none;
            }
        }

        @media (max-width: 1199.98px) {
            .navbar-expand-xl > .container,
            .navbar-expand-xl > .container-fluid,
            .navbar-expand-xl > .container-sm,
            .navbar-expand-xl > .container-md,
            .navbar-expand-xl > .container-lg,
            .navbar-expand-xl > .container-xl {
                padding-right: 0;
                padding-left: 0;
            }
        }

        @media (min-width: 1200px) {
            .navbar-expand-xl {
                flex-flow: row nowrap;
                justify-content: flex-start;
            }

            .navbar-expand-xl .navbar-nav {
                flex-direction: row;
            }

            .navbar-expand-xl .navbar-nav .dropdown-menu {
                position: absolute;
            }

            .navbar-expand-xl .navbar-nav .nav-link {
                padding-right: 0.5rem;
                padding-left: 0.5rem;
            }

            .navbar-expand-xl > .container,
            .navbar-expand-xl > .container-fluid,
            .navbar-expand-xl > .container-sm,
            .navbar-expand-xl > .container-md,
            .navbar-expand-xl > .container-lg,
            .navbar-expand-xl > .container-xl {
                flex-wrap: nowrap;
            }

            .navbar-expand-xl .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }

            .navbar-expand-xl .navbar-toggler {
                display: none;
            }
        }

        .navbar-expand {
            flex-flow: row nowrap;
            justify-content: flex-start;
        }

        .navbar-expand > .container,
        .navbar-expand > .container-fluid,
        .navbar-expand > .container-sm,
        .navbar-expand > .container-md,
        .navbar-expand > .container-lg,
        .navbar-expand > .container-xl {
            padding-right: 0;
            padding-left: 0;
        }

        .navbar-expand .navbar-nav {
            flex-direction: row;
        }

        .navbar-expand .navbar-nav .dropdown-menu {
            position: absolute;
        }

        .navbar-expand .navbar-nav .nav-link {
            padding-right: 0.5rem;
            padding-left: 0.5rem;
        }

        .navbar-expand > .container,
        .navbar-expand > .container-fluid,
        .navbar-expand > .container-sm,
        .navbar-expand > .container-md,
        .navbar-expand > .container-lg,
        .navbar-expand > .container-xl {
            flex-wrap: nowrap;
        }

        .navbar-expand .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }

        .navbar-expand .navbar-toggler {
            display: none;
        }

        .navbar-light .navbar-brand {
            color: rgba(0, 0, 0, 0.9);
        }

        .navbar-light .navbar-brand:hover,
        .navbar-light .navbar-brand:focus {
            color: rgba(0, 0, 0, 0.9);
        }

        .navbar-light .navbar-nav .nav-link {
            color: rgba(0, 0, 0, 0.5);
        }

        .navbar-light .navbar-nav .nav-link:hover,
        .navbar-light .navbar-nav .nav-link:focus {
            color: rgba(0, 0, 0, 0.7);
        }

        .navbar-light .navbar-nav .nav-link.disabled {
            color: rgba(0, 0, 0, 0.3);
        }

        .navbar-light .navbar-nav .show > .nav-link,
        .navbar-light .navbar-nav .active > .nav-link,
        .navbar-light .navbar-nav .nav-link.show,
        .navbar-light .navbar-nav .nav-link.active {
            color: rgba(0, 0, 0, 0.9);
        }

        .navbar-light .navbar-toggler {
            color: rgba(0, 0, 0, 0.5);
            border-color: rgba(0, 0, 0, 0.1);
        }

        .navbar-light .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(0, 0, 0, 0.5)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-light .navbar-text {
            color: rgba(0, 0, 0, 0.5);
        }

        .navbar-light .navbar-text a {
            color: rgba(0, 0, 0, 0.9);
        }

        .navbar-light .navbar-text a:hover,
        .navbar-light .navbar-text a:focus {
            color: rgba(0, 0, 0, 0.9);
        }

        .navbar-dark .navbar-brand {
            color: #fff;
        }

        .navbar-dark .navbar-brand:hover,
        .navbar-dark .navbar-brand:focus {
            color: #fff;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.5);
        }

        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link:focus {
            color: rgba(255, 255, 255, 0.75);
        }

        .navbar-dark .navbar-nav .nav-link.disabled {
            color: rgba(255, 255, 255, 0.25);
        }

        .navbar-dark .navbar-nav .show > .nav-link,
        .navbar-dark .navbar-nav .active > .nav-link,
        .navbar-dark .navbar-nav .nav-link.show,
        .navbar-dark .navbar-nav .nav-link.active {
            color: #fff;
        }

        .navbar-dark .navbar-toggler {
            color: rgba(255, 255, 255, 0.5);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-dark .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.5)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-dark .navbar-text {
            color: rgba(255, 255, 255, 0.5);
        }

        .navbar-dark .navbar-text a {
            color: #fff;
        }

        .navbar-dark .navbar-text a:hover,
        .navbar-dark .navbar-text a:focus {
            color: #fff;
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
        }

        .card > hr {
            margin-right: 0;
            margin-left: 0;
        }

        .card > .list-group:first-child .list-group-item:first-child {
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        .card > .list-group:last-child .list-group-item:last-child {
            border-bottom-right-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem;
        }

        .card-title {
            margin-bottom: 0.75rem;
        }

        .card-subtitle {
            margin-top: -0.375rem;
            margin-bottom: 0;
        }

        .card-text:last-child {
            margin-bottom: 0;
        }

        .card-link:hover {
            text-decoration: none;
        }

        .card-link + .card-link {
            margin-left: 1.25rem;
        }

        .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-header:first-child {
            border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0;
        }

        .card-header + .list-group .list-group-item:first-child {
            border-top: 0;
        }

        .card-footer {
            padding: 0.75rem 1.25rem;
            background-color: rgba(0, 0, 0, 0.03);
            border-top: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-footer:last-child {
            border-radius: 0 0 calc(0.25rem - 1px) calc(0.25rem - 1px);
        }

        .card-header-tabs {
            margin-right: -0.625rem;
            margin-bottom: -0.75rem;
            margin-left: -0.625rem;
            border-bottom: 0;
        }

        .card-header-pills {
            margin-right: -0.625rem;
            margin-left: -0.625rem;
        }

        .card-img-overlay {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            padding: 1.25rem;
        }

        .card-img,
        .card-img-top,
        .card-img-bottom {
            flex-shrink: 0;
            width: 100%;
        }

        .card-img,
        .card-img-top {
            border-top-left-radius: calc(0.25rem - 1px);
            border-top-right-radius: calc(0.25rem - 1px);
        }

        .card-img,
        .card-img-bottom {
            border-bottom-right-radius: calc(0.25rem - 1px);
            border-bottom-left-radius: calc(0.25rem - 1px);
        }

        .card-deck .card {
            margin-bottom: 15px;
        }

        @media (min-width: 576px) {
            .card-deck {
                display: flex;
                flex-flow: row wrap;
                margin-right: -15px;
                margin-left: -15px;
            }

            .card-deck .card {
                flex: 1 0 0%;
                margin-right: 15px;
                margin-bottom: 0;
                margin-left: 15px;
            }
        }

        .card-group > .card {
            margin-bottom: 15px;
        }

        @media (min-width: 576px) {
            .card-group {
                display: flex;
                flex-flow: row wrap;
            }

            .card-group > .card {
                flex: 1 0 0%;
                margin-bottom: 0;
            }

            .card-group > .card + .card {
                margin-left: 0;
                border-left: 0;
            }

            .card-group > .card:not(:last-child) {
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
            }

            .card-group > .card:not(:last-child) .card-img-top,
            .card-group > .card:not(:last-child) .card-header {
                border-top-right-radius: 0;
            }

            .card-group > .card:not(:last-child) .card-img-bottom,
            .card-group > .card:not(:last-child) .card-footer {
                border-bottom-right-radius: 0;
            }

            .card-group > .card:not(:first-child) {
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }

            .card-group > .card:not(:first-child) .card-img-top,
            .card-group > .card:not(:first-child) .card-header {
                border-top-left-radius: 0;
            }

            .card-group > .card:not(:first-child) .card-img-bottom,
            .card-group > .card:not(:first-child) .card-footer {
                border-bottom-left-radius: 0;
            }
        }

        .card-columns .card {
            margin-bottom: 0.75rem;
        }

        @media (min-width: 576px) {
            .card-columns {
                -webkit-column-count: 3;
                column-count: 3;
                -webkit-column-gap: 1.25rem;
                column-gap: 1.25rem;
                orphans: 1;
                widows: 1;
            }

            .card-columns .card {
                display: inline-block;
                width: 100%;
            }
        }

        .accordion > .card {
            overflow: hidden;
        }

        .accordion > .card:not(:last-of-type) {
            border-bottom: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .accordion > .card:not(:first-of-type) {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .accordion > .card > .card-header {
            border-radius: 0;
            margin-bottom: -1px;
        }

        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            list-style: none;
            background-color: #e9ecef;
            border-radius: 0.25rem;
        }

        .breadcrumb-item + .breadcrumb-item {
            padding-left: 0.5rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            display: inline-block;
            padding-right: 0.5rem;
            color: #6c757d;
            content: "/";
        }

        .breadcrumb-item + .breadcrumb-item:hover::before {
            text-decoration: underline;
        }

        .breadcrumb-item + .breadcrumb-item:hover::before {
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
            border-radius: 0.25rem;
        }

        .page-link {
            position: relative;
            display: block;
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            line-height: 1.25;
            color: #3490dc;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }

        .page-link:hover {
            z-index: 2;
            color: #1d68a7;
            text-decoration: none;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .page-link:focus {
            z-index: 3;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .page-item:first-child .page-link {
            margin-left: 0;
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .page-item:last-child .page-link {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #3490dc;
            border-color: #3490dc;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: auto;
            background-color: #fff;
            border-color: #dee2e6;
        }

        .pagination-lg .page-link {
            padding: 0.75rem 1.5rem;
            font-size: 1.125rem;
            line-height: 1.5;
        }

        .pagination-lg .page-item:first-child .page-link {
            border-top-left-radius: 0.3rem;
            border-bottom-left-radius: 0.3rem;
        }

        .pagination-lg .page-item:last-child .page-link {
            border-top-right-radius: 0.3rem;
            border-bottom-right-radius: 0.3rem;
        }

        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.7875rem;
            line-height: 1.5;
        }

        .pagination-sm .page-item:first-child .page-link {
            border-top-left-radius: 0.2rem;
            border-bottom-left-radius: 0.2rem;
        }

        .pagination-sm .page-item:last-child .page-link {
            border-top-right-radius: 0.2rem;
            border-bottom-right-radius: 0.2rem;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {
            .badge {
                transition: none;
            }
        }

        a.badge:hover,
        a.badge:focus {
            text-decoration: none;
        }

        .badge:empty {
            display: none;
        }

        .btn .badge {
            position: relative;
            top: -1px;
        }

        .badge-pill {
            padding-right: 0.6em;
            padding-left: 0.6em;
            border-radius: 10rem;
        }

        .badge-primary {
            color: #fff;
            background-color: #3490dc;
        }

        a.badge-primary:hover,
        a.badge-primary:focus {
            color: #fff;
            background-color: #2176bd;
        }

        a.badge-primary:focus,
        a.badge-primary.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.5);
        }

        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }

        a.badge-secondary:hover,
        a.badge-secondary:focus {
            color: #fff;
            background-color: #545b62;
        }

        a.badge-secondary:focus,
        a.badge-secondary.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }

        .badge-success {
            color: #fff;
            background-color: #38c172;
        }

        a.badge-success:hover,
        a.badge-success:focus {
            color: #fff;
            background-color: #2d995b;
        }

        a.badge-success:focus,
        a.badge-success.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(56, 193, 114, 0.5);
        }

        .badge-info {
            color: #212529;
            background-color: #6cb2eb;
        }

        a.badge-info:hover,
        a.badge-info:focus {
            color: #212529;
            background-color: #3f9ae5;
        }

        a.badge-info:focus,
        a.badge-info.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(108, 178, 235, 0.5);
        }

        .badge-warning {
            color: #212529;
            background-color: #ffed4a;
        }

        a.badge-warning:hover,
        a.badge-warning:focus {
            color: #212529;
            background-color: #ffe817;
        }

        a.badge-warning:focus,
        a.badge-warning.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(255, 237, 74, 0.5);
        }

        .badge-danger {
            color: #fff;
            background-color: #e3342f;
        }

        a.badge-danger:hover,
        a.badge-danger:focus {
            color: #fff;
            background-color: #c51f1a;
        }

        a.badge-danger:focus,
        a.badge-danger.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(227, 52, 47, 0.5);
        }

        .badge-light {
            color: #212529;
            background-color: #f8f9fa;
        }

        a.badge-light:hover,
        a.badge-light:focus {
            color: #212529;
            background-color: #dae0e5;
        }

        a.badge-light:focus,
        a.badge-light.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(248, 249, 250, 0.5);
        }

        .badge-dark {
            color: #fff;
            background-color: #343a40;
        }

        a.badge-dark:hover,
        a.badge-dark:focus {
            color: #fff;
            background-color: #1d2124;
        }

        a.badge-dark:focus,
        a.badge-dark.focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 58, 64, 0.5);
        }

        .jumbotron {
            padding: 2rem 1rem;
            margin-bottom: 2rem;
            background-color: #e9ecef;
            border-radius: 0.3rem;
        }

        @media (min-width: 576px) {
            .jumbotron {
                padding: 4rem 2rem;
            }
        }

        .jumbotron-fluid {
            padding-right: 0;
            padding-left: 0;
            border-radius: 0;
        }

        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }

        .alert-heading {
            color: inherit;
        }

        .alert-link {
            font-weight: 700;
        }

        .alert-dismissible {
            padding-right: 3.85rem;
        }

        .alert-dismissible .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.75rem 1.25rem;
            color: inherit;
        }

        .alert-primary {
            color: #1b4b72;
            background-color: #d6e9f8;
            border-color: #c6e0f5;
        }

        .alert-primary hr {
            border-top-color: #b0d4f1;
        }

        .alert-primary .alert-link {
            color: #113049;
        }

        .alert-secondary {
            color: #383d41;
            background-color: #e2e3e5;
            border-color: #d6d8db;
        }

        .alert-secondary hr {
            border-top-color: #c8cbcf;
        }

        .alert-secondary .alert-link {
            color: #202326;
        }

        .alert-success {
            color: #1d643b;
            background-color: #d7f3e3;
            border-color: #c7eed8;
        }

        .alert-success hr {
            border-top-color: #b3e8ca;
        }

        .alert-success .alert-link {
            color: #123c24;
        }

        .alert-info {
            color: #385d7a;
            background-color: #e2f0fb;
            border-color: #d6e9f9;
        }

        .alert-info hr {
            border-top-color: #c0ddf6;
        }

        .alert-info .alert-link {
            color: #284257;
        }

        .alert-warning {
            color: #857b26;
            background-color: #fffbdb;
            border-color: #fffacc;
        }

        .alert-warning hr {
            border-top-color: #fff8b3;
        }

        .alert-warning .alert-link {
            color: #5d561b;
        }

        .alert-danger {
            color: #761b18;
            background-color: #f9d6d5;
            border-color: #f7c6c5;
        }

        .alert-danger hr {
            border-top-color: #f4b0af;
        }

        .alert-danger .alert-link {
            color: #4c110f;
        }

        .alert-light {
            color: #818182;
            background-color: #fefefe;
            border-color: #fdfdfe;
        }

        .alert-light hr {
            border-top-color: #ececf6;
        }

        .alert-light .alert-link {
            color: #686868;
        }

        .alert-dark {
            color: #1b1e21;
            background-color: #d6d8d9;
            border-color: #c6c8ca;
        }

        .alert-dark hr {
            border-top-color: #b9bbbe;
        }

        .alert-dark .alert-link {
            color: #040505;
        }

        @-webkit-keyframes progress-bar-stripes {
            from {
                background-position: 1rem 0;
            }

            to {
                background-position: 0 0;
            }
        }

        @keyframes progress-bar-stripes {
            from {
                background-position: 1rem 0;
            }

            to {
                background-position: 0 0;
            }
        }

        .progress {
            display: flex;
            height: 1rem;
            overflow: hidden;
            font-size: 0.675rem;
            background-color: #e9ecef;
            border-radius: 0.25rem;
        }

        .progress-bar {
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            background-color: #3490dc;
            transition: width 0.6s ease;
        }

        @media (prefers-reduced-motion: reduce) {
            .progress-bar {
                transition: none;
            }
        }

        .progress-bar-striped {
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            background-size: 1rem 1rem;
        }

        .progress-bar-animated {
            -webkit-animation: progress-bar-stripes 1s linear infinite;
            animation: progress-bar-stripes 1s linear infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .progress-bar-animated {
                -webkit-animation: none;
                animation: none;
            }
        }

        .media {
            display: flex;
            align-items: flex-start;
        }

        .media-body {
            flex: 1;
        }

        .list-group {
            display: flex;
            flex-direction: column;
            padding-left: 0;
            margin-bottom: 0;
        }

        .list-group-item-action {
            width: 100%;
            color: #495057;
            text-align: inherit;
        }

        .list-group-item-action:hover,
        .list-group-item-action:focus {
            z-index: 1;
            color: #495057;
            text-decoration: none;
            background-color: #f8f9fa;
        }

        .list-group-item-action:active {
            color: #212529;
            background-color: #e9ecef;
        }

        .list-group-item {
            position: relative;
            display: block;
            padding: 0.75rem 1.25rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .list-group-item:first-child {
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        .list-group-item:last-child {
            border-bottom-right-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .list-group-item.disabled,
        .list-group-item:disabled {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
        }

        .list-group-item.active {
            z-index: 2;
            color: #fff;
            background-color: #3490dc;
            border-color: #3490dc;
        }

        .list-group-item + .list-group-item {
            border-top-width: 0;
        }

        .list-group-item + .list-group-item.active {
            margin-top: -1px;
            border-top-width: 1px;
        }

        .list-group-horizontal {
            flex-direction: row;
        }

        .list-group-horizontal .list-group-item:first-child {
            border-bottom-left-radius: 0.25rem;
            border-top-right-radius: 0;
        }

        .list-group-horizontal .list-group-item:last-child {
            border-top-right-radius: 0.25rem;
            border-bottom-left-radius: 0;
        }

        .list-group-horizontal .list-group-item.active {
            margin-top: 0;
        }

        .list-group-horizontal .list-group-item + .list-group-item {
            border-top-width: 1px;
            border-left-width: 0;
        }

        .list-group-horizontal .list-group-item + .list-group-item.active {
            margin-left: -1px;
            border-left-width: 1px;
        }

        @media (min-width: 576px) {
            .list-group-horizontal-sm {
                flex-direction: row;
            }

            .list-group-horizontal-sm .list-group-item:first-child {
                border-bottom-left-radius: 0.25rem;
                border-top-right-radius: 0;
            }

            .list-group-horizontal-sm .list-group-item:last-child {
                border-top-right-radius: 0.25rem;
                border-bottom-left-radius: 0;
            }

            .list-group-horizontal-sm .list-group-item.active {
                margin-top: 0;
            }

            .list-group-horizontal-sm .list-group-item + .list-group-item {
                border-top-width: 1px;
                border-left-width: 0;
            }

            .list-group-horizontal-sm .list-group-item + .list-group-item.active {
                margin-left: -1px;
                border-left-width: 1px;
            }
        }

        @media (min-width: 768px) {
            .list-group-horizontal-md {
                flex-direction: row;
            }

            .list-group-horizontal-md .list-group-item:first-child {
                border-bottom-left-radius: 0.25rem;
                border-top-right-radius: 0;
            }

            .list-group-horizontal-md .list-group-item:last-child {
                border-top-right-radius: 0.25rem;
                border-bottom-left-radius: 0;
            }

            .list-group-horizontal-md .list-group-item.active {
                margin-top: 0;
            }

            .list-group-horizontal-md .list-group-item + .list-group-item {
                border-top-width: 1px;
                border-left-width: 0;
            }

            .list-group-horizontal-md .list-group-item + .list-group-item.active {
                margin-left: -1px;
                border-left-width: 1px;
            }
        }

        @media (min-width: 992px) {
            .list-group-horizontal-lg {
                flex-direction: row;
            }

            .list-group-horizontal-lg .list-group-item:first-child {
                border-bottom-left-radius: 0.25rem;
                border-top-right-radius: 0;
            }

            .list-group-horizontal-lg .list-group-item:last-child {
                border-top-right-radius: 0.25rem;
                border-bottom-left-radius: 0;
            }

            .list-group-horizontal-lg .list-group-item.active {
                margin-top: 0;
            }

            .list-group-horizontal-lg .list-group-item + .list-group-item {
                border-top-width: 1px;
                border-left-width: 0;
            }

            .list-group-horizontal-lg .list-group-item + .list-group-item.active {
                margin-left: -1px;
                border-left-width: 1px;
            }
        }

        @media (min-width: 1200px) {
            .list-group-horizontal-xl {
                flex-direction: row;
            }

            .list-group-horizontal-xl .list-group-item:first-child {
                border-bottom-left-radius: 0.25rem;
                border-top-right-radius: 0;
            }

            .list-group-horizontal-xl .list-group-item:last-child {
                border-top-right-radius: 0.25rem;
                border-bottom-left-radius: 0;
            }

            .list-group-horizontal-xl .list-group-item.active {
                margin-top: 0;
            }

            .list-group-horizontal-xl .list-group-item + .list-group-item {
                border-top-width: 1px;
                border-left-width: 0;
            }

            .list-group-horizontal-xl .list-group-item + .list-group-item.active {
                margin-left: -1px;
                border-left-width: 1px;
            }
        }

        .list-group-flush .list-group-item {
            border-right-width: 0;
            border-left-width: 0;
            border-radius: 0;
        }

        .list-group-flush .list-group-item:first-child {
            border-top-width: 0;
        }

        .list-group-flush:last-child .list-group-item:last-child {
            border-bottom-width: 0;
        }

        .list-group-item-primary {
            color: #1b4b72;
            background-color: #c6e0f5;
        }

        .list-group-item-primary.list-group-item-action:hover,
        .list-group-item-primary.list-group-item-action:focus {
            color: #1b4b72;
            background-color: #b0d4f1;
        }

        .list-group-item-primary.list-group-item-action.active {
            color: #fff;
            background-color: #1b4b72;
            border-color: #1b4b72;
        }

        .list-group-item-secondary {
            color: #383d41;
            background-color: #d6d8db;
        }

        .list-group-item-secondary.list-group-item-action:hover,
        .list-group-item-secondary.list-group-item-action:focus {
            color: #383d41;
            background-color: #c8cbcf;
        }

        .list-group-item-secondary.list-group-item-action.active {
            color: #fff;
            background-color: #383d41;
            border-color: #383d41;
        }

        .list-group-item-success {
            color: #1d643b;
            background-color: #c7eed8;
        }

        .list-group-item-success.list-group-item-action:hover,
        .list-group-item-success.list-group-item-action:focus {
            color: #1d643b;
            background-color: #b3e8ca;
        }

        .list-group-item-success.list-group-item-action.active {
            color: #fff;
            background-color: #1d643b;
            border-color: #1d643b;
        }

        .list-group-item-info {
            color: #385d7a;
            background-color: #d6e9f9;
        }

        .list-group-item-info.list-group-item-action:hover,
        .list-group-item-info.list-group-item-action:focus {
            color: #385d7a;
            background-color: #c0ddf6;
        }

        .list-group-item-info.list-group-item-action.active {
            color: #fff;
            background-color: #385d7a;
            border-color: #385d7a;
        }

        .list-group-item-warning {
            color: #857b26;
            background-color: #fffacc;
        }

        .list-group-item-warning.list-group-item-action:hover,
        .list-group-item-warning.list-group-item-action:focus {
            color: #857b26;
            background-color: #fff8b3;
        }

        .list-group-item-warning.list-group-item-action.active {
            color: #fff;
            background-color: #857b26;
            border-color: #857b26;
        }

        .list-group-item-danger {
            color: #761b18;
            background-color: #f7c6c5;
        }

        .list-group-item-danger.list-group-item-action:hover,
        .list-group-item-danger.list-group-item-action:focus {
            color: #761b18;
            background-color: #f4b0af;
        }

        .list-group-item-danger.list-group-item-action.active {
            color: #fff;
            background-color: #761b18;
            border-color: #761b18;
        }

        .list-group-item-light {
            color: #818182;
            background-color: #fdfdfe;
        }

        .list-group-item-light.list-group-item-action:hover,
        .list-group-item-light.list-group-item-action:focus {
            color: #818182;
            background-color: #ececf6;
        }

        .list-group-item-light.list-group-item-action.active {
            color: #fff;
            background-color: #818182;
            border-color: #818182;
        }

        .list-group-item-dark {
            color: #1b1e21;
            background-color: #c6c8ca;
        }

        .list-group-item-dark.list-group-item-action:hover,
        .list-group-item-dark.list-group-item-action:focus {
            color: #1b1e21;
            background-color: #b9bbbe;
        }

        .list-group-item-dark.list-group-item-action.active {
            color: #fff;
            background-color: #1b1e21;
            border-color: #1b1e21;
        }

        .close {
            float: right;
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: 0.5;
        }

        .close:hover {
            color: #000;
            text-decoration: none;
        }

        .close:not(:disabled):not(.disabled):hover,
        .close:not(:disabled):not(.disabled):focus {
            opacity: 0.75;
        }

        button.close {
            padding: 0;
            background-color: transparent;
            border: 0;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        a.close.disabled {
            pointer-events: none;
        }

        .toast {
            max-width: 350px;
            overflow: hidden;
            font-size: 0.875rem;
            background-color: rgba(255, 255, 255, 0.85);
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            opacity: 0;
            border-radius: 0.25rem;
        }

        .toast:not(:last-child) {
            margin-bottom: 0.75rem;
        }

        .toast.showing {
            opacity: 1;
        }

        .toast.show {
            display: block;
            opacity: 1;
        }

        .toast.hide {
            display: none;
        }

        .toast-header {
            display: flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            color: #6c757d;
            background-color: rgba(255, 255, 255, 0.85);
            background-clip: padding-box;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .toast-body {
            padding: 0.75rem;
        }

        .modal-open {
            overflow: hidden;
        }

        .modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: none;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 0.5rem;
            pointer-events: none;
        }

        .modal.fade .modal-dialog {
            transition: -webkit-transform 0.3s ease-out;
            transition: transform 0.3s ease-out;
            transition: transform 0.3s ease-out, -webkit-transform 0.3s ease-out;
            -webkit-transform: translate(0, -50px);
            transform: translate(0, -50px);
        }

        @media (prefers-reduced-motion: reduce) {
            .modal.fade .modal-dialog {
                transition: none;
            }
        }

        .modal.show .modal-dialog {
            -webkit-transform: none;
            transform: none;
        }

        .modal.modal-static .modal-dialog {
            -webkit-transform: scale(1.02);
            transform: scale(1.02);
        }

        .modal-dialog-scrollable {
            display: flex;
            max-height: calc(100% - 1rem);
        }

        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 1rem);
            overflow: hidden;
        }

        .modal-dialog-scrollable .modal-header,
        .modal-dialog-scrollable .modal-footer {
            flex-shrink: 0;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }

        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        .modal-dialog-centered::before {
            display: block;
            height: calc(100vh - 1rem);
            content: "";
        }

        .modal-dialog-centered.modal-dialog-scrollable {
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        .modal-dialog-centered.modal-dialog-scrollable .modal-content {
            max-height: none;
        }

        .modal-dialog-centered.modal-dialog-scrollable::before {
            content: none;
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 0.3rem;
            outline: 0;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: #000;
        }

        .modal-backdrop.fade {
            opacity: 0;
        }

        .modal-backdrop.show {
            opacity: 0.5;
        }

        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1rem 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }

        .modal-header .close {
            padding: 1rem 1rem;
            margin: -1rem -1rem -1rem auto;
        }

        .modal-title {
            margin-bottom: 0;
            line-height: 1.6;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }

        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: calc(0.3rem - 1px);
            border-bottom-left-radius: calc(0.3rem - 1px);
        }

        .modal-footer > * {
            margin: 0.25rem;
        }

        .modal-scrollbar-measure {
            position: absolute;
            top: -9999px;
            width: 50px;
            height: 50px;
            overflow: scroll;
        }

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px;
                margin: 1.75rem auto;
            }

            .modal-dialog-scrollable {
                max-height: calc(100% - 3.5rem);
            }

            .modal-dialog-scrollable .modal-content {
                max-height: calc(100vh - 3.5rem);
            }

            .modal-dialog-centered {
                min-height: calc(100% - 3.5rem);
            }

            .modal-dialog-centered::before {
                height: calc(100vh - 3.5rem);
            }

            .modal-sm {
                max-width: 300px;
            }
        }

        @media (min-width: 992px) {
            .modal-lg,
            .modal-xl {
                max-width: 800px;
            }
        }

        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px;
            }
        }

        .tooltip {
            position: absolute;
            z-index: 1070;
            display: block;
            margin: 0;
            font-family: "Nunito", sans-serif;
            font-style: normal;
            font-weight: 400;
            line-height: 1.6;
            text-align: left;
            text-align: start;
            text-decoration: none;
            text-shadow: none;
            text-transform: none;
            letter-spacing: normal;
            word-break: normal;
            word-spacing: normal;
            white-space: normal;
            line-break: auto;
            font-size: 0.7875rem;
            word-wrap: break-word;
            opacity: 0;
        }

        .tooltip.show {
            opacity: 0.9;
        }

        .tooltip .arrow {
            position: absolute;
            display: block;
            width: 0.8rem;
            height: 0.4rem;
        }

        .tooltip .arrow::before {
            position: absolute;
            content: "";
            border-color: transparent;
            border-style: solid;
        }

        .bs-tooltip-top,
        .bs-tooltip-auto[x-placement^=top] {
            padding: 0.4rem 0;
        }

        .bs-tooltip-top .arrow,
        .bs-tooltip-auto[x-placement^=top] .arrow {
            bottom: 0;
        }

        .bs-tooltip-top .arrow::before,
        .bs-tooltip-auto[x-placement^=top] .arrow::before {
            top: 0;
            border-width: 0.4rem 0.4rem 0;
            border-top-color: #000;
        }

        .bs-tooltip-right,
        .bs-tooltip-auto[x-placement^=right] {
            padding: 0 0.4rem;
        }

        .bs-tooltip-right .arrow,
        .bs-tooltip-auto[x-placement^=right] .arrow {
            left: 0;
            width: 0.4rem;
            height: 0.8rem;
        }

        .bs-tooltip-right .arrow::before,
        .bs-tooltip-auto[x-placement^=right] .arrow::before {
            right: 0;
            border-width: 0.4rem 0.4rem 0.4rem 0;
            border-right-color: #000;
        }

        .bs-tooltip-bottom,
        .bs-tooltip-auto[x-placement^=bottom] {
            padding: 0.4rem 0;
        }

        .bs-tooltip-bottom .arrow,
        .bs-tooltip-auto[x-placement^=bottom] .arrow {
            top: 0;
        }

        .bs-tooltip-bottom .arrow::before,
        .bs-tooltip-auto[x-placement^=bottom] .arrow::before {
            bottom: 0;
            border-width: 0 0.4rem 0.4rem;
            border-bottom-color: #000;
        }

        .bs-tooltip-left,
        .bs-tooltip-auto[x-placement^=left] {
            padding: 0 0.4rem;
        }

        .bs-tooltip-left .arrow,
        .bs-tooltip-auto[x-placement^=left] .arrow {
            right: 0;
            width: 0.4rem;
            height: 0.8rem;
        }

        .bs-tooltip-left .arrow::before,
        .bs-tooltip-auto[x-placement^=left] .arrow::before {
            left: 0;
            border-width: 0.4rem 0 0.4rem 0.4rem;
            border-left-color: #000;
        }

        .tooltip-inner {
            max-width: 200px;
            padding: 0.25rem 0.5rem;
            color: #fff;
            text-align: center;
            background-color: #000;
            border-radius: 0.25rem;
        }

        .popover {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1060;
            display: block;
            max-width: 276px;
            font-family: "Nunito", sans-serif;
            font-style: normal;
            font-weight: 400;
            line-height: 1.6;
            text-align: left;
            text-align: start;
            text-decoration: none;
            text-shadow: none;
            text-transform: none;
            letter-spacing: normal;
            word-break: normal;
            word-spacing: normal;
            white-space: normal;
            line-break: auto;
            font-size: 0.7875rem;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 0.3rem;
        }

        .popover .arrow {
            position: absolute;
            display: block;
            width: 1rem;
            height: 0.5rem;
            margin: 0 0.3rem;
        }

        .popover .arrow::before,
        .popover .arrow::after {
            position: absolute;
            display: block;
            content: "";
            border-color: transparent;
            border-style: solid;
        }

        .bs-popover-top,
        .bs-popover-auto[x-placement^=top] {
            margin-bottom: 0.5rem;
        }

        .bs-popover-top > .arrow,
        .bs-popover-auto[x-placement^=top] > .arrow {
            bottom: calc(-0.5rem - 1px);
        }

        .bs-popover-top > .arrow::before,
        .bs-popover-auto[x-placement^=top] > .arrow::before {
            bottom: 0;
            border-width: 0.5rem 0.5rem 0;
            border-top-color: rgba(0, 0, 0, 0.25);
        }

        .bs-popover-top > .arrow::after,
        .bs-popover-auto[x-placement^=top] > .arrow::after {
            bottom: 1px;
            border-width: 0.5rem 0.5rem 0;
            border-top-color: #fff;
        }

        .bs-popover-right,
        .bs-popover-auto[x-placement^=right] {
            margin-left: 0.5rem;
        }

        .bs-popover-right > .arrow,
        .bs-popover-auto[x-placement^=right] > .arrow {
            left: calc(-0.5rem - 1px);
            width: 0.5rem;
            height: 1rem;
            margin: 0.3rem 0;
        }

        .bs-popover-right > .arrow::before,
        .bs-popover-auto[x-placement^=right] > .arrow::before {
            left: 0;
            border-width: 0.5rem 0.5rem 0.5rem 0;
            border-right-color: rgba(0, 0, 0, 0.25);
        }

        .bs-popover-right > .arrow::after,
        .bs-popover-auto[x-placement^=right] > .arrow::after {
            left: 1px;
            border-width: 0.5rem 0.5rem 0.5rem 0;
            border-right-color: #fff;
        }

        .bs-popover-bottom,
        .bs-popover-auto[x-placement^=bottom] {
            margin-top: 0.5rem;
        }

        .bs-popover-bottom > .arrow,
        .bs-popover-auto[x-placement^=bottom] > .arrow {
            top: calc(-0.5rem - 1px);
        }

        .bs-popover-bottom > .arrow::before,
        .bs-popover-auto[x-placement^=bottom] > .arrow::before {
            top: 0;
            border-width: 0 0.5rem 0.5rem 0.5rem;
            border-bottom-color: rgba(0, 0, 0, 0.25);
        }

        .bs-popover-bottom > .arrow::after,
        .bs-popover-auto[x-placement^=bottom] > .arrow::after {
            top: 1px;
            border-width: 0 0.5rem 0.5rem 0.5rem;
            border-bottom-color: #fff;
        }

        .bs-popover-bottom .popover-header::before,
        .bs-popover-auto[x-placement^=bottom] .popover-header::before {
            position: absolute;
            top: 0;
            left: 50%;
            display: block;
            width: 1rem;
            margin-left: -0.5rem;
            content: "";
            border-bottom: 1px solid #f7f7f7;
        }

        .bs-popover-left,
        .bs-popover-auto[x-placement^=left] {
            margin-right: 0.5rem;
        }

        .bs-popover-left > .arrow,
        .bs-popover-auto[x-placement^=left] > .arrow {
            right: calc(-0.5rem - 1px);
            width: 0.5rem;
            height: 1rem;
            margin: 0.3rem 0;
        }

        .bs-popover-left > .arrow::before,
        .bs-popover-auto[x-placement^=left] > .arrow::before {
            right: 0;
            border-width: 0.5rem 0 0.5rem 0.5rem;
            border-left-color: rgba(0, 0, 0, 0.25);
        }

        .bs-popover-left > .arrow::after,
        .bs-popover-auto[x-placement^=left] > .arrow::after {
            right: 1px;
            border-width: 0.5rem 0 0.5rem 0.5rem;
            border-left-color: #fff;
        }

        .popover-header {
            padding: 0.5rem 0.75rem;
            margin-bottom: 0;
            font-size: 0.9rem;
            background-color: #f7f7f7;
            border-bottom: 1px solid #ebebeb;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }

        .popover-header:empty {
            display: none;
        }

        .popover-body {
            padding: 0.5rem 0.75rem;
            color: #212529;
        }

        .carousel {
            position: relative;
        }

        .carousel.pointer-event {
            touch-action: pan-y;
        }

        .carousel-inner {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .carousel-inner::after {
            display: block;
            clear: both;
            content: "";
        }

        .carousel-item {
            position: relative;
            display: none;
            float: left;
            width: 100%;
            margin-right: -100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transition: -webkit-transform 0.6s ease-in-out;
            transition: transform 0.6s ease-in-out;
            transition: transform 0.6s ease-in-out, -webkit-transform 0.6s ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {
            .carousel-item {
                transition: none;
            }
        }

        .carousel-item.active,
        .carousel-item-next,
        .carousel-item-prev {
            display: block;
        }

        .carousel-item-next:not(.carousel-item-left),
        .active.carousel-item-right {
            -webkit-transform: translateX(100%);
            transform: translateX(100%);
        }

        .carousel-item-prev:not(.carousel-item-right),
        .active.carousel-item-left {
            -webkit-transform: translateX(-100%);
            transform: translateX(-100%);
        }

        .carousel-fade .carousel-item {
            opacity: 0;
            transition-property: opacity;
            -webkit-transform: none;
            transform: none;
        }

        .carousel-fade .carousel-item.active,
        .carousel-fade .carousel-item-next.carousel-item-left,
        .carousel-fade .carousel-item-prev.carousel-item-right {
            z-index: 1;
            opacity: 1;
        }

        .carousel-fade .active.carousel-item-left,
        .carousel-fade .active.carousel-item-right {
            z-index: 0;
            opacity: 0;
            transition: opacity 0s 0.6s;
        }

        @media (prefers-reduced-motion: reduce) {
            .carousel-fade .active.carousel-item-left,
            .carousel-fade .active.carousel-item-right {
                transition: none;
            }
        }

        .carousel-control-prev,
        .carousel-control-next {
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 15%;
            color: #fff;
            text-align: center;
            opacity: 0.5;
            transition: opacity 0.15s ease;
        }

        @media (prefers-reduced-motion: reduce) {
            .carousel-control-prev,
            .carousel-control-next {
                transition: none;
            }
        }

        .carousel-control-prev:hover,
        .carousel-control-prev:focus,
        .carousel-control-next:hover,
        .carousel-control-next:focus {
            color: #fff;
            text-decoration: none;
            outline: 0;
            opacity: 0.9;
        }

        .carousel-control-prev {
            left: 0;
        }

        .carousel-control-next {
            right: 0;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: no-repeat 50%/100% 100%;
        }

        .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M5.25 0l-4 4 4 4 1.5-1.5L4.25 4l2.5-2.5L5.25 0z'/%3e%3c/svg%3e");
        }

        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M2.75 0l-1.5 1.5L3.75 4l-2.5 2.5L2.75 8l4-4-4-4z'/%3e%3c/svg%3e");
        }

        .carousel-indicators {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 15;
            display: flex;
            justify-content: center;
            padding-left: 0;
            margin-right: 15%;
            margin-left: 15%;
            list-style: none;
        }

        .carousel-indicators li {
            box-sizing: content-box;
            flex: 0 1 auto;
            width: 30px;
            height: 3px;
            margin-right: 3px;
            margin-left: 3px;
            text-indent: -999px;
            cursor: pointer;
            background-color: #fff;
            background-clip: padding-box;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            opacity: 0.5;
            transition: opacity 0.6s ease;
        }

        @media (prefers-reduced-motion: reduce) {
            .carousel-indicators li {
                transition: none;
            }
        }

        .carousel-indicators .active {
            opacity: 1;
        }

        .carousel-caption {
            position: absolute;
            right: 15%;
            bottom: 20px;
            left: 15%;
            z-index: 10;
            padding-top: 20px;
            padding-bottom: 20px;
            color: #fff;
            text-align: center;
        }

        @-webkit-keyframes spinner-border {
            to {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes spinner-border {
            to {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border 0.75s linear infinite;
            animation: spinner-border 0.75s linear infinite;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        @-webkit-keyframes spinner-grow {
            0% {
                -webkit-transform: scale(0);
                transform: scale(0);
            }

            50% {
                opacity: 1;
            }
        }

        @keyframes spinner-grow {
            0% {
                -webkit-transform: scale(0);
                transform: scale(0);
            }

            50% {
                opacity: 1;
            }
        }

        .spinner-grow {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            background-color: currentColor;
            border-radius: 50%;
            opacity: 0;
            -webkit-animation: spinner-grow 0.75s linear infinite;
            animation: spinner-grow 0.75s linear infinite;
        }

        .spinner-grow-sm {
            width: 1rem;
            height: 1rem;
        }

        .align-baseline {
            vertical-align: baseline !important;
        }

        .align-top {
            vertical-align: top !important;
        }

        .align-middle {
            vertical-align: middle !important;
        }

        .align-bottom {
            vertical-align: bottom !important;
        }

        .align-text-bottom {
            vertical-align: text-bottom !important;
        }

        .align-text-top {
            vertical-align: text-top !important;
        }

        .bg-primary {
            background-color: #3490dc !important;
        }

        a.bg-primary:hover,
        a.bg-primary:focus,
        button.bg-primary:hover,
        button.bg-primary:focus {
            background-color: #2176bd !important;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }

        a.bg-secondary:hover,
        a.bg-secondary:focus,
        button.bg-secondary:hover,
        button.bg-secondary:focus {
            background-color: #545b62 !important;
        }

        .bg-success {
            background-color: #38c172 !important;
        }

        a.bg-success:hover,
        a.bg-success:focus,
        button.bg-success:hover,
        button.bg-success:focus {
            background-color: #2d995b !important;
        }

        .bg-info {
            background-color: #6cb2eb !important;
        }

        a.bg-info:hover,
        a.bg-info:focus,
        button.bg-info:hover,
        button.bg-info:focus {
            background-color: #3f9ae5 !important;
        }

        .bg-warning {
            background-color: #ffed4a !important;
        }

        a.bg-warning:hover,
        a.bg-warning:focus,
        button.bg-warning:hover,
        button.bg-warning:focus {
            background-color: #ffe817 !important;
        }

        .bg-danger {
            background-color: #e3342f !important;
        }

        a.bg-danger:hover,
        a.bg-danger:focus,
        button.bg-danger:hover,
        button.bg-danger:focus {
            background-color: #c51f1a !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        a.bg-light:hover,
        a.bg-light:focus,
        button.bg-light:hover,
        button.bg-light:focus {
            background-color: #dae0e5 !important;
        }

        .bg-dark {
            background-color: #343a40 !important;
        }

        a.bg-dark:hover,
        a.bg-dark:focus,
        button.bg-dark:hover,
        button.bg-dark:focus {
            background-color: #1d2124 !important;
        }

        .bg-white {
            background-color: #fff !important;
        }

        .bg-transparent {
            background-color: transparent !important;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }

        .border-right {
            border-right: 1px solid #dee2e6 !important;
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }

        .border-left {
            border-left: 1px solid #dee2e6 !important;
        }

        .border-0 {
            border: 0 !important;
        }

        .border-top-0 {
            border-top: 0 !important;
        }

        .border-right-0 {
            border-right: 0 !important;
        }

        .border-bottom-0 {
            border-bottom: 0 !important;
        }

        .border-left-0 {
            border-left: 0 !important;
        }

        .border-primary {
            border-color: #3490dc !important;
        }

        .border-secondary {
            border-color: #6c757d !important;
        }

        .border-success {
            border-color: #38c172 !important;
        }

        .border-info {
            border-color: #6cb2eb !important;
        }

        .border-warning {
            border-color: #ffed4a !important;
        }

        .border-danger {
            border-color: #e3342f !important;
        }

        .border-light {
            border-color: #f8f9fa !important;
        }

        .border-dark {
            border-color: #343a40 !important;
        }

        .border-white {
            border-color: #fff !important;
        }

        .rounded-sm {
            border-radius: 0.2rem !important;
        }

        .rounded {
            border-radius: 0.25rem !important;
        }

        .rounded-top {
            border-top-left-radius: 0.25rem !important;
            border-top-right-radius: 0.25rem !important;
        }

        .rounded-right {
            border-top-right-radius: 0.25rem !important;
            border-bottom-right-radius: 0.25rem !important;
        }

        .rounded-bottom {
            border-bottom-right-radius: 0.25rem !important;
            border-bottom-left-radius: 0.25rem !important;
        }

        .rounded-left {
            border-top-left-radius: 0.25rem !important;
            border-bottom-left-radius: 0.25rem !important;
        }

        .rounded-lg {
            border-radius: 0.3rem !important;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }

        .rounded-0 {
            border-radius: 0 !important;
        }

        .clearfix::after {
            display: block;
            clear: both;
            content: "";
        }

        .d-none {
            display: none !important;
        }

        .d-inline {
            display: inline !important;
        }

        .d-inline-block {
            display: inline-block !important;
        }

        .d-block {
            display: block !important;
        }

        .d-table {
            display: table !important;
        }

        .d-table-row {
            display: table-row !important;
        }

        .d-table-cell {
            display: table-cell !important;
        }

        .d-flex {
            display: flex !important;
        }

        .d-inline-flex {
            display: inline-flex !important;
        }

        @media (min-width: 576px) {
            .d-sm-none {
                display: none !important;
            }

            .d-sm-inline {
                display: inline !important;
            }

            .d-sm-inline-block {
                display: inline-block !important;
            }

            .d-sm-block {
                display: block !important;
            }

            .d-sm-table {
                display: table !important;
            }

            .d-sm-table-row {
                display: table-row !important;
            }

            .d-sm-table-cell {
                display: table-cell !important;
            }

            .d-sm-flex {
                display: flex !important;
            }

            .d-sm-inline-flex {
                display: inline-flex !important;
            }
        }

        @media (min-width: 768px) {
            .d-md-none {
                display: none !important;
            }

            .d-md-inline {
                display: inline !important;
            }

            .d-md-inline-block {
                display: inline-block !important;
            }

            .d-md-block {
                display: block !important;
            }

            .d-md-table {
                display: table !important;
            }

            .d-md-table-row {
                display: table-row !important;
            }

            .d-md-table-cell {
                display: table-cell !important;
            }

            .d-md-flex {
                display: flex !important;
            }

            .d-md-inline-flex {
                display: inline-flex !important;
            }
        }

        @media (min-width: 992px) {
            .d-lg-none {
                display: none !important;
            }

            .d-lg-inline {
                display: inline !important;
            }

            .d-lg-inline-block {
                display: inline-block !important;
            }

            .d-lg-block {
                display: block !important;
            }

            .d-lg-table {
                display: table !important;
            }

            .d-lg-table-row {
                display: table-row !important;
            }

            .d-lg-table-cell {
                display: table-cell !important;
            }

            .d-lg-flex {
                display: flex !important;
            }

            .d-lg-inline-flex {
                display: inline-flex !important;
            }
        }

        @media (min-width: 1200px) {
            .d-xl-none {
                display: none !important;
            }

            .d-xl-inline {
                display: inline !important;
            }

            .d-xl-inline-block {
                display: inline-block !important;
            }

            .d-xl-block {
                display: block !important;
            }

            .d-xl-table {
                display: table !important;
            }

            .d-xl-table-row {
                display: table-row !important;
            }

            .d-xl-table-cell {
                display: table-cell !important;
            }

            .d-xl-flex {
                display: flex !important;
            }

            .d-xl-inline-flex {
                display: inline-flex !important;
            }
        }

        @media print {
            .d-print-none {
                display: none !important;
            }

            .d-print-inline {
                display: inline !important;
            }

            .d-print-inline-block {
                display: inline-block !important;
            }

            .d-print-block {
                display: block !important;
            }

            .d-print-table {
                display: table !important;
            }

            .d-print-table-row {
                display: table-row !important;
            }

            .d-print-table-cell {
                display: table-cell !important;
            }

            .d-print-flex {
                display: flex !important;
            }

            .d-print-inline-flex {
                display: inline-flex !important;
            }
        }

        .embed-responsive {
            position: relative;
            display: block;
            width: 100%;
            padding: 0;
            overflow: hidden;
        }

        .embed-responsive::before {
            display: block;
            content: "";
        }

        .embed-responsive .embed-responsive-item,
        .embed-responsive iframe,
        .embed-responsive embed,
        .embed-responsive object,
        .embed-responsive video {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .embed-responsive-21by9::before {
            padding-top: 42.8571428571%;
        }

        .embed-responsive-16by9::before {
            padding-top: 56.25%;
        }

        .embed-responsive-4by3::before {
            padding-top: 75%;
        }

        .embed-responsive-1by1::before {
            padding-top: 100%;
        }

        .flex-row {
            flex-direction: row !important;
        }

        .flex-column {
            flex-direction: column !important;
        }

        .flex-row-reverse {
            flex-direction: row-reverse !important;
        }

        .flex-column-reverse {
            flex-direction: column-reverse !important;
        }

        .flex-wrap {
            flex-wrap: wrap !important;
        }

        .flex-nowrap {
            flex-wrap: nowrap !important;
        }

        .flex-wrap-reverse {
            flex-wrap: wrap-reverse !important;
        }

        .flex-fill {
            flex: 1 1 auto !important;
        }

        .flex-grow-0 {
            flex-grow: 0 !important;
        }

        .flex-grow-1 {
            flex-grow: 1 !important;
        }

        .flex-shrink-0 {
            flex-shrink: 0 !important;
        }

        .flex-shrink-1 {
            flex-shrink: 1 !important;
        }

        .justify-content-start {
            justify-content: flex-start !important;
        }

        .justify-content-end {
            justify-content: flex-end !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .justify-content-around {
            justify-content: space-around !important;
        }

        .align-items-start {
            align-items: flex-start !important;
        }

        .align-items-end {
            align-items: flex-end !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .align-items-baseline {
            align-items: baseline !important;
        }

        .align-items-stretch {
            align-items: stretch !important;
        }

        .align-content-start {
            align-content: flex-start !important;
        }

        .align-content-end {
            align-content: flex-end !important;
        }

        .align-content-center {
            align-content: center !important;
        }

        .align-content-between {
            align-content: space-between !important;
        }

        .align-content-around {
            align-content: space-around !important;
        }

        .align-content-stretch {
            align-content: stretch !important;
        }

        .align-self-auto {
            align-self: auto !important;
        }

        .align-self-start {
            align-self: flex-start !important;
        }

        .align-self-end {
            align-self: flex-end !important;
        }

        .align-self-center {
            align-self: center !important;
        }

        .align-self-baseline {
            align-self: baseline !important;
        }

        .align-self-stretch {
            align-self: stretch !important;
        }

        @media (min-width: 576px) {
            .flex-sm-row {
                flex-direction: row !important;
            }

            .flex-sm-column {
                flex-direction: column !important;
            }

            .flex-sm-row-reverse {
                flex-direction: row-reverse !important;
            }

            .flex-sm-column-reverse {
                flex-direction: column-reverse !important;
            }

            .flex-sm-wrap {
                flex-wrap: wrap !important;
            }

            .flex-sm-nowrap {
                flex-wrap: nowrap !important;
            }

            .flex-sm-wrap-reverse {
                flex-wrap: wrap-reverse !important;
            }

            .flex-sm-fill {
                flex: 1 1 auto !important;
            }

            .flex-sm-grow-0 {
                flex-grow: 0 !important;
            }

            .flex-sm-grow-1 {
                flex-grow: 1 !important;
            }

            .flex-sm-shrink-0 {
                flex-shrink: 0 !important;
            }

            .flex-sm-shrink-1 {
                flex-shrink: 1 !important;
            }

            .justify-content-sm-start {
                justify-content: flex-start !important;
            }

            .justify-content-sm-end {
                justify-content: flex-end !important;
            }

            .justify-content-sm-center {
                justify-content: center !important;
            }

            .justify-content-sm-between {
                justify-content: space-between !important;
            }

            .justify-content-sm-around {
                justify-content: space-around !important;
            }

            .align-items-sm-start {
                align-items: flex-start !important;
            }

            .align-items-sm-end {
                align-items: flex-end !important;
            }

            .align-items-sm-center {
                align-items: center !important;
            }

            .align-items-sm-baseline {
                align-items: baseline !important;
            }

            .align-items-sm-stretch {
                align-items: stretch !important;
            }

            .align-content-sm-start {
                align-content: flex-start !important;
            }

            .align-content-sm-end {
                align-content: flex-end !important;
            }

            .align-content-sm-center {
                align-content: center !important;
            }

            .align-content-sm-between {
                align-content: space-between !important;
            }

            .align-content-sm-around {
                align-content: space-around !important;
            }

            .align-content-sm-stretch {
                align-content: stretch !important;
            }

            .align-self-sm-auto {
                align-self: auto !important;
            }

            .align-self-sm-start {
                align-self: flex-start !important;
            }

            .align-self-sm-end {
                align-self: flex-end !important;
            }

            .align-self-sm-center {
                align-self: center !important;
            }

            .align-self-sm-baseline {
                align-self: baseline !important;
            }

            .align-self-sm-stretch {
                align-self: stretch !important;
            }
        }

        @media (min-width: 768px) {
            .flex-md-row {
                flex-direction: row !important;
            }

            .flex-md-column {
                flex-direction: column !important;
            }

            .flex-md-row-reverse {
                flex-direction: row-reverse !important;
            }

            .flex-md-column-reverse {
                flex-direction: column-reverse !important;
            }

            .flex-md-wrap {
                flex-wrap: wrap !important;
            }

            .flex-md-nowrap {
                flex-wrap: nowrap !important;
            }

            .flex-md-wrap-reverse {
                flex-wrap: wrap-reverse !important;
            }

            .flex-md-fill {
                flex: 1 1 auto !important;
            }

            .flex-md-grow-0 {
                flex-grow: 0 !important;
            }

            .flex-md-grow-1 {
                flex-grow: 1 !important;
            }

            .flex-md-shrink-0 {
                flex-shrink: 0 !important;
            }

            .flex-md-shrink-1 {
                flex-shrink: 1 !important;
            }

            .justify-content-md-start {
                justify-content: flex-start !important;
            }

            .justify-content-md-end {
                justify-content: flex-end !important;
            }

            .justify-content-md-center {
                justify-content: center !important;
            }

            .justify-content-md-between {
                justify-content: space-between !important;
            }

            .justify-content-md-around {
                justify-content: space-around !important;
            }

            .align-items-md-start {
                align-items: flex-start !important;
            }

            .align-items-md-end {
                align-items: flex-end !important;
            }

            .align-items-md-center {
                align-items: center !important;
            }

            .align-items-md-baseline {
                align-items: baseline !important;
            }

            .align-items-md-stretch {
                align-items: stretch !important;
            }

            .align-content-md-start {
                align-content: flex-start !important;
            }

            .align-content-md-end {
                align-content: flex-end !important;
            }

            .align-content-md-center {
                align-content: center !important;
            }

            .align-content-md-between {
                align-content: space-between !important;
            }

            .align-content-md-around {
                align-content: space-around !important;
            }

            .align-content-md-stretch {
                align-content: stretch !important;
            }

            .align-self-md-auto {
                align-self: auto !important;
            }

            .align-self-md-start {
                align-self: flex-start !important;
            }

            .align-self-md-end {
                align-self: flex-end !important;
            }

            .align-self-md-center {
                align-self: center !important;
            }

            .align-self-md-baseline {
                align-self: baseline !important;
            }

            .align-self-md-stretch {
                align-self: stretch !important;
            }
        }

        @media (min-width: 992px) {
            .flex-lg-row {
                flex-direction: row !important;
            }

            .flex-lg-column {
                flex-direction: column !important;
            }

            .flex-lg-row-reverse {
                flex-direction: row-reverse !important;
            }

            .flex-lg-column-reverse {
                flex-direction: column-reverse !important;
            }

            .flex-lg-wrap {
                flex-wrap: wrap !important;
            }

            .flex-lg-nowrap {
                flex-wrap: nowrap !important;
            }

            .flex-lg-wrap-reverse {
                flex-wrap: wrap-reverse !important;
            }

            .flex-lg-fill {
                flex: 1 1 auto !important;
            }

            .flex-lg-grow-0 {
                flex-grow: 0 !important;
            }

            .flex-lg-grow-1 {
                flex-grow: 1 !important;
            }

            .flex-lg-shrink-0 {
                flex-shrink: 0 !important;
            }

            .flex-lg-shrink-1 {
                flex-shrink: 1 !important;
            }

            .justify-content-lg-start {
                justify-content: flex-start !important;
            }

            .justify-content-lg-end {
                justify-content: flex-end !important;
            }

            .justify-content-lg-center {
                justify-content: center !important;
            }

            .justify-content-lg-between {
                justify-content: space-between !important;
            }

            .justify-content-lg-around {
                justify-content: space-around !important;
            }

            .align-items-lg-start {
                align-items: flex-start !important;
            }

            .align-items-lg-end {
                align-items: flex-end !important;
            }

            .align-items-lg-center {
                align-items: center !important;
            }

            .align-items-lg-baseline {
                align-items: baseline !important;
            }

            .align-items-lg-stretch {
                align-items: stretch !important;
            }

            .align-content-lg-start {
                align-content: flex-start !important;
            }

            .align-content-lg-end {
                align-content: flex-end !important;
            }

            .align-content-lg-center {
                align-content: center !important;
            }

            .align-content-lg-between {
                align-content: space-between !important;
            }

            .align-content-lg-around {
                align-content: space-around !important;
            }

            .align-content-lg-stretch {
                align-content: stretch !important;
            }

            .align-self-lg-auto {
                align-self: auto !important;
            }

            .align-self-lg-start {
                align-self: flex-start !important;
            }

            .align-self-lg-end {
                align-self: flex-end !important;
            }

            .align-self-lg-center {
                align-self: center !important;
            }

            .align-self-lg-baseline {
                align-self: baseline !important;
            }

            .align-self-lg-stretch {
                align-self: stretch !important;
            }
        }

        @media (min-width: 1200px) {
            .flex-xl-row {
                flex-direction: row !important;
            }

            .flex-xl-column {
                flex-direction: column !important;
            }

            .flex-xl-row-reverse {
                flex-direction: row-reverse !important;
            }

            .flex-xl-column-reverse {
                flex-direction: column-reverse !important;
            }

            .flex-xl-wrap {
                flex-wrap: wrap !important;
            }

            .flex-xl-nowrap {
                flex-wrap: nowrap !important;
            }

            .flex-xl-wrap-reverse {
                flex-wrap: wrap-reverse !important;
            }

            .flex-xl-fill {
                flex: 1 1 auto !important;
            }

            .flex-xl-grow-0 {
                flex-grow: 0 !important;
            }

            .flex-xl-grow-1 {
                flex-grow: 1 !important;
            }

            .flex-xl-shrink-0 {
                flex-shrink: 0 !important;
            }

            .flex-xl-shrink-1 {
                flex-shrink: 1 !important;
            }

            .justify-content-xl-start {
                justify-content: flex-start !important;
            }

            .justify-content-xl-end {
                justify-content: flex-end !important;
            }

            .justify-content-xl-center {
                justify-content: center !important;
            }

            .justify-content-xl-between {
                justify-content: space-between !important;
            }

            .justify-content-xl-around {
                justify-content: space-around !important;
            }

            .align-items-xl-start {
                align-items: flex-start !important;
            }

            .align-items-xl-end {
                align-items: flex-end !important;
            }

            .align-items-xl-center {
                align-items: center !important;
            }

            .align-items-xl-baseline {
                align-items: baseline !important;
            }

            .align-items-xl-stretch {
                align-items: stretch !important;
            }

            .align-content-xl-start {
                align-content: flex-start !important;
            }

            .align-content-xl-end {
                align-content: flex-end !important;
            }

            .align-content-xl-center {
                align-content: center !important;
            }

            .align-content-xl-between {
                align-content: space-between !important;
            }

            .align-content-xl-around {
                align-content: space-around !important;
            }

            .align-content-xl-stretch {
                align-content: stretch !important;
            }

            .align-self-xl-auto {
                align-self: auto !important;
            }

            .align-self-xl-start {
                align-self: flex-start !important;
            }

            .align-self-xl-end {
                align-self: flex-end !important;
            }

            .align-self-xl-center {
                align-self: center !important;
            }

            .align-self-xl-baseline {
                align-self: baseline !important;
            }

            .align-self-xl-stretch {
                align-self: stretch !important;
            }
        }

        .float-left {
            float: left !important;
        }

        .float-right {
            float: right !important;
        }

        .float-none {
            float: none !important;
        }

        @media (min-width: 576px) {
            .float-sm-left {
                float: left !important;
            }

            .float-sm-right {
                float: right !important;
            }

            .float-sm-none {
                float: none !important;
            }
        }

        @media (min-width: 768px) {
            .float-md-left {
                float: left !important;
            }

            .float-md-right {
                float: right !important;
            }

            .float-md-none {
                float: none !important;
            }
        }

        @media (min-width: 992px) {
            .float-lg-left {
                float: left !important;
            }

            .float-lg-right {
                float: right !important;
            }

            .float-lg-none {
                float: none !important;
            }
        }

        @media (min-width: 1200px) {
            .float-xl-left {
                float: left !important;
            }

            .float-xl-right {
                float: right !important;
            }

            .float-xl-none {
                float: none !important;
            }
        }

        .overflow-auto {
            overflow: auto !important;
        }

        .overflow-hidden {
            overflow: hidden !important;
        }

        .position-static {
            position: static !important;
        }

        .position-relative {
            position: relative !important;
        }

        .position-absolute {
            position: absolute !important;
        }

        .position-fixed {
            position: fixed !important;
        }

        .position-sticky {
            position: -webkit-sticky !important;
            position: sticky !important;
        }

        .fixed-top {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1030;
        }

        .fixed-bottom {
            position: fixed;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
        }

        @supports ((position: -webkit-sticky) or (position: sticky)) {
            .sticky-top {
                position: -webkit-sticky;
                position: sticky;
                top: 0;
                z-index: 1020;
            }
        }

        .sr-only,
        .bootstrap-datetimepicker-widget table th.next::after,
        .bootstrap-datetimepicker-widget table th.prev::after,
        .bootstrap-datetimepicker-widget .picker-switch::after,
        .bootstrap-datetimepicker-widget .btn[data-action=today]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=clear]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=togglePeriod]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=showMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=showHours]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=decrementMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=decrementHours]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=incrementMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=incrementHours]::after {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .sr-only-focusable:active,
        .sr-only-focusable:focus {
            position: static;
            width: auto;
            height: auto;
            overflow: visible;
            clip: auto;
            white-space: normal;
        }

        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .shadow-lg {
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        }

        .shadow-none {
            box-shadow: none !important;
        }

        .w-25 {
            width: 25% !important;
        }

        .w-50 {
            width: 50% !important;
        }

        .w-75 {
            width: 75% !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .w-auto {
            width: auto !important;
        }

        .h-25 {
            height: 25% !important;
        }

        .h-50 {
            height: 50% !important;
        }

        .h-75 {
            height: 75% !important;
        }

        .h-100 {
            height: 100% !important;
        }

        .h-auto {
            height: auto !important;
        }

        .mw-100 {
            max-width: 100% !important;
        }

        .mh-100 {
            max-height: 100% !important;
        }

        .min-vw-100 {
            min-width: 100vw !important;
        }

        .min-vh-100 {
            min-height: 100vh !important;
        }

        .vw-100 {
            width: 100vw !important;
        }

        .vh-100 {
            height: 100vh !important;
        }

        .stretched-link::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1;
            pointer-events: auto;
            content: "";
            background-color: rgba(0, 0, 0, 0);
        }

        .m-0 {
            margin: 0 !important;
        }

        .mt-0,
        .my-0 {
            margin-top: 0 !important;
        }

        .mr-0,
        .mx-0 {
            margin-right: 0 !important;
        }

        .mb-0,
        .my-0 {
            margin-bottom: 0 !important;
        }

        .ml-0,
        .mx-0 {
            margin-left: 0 !important;
        }

        .m-1 {
            margin: 0.25rem !important;
        }

        .mt-1,
        .my-1 {
            margin-top: 0.25rem !important;
        }

        .mr-1,
        .mx-1 {
            margin-right: 0.25rem !important;
        }

        .mb-1,
        .my-1 {
            margin-bottom: 0.25rem !important;
        }

        .ml-1,
        .mx-1 {
            margin-left: 0.25rem !important;
        }

        .m-2 {
            margin: 0.5rem !important;
        }

        .mt-2,
        .my-2 {
            margin-top: 0.5rem !important;
        }

        .mr-2,
        .mx-2 {
            margin-right: 0.5rem !important;
        }

        .mb-2,
        .my-2 {
            margin-bottom: 0.5rem !important;
        }

        .ml-2,
        .mx-2 {
            margin-left: 0.5rem !important;
        }

        .m-3 {
            margin: 1rem !important;
        }

        .mt-3,
        .my-3 {
            margin-top: 1rem !important;
        }

        .mr-3,
        .mx-3 {
            margin-right: 1rem !important;
        }

        .mb-3,
        .my-3 {
            margin-bottom: 1rem !important;
        }

        .ml-3,
        .mx-3 {
            margin-left: 1rem !important;
        }

        .m-4 {
            margin: 1.5rem !important;
        }

        .mt-4,
        .my-4 {
            margin-top: 1.5rem !important;
        }

        .mr-4,
        .mx-4 {
            margin-right: 1.5rem !important;
        }

        .mb-4,
        .my-4 {
            margin-bottom: 1.5rem !important;
        }

        .ml-4,
        .mx-4 {
            margin-left: 1.5rem !important;
        }

        .m-5 {
            margin: 3rem !important;
        }

        .mt-5,
        .my-5 {
            margin-top: 3rem !important;
        }

        .mr-5,
        .mx-5 {
            margin-right: 3rem !important;
        }

        .mb-5,
        .my-5 {
            margin-bottom: 3rem !important;
        }

        .ml-5,
        .mx-5 {
            margin-left: 3rem !important;
        }

        .p-0 {
            padding: 0 !important;
        }

        .pt-0,
        .py-0 {
            padding-top: 0 !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pb-0,
        .py-0 {
            padding-bottom: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .p-1 {
            padding: 0.25rem !important;
        }

        .pt-1,
        .py-1 {
            padding-top: 0.25rem !important;
        }

        .pr-1,
        .px-1 {
            padding-right: 0.25rem !important;
        }

        .pb-1,
        .py-1 {
            padding-bottom: 0.25rem !important;
        }

        .pl-1,
        .px-1 {
            padding-left: 0.25rem !important;
        }

        .p-2 {
            padding: 0.5rem !important;
        }

        .pt-2,
        .py-2 {
            padding-top: 0.5rem !important;
        }

        .pr-2,
        .px-2 {
            padding-right: 0.5rem !important;
        }

        .pb-2,
        .py-2 {
            padding-bottom: 0.5rem !important;
        }

        .pl-2,
        .px-2 {
            padding-left: 0.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .pt-3,
        .py-3 {
            padding-top: 1rem !important;
        }

        .pr-3,
        .px-3 {
            padding-right: 1rem !important;
        }

        .pb-3,
        .py-3 {
            padding-bottom: 1rem !important;
        }

        .pl-3,
        .px-3 {
            padding-left: 1rem !important;
        }

        .p-4 {
            padding: 1.5rem !important;
        }

        .pt-4,
        .py-4 {
            padding-top: 1.5rem !important;
        }

        .pr-4,
        .px-4 {
            padding-right: 1.5rem !important;
        }

        .pb-4,
        .py-4 {
            padding-bottom: 1.5rem !important;
        }

        .pl-4,
        .px-4 {
            padding-left: 1.5rem !important;
        }

        .p-5 {
            padding: 3rem !important;
        }

        .pt-5,
        .py-5 {
            padding-top: 3rem !important;
        }

        .pr-5,
        .px-5 {
            padding-right: 3rem !important;
        }

        .pb-5,
        .py-5 {
            padding-bottom: 3rem !important;
        }

        .pl-5,
        .px-5 {
            padding-left: 3rem !important;
        }

        .m-n1 {
            margin: -0.25rem !important;
        }

        .mt-n1,
        .my-n1 {
            margin-top: -0.25rem !important;
        }

        .mr-n1,
        .mx-n1 {
            margin-right: -0.25rem !important;
        }

        .mb-n1,
        .my-n1 {
            margin-bottom: -0.25rem !important;
        }

        .ml-n1,
        .mx-n1 {
            margin-left: -0.25rem !important;
        }

        .m-n2 {
            margin: -0.5rem !important;
        }

        .mt-n2,
        .my-n2 {
            margin-top: -0.5rem !important;
        }

        .mr-n2,
        .mx-n2 {
            margin-right: -0.5rem !important;
        }

        .mb-n2,
        .my-n2 {
            margin-bottom: -0.5rem !important;
        }

        .ml-n2,
        .mx-n2 {
            margin-left: -0.5rem !important;
        }

        .m-n3 {
            margin: -1rem !important;
        }

        .mt-n3,
        .my-n3 {
            margin-top: -1rem !important;
        }

        .mr-n3,
        .mx-n3 {
            margin-right: -1rem !important;
        }

        .mb-n3,
        .my-n3 {
            margin-bottom: -1rem !important;
        }

        .ml-n3,
        .mx-n3 {
            margin-left: -1rem !important;
        }

        .m-n4 {
            margin: -1.5rem !important;
        }

        .mt-n4,
        .my-n4 {
            margin-top: -1.5rem !important;
        }

        .mr-n4,
        .mx-n4 {
            margin-right: -1.5rem !important;
        }

        .mb-n4,
        .my-n4 {
            margin-bottom: -1.5rem !important;
        }

        .ml-n4,
        .mx-n4 {
            margin-left: -1.5rem !important;
        }

        .m-n5 {
            margin: -3rem !important;
        }

        .mt-n5,
        .my-n5 {
            margin-top: -3rem !important;
        }

        .mr-n5,
        .mx-n5 {
            margin-right: -3rem !important;
        }

        .mb-n5,
        .my-n5 {
            margin-bottom: -3rem !important;
        }

        .ml-n5,
        .mx-n5 {
            margin-left: -3rem !important;
        }

        .m-auto {
            margin: auto !important;
        }

        .mt-auto,
        .my-auto {
            margin-top: auto !important;
        }

        .mr-auto,
        .mx-auto {
            margin-right: auto !important;
        }

        .mb-auto,
        .my-auto {
            margin-bottom: auto !important;
        }

        .ml-auto,
        .mx-auto {
            margin-left: auto !important;
        }

        @media (min-width: 576px) {
            .m-sm-0 {
                margin: 0 !important;
            }

            .mt-sm-0,
            .my-sm-0 {
                margin-top: 0 !important;
            }

            .mr-sm-0,
            .mx-sm-0 {
                margin-right: 0 !important;
            }

            .mb-sm-0,
            .my-sm-0 {
                margin-bottom: 0 !important;
            }

            .ml-sm-0,
            .mx-sm-0 {
                margin-left: 0 !important;
            }

            .m-sm-1 {
                margin: 0.25rem !important;
            }

            .mt-sm-1,
            .my-sm-1 {
                margin-top: 0.25rem !important;
            }

            .mr-sm-1,
            .mx-sm-1 {
                margin-right: 0.25rem !important;
            }

            .mb-sm-1,
            .my-sm-1 {
                margin-bottom: 0.25rem !important;
            }

            .ml-sm-1,
            .mx-sm-1 {
                margin-left: 0.25rem !important;
            }

            .m-sm-2 {
                margin: 0.5rem !important;
            }

            .mt-sm-2,
            .my-sm-2 {
                margin-top: 0.5rem !important;
            }

            .mr-sm-2,
            .mx-sm-2 {
                margin-right: 0.5rem !important;
            }

            .mb-sm-2,
            .my-sm-2 {
                margin-bottom: 0.5rem !important;
            }

            .ml-sm-2,
            .mx-sm-2 {
                margin-left: 0.5rem !important;
            }

            .m-sm-3 {
                margin: 1rem !important;
            }

            .mt-sm-3,
            .my-sm-3 {
                margin-top: 1rem !important;
            }

            .mr-sm-3,
            .mx-sm-3 {
                margin-right: 1rem !important;
            }

            .mb-sm-3,
            .my-sm-3 {
                margin-bottom: 1rem !important;
            }

            .ml-sm-3,
            .mx-sm-3 {
                margin-left: 1rem !important;
            }

            .m-sm-4 {
                margin: 1.5rem !important;
            }

            .mt-sm-4,
            .my-sm-4 {
                margin-top: 1.5rem !important;
            }

            .mr-sm-4,
            .mx-sm-4 {
                margin-right: 1.5rem !important;
            }

            .mb-sm-4,
            .my-sm-4 {
                margin-bottom: 1.5rem !important;
            }

            .ml-sm-4,
            .mx-sm-4 {
                margin-left: 1.5rem !important;
            }

            .m-sm-5 {
                margin: 3rem !important;
            }

            .mt-sm-5,
            .my-sm-5 {
                margin-top: 3rem !important;
            }

            .mr-sm-5,
            .mx-sm-5 {
                margin-right: 3rem !important;
            }

            .mb-sm-5,
            .my-sm-5 {
                margin-bottom: 3rem !important;
            }

            .ml-sm-5,
            .mx-sm-5 {
                margin-left: 3rem !important;
            }

            .p-sm-0 {
                padding: 0 !important;
            }

            .pt-sm-0,
            .py-sm-0 {
                padding-top: 0 !important;
            }

            .pr-sm-0,
            .px-sm-0 {
                padding-right: 0 !important;
            }

            .pb-sm-0,
            .py-sm-0 {
                padding-bottom: 0 !important;
            }

            .pl-sm-0,
            .px-sm-0 {
                padding-left: 0 !important;
            }

            .p-sm-1 {
                padding: 0.25rem !important;
            }

            .pt-sm-1,
            .py-sm-1 {
                padding-top: 0.25rem !important;
            }

            .pr-sm-1,
            .px-sm-1 {
                padding-right: 0.25rem !important;
            }

            .pb-sm-1,
            .py-sm-1 {
                padding-bottom: 0.25rem !important;
            }

            .pl-sm-1,
            .px-sm-1 {
                padding-left: 0.25rem !important;
            }

            .p-sm-2 {
                padding: 0.5rem !important;
            }

            .pt-sm-2,
            .py-sm-2 {
                padding-top: 0.5rem !important;
            }

            .pr-sm-2,
            .px-sm-2 {
                padding-right: 0.5rem !important;
            }

            .pb-sm-2,
            .py-sm-2 {
                padding-bottom: 0.5rem !important;
            }

            .pl-sm-2,
            .px-sm-2 {
                padding-left: 0.5rem !important;
            }

            .p-sm-3 {
                padding: 1rem !important;
            }

            .pt-sm-3,
            .py-sm-3 {
                padding-top: 1rem !important;
            }

            .pr-sm-3,
            .px-sm-3 {
                padding-right: 1rem !important;
            }

            .pb-sm-3,
            .py-sm-3 {
                padding-bottom: 1rem !important;
            }

            .pl-sm-3,
            .px-sm-3 {
                padding-left: 1rem !important;
            }

            .p-sm-4 {
                padding: 1.5rem !important;
            }

            .pt-sm-4,
            .py-sm-4 {
                padding-top: 1.5rem !important;
            }

            .pr-sm-4,
            .px-sm-4 {
                padding-right: 1.5rem !important;
            }

            .pb-sm-4,
            .py-sm-4 {
                padding-bottom: 1.5rem !important;
            }

            .pl-sm-4,
            .px-sm-4 {
                padding-left: 1.5rem !important;
            }

            .p-sm-5 {
                padding: 3rem !important;
            }

            .pt-sm-5,
            .py-sm-5 {
                padding-top: 3rem !important;
            }

            .pr-sm-5,
            .px-sm-5 {
                padding-right: 3rem !important;
            }

            .pb-sm-5,
            .py-sm-5 {
                padding-bottom: 3rem !important;
            }

            .pl-sm-5,
            .px-sm-5 {
                padding-left: 3rem !important;
            }

            .m-sm-n1 {
                margin: -0.25rem !important;
            }

            .mt-sm-n1,
            .my-sm-n1 {
                margin-top: -0.25rem !important;
            }

            .mr-sm-n1,
            .mx-sm-n1 {
                margin-right: -0.25rem !important;
            }

            .mb-sm-n1,
            .my-sm-n1 {
                margin-bottom: -0.25rem !important;
            }

            .ml-sm-n1,
            .mx-sm-n1 {
                margin-left: -0.25rem !important;
            }

            .m-sm-n2 {
                margin: -0.5rem !important;
            }

            .mt-sm-n2,
            .my-sm-n2 {
                margin-top: -0.5rem !important;
            }

            .mr-sm-n2,
            .mx-sm-n2 {
                margin-right: -0.5rem !important;
            }

            .mb-sm-n2,
            .my-sm-n2 {
                margin-bottom: -0.5rem !important;
            }

            .ml-sm-n2,
            .mx-sm-n2 {
                margin-left: -0.5rem !important;
            }

            .m-sm-n3 {
                margin: -1rem !important;
            }

            .mt-sm-n3,
            .my-sm-n3 {
                margin-top: -1rem !important;
            }

            .mr-sm-n3,
            .mx-sm-n3 {
                margin-right: -1rem !important;
            }

            .mb-sm-n3,
            .my-sm-n3 {
                margin-bottom: -1rem !important;
            }

            .ml-sm-n3,
            .mx-sm-n3 {
                margin-left: -1rem !important;
            }

            .m-sm-n4 {
                margin: -1.5rem !important;
            }

            .mt-sm-n4,
            .my-sm-n4 {
                margin-top: -1.5rem !important;
            }

            .mr-sm-n4,
            .mx-sm-n4 {
                margin-right: -1.5rem !important;
            }

            .mb-sm-n4,
            .my-sm-n4 {
                margin-bottom: -1.5rem !important;
            }

            .ml-sm-n4,
            .mx-sm-n4 {
                margin-left: -1.5rem !important;
            }

            .m-sm-n5 {
                margin: -3rem !important;
            }

            .mt-sm-n5,
            .my-sm-n5 {
                margin-top: -3rem !important;
            }

            .mr-sm-n5,
            .mx-sm-n5 {
                margin-right: -3rem !important;
            }

            .mb-sm-n5,
            .my-sm-n5 {
                margin-bottom: -3rem !important;
            }

            .ml-sm-n5,
            .mx-sm-n5 {
                margin-left: -3rem !important;
            }

            .m-sm-auto {
                margin: auto !important;
            }

            .mt-sm-auto,
            .my-sm-auto {
                margin-top: auto !important;
            }

            .mr-sm-auto,
            .mx-sm-auto {
                margin-right: auto !important;
            }

            .mb-sm-auto,
            .my-sm-auto {
                margin-bottom: auto !important;
            }

            .ml-sm-auto,
            .mx-sm-auto {
                margin-left: auto !important;
            }
        }

        @media (min-width: 768px) {
            .m-md-0 {
                margin: 0 !important;
            }

            .mt-md-0,
            .my-md-0 {
                margin-top: 0 !important;
            }

            .mr-md-0,
            .mx-md-0 {
                margin-right: 0 !important;
            }

            .mb-md-0,
            .my-md-0 {
                margin-bottom: 0 !important;
            }

            .ml-md-0,
            .mx-md-0 {
                margin-left: 0 !important;
            }

            .m-md-1 {
                margin: 0.25rem !important;
            }

            .mt-md-1,
            .my-md-1 {
                margin-top: 0.25rem !important;
            }

            .mr-md-1,
            .mx-md-1 {
                margin-right: 0.25rem !important;
            }

            .mb-md-1,
            .my-md-1 {
                margin-bottom: 0.25rem !important;
            }

            .ml-md-1,
            .mx-md-1 {
                margin-left: 0.25rem !important;
            }

            .m-md-2 {
                margin: 0.5rem !important;
            }

            .mt-md-2,
            .my-md-2 {
                margin-top: 0.5rem !important;
            }

            .mr-md-2,
            .mx-md-2 {
                margin-right: 0.5rem !important;
            }

            .mb-md-2,
            .my-md-2 {
                margin-bottom: 0.5rem !important;
            }

            .ml-md-2,
            .mx-md-2 {
                margin-left: 0.5rem !important;
            }

            .m-md-3 {
                margin: 1rem !important;
            }

            .mt-md-3,
            .my-md-3 {
                margin-top: 1rem !important;
            }

            .mr-md-3,
            .mx-md-3 {
                margin-right: 1rem !important;
            }

            .mb-md-3,
            .my-md-3 {
                margin-bottom: 1rem !important;
            }

            .ml-md-3,
            .mx-md-3 {
                margin-left: 1rem !important;
            }

            .m-md-4 {
                margin: 1.5rem !important;
            }

            .mt-md-4,
            .my-md-4 {
                margin-top: 1.5rem !important;
            }

            .mr-md-4,
            .mx-md-4 {
                margin-right: 1.5rem !important;
            }

            .mb-md-4,
            .my-md-4 {
                margin-bottom: 1.5rem !important;
            }

            .ml-md-4,
            .mx-md-4 {
                margin-left: 1.5rem !important;
            }

            .m-md-5 {
                margin: 3rem !important;
            }

            .mt-md-5,
            .my-md-5 {
                margin-top: 3rem !important;
            }

            .mr-md-5,
            .mx-md-5 {
                margin-right: 3rem !important;
            }

            .mb-md-5,
            .my-md-5 {
                margin-bottom: 3rem !important;
            }

            .ml-md-5,
            .mx-md-5 {
                margin-left: 3rem !important;
            }

            .p-md-0 {
                padding: 0 !important;
            }

            .pt-md-0,
            .py-md-0 {
                padding-top: 0 !important;
            }

            .pr-md-0,
            .px-md-0 {
                padding-right: 0 !important;
            }

            .pb-md-0,
            .py-md-0 {
                padding-bottom: 0 !important;
            }

            .pl-md-0,
            .px-md-0 {
                padding-left: 0 !important;
            }

            .p-md-1 {
                padding: 0.25rem !important;
            }

            .pt-md-1,
            .py-md-1 {
                padding-top: 0.25rem !important;
            }

            .pr-md-1,
            .px-md-1 {
                padding-right: 0.25rem !important;
            }

            .pb-md-1,
            .py-md-1 {
                padding-bottom: 0.25rem !important;
            }

            .pl-md-1,
            .px-md-1 {
                padding-left: 0.25rem !important;
            }

            .p-md-2 {
                padding: 0.5rem !important;
            }

            .pt-md-2,
            .py-md-2 {
                padding-top: 0.5rem !important;
            }

            .pr-md-2,
            .px-md-2 {
                padding-right: 0.5rem !important;
            }

            .pb-md-2,
            .py-md-2 {
                padding-bottom: 0.5rem !important;
            }

            .pl-md-2,
            .px-md-2 {
                padding-left: 0.5rem !important;
            }

            .p-md-3 {
                padding: 1rem !important;
            }

            .pt-md-3,
            .py-md-3 {
                padding-top: 1rem !important;
            }

            .pr-md-3,
            .px-md-3 {
                padding-right: 1rem !important;
            }

            .pb-md-3,
            .py-md-3 {
                padding-bottom: 1rem !important;
            }

            .pl-md-3,
            .px-md-3 {
                padding-left: 1rem !important;
            }

            .p-md-4 {
                padding: 1.5rem !important;
            }

            .pt-md-4,
            .py-md-4 {
                padding-top: 1.5rem !important;
            }

            .pr-md-4,
            .px-md-4 {
                padding-right: 1.5rem !important;
            }

            .pb-md-4,
            .py-md-4 {
                padding-bottom: 1.5rem !important;
            }

            .pl-md-4,
            .px-md-4 {
                padding-left: 1.5rem !important;
            }

            .p-md-5 {
                padding: 3rem !important;
            }

            .pt-md-5,
            .py-md-5 {
                padding-top: 3rem !important;
            }

            .pr-md-5,
            .px-md-5 {
                padding-right: 3rem !important;
            }

            .pb-md-5,
            .py-md-5 {
                padding-bottom: 3rem !important;
            }

            .pl-md-5,
            .px-md-5 {
                padding-left: 3rem !important;
            }

            .m-md-n1 {
                margin: -0.25rem !important;
            }

            .mt-md-n1,
            .my-md-n1 {
                margin-top: -0.25rem !important;
            }

            .mr-md-n1,
            .mx-md-n1 {
                margin-right: -0.25rem !important;
            }

            .mb-md-n1,
            .my-md-n1 {
                margin-bottom: -0.25rem !important;
            }

            .ml-md-n1,
            .mx-md-n1 {
                margin-left: -0.25rem !important;
            }

            .m-md-n2 {
                margin: -0.5rem !important;
            }

            .mt-md-n2,
            .my-md-n2 {
                margin-top: -0.5rem !important;
            }

            .mr-md-n2,
            .mx-md-n2 {
                margin-right: -0.5rem !important;
            }

            .mb-md-n2,
            .my-md-n2 {
                margin-bottom: -0.5rem !important;
            }

            .ml-md-n2,
            .mx-md-n2 {
                margin-left: -0.5rem !important;
            }

            .m-md-n3 {
                margin: -1rem !important;
            }

            .mt-md-n3,
            .my-md-n3 {
                margin-top: -1rem !important;
            }

            .mr-md-n3,
            .mx-md-n3 {
                margin-right: -1rem !important;
            }

            .mb-md-n3,
            .my-md-n3 {
                margin-bottom: -1rem !important;
            }

            .ml-md-n3,
            .mx-md-n3 {
                margin-left: -1rem !important;
            }

            .m-md-n4 {
                margin: -1.5rem !important;
            }

            .mt-md-n4,
            .my-md-n4 {
                margin-top: -1.5rem !important;
            }

            .mr-md-n4,
            .mx-md-n4 {
                margin-right: -1.5rem !important;
            }

            .mb-md-n4,
            .my-md-n4 {
                margin-bottom: -1.5rem !important;
            }

            .ml-md-n4,
            .mx-md-n4 {
                margin-left: -1.5rem !important;
            }

            .m-md-n5 {
                margin: -3rem !important;
            }

            .mt-md-n5,
            .my-md-n5 {
                margin-top: -3rem !important;
            }

            .mr-md-n5,
            .mx-md-n5 {
                margin-right: -3rem !important;
            }

            .mb-md-n5,
            .my-md-n5 {
                margin-bottom: -3rem !important;
            }

            .ml-md-n5,
            .mx-md-n5 {
                margin-left: -3rem !important;
            }

            .m-md-auto {
                margin: auto !important;
            }

            .mt-md-auto,
            .my-md-auto {
                margin-top: auto !important;
            }

            .mr-md-auto,
            .mx-md-auto {
                margin-right: auto !important;
            }

            .mb-md-auto,
            .my-md-auto {
                margin-bottom: auto !important;
            }

            .ml-md-auto,
            .mx-md-auto {
                margin-left: auto !important;
            }
        }

        @media (min-width: 992px) {
            .m-lg-0 {
                margin: 0 !important;
            }

            .mt-lg-0,
            .my-lg-0 {
                margin-top: 0 !important;
            }

            .mr-lg-0,
            .mx-lg-0 {
                margin-right: 0 !important;
            }

            .mb-lg-0,
            .my-lg-0 {
                margin-bottom: 0 !important;
            }

            .ml-lg-0,
            .mx-lg-0 {
                margin-left: 0 !important;
            }

            .m-lg-1 {
                margin: 0.25rem !important;
            }

            .mt-lg-1,
            .my-lg-1 {
                margin-top: 0.25rem !important;
            }

            .mr-lg-1,
            .mx-lg-1 {
                margin-right: 0.25rem !important;
            }

            .mb-lg-1,
            .my-lg-1 {
                margin-bottom: 0.25rem !important;
            }

            .ml-lg-1,
            .mx-lg-1 {
                margin-left: 0.25rem !important;
            }

            .m-lg-2 {
                margin: 0.5rem !important;
            }

            .mt-lg-2,
            .my-lg-2 {
                margin-top: 0.5rem !important;
            }

            .mr-lg-2,
            .mx-lg-2 {
                margin-right: 0.5rem !important;
            }

            .mb-lg-2,
            .my-lg-2 {
                margin-bottom: 0.5rem !important;
            }

            .ml-lg-2,
            .mx-lg-2 {
                margin-left: 0.5rem !important;
            }

            .m-lg-3 {
                margin: 1rem !important;
            }

            .mt-lg-3,
            .my-lg-3 {
                margin-top: 1rem !important;
            }

            .mr-lg-3,
            .mx-lg-3 {
                margin-right: 1rem !important;
            }

            .mb-lg-3,
            .my-lg-3 {
                margin-bottom: 1rem !important;
            }

            .ml-lg-3,
            .mx-lg-3 {
                margin-left: 1rem !important;
            }

            .m-lg-4 {
                margin: 1.5rem !important;
            }

            .mt-lg-4,
            .my-lg-4 {
                margin-top: 1.5rem !important;
            }

            .mr-lg-4,
            .mx-lg-4 {
                margin-right: 1.5rem !important;
            }

            .mb-lg-4,
            .my-lg-4 {
                margin-bottom: 1.5rem !important;
            }

            .ml-lg-4,
            .mx-lg-4 {
                margin-left: 1.5rem !important;
            }

            .m-lg-5 {
                margin: 3rem !important;
            }

            .mt-lg-5,
            .my-lg-5 {
                margin-top: 3rem !important;
            }

            .mr-lg-5,
            .mx-lg-5 {
                margin-right: 3rem !important;
            }

            .mb-lg-5,
            .my-lg-5 {
                margin-bottom: 3rem !important;
            }

            .ml-lg-5,
            .mx-lg-5 {
                margin-left: 3rem !important;
            }

            .p-lg-0 {
                padding: 0 !important;
            }

            .pt-lg-0,
            .py-lg-0 {
                padding-top: 0 !important;
            }

            .pr-lg-0,
            .px-lg-0 {
                padding-right: 0 !important;
            }

            .pb-lg-0,
            .py-lg-0 {
                padding-bottom: 0 !important;
            }

            .pl-lg-0,
            .px-lg-0 {
                padding-left: 0 !important;
            }

            .p-lg-1 {
                padding: 0.25rem !important;
            }

            .pt-lg-1,
            .py-lg-1 {
                padding-top: 0.25rem !important;
            }

            .pr-lg-1,
            .px-lg-1 {
                padding-right: 0.25rem !important;
            }

            .pb-lg-1,
            .py-lg-1 {
                padding-bottom: 0.25rem !important;
            }

            .pl-lg-1,
            .px-lg-1 {
                padding-left: 0.25rem !important;
            }

            .p-lg-2 {
                padding: 0.5rem !important;
            }

            .pt-lg-2,
            .py-lg-2 {
                padding-top: 0.5rem !important;
            }

            .pr-lg-2,
            .px-lg-2 {
                padding-right: 0.5rem !important;
            }

            .pb-lg-2,
            .py-lg-2 {
                padding-bottom: 0.5rem !important;
            }

            .pl-lg-2,
            .px-lg-2 {
                padding-left: 0.5rem !important;
            }

            .p-lg-3 {
                padding: 1rem !important;
            }

            .pt-lg-3,
            .py-lg-3 {
                padding-top: 1rem !important;
            }

            .pr-lg-3,
            .px-lg-3 {
                padding-right: 1rem !important;
            }

            .pb-lg-3,
            .py-lg-3 {
                padding-bottom: 1rem !important;
            }

            .pl-lg-3,
            .px-lg-3 {
                padding-left: 1rem !important;
            }

            .p-lg-4 {
                padding: 1.5rem !important;
            }

            .pt-lg-4,
            .py-lg-4 {
                padding-top: 1.5rem !important;
            }

            .pr-lg-4,
            .px-lg-4 {
                padding-right: 1.5rem !important;
            }

            .pb-lg-4,
            .py-lg-4 {
                padding-bottom: 1.5rem !important;
            }

            .pl-lg-4,
            .px-lg-4 {
                padding-left: 1.5rem !important;
            }

            .p-lg-5 {
                padding: 3rem !important;
            }

            .pt-lg-5,
            .py-lg-5 {
                padding-top: 3rem !important;
            }

            .pr-lg-5,
            .px-lg-5 {
                padding-right: 3rem !important;
            }

            .pb-lg-5,
            .py-lg-5 {
                padding-bottom: 3rem !important;
            }

            .pl-lg-5,
            .px-lg-5 {
                padding-left: 3rem !important;
            }

            .m-lg-n1 {
                margin: -0.25rem !important;
            }

            .mt-lg-n1,
            .my-lg-n1 {
                margin-top: -0.25rem !important;
            }

            .mr-lg-n1,
            .mx-lg-n1 {
                margin-right: -0.25rem !important;
            }

            .mb-lg-n1,
            .my-lg-n1 {
                margin-bottom: -0.25rem !important;
            }

            .ml-lg-n1,
            .mx-lg-n1 {
                margin-left: -0.25rem !important;
            }

            .m-lg-n2 {
                margin: -0.5rem !important;
            }

            .mt-lg-n2,
            .my-lg-n2 {
                margin-top: -0.5rem !important;
            }

            .mr-lg-n2,
            .mx-lg-n2 {
                margin-right: -0.5rem !important;
            }

            .mb-lg-n2,
            .my-lg-n2 {
                margin-bottom: -0.5rem !important;
            }

            .ml-lg-n2,
            .mx-lg-n2 {
                margin-left: -0.5rem !important;
            }

            .m-lg-n3 {
                margin: -1rem !important;
            }

            .mt-lg-n3,
            .my-lg-n3 {
                margin-top: -1rem !important;
            }

            .mr-lg-n3,
            .mx-lg-n3 {
                margin-right: -1rem !important;
            }

            .mb-lg-n3,
            .my-lg-n3 {
                margin-bottom: -1rem !important;
            }

            .ml-lg-n3,
            .mx-lg-n3 {
                margin-left: -1rem !important;
            }

            .m-lg-n4 {
                margin: -1.5rem !important;
            }

            .mt-lg-n4,
            .my-lg-n4 {
                margin-top: -1.5rem !important;
            }

            .mr-lg-n4,
            .mx-lg-n4 {
                margin-right: -1.5rem !important;
            }

            .mb-lg-n4,
            .my-lg-n4 {
                margin-bottom: -1.5rem !important;
            }

            .ml-lg-n4,
            .mx-lg-n4 {
                margin-left: -1.5rem !important;
            }

            .m-lg-n5 {
                margin: -3rem !important;
            }

            .mt-lg-n5,
            .my-lg-n5 {
                margin-top: -3rem !important;
            }

            .mr-lg-n5,
            .mx-lg-n5 {
                margin-right: -3rem !important;
            }

            .mb-lg-n5,
            .my-lg-n5 {
                margin-bottom: -3rem !important;
            }

            .ml-lg-n5,
            .mx-lg-n5 {
                margin-left: -3rem !important;
            }

            .m-lg-auto {
                margin: auto !important;
            }

            .mt-lg-auto,
            .my-lg-auto {
                margin-top: auto !important;
            }

            .mr-lg-auto,
            .mx-lg-auto {
                margin-right: auto !important;
            }

            .mb-lg-auto,
            .my-lg-auto {
                margin-bottom: auto !important;
            }

            .ml-lg-auto,
            .mx-lg-auto {
                margin-left: auto !important;
            }
        }

        @media (min-width: 1200px) {
            .m-xl-0 {
                margin: 0 !important;
            }

            .mt-xl-0,
            .my-xl-0 {
                margin-top: 0 !important;
            }

            .mr-xl-0,
            .mx-xl-0 {
                margin-right: 0 !important;
            }

            .mb-xl-0,
            .my-xl-0 {
                margin-bottom: 0 !important;
            }

            .ml-xl-0,
            .mx-xl-0 {
                margin-left: 0 !important;
            }

            .m-xl-1 {
                margin: 0.25rem !important;
            }

            .mt-xl-1,
            .my-xl-1 {
                margin-top: 0.25rem !important;
            }

            .mr-xl-1,
            .mx-xl-1 {
                margin-right: 0.25rem !important;
            }

            .mb-xl-1,
            .my-xl-1 {
                margin-bottom: 0.25rem !important;
            }

            .ml-xl-1,
            .mx-xl-1 {
                margin-left: 0.25rem !important;
            }

            .m-xl-2 {
                margin: 0.5rem !important;
            }

            .mt-xl-2,
            .my-xl-2 {
                margin-top: 0.5rem !important;
            }

            .mr-xl-2,
            .mx-xl-2 {
                margin-right: 0.5rem !important;
            }

            .mb-xl-2,
            .my-xl-2 {
                margin-bottom: 0.5rem !important;
            }

            .ml-xl-2,
            .mx-xl-2 {
                margin-left: 0.5rem !important;
            }

            .m-xl-3 {
                margin: 1rem !important;
            }

            .mt-xl-3,
            .my-xl-3 {
                margin-top: 1rem !important;
            }

            .mr-xl-3,
            .mx-xl-3 {
                margin-right: 1rem !important;
            }

            .mb-xl-3,
            .my-xl-3 {
                margin-bottom: 1rem !important;
            }

            .ml-xl-3,
            .mx-xl-3 {
                margin-left: 1rem !important;
            }

            .m-xl-4 {
                margin: 1.5rem !important;
            }

            .mt-xl-4,
            .my-xl-4 {
                margin-top: 1.5rem !important;
            }

            .mr-xl-4,
            .mx-xl-4 {
                margin-right: 1.5rem !important;
            }

            .mb-xl-4,
            .my-xl-4 {
                margin-bottom: 1.5rem !important;
            }

            .ml-xl-4,
            .mx-xl-4 {
                margin-left: 1.5rem !important;
            }

            .m-xl-5 {
                margin: 3rem !important;
            }

            .mt-xl-5,
            .my-xl-5 {
                margin-top: 3rem !important;
            }

            .mr-xl-5,
            .mx-xl-5 {
                margin-right: 3rem !important;
            }

            .mb-xl-5,
            .my-xl-5 {
                margin-bottom: 3rem !important;
            }

            .ml-xl-5,
            .mx-xl-5 {
                margin-left: 3rem !important;
            }

            .p-xl-0 {
                padding: 0 !important;
            }

            .pt-xl-0,
            .py-xl-0 {
                padding-top: 0 !important;
            }

            .pr-xl-0,
            .px-xl-0 {
                padding-right: 0 !important;
            }

            .pb-xl-0,
            .py-xl-0 {
                padding-bottom: 0 !important;
            }

            .pl-xl-0,
            .px-xl-0 {
                padding-left: 0 !important;
            }

            .p-xl-1 {
                padding: 0.25rem !important;
            }

            .pt-xl-1,
            .py-xl-1 {
                padding-top: 0.25rem !important;
            }

            .pr-xl-1,
            .px-xl-1 {
                padding-right: 0.25rem !important;
            }

            .pb-xl-1,
            .py-xl-1 {
                padding-bottom: 0.25rem !important;
            }

            .pl-xl-1,
            .px-xl-1 {
                padding-left: 0.25rem !important;
            }

            .p-xl-2 {
                padding: 0.5rem !important;
            }

            .pt-xl-2,
            .py-xl-2 {
                padding-top: 0.5rem !important;
            }

            .pr-xl-2,
            .px-xl-2 {
                padding-right: 0.5rem !important;
            }

            .pb-xl-2,
            .py-xl-2 {
                padding-bottom: 0.5rem !important;
            }

            .pl-xl-2,
            .px-xl-2 {
                padding-left: 0.5rem !important;
            }

            .p-xl-3 {
                padding: 1rem !important;
            }

            .pt-xl-3,
            .py-xl-3 {
                padding-top: 1rem !important;
            }

            .pr-xl-3,
            .px-xl-3 {
                padding-right: 1rem !important;
            }

            .pb-xl-3,
            .py-xl-3 {
                padding-bottom: 1rem !important;
            }

            .pl-xl-3,
            .px-xl-3 {
                padding-left: 1rem !important;
            }

            .p-xl-4 {
                padding: 1.5rem !important;
            }

            .pt-xl-4,
            .py-xl-4 {
                padding-top: 1.5rem !important;
            }

            .pr-xl-4,
            .px-xl-4 {
                padding-right: 1.5rem !important;
            }

            .pb-xl-4,
            .py-xl-4 {
                padding-bottom: 1.5rem !important;
            }

            .pl-xl-4,
            .px-xl-4 {
                padding-left: 1.5rem !important;
            }

            .p-xl-5 {
                padding: 3rem !important;
            }

            .pt-xl-5,
            .py-xl-5 {
                padding-top: 3rem !important;
            }

            .pr-xl-5,
            .px-xl-5 {
                padding-right: 3rem !important;
            }

            .pb-xl-5,
            .py-xl-5 {
                padding-bottom: 3rem !important;
            }

            .pl-xl-5,
            .px-xl-5 {
                padding-left: 3rem !important;
            }

            .m-xl-n1 {
                margin: -0.25rem !important;
            }

            .mt-xl-n1,
            .my-xl-n1 {
                margin-top: -0.25rem !important;
            }

            .mr-xl-n1,
            .mx-xl-n1 {
                margin-right: -0.25rem !important;
            }

            .mb-xl-n1,
            .my-xl-n1 {
                margin-bottom: -0.25rem !important;
            }

            .ml-xl-n1,
            .mx-xl-n1 {
                margin-left: -0.25rem !important;
            }

            .m-xl-n2 {
                margin: -0.5rem !important;
            }

            .mt-xl-n2,
            .my-xl-n2 {
                margin-top: -0.5rem !important;
            }

            .mr-xl-n2,
            .mx-xl-n2 {
                margin-right: -0.5rem !important;
            }

            .mb-xl-n2,
            .my-xl-n2 {
                margin-bottom: -0.5rem !important;
            }

            .ml-xl-n2,
            .mx-xl-n2 {
                margin-left: -0.5rem !important;
            }

            .m-xl-n3 {
                margin: -1rem !important;
            }

            .mt-xl-n3,
            .my-xl-n3 {
                margin-top: -1rem !important;
            }

            .mr-xl-n3,
            .mx-xl-n3 {
                margin-right: -1rem !important;
            }

            .mb-xl-n3,
            .my-xl-n3 {
                margin-bottom: -1rem !important;
            }

            .ml-xl-n3,
            .mx-xl-n3 {
                margin-left: -1rem !important;
            }

            .m-xl-n4 {
                margin: -1.5rem !important;
            }

            .mt-xl-n4,
            .my-xl-n4 {
                margin-top: -1.5rem !important;
            }

            .mr-xl-n4,
            .mx-xl-n4 {
                margin-right: -1.5rem !important;
            }

            .mb-xl-n4,
            .my-xl-n4 {
                margin-bottom: -1.5rem !important;
            }

            .ml-xl-n4,
            .mx-xl-n4 {
                margin-left: -1.5rem !important;
            }

            .m-xl-n5 {
                margin: -3rem !important;
            }

            .mt-xl-n5,
            .my-xl-n5 {
                margin-top: -3rem !important;
            }

            .mr-xl-n5,
            .mx-xl-n5 {
                margin-right: -3rem !important;
            }

            .mb-xl-n5,
            .my-xl-n5 {
                margin-bottom: -3rem !important;
            }

            .ml-xl-n5,
            .mx-xl-n5 {
                margin-left: -3rem !important;
            }

            .m-xl-auto {
                margin: auto !important;
            }

            .mt-xl-auto,
            .my-xl-auto {
                margin-top: auto !important;
            }

            .mr-xl-auto,
            .mx-xl-auto {
                margin-right: auto !important;
            }

            .mb-xl-auto,
            .my-xl-auto {
                margin-bottom: auto !important;
            }

            .ml-xl-auto,
            .mx-xl-auto {
                margin-left: auto !important;
            }
        }

        .text-monospace {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        }

        .text-justify {
            text-align: justify !important;
        }

        .text-wrap {
            white-space: normal !important;
        }

        .text-nowrap {
            white-space: nowrap !important;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        @media (min-width: 576px) {
            .text-sm-left {
                text-align: left !important;
            }

            .text-sm-right {
                text-align: right !important;
            }

            .text-sm-center {
                text-align: center !important;
            }
        }

        @media (min-width: 768px) {
            .text-md-left {
                text-align: left !important;
            }

            .text-md-right {
                text-align: right !important;
            }

            .text-md-center {
                text-align: center !important;
            }
        }

        @media (min-width: 992px) {
            .text-lg-left {
                text-align: left !important;
            }

            .text-lg-right {
                text-align: right !important;
            }

            .text-lg-center {
                text-align: center !important;
            }
        }

        @media (min-width: 1200px) {
            .text-xl-left {
                text-align: left !important;
            }

            .text-xl-right {
                text-align: right !important;
            }

            .text-xl-center {
                text-align: center !important;
            }
        }

        .text-lowercase {
            text-transform: lowercase !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .text-capitalize {
            text-transform: capitalize !important;
        }

        .font-weight-light {
            font-weight: 300 !important;
        }

        .font-weight-lighter {
            font-weight: lighter !important;
        }

        .font-weight-normal {
            font-weight: 400 !important;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .font-weight-bolder {
            font-weight: bolder !important;
        }

        .font-italic {
            font-style: italic !important;
        }

        .text-white {
            color: #fff !important;
        }

        .text-primary {
            color: #3490dc !important;
        }

        a.text-primary:hover,
        a.text-primary:focus {
            color: #1d68a7 !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        a.text-secondary:hover,
        a.text-secondary:focus {
            color: #494f54 !important;
        }

        .text-success {
            color: #38c172 !important;
        }

        a.text-success:hover,
        a.text-success:focus {
            color: #27864f !important;
        }

        .text-info {
            color: #6cb2eb !important;
        }

        a.text-info:hover,
        a.text-info:focus {
            color: #298fe2 !important;
        }

        .text-warning {
            color: #ffed4a !important;
        }

        a.text-warning:hover,
        a.text-warning:focus {
            color: #fde300 !important;
        }

        .text-danger {
            color: #e3342f !important;
        }

        a.text-danger:hover,
        a.text-danger:focus {
            color: #ae1c17 !important;
        }

        .text-light {
            color: #f8f9fa !important;
        }

        a.text-light:hover,
        a.text-light:focus {
            color: #cbd3da !important;
        }

        .text-dark {
            color: #343a40 !important;
        }

        a.text-dark:hover,
        a.text-dark:focus {
            color: #121416 !important;
        }

        .text-body {
            color: #212529 !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-black-50 {
            color: rgba(0, 0, 0, 0.5) !important;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .text-hide {
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }

        .text-decoration-none {
            text-decoration: none !important;
        }

        .text-break {
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }

        .text-reset {
            color: inherit !important;
        }

        .visible {
            visibility: visible !important;
        }

        .invisible {
            visibility: hidden !important;
        }

        @media print {
            *,
            *::before,
            *::after {
                text-shadow: none !important;
                box-shadow: none !important;
            }

            a:not(.btn) {
                text-decoration: underline;
            }

            abbr[title]::after {
                content: " (" attr(title) ")";
            }

            pre {
                white-space: pre-wrap !important;
            }

            pre,
            blockquote {
                border: 1px solid #adb5bd;
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }

            tr,
            img {
                page-break-inside: avoid;
            }

            p,
            h2,
            h3 {
                orphans: 3;
                widows: 3;
            }

            h2,
            h3 {
                page-break-after: avoid;
            }

            @page {
                size: a3;
            }

            body {
                min-width: 992px !important;
            }

            .container {
                min-width: 992px !important;
            }

            .navbar {
                display: none;
            }

            .badge {
                border: 1px solid #000;
            }

            .table {
                border-collapse: collapse !important;
            }

            .table td,
            .table th {
                background-color: #fff !important;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #dee2e6 !important;
            }

            .table-dark {
                color: inherit;
            }

            .table-dark th,
            .table-dark td,
            .table-dark thead th,
            .table-dark tbody + tbody {
                border-color: #dee2e6;
            }

            .table .thead-dark th {
                color: inherit;
                border-color: #dee2e6;
            }
        }

        /*!
 * Font Awesome Free 5.12.0 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 */

        @font-face {
            font-family: "Font Awesome 5 Brands";
            font-style: normal;
            font-weight: normal;
            font-display: auto;
            src: url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.eot?088a34f78f530102fd9661173b4a4f26);
            src: url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.eot?088a34f78f530102fd9661173b4a4f26) format("embedded-opentype"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.woff2?822d94f19fe57477865209e1242a3c63) format("woff2"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.woff?f4920c94c0861c537f72ba36590f6362) format("woff"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.ttf?273dc9bf9778fd37fa61357645d46a28) format("truetype"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.svg?d72293118cda50ec50c39957d9d836d0) format("svg");
        }

        .fab {
            font-family: "Font Awesome 5 Brands";
        }

        /*!
 * Font Awesome Free 5.12.0 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 */

        @font-face {
            font-family: "Font Awesome 5 Free";
            font-style: normal;
            font-weight: 400;
            font-display: auto;
            src: url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.eot?3ac49cb33f43a6471f21ab3df40d1b1e);
            src: url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.eot?3ac49cb33f43a6471f21ab3df40d1b1e) format("embedded-opentype"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.woff2?9efb86976bd53e159166c12365f61e25) format("woff2"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.woff?a57bcf76c178aee452db7a57b75509b6) format("woff"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.ttf?ece54318791c51b52dfdc689efdb6271) format("truetype"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.svg?d2e53334c22a9a4937bc26e84b36e1e0) format("svg");
        }

        .far {
            font-family: "Font Awesome 5 Free";
            font-weight: 400;
        }

        /*!
 * Font Awesome Free 5.12.0 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 */

        @font-face {
            font-family: "Font Awesome 5 Free";
            font-style: normal;
            font-weight: 900;
            font-display: auto;
            src: url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.eot?7fb1cdd9c3b889161216a13267b55fe2);
            src: url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.eot?7fb1cdd9c3b889161216a13267b55fe2) format("embedded-opentype"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.woff2?f6121be597a72928f54e7ab5b95512a1) format("woff2"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.woff?93f284548b42ab76fe3fd03a9d3a2180) format("woff"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.ttf?2aa6edf8f296a43b32df35f330b7c81c) format("truetype"), url(/fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.svg?7a5de9b08012e4da40504f2cf126a351) format("svg");
        }

        .fa,
        .glyphicon,
        .fas {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        /*!
 * Font Awesome Free 5.12.0 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 */

        .fa,
        .glyphicon,
        .fas,
        .far,
        .fal,
        .fad,
        .fab {
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
        }

        .fa-lg {
            font-size: 1.3333333333em;
            line-height: 0.75em;
            vertical-align: -0.0667em;
        }

        .fa-xs {
            font-size: 0.75em;
        }

        .fa-sm {
            font-size: 0.875em;
        }

        .fa-1x {
            font-size: 1em;
        }

        .fa-2x {
            font-size: 2em;
        }

        .fa-3x {
            font-size: 3em;
        }

        .fa-4x {
            font-size: 4em;
        }

        .fa-5x {
            font-size: 5em;
        }

        .fa-6x {
            font-size: 6em;
        }

        .fa-7x {
            font-size: 7em;
        }

        .fa-8x {
            font-size: 8em;
        }

        .fa-9x {
            font-size: 9em;
        }

        .fa-10x {
            font-size: 10em;
        }

        .fa-fw {
            text-align: center;
            width: 1.25em;
        }

        .fa-ul {
            list-style-type: none;
            margin-left: 2.5em;
            padding-left: 0;
        }

        .fa-ul > li {
            position: relative;
        }

        .fa-li {
            left: -2em;
            position: absolute;
            text-align: center;
            width: 2em;
            line-height: inherit;
        }

        .fa-border {
            border: solid 0.08em #eee;
            border-radius: 0.1em;
            padding: 0.2em 0.25em 0.15em;
        }

        .fa-pull-left {
            float: left;
        }

        .fa-pull-right {
            float: right;
        }

        .fa.fa-pull-left,
        .fa-pull-left.glyphicon,
        .fas.fa-pull-left,
        .far.fa-pull-left,
        .fal.fa-pull-left,
        .fab.fa-pull-left {
            margin-right: 0.3em;
        }

        .fa.fa-pull-right,
        .fa-pull-right.glyphicon,
        .fas.fa-pull-right,
        .far.fa-pull-right,
        .fal.fa-pull-right,
        .fab.fa-pull-right {
            margin-left: 0.3em;
        }

        .fa-spin {
            -webkit-animation: fa-spin 2s infinite linear;
            animation: fa-spin 2s infinite linear;
        }

        .fa-pulse {
            -webkit-animation: fa-spin 1s infinite steps(8);
            animation: fa-spin 1s infinite steps(8);
        }

        @-webkit-keyframes fa-spin {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes fa-spin {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .fa-rotate-90 {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=1)";
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
        }

        .fa-rotate-180 {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=2)";
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .fa-rotate-270 {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)";
            -webkit-transform: rotate(270deg);
            transform: rotate(270deg);
        }

        .fa-flip-horizontal {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0, mirror=1)";
            -webkit-transform: scale(-1, 1);
            transform: scale(-1, 1);
        }

        .fa-flip-vertical {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=2, mirror=1)";
            -webkit-transform: scale(1, -1);
            transform: scale(1, -1);
        }

        .fa-flip-both,
        .fa-flip-horizontal.fa-flip-vertical {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=2, mirror=1)";
            -webkit-transform: scale(-1, -1);
            transform: scale(-1, -1);
        }

        :root .fa-rotate-90,
        :root .fa-rotate-180,
        :root .fa-rotate-270,
        :root .fa-flip-horizontal,
        :root .fa-flip-vertical,
        :root .fa-flip-both {
            -webkit-filter: none;
            filter: none;
        }

        .fa-stack {
            display: inline-block;
            height: 2em;
            line-height: 2em;
            position: relative;
            vertical-align: middle;
            width: 2.5em;
        }

        .fa-stack-1x,
        .fa-stack-2x {
            left: 0;
            position: absolute;
            text-align: center;
            width: 100%;
        }

        .fa-stack-1x {
            line-height: inherit;
        }

        .fa-stack-2x {
            font-size: 2em;
        }

        .fa-inverse {
            color: #fff;
        }

        /* Font Awesome uses the Unicode Private Use Area (PUA) to ensure screen
readers do not read off random characters that represent icons */

        .fa-500px:before {
            content: "\F26E";
        }

        .fa-accessible-icon:before {
            content: "\F368";
        }

        .fa-accusoft:before {
            content: "\F369";
        }

        .fa-acquisitions-incorporated:before {
            content: "\F6AF";
        }

        .fa-ad:before {
            content: "\F641";
        }

        .fa-address-book:before {
            content: "\F2B9";
        }

        .fa-address-card:before {
            content: "\F2BB";
        }

        .fa-adjust:before {
            content: "\F042";
        }

        .fa-adn:before {
            content: "\F170";
        }

        .fa-adobe:before {
            content: "\F778";
        }

        .fa-adversal:before {
            content: "\F36A";
        }

        .fa-affiliatetheme:before {
            content: "\F36B";
        }

        .fa-air-freshener:before {
            content: "\F5D0";
        }

        .fa-airbnb:before {
            content: "\F834";
        }

        .fa-algolia:before {
            content: "\F36C";
        }

        .fa-align-center:before {
            content: "\F037";
        }

        .fa-align-justify:before {
            content: "\F039";
        }

        .fa-align-left:before {
            content: "\F036";
        }

        .fa-align-right:before {
            content: "\F038";
        }

        .fa-alipay:before {
            content: "\F642";
        }

        .fa-allergies:before {
            content: "\F461";
        }

        .fa-amazon:before {
            content: "\F270";
        }

        .fa-amazon-pay:before {
            content: "\F42C";
        }

        .fa-ambulance:before {
            content: "\F0F9";
        }

        .fa-american-sign-language-interpreting:before {
            content: "\F2A3";
        }

        .fa-amilia:before {
            content: "\F36D";
        }

        .fa-anchor:before {
            content: "\F13D";
        }

        .fa-android:before {
            content: "\F17B";
        }

        .fa-angellist:before {
            content: "\F209";
        }

        .fa-angle-double-down:before {
            content: "\F103";
        }

        .fa-angle-double-left:before {
            content: "\F100";
        }

        .fa-angle-double-right:before {
            content: "\F101";
        }

        .fa-angle-double-up:before {
            content: "\F102";
        }

        .fa-angle-down:before {
            content: "\F107";
        }

        .fa-angle-left:before {
            content: "\F104";
        }

        .fa-angle-right:before {
            content: "\F105";
        }

        .fa-angle-up:before {
            content: "\F106";
        }

        .fa-angry:before {
            content: "\F556";
        }

        .fa-angrycreative:before {
            content: "\F36E";
        }

        .fa-angular:before {
            content: "\F420";
        }

        .fa-ankh:before {
            content: "\F644";
        }

        .fa-app-store:before {
            content: "\F36F";
        }

        .fa-app-store-ios:before {
            content: "\F370";
        }

        .fa-apper:before {
            content: "\F371";
        }

        .fa-apple:before {
            content: "\F179";
        }

        .fa-apple-alt:before {
            content: "\F5D1";
        }

        .fa-apple-pay:before {
            content: "\F415";
        }

        .fa-archive:before {
            content: "\F187";
        }

        .fa-archway:before {
            content: "\F557";
        }

        .fa-arrow-alt-circle-down:before {
            content: "\F358";
        }

        .fa-arrow-alt-circle-left:before {
            content: "\F359";
        }

        .fa-arrow-alt-circle-right:before {
            content: "\F35A";
        }

        .fa-arrow-alt-circle-up:before {
            content: "\F35B";
        }

        .fa-arrow-circle-down:before {
            content: "\F0AB";
        }

        .fa-arrow-circle-left:before {
            content: "\F0A8";
        }

        .fa-arrow-circle-right:before {
            content: "\F0A9";
        }

        .fa-arrow-circle-up:before {
            content: "\F0AA";
        }

        .fa-arrow-down:before {
            content: "\F063";
        }

        .fa-arrow-left:before {
            content: "\F060";
        }

        .fa-arrow-right:before {
            content: "\F061";
        }

        .fa-arrow-up:before {
            content: "\F062";
        }

        .fa-arrows-alt:before {
            content: "\F0B2";
        }

        .fa-arrows-alt-h:before {
            content: "\F337";
        }

        .fa-arrows-alt-v:before {
            content: "\F338";
        }

        .fa-artstation:before {
            content: "\F77A";
        }

        .fa-assistive-listening-systems:before {
            content: "\F2A2";
        }

        .fa-asterisk:before {
            content: "\F069";
        }

        .fa-asymmetrik:before {
            content: "\F372";
        }

        .fa-at:before {
            content: "\F1FA";
        }

        .fa-atlas:before {
            content: "\F558";
        }

        .fa-atlassian:before {
            content: "\F77B";
        }

        .fa-atom:before {
            content: "\F5D2";
        }

        .fa-audible:before {
            content: "\F373";
        }

        .fa-audio-description:before {
            content: "\F29E";
        }

        .fa-autoprefixer:before {
            content: "\F41C";
        }

        .fa-avianex:before {
            content: "\F374";
        }

        .fa-aviato:before {
            content: "\F421";
        }

        .fa-award:before {
            content: "\F559";
        }

        .fa-aws:before {
            content: "\F375";
        }

        .fa-baby:before {
            content: "\F77C";
        }

        .fa-baby-carriage:before {
            content: "\F77D";
        }

        .fa-backspace:before {
            content: "\F55A";
        }

        .fa-backward:before {
            content: "\F04A";
        }

        .fa-bacon:before {
            content: "\F7E5";
        }

        .fa-bahai:before {
            content: "\F666";
        }

        .fa-balance-scale:before {
            content: "\F24E";
        }

        .fa-balance-scale-left:before {
            content: "\F515";
        }

        .fa-balance-scale-right:before {
            content: "\F516";
        }

        .fa-ban:before {
            content: "\F05E";
        }

        .fa-band-aid:before {
            content: "\F462";
        }

        .fa-bandcamp:before {
            content: "\F2D5";
        }

        .fa-barcode:before {
            content: "\F02A";
        }

        .fa-bars:before {
            content: "\F0C9";
        }

        .fa-baseball-ball:before {
            content: "\F433";
        }

        .fa-basketball-ball:before {
            content: "\F434";
        }

        .fa-bath:before {
            content: "\F2CD";
        }

        .fa-battery-empty:before {
            content: "\F244";
        }

        .fa-battery-full:before {
            content: "\F240";
        }

        .fa-battery-half:before {
            content: "\F242";
        }

        .fa-battery-quarter:before {
            content: "\F243";
        }

        .fa-battery-three-quarters:before {
            content: "\F241";
        }

        .fa-battle-net:before {
            content: "\F835";
        }

        .fa-bed:before {
            content: "\F236";
        }

        .fa-beer:before {
            content: "\F0FC";
        }

        .fa-behance:before {
            content: "\F1B4";
        }

        .fa-behance-square:before {
            content: "\F1B5";
        }

        .fa-bell:before {
            content: "\F0F3";
        }

        .fa-bell-slash:before {
            content: "\F1F6";
        }

        .fa-bezier-curve:before {
            content: "\F55B";
        }

        .fa-bible:before {
            content: "\F647";
        }

        .fa-bicycle:before {
            content: "\F206";
        }

        .fa-biking:before {
            content: "\F84A";
        }

        .fa-bimobject:before {
            content: "\F378";
        }

        .fa-binoculars:before {
            content: "\F1E5";
        }

        .fa-biohazard:before {
            content: "\F780";
        }

        .fa-birthday-cake:before {
            content: "\F1FD";
        }

        .fa-bitbucket:before {
            content: "\F171";
        }

        .fa-bitcoin:before {
            content: "\F379";
        }

        .fa-bity:before {
            content: "\F37A";
        }

        .fa-black-tie:before {
            content: "\F27E";
        }

        .fa-blackberry:before {
            content: "\F37B";
        }

        .fa-blender:before {
            content: "\F517";
        }

        .fa-blender-phone:before {
            content: "\F6B6";
        }

        .fa-blind:before {
            content: "\F29D";
        }

        .fa-blog:before {
            content: "\F781";
        }

        .fa-blogger:before {
            content: "\F37C";
        }

        .fa-blogger-b:before {
            content: "\F37D";
        }

        .fa-bluetooth:before {
            content: "\F293";
        }

        .fa-bluetooth-b:before {
            content: "\F294";
        }

        .fa-bold:before {
            content: "\F032";
        }

        .fa-bolt:before {
            content: "\F0E7";
        }

        .fa-bomb:before {
            content: "\F1E2";
        }

        .fa-bone:before {
            content: "\F5D7";
        }

        .fa-bong:before {
            content: "\F55C";
        }

        .fa-book:before {
            content: "\F02D";
        }

        .fa-book-dead:before {
            content: "\F6B7";
        }

        .fa-book-medical:before {
            content: "\F7E6";
        }

        .fa-book-open:before {
            content: "\F518";
        }

        .fa-book-reader:before {
            content: "\F5DA";
        }

        .fa-bookmark:before {
            content: "\F02E";
        }

        .fa-bootstrap:before {
            content: "\F836";
        }

        .fa-border-all:before {
            content: "\F84C";
        }

        .fa-border-none:before {
            content: "\F850";
        }

        .fa-border-style:before {
            content: "\F853";
        }

        .fa-bowling-ball:before {
            content: "\F436";
        }

        .fa-box:before {
            content: "\F466";
        }

        .fa-box-open:before {
            content: "\F49E";
        }

        .fa-boxes:before {
            content: "\F468";
        }

        .fa-braille:before {
            content: "\F2A1";
        }

        .fa-brain:before {
            content: "\F5DC";
        }

        .fa-bread-slice:before {
            content: "\F7EC";
        }

        .fa-briefcase:before {
            content: "\F0B1";
        }

        .fa-briefcase-medical:before {
            content: "\F469";
        }

        .fa-broadcast-tower:before {
            content: "\F519";
        }

        .fa-broom:before {
            content: "\F51A";
        }

        .fa-brush:before {
            content: "\F55D";
        }

        .fa-btc:before {
            content: "\F15A";
        }

        .fa-buffer:before {
            content: "\F837";
        }

        .fa-bug:before {
            content: "\F188";
        }

        .fa-building:before {
            content: "\F1AD";
        }

        .fa-bullhorn:before {
            content: "\F0A1";
        }

        .fa-bullseye:before {
            content: "\F140";
        }

        .fa-burn:before {
            content: "\F46A";
        }

        .fa-buromobelexperte:before {
            content: "\F37F";
        }

        .fa-bus:before {
            content: "\F207";
        }

        .fa-bus-alt:before {
            content: "\F55E";
        }

        .fa-business-time:before {
            content: "\F64A";
        }

        .fa-buy-n-large:before {
            content: "\F8A6";
        }

        .fa-buysellads:before {
            content: "\F20D";
        }

        .fa-calculator:before {
            content: "\F1EC";
        }

        .fa-calendar:before {
            content: "\F133";
        }

        .fa-calendar-alt:before {
            content: "\F073";
        }

        .fa-calendar-check:before {
            content: "\F274";
        }

        .fa-calendar-day:before {
            content: "\F783";
        }

        .fa-calendar-minus:before {
            content: "\F272";
        }

        .fa-calendar-plus:before {
            content: "\F271";
        }

        .fa-calendar-times:before {
            content: "\F273";
        }

        .fa-calendar-week:before {
            content: "\F784";
        }

        .fa-camera:before {
            content: "\F030";
        }

        .fa-camera-retro:before {
            content: "\F083";
        }

        .fa-campground:before {
            content: "\F6BB";
        }

        .fa-canadian-maple-leaf:before {
            content: "\F785";
        }

        .fa-candy-cane:before {
            content: "\F786";
        }

        .fa-cannabis:before {
            content: "\F55F";
        }

        .fa-capsules:before {
            content: "\F46B";
        }

        .fa-car:before {
            content: "\F1B9";
        }

        .fa-car-alt:before {
            content: "\F5DE";
        }

        .fa-car-battery:before {
            content: "\F5DF";
        }

        .fa-car-crash:before {
            content: "\F5E1";
        }

        .fa-car-side:before {
            content: "\F5E4";
        }

        .fa-caravan:before {
            content: "\F8FF";
        }

        .fa-caret-down:before {
            content: "\F0D7";
        }

        .fa-caret-left:before {
            content: "\F0D9";
        }

        .fa-caret-right:before {
            content: "\F0DA";
        }

        .fa-caret-square-down:before {
            content: "\F150";
        }

        .fa-caret-square-left:before {
            content: "\F191";
        }

        .fa-caret-square-right:before {
            content: "\F152";
        }

        .fa-caret-square-up:before {
            content: "\F151";
        }

        .fa-caret-up:before {
            content: "\F0D8";
        }

        .fa-carrot:before {
            content: "\F787";
        }

        .fa-cart-arrow-down:before {
            content: "\F218";
        }

        .fa-cart-plus:before {
            content: "\F217";
        }

        .fa-cash-register:before {
            content: "\F788";
        }

        .fa-cat:before {
            content: "\F6BE";
        }

        .fa-cc-amazon-pay:before {
            content: "\F42D";
        }

        .fa-cc-amex:before {
            content: "\F1F3";
        }

        .fa-cc-apple-pay:before {
            content: "\F416";
        }

        .fa-cc-diners-club:before {
            content: "\F24C";
        }

        .fa-cc-discover:before {
            content: "\F1F2";
        }

        .fa-cc-jcb:before {
            content: "\F24B";
        }

        .fa-cc-mastercard:before {
            content: "\F1F1";
        }

        .fa-cc-paypal:before {
            content: "\F1F4";
        }

        .fa-cc-stripe:before {
            content: "\F1F5";
        }

        .fa-cc-visa:before {
            content: "\F1F0";
        }

        .fa-centercode:before {
            content: "\F380";
        }

        .fa-centos:before {
            content: "\F789";
        }

        .fa-certificate:before {
            content: "\F0A3";
        }

        .fa-chair:before {
            content: "\F6C0";
        }

        .fa-chalkboard:before {
            content: "\F51B";
        }

        .fa-chalkboard-teacher:before {
            content: "\F51C";
        }

        .fa-charging-station:before {
            content: "\F5E7";
        }

        .fa-chart-area:before {
            content: "\F1FE";
        }

        .fa-chart-bar:before {
            content: "\F080";
        }

        .fa-chart-line:before {
            content: "\F201";
        }

        .fa-chart-pie:before {
            content: "\F200";
        }

        .fa-check:before {
            content: "\F00C";
        }

        .fa-check-circle:before {
            content: "\F058";
        }

        .fa-check-double:before {
            content: "\F560";
        }

        .fa-check-square:before {
            content: "\F14A";
        }

        .fa-cheese:before {
            content: "\F7EF";
        }

        .fa-chess:before {
            content: "\F439";
        }

        .fa-chess-bishop:before {
            content: "\F43A";
        }

        .fa-chess-board:before {
            content: "\F43C";
        }

        .fa-chess-king:before {
            content: "\F43F";
        }

        .fa-chess-knight:before {
            content: "\F441";
        }

        .fa-chess-pawn:before {
            content: "\F443";
        }

        .fa-chess-queen:before {
            content: "\F445";
        }

        .fa-chess-rook:before {
            content: "\F447";
        }

        .fa-chevron-circle-down:before {
            content: "\F13A";
        }

        .fa-chevron-circle-left:before {
            content: "\F137";
        }

        .fa-chevron-circle-right:before {
            content: "\F138";
        }

        .fa-chevron-circle-up:before {
            content: "\F139";
        }

        .fa-chevron-down:before {
            content: "\F078";
        }

        .fa-chevron-left:before,
        .glyphicon.glyphicon-time:before,
        .glyphicon.glyphicon-chevron-left:before {
            content: "\F053";
        }

        .fa-chevron-right:before,
        .glyphicon.glyphicon-chevron-right:before {
            content: "\F054";
        }

        .fa-chevron-up:before {
            content: "\F077";
        }

        .fa-child:before {
            content: "\F1AE";
        }

        .fa-chrome:before {
            content: "\F268";
        }

        .fa-chromecast:before {
            content: "\F838";
        }

        .fa-church:before {
            content: "\F51D";
        }

        .fa-circle:before {
            content: "\F111";
        }

        .fa-circle-notch:before {
            content: "\F1CE";
        }

        .fa-city:before {
            content: "\F64F";
        }

        .fa-clinic-medical:before {
            content: "\F7F2";
        }

        .fa-clipboard:before {
            content: "\F328";
        }

        .fa-clipboard-check:before {
            content: "\F46C";
        }

        .fa-clipboard-list:before {
            content: "\F46D";
        }

        .fa-clock:before {
            content: "\F017";
        }

        .fa-clone:before {
            content: "\F24D";
        }

        .fa-closed-captioning:before {
            content: "\F20A";
        }

        .fa-cloud:before {
            content: "\F0C2";
        }

        .fa-cloud-download-alt:before {
            content: "\F381";
        }

        .fa-cloud-meatball:before {
            content: "\F73B";
        }

        .fa-cloud-moon:before {
            content: "\F6C3";
        }

        .fa-cloud-moon-rain:before {
            content: "\F73C";
        }

        .fa-cloud-rain:before {
            content: "\F73D";
        }

        .fa-cloud-showers-heavy:before {
            content: "\F740";
        }

        .fa-cloud-sun:before {
            content: "\F6C4";
        }

        .fa-cloud-sun-rain:before {
            content: "\F743";
        }

        .fa-cloud-upload-alt:before {
            content: "\F382";
        }

        .fa-cloudscale:before {
            content: "\F383";
        }

        .fa-cloudsmith:before {
            content: "\F384";
        }

        .fa-cloudversify:before {
            content: "\F385";
        }

        .fa-cocktail:before {
            content: "\F561";
        }

        .fa-code:before {
            content: "\F121";
        }

        .fa-code-branch:before {
            content: "\F126";
        }

        .fa-codepen:before {
            content: "\F1CB";
        }

        .fa-codiepie:before {
            content: "\F284";
        }

        .fa-coffee:before {
            content: "\F0F4";
        }

        .fa-cog:before {
            content: "\F013";
        }

        .fa-cogs:before {
            content: "\F085";
        }

        .fa-coins:before {
            content: "\F51E";
        }

        .fa-columns:before {
            content: "\F0DB";
        }

        .fa-comment:before {
            content: "\F075";
        }

        .fa-comment-alt:before {
            content: "\F27A";
        }

        .fa-comment-dollar:before {
            content: "\F651";
        }

        .fa-comment-dots:before {
            content: "\F4AD";
        }

        .fa-comment-medical:before {
            content: "\F7F5";
        }

        .fa-comment-slash:before {
            content: "\F4B3";
        }

        .fa-comments:before {
            content: "\F086";
        }

        .fa-comments-dollar:before {
            content: "\F653";
        }

        .fa-compact-disc:before {
            content: "\F51F";
        }

        .fa-compass:before {
            content: "\F14E";
        }

        .fa-compress:before {
            content: "\F066";
        }

        .fa-compress-alt:before {
            content: "\F422";
        }

        .fa-compress-arrows-alt:before {
            content: "\F78C";
        }

        .fa-concierge-bell:before {
            content: "\F562";
        }

        .fa-confluence:before {
            content: "\F78D";
        }

        .fa-connectdevelop:before {
            content: "\F20E";
        }

        .fa-contao:before {
            content: "\F26D";
        }

        .fa-cookie:before {
            content: "\F563";
        }

        .fa-cookie-bite:before {
            content: "\F564";
        }

        .fa-copy:before {
            content: "\F0C5";
        }

        .fa-copyright:before {
            content: "\F1F9";
        }

        .fa-cotton-bureau:before {
            content: "\F89E";
        }

        .fa-couch:before {
            content: "\F4B8";
        }

        .fa-cpanel:before {
            content: "\F388";
        }

        .fa-creative-commons:before {
            content: "\F25E";
        }

        .fa-creative-commons-by:before {
            content: "\F4E7";
        }

        .fa-creative-commons-nc:before {
            content: "\F4E8";
        }

        .fa-creative-commons-nc-eu:before {
            content: "\F4E9";
        }

        .fa-creative-commons-nc-jp:before {
            content: "\F4EA";
        }

        .fa-creative-commons-nd:before {
            content: "\F4EB";
        }

        .fa-creative-commons-pd:before {
            content: "\F4EC";
        }

        .fa-creative-commons-pd-alt:before {
            content: "\F4ED";
        }

        .fa-creative-commons-remix:before {
            content: "\F4EE";
        }

        .fa-creative-commons-sa:before {
            content: "\F4EF";
        }

        .fa-creative-commons-sampling:before {
            content: "\F4F0";
        }

        .fa-creative-commons-sampling-plus:before {
            content: "\F4F1";
        }

        .fa-creative-commons-share:before {
            content: "\F4F2";
        }

        .fa-creative-commons-zero:before {
            content: "\F4F3";
        }

        .fa-credit-card:before {
            content: "\F09D";
        }

        .fa-critical-role:before {
            content: "\F6C9";
        }

        .fa-crop:before {
            content: "\F125";
        }

        .fa-crop-alt:before {
            content: "\F565";
        }

        .fa-cross:before {
            content: "\F654";
        }

        .fa-crosshairs:before {
            content: "\F05B";
        }

        .fa-crow:before {
            content: "\F520";
        }

        .fa-crown:before {
            content: "\F521";
        }

        .fa-crutch:before {
            content: "\F7F7";
        }

        .fa-css3:before {
            content: "\F13C";
        }

        .fa-css3-alt:before {
            content: "\F38B";
        }

        .fa-cube:before {
            content: "\F1B2";
        }

        .fa-cubes:before {
            content: "\F1B3";
        }

        .fa-cut:before {
            content: "\F0C4";
        }

        .fa-cuttlefish:before {
            content: "\F38C";
        }

        .fa-d-and-d:before {
            content: "\F38D";
        }

        .fa-d-and-d-beyond:before {
            content: "\F6CA";
        }

        .fa-dashcube:before {
            content: "\F210";
        }

        .fa-database:before {
            content: "\F1C0";
        }

        .fa-deaf:before {
            content: "\F2A4";
        }

        .fa-delicious:before {
            content: "\F1A5";
        }

        .fa-democrat:before {
            content: "\F747";
        }

        .fa-deploydog:before {
            content: "\F38E";
        }

        .fa-deskpro:before {
            content: "\F38F";
        }

        .fa-desktop:before {
            content: "\F108";
        }

        .fa-dev:before {
            content: "\F6CC";
        }

        .fa-deviantart:before {
            content: "\F1BD";
        }

        .fa-dharmachakra:before {
            content: "\F655";
        }

        .fa-dhl:before {
            content: "\F790";
        }

        .fa-diagnoses:before {
            content: "\F470";
        }

        .fa-diaspora:before {
            content: "\F791";
        }

        .fa-dice:before {
            content: "\F522";
        }

        .fa-dice-d20:before {
            content: "\F6CF";
        }

        .fa-dice-d6:before {
            content: "\F6D1";
        }

        .fa-dice-five:before {
            content: "\F523";
        }

        .fa-dice-four:before {
            content: "\F524";
        }

        .fa-dice-one:before {
            content: "\F525";
        }

        .fa-dice-six:before {
            content: "\F526";
        }

        .fa-dice-three:before {
            content: "\F527";
        }

        .fa-dice-two:before {
            content: "\F528";
        }

        .fa-digg:before {
            content: "\F1A6";
        }

        .fa-digital-ocean:before {
            content: "\F391";
        }

        .fa-digital-tachograph:before {
            content: "\F566";
        }

        .fa-directions:before {
            content: "\F5EB";
        }

        .fa-discord:before {
            content: "\F392";
        }

        .fa-discourse:before {
            content: "\F393";
        }

        .fa-divide:before {
            content: "\F529";
        }

        .fa-dizzy:before {
            content: "\F567";
        }

        .fa-dna:before {
            content: "\F471";
        }

        .fa-dochub:before {
            content: "\F394";
        }

        .fa-docker:before {
            content: "\F395";
        }

        .fa-dog:before {
            content: "\F6D3";
        }

        .fa-dollar-sign:before {
            content: "\F155";
        }

        .fa-dolly:before {
            content: "\F472";
        }

        .fa-dolly-flatbed:before {
            content: "\F474";
        }

        .fa-donate:before {
            content: "\F4B9";
        }

        .fa-door-closed:before {
            content: "\F52A";
        }

        .fa-door-open:before {
            content: "\F52B";
        }

        .fa-dot-circle:before {
            content: "\F192";
        }

        .fa-dove:before {
            content: "\F4BA";
        }

        .fa-download:before {
            content: "\F019";
        }

        .fa-draft2digital:before {
            content: "\F396";
        }

        .fa-drafting-compass:before {
            content: "\F568";
        }

        .fa-dragon:before {
            content: "\F6D5";
        }

        .fa-draw-polygon:before {
            content: "\F5EE";
        }

        .fa-dribbble:before {
            content: "\F17D";
        }

        .fa-dribbble-square:before {
            content: "\F397";
        }

        .fa-dropbox:before {
            content: "\F16B";
        }

        .fa-drum:before {
            content: "\F569";
        }

        .fa-drum-steelpan:before {
            content: "\F56A";
        }

        .fa-drumstick-bite:before {
            content: "\F6D7";
        }

        .fa-drupal:before {
            content: "\F1A9";
        }

        .fa-dumbbell:before {
            content: "\F44B";
        }

        .fa-dumpster:before {
            content: "\F793";
        }

        .fa-dumpster-fire:before {
            content: "\F794";
        }

        .fa-dungeon:before {
            content: "\F6D9";
        }

        .fa-dyalog:before {
            content: "\F399";
        }

        .fa-earlybirds:before {
            content: "\F39A";
        }

        .fa-ebay:before {
            content: "\F4F4";
        }

        .fa-edge:before {
            content: "\F282";
        }

        .fa-edit:before {
            content: "\F044";
        }

        .fa-egg:before {
            content: "\F7FB";
        }

        .fa-eject:before {
            content: "\F052";
        }

        .fa-elementor:before {
            content: "\F430";
        }

        .fa-ellipsis-h:before {
            content: "\F141";
        }

        .fa-ellipsis-v:before {
            content: "\F142";
        }

        .fa-ello:before {
            content: "\F5F1";
        }

        .fa-ember:before {
            content: "\F423";
        }

        .fa-empire:before {
            content: "\F1D1";
        }

        .fa-envelope:before {
            content: "\F0E0";
        }

        .fa-envelope-open:before {
            content: "\F2B6";
        }

        .fa-envelope-open-text:before {
            content: "\F658";
        }

        .fa-envelope-square:before {
            content: "\F199";
        }

        .fa-envira:before {
            content: "\F299";
        }

        .fa-equals:before {
            content: "\F52C";
        }

        .fa-eraser:before {
            content: "\F12D";
        }

        .fa-erlang:before {
            content: "\F39D";
        }

        .fa-ethereum:before {
            content: "\F42E";
        }

        .fa-ethernet:before {
            content: "\F796";
        }

        .fa-etsy:before {
            content: "\F2D7";
        }

        .fa-euro-sign:before {
            content: "\F153";
        }

        .fa-evernote:before {
            content: "\F839";
        }

        .fa-exchange-alt:before {
            content: "\F362";
        }

        .fa-exclamation:before {
            content: "\F12A";
        }

        .fa-exclamation-circle:before {
            content: "\F06A";
        }

        .fa-exclamation-triangle:before {
            content: "\F071";
        }

        .fa-expand:before {
            content: "\F065";
        }

        .fa-expand-alt:before {
            content: "\F424";
        }

        .fa-expand-arrows-alt:before {
            content: "\F31E";
        }

        .fa-expeditedssl:before {
            content: "\F23E";
        }

        .fa-external-link-alt:before {
            content: "\F35D";
        }

        .fa-external-link-square-alt:before {
            content: "\F360";
        }

        .fa-eye:before {
            content: "\F06E";
        }

        .fa-eye-dropper:before {
            content: "\F1FB";
        }

        .fa-eye-slash:before {
            content: "\F070";
        }

        .fa-facebook:before {
            content: "\F09A";
        }

        .fa-facebook-f:before {
            content: "\F39E";
        }

        .fa-facebook-messenger:before {
            content: "\F39F";
        }

        .fa-facebook-square:before {
            content: "\F082";
        }

        .fa-fan:before {
            content: "\F863";
        }

        .fa-fantasy-flight-games:before {
            content: "\F6DC";
        }

        .fa-fast-backward:before {
            content: "\F049";
        }

        .fa-fast-forward:before {
            content: "\F050";
        }

        .fa-fax:before {
            content: "\F1AC";
        }

        .fa-feather:before {
            content: "\F52D";
        }

        .fa-feather-alt:before {
            content: "\F56B";
        }

        .fa-fedex:before {
            content: "\F797";
        }

        .fa-fedora:before {
            content: "\F798";
        }

        .fa-female:before {
            content: "\F182";
        }

        .fa-fighter-jet:before {
            content: "\F0FB";
        }

        .fa-figma:before {
            content: "\F799";
        }

        .fa-file:before {
            content: "\F15B";
        }

        .fa-file-alt:before {
            content: "\F15C";
        }

        .fa-file-archive:before {
            content: "\F1C6";
        }

        .fa-file-audio:before {
            content: "\F1C7";
        }

        .fa-file-code:before {
            content: "\F1C9";
        }

        .fa-file-contract:before {
            content: "\F56C";
        }

        .fa-file-csv:before {
            content: "\F6DD";
        }

        .fa-file-download:before {
            content: "\F56D";
        }

        .fa-file-excel:before {
            content: "\F1C3";
        }

        .fa-file-export:before {
            content: "\F56E";
        }

        .fa-file-image:before {
            content: "\F1C5";
        }

        .fa-file-import:before {
            content: "\F56F";
        }

        .fa-file-invoice:before {
            content: "\F570";
        }

        .fa-file-invoice-dollar:before {
            content: "\F571";
        }

        .fa-file-medical:before {
            content: "\F477";
        }

        .fa-file-medical-alt:before {
            content: "\F478";
        }

        .fa-file-pdf:before {
            content: "\F1C1";
        }

        .fa-file-powerpoint:before {
            content: "\F1C4";
        }

        .fa-file-prescription:before {
            content: "\F572";
        }

        .fa-file-signature:before {
            content: "\F573";
        }

        .fa-file-upload:before {
            content: "\F574";
        }

        .fa-file-video:before {
            content: "\F1C8";
        }

        .fa-file-word:before {
            content: "\F1C2";
        }

        .fa-fill:before {
            content: "\F575";
        }

        .fa-fill-drip:before {
            content: "\F576";
        }

        .fa-film:before {
            content: "\F008";
        }

        .fa-filter:before {
            content: "\F0B0";
        }

        .fa-fingerprint:before {
            content: "\F577";
        }

        .fa-fire:before {
            content: "\F06D";
        }

        .fa-fire-alt:before {
            content: "\F7E4";
        }

        .fa-fire-extinguisher:before {
            content: "\F134";
        }

        .fa-firefox:before {
            content: "\F269";
        }

        .fa-firefox-browser:before {
            content: "\F907";
        }

        .fa-first-aid:before {
            content: "\F479";
        }

        .fa-first-order:before {
            content: "\F2B0";
        }

        .fa-first-order-alt:before {
            content: "\F50A";
        }

        .fa-firstdraft:before {
            content: "\F3A1";
        }

        .fa-fish:before {
            content: "\F578";
        }

        .fa-fist-raised:before {
            content: "\F6DE";
        }

        .fa-flag:before {
            content: "\F024";
        }

        .fa-flag-checkered:before {
            content: "\F11E";
        }

        .fa-flag-usa:before {
            content: "\F74D";
        }

        .fa-flask:before {
            content: "\F0C3";
        }

        .fa-flickr:before {
            content: "\F16E";
        }

        .fa-flipboard:before {
            content: "\F44D";
        }

        .fa-flushed:before {
            content: "\F579";
        }

        .fa-fly:before {
            content: "\F417";
        }

        .fa-folder:before {
            content: "\F07B";
        }

        .fa-folder-minus:before {
            content: "\F65D";
        }

        .fa-folder-open:before {
            content: "\F07C";
        }

        .fa-folder-plus:before {
            content: "\F65E";
        }

        .fa-font:before {
            content: "\F031";
        }

        .fa-font-awesome:before {
            content: "\F2B4";
        }

        .fa-font-awesome-alt:before {
            content: "\F35C";
        }

        .fa-font-awesome-flag:before {
            content: "\F425";
        }

        .fa-font-awesome-logo-full:before {
            content: "\F4E6";
        }

        .fa-fonticons:before {
            content: "\F280";
        }

        .fa-fonticons-fi:before {
            content: "\F3A2";
        }

        .fa-football-ball:before {
            content: "\F44E";
        }

        .fa-fort-awesome:before {
            content: "\F286";
        }

        .fa-fort-awesome-alt:before {
            content: "\F3A3";
        }

        .fa-forumbee:before {
            content: "\F211";
        }

        .fa-forward:before {
            content: "\F04E";
        }

        .fa-foursquare:before {
            content: "\F180";
        }

        .fa-free-code-camp:before {
            content: "\F2C5";
        }

        .fa-freebsd:before {
            content: "\F3A4";
        }

        .fa-frog:before {
            content: "\F52E";
        }

        .fa-frown:before {
            content: "\F119";
        }

        .fa-frown-open:before {
            content: "\F57A";
        }

        .fa-fulcrum:before {
            content: "\F50B";
        }

        .fa-funnel-dollar:before {
            content: "\F662";
        }

        .fa-futbol:before {
            content: "\F1E3";
        }

        .fa-galactic-republic:before {
            content: "\F50C";
        }

        .fa-galactic-senate:before {
            content: "\F50D";
        }

        .fa-gamepad:before {
            content: "\F11B";
        }

        .fa-gas-pump:before {
            content: "\F52F";
        }

        .fa-gavel:before {
            content: "\F0E3";
        }

        .fa-gem:before {
            content: "\F3A5";
        }

        .fa-genderless:before {
            content: "\F22D";
        }

        .fa-get-pocket:before {
            content: "\F265";
        }

        .fa-gg:before {
            content: "\F260";
        }

        .fa-gg-circle:before {
            content: "\F261";
        }

        .fa-ghost:before {
            content: "\F6E2";
        }

        .fa-gift:before {
            content: "\F06B";
        }

        .fa-gifts:before {
            content: "\F79C";
        }

        .fa-git:before {
            content: "\F1D3";
        }

        .fa-git-alt:before {
            content: "\F841";
        }

        .fa-git-square:before {
            content: "\F1D2";
        }

        .fa-github:before {
            content: "\F09B";
        }

        .fa-github-alt:before {
            content: "\F113";
        }

        .fa-github-square:before {
            content: "\F092";
        }

        .fa-gitkraken:before {
            content: "\F3A6";
        }

        .fa-gitlab:before {
            content: "\F296";
        }

        .fa-gitter:before {
            content: "\F426";
        }

        .fa-glass-cheers:before {
            content: "\F79F";
        }

        .fa-glass-martini:before {
            content: "\F000";
        }

        .fa-glass-martini-alt:before {
            content: "\F57B";
        }

        .fa-glass-whiskey:before {
            content: "\F7A0";
        }

        .fa-glasses:before {
            content: "\F530";
        }

        .fa-glide:before {
            content: "\F2A5";
        }

        .fa-glide-g:before {
            content: "\F2A6";
        }

        .fa-globe:before {
            content: "\F0AC";
        }

        .fa-globe-africa:before {
            content: "\F57C";
        }

        .fa-globe-americas:before {
            content: "\F57D";
        }

        .fa-globe-asia:before {
            content: "\F57E";
        }

        .fa-globe-europe:before {
            content: "\F7A2";
        }

        .fa-gofore:before {
            content: "\F3A7";
        }

        .fa-golf-ball:before {
            content: "\F450";
        }

        .fa-goodreads:before {
            content: "\F3A8";
        }

        .fa-goodreads-g:before {
            content: "\F3A9";
        }

        .fa-google:before {
            content: "\F1A0";
        }

        .fa-google-drive:before {
            content: "\F3AA";
        }

        .fa-google-play:before {
            content: "\F3AB";
        }

        .fa-google-plus:before {
            content: "\F2B3";
        }

        .fa-google-plus-g:before {
            content: "\F0D5";
        }

        .fa-google-plus-square:before {
            content: "\F0D4";
        }

        .fa-google-wallet:before {
            content: "\F1EE";
        }

        .fa-gopuram:before {
            content: "\F664";
        }

        .fa-graduation-cap:before {
            content: "\F19D";
        }

        .fa-gratipay:before {
            content: "\F184";
        }

        .fa-grav:before {
            content: "\F2D6";
        }

        .fa-greater-than:before {
            content: "\F531";
        }

        .fa-greater-than-equal:before {
            content: "\F532";
        }

        .fa-grimace:before {
            content: "\F57F";
        }

        .fa-grin:before {
            content: "\F580";
        }

        .fa-grin-alt:before {
            content: "\F581";
        }

        .fa-grin-beam:before {
            content: "\F582";
        }

        .fa-grin-beam-sweat:before {
            content: "\F583";
        }

        .fa-grin-hearts:before {
            content: "\F584";
        }

        .fa-grin-squint:before {
            content: "\F585";
        }

        .fa-grin-squint-tears:before {
            content: "\F586";
        }

        .fa-grin-stars:before {
            content: "\F587";
        }

        .fa-grin-tears:before {
            content: "\F588";
        }

        .fa-grin-tongue:before {
            content: "\F589";
        }

        .fa-grin-tongue-squint:before {
            content: "\F58A";
        }

        .fa-grin-tongue-wink:before {
            content: "\F58B";
        }

        .fa-grin-wink:before {
            content: "\F58C";
        }

        .fa-grip-horizontal:before {
            content: "\F58D";
        }

        .fa-grip-lines:before {
            content: "\F7A4";
        }

        .fa-grip-lines-vertical:before {
            content: "\F7A5";
        }

        .fa-grip-vertical:before {
            content: "\F58E";
        }

        .fa-gripfire:before {
            content: "\F3AC";
        }

        .fa-grunt:before {
            content: "\F3AD";
        }

        .fa-guitar:before {
            content: "\F7A6";
        }

        .fa-gulp:before {
            content: "\F3AE";
        }

        .fa-h-square:before {
            content: "\F0FD";
        }

        .fa-hacker-news:before {
            content: "\F1D4";
        }

        .fa-hacker-news-square:before {
            content: "\F3AF";
        }

        .fa-hackerrank:before {
            content: "\F5F7";
        }

        .fa-hamburger:before {
            content: "\F805";
        }

        .fa-hammer:before {
            content: "\F6E3";
        }

        .fa-hamsa:before {
            content: "\F665";
        }

        .fa-hand-holding:before {
            content: "\F4BD";
        }

        .fa-hand-holding-heart:before {
            content: "\F4BE";
        }

        .fa-hand-holding-usd:before {
            content: "\F4C0";
        }

        .fa-hand-lizard:before {
            content: "\F258";
        }

        .fa-hand-middle-finger:before {
            content: "\F806";
        }

        .fa-hand-paper:before {
            content: "\F256";
        }

        .fa-hand-peace:before {
            content: "\F25B";
        }

        .fa-hand-point-down:before {
            content: "\F0A7";
        }

        .fa-hand-point-left:before {
            content: "\F0A5";
        }

        .fa-hand-point-right:before {
            content: "\F0A4";
        }

        .fa-hand-point-up:before {
            content: "\F0A6";
        }

        .fa-hand-pointer:before {
            content: "\F25A";
        }

        .fa-hand-rock:before {
            content: "\F255";
        }

        .fa-hand-scissors:before {
            content: "\F257";
        }

        .fa-hand-spock:before {
            content: "\F259";
        }

        .fa-hands:before {
            content: "\F4C2";
        }

        .fa-hands-helping:before {
            content: "\F4C4";
        }

        .fa-handshake:before {
            content: "\F2B5";
        }

        .fa-hanukiah:before {
            content: "\F6E6";
        }

        .fa-hard-hat:before {
            content: "\F807";
        }

        .fa-hashtag:before {
            content: "\F292";
        }

        .fa-hat-cowboy:before {
            content: "\F8C0";
        }

        .fa-hat-cowboy-side:before {
            content: "\F8C1";
        }

        .fa-hat-wizard:before {
            content: "\F6E8";
        }

        .fa-hdd:before {
            content: "\F0A0";
        }

        .fa-heading:before {
            content: "\F1DC";
        }

        .fa-headphones:before {
            content: "\F025";
        }

        .fa-headphones-alt:before {
            content: "\F58F";
        }

        .fa-headset:before {
            content: "\F590";
        }

        .fa-heart:before {
            content: "\F004";
        }

        .fa-heart-broken:before {
            content: "\F7A9";
        }

        .fa-heartbeat:before {
            content: "\F21E";
        }

        .fa-helicopter:before {
            content: "\F533";
        }

        .fa-highlighter:before {
            content: "\F591";
        }

        .fa-hiking:before {
            content: "\F6EC";
        }

        .fa-hippo:before {
            content: "\F6ED";
        }

        .fa-hips:before {
            content: "\F452";
        }

        .fa-hire-a-helper:before {
            content: "\F3B0";
        }

        .fa-history:before {
            content: "\F1DA";
        }

        .fa-hockey-puck:before {
            content: "\F453";
        }

        .fa-holly-berry:before {
            content: "\F7AA";
        }

        .fa-home:before {
            content: "\F015";
        }

        .fa-hooli:before {
            content: "\F427";
        }

        .fa-hornbill:before {
            content: "\F592";
        }

        .fa-horse:before {
            content: "\F6F0";
        }

        .fa-horse-head:before {
            content: "\F7AB";
        }

        .fa-hospital:before {
            content: "\F0F8";
        }

        .fa-hospital-alt:before {
            content: "\F47D";
        }

        .fa-hospital-symbol:before {
            content: "\F47E";
        }

        .fa-hot-tub:before {
            content: "\F593";
        }

        .fa-hotdog:before {
            content: "\F80F";
        }

        .fa-hotel:before {
            content: "\F594";
        }

        .fa-hotjar:before {
            content: "\F3B1";
        }

        .fa-hourglass:before {
            content: "\F254";
        }

        .fa-hourglass-end:before {
            content: "\F253";
        }

        .fa-hourglass-half:before {
            content: "\F252";
        }

        .fa-hourglass-start:before {
            content: "\F251";
        }

        .fa-house-damage:before {
            content: "\F6F1";
        }

        .fa-houzz:before {
            content: "\F27C";
        }

        .fa-hryvnia:before {
            content: "\F6F2";
        }

        .fa-html5:before {
            content: "\F13B";
        }

        .fa-hubspot:before {
            content: "\F3B2";
        }

        .fa-i-cursor:before {
            content: "\F246";
        }

        .fa-ice-cream:before {
            content: "\F810";
        }

        .fa-icicles:before {
            content: "\F7AD";
        }

        .fa-icons:before {
            content: "\F86D";
        }

        .fa-id-badge:before {
            content: "\F2C1";
        }

        .fa-id-card:before {
            content: "\F2C2";
        }

        .fa-id-card-alt:before {
            content: "\F47F";
        }

        .fa-ideal:before {
            content: "\F913";
        }

        .fa-igloo:before {
            content: "\F7AE";
        }

        .fa-image:before {
            content: "\F03E";
        }

        .fa-images:before {
            content: "\F302";
        }

        .fa-imdb:before {
            content: "\F2D8";
        }

        .fa-inbox:before {
            content: "\F01C";
        }

        .fa-indent:before {
            content: "\F03C";
        }

        .fa-industry:before {
            content: "\F275";
        }

        .fa-infinity:before {
            content: "\F534";
        }

        .fa-info:before {
            content: "\F129";
        }

        .fa-info-circle:before {
            content: "\F05A";
        }

        .fa-instagram:before {
            content: "\F16D";
        }

        .fa-intercom:before {
            content: "\F7AF";
        }

        .fa-internet-explorer:before {
            content: "\F26B";
        }

        .fa-invision:before {
            content: "\F7B0";
        }

        .fa-ioxhost:before {
            content: "\F208";
        }

        .fa-italic:before {
            content: "\F033";
        }

        .fa-itch-io:before {
            content: "\F83A";
        }

        .fa-itunes:before {
            content: "\F3B4";
        }

        .fa-itunes-note:before {
            content: "\F3B5";
        }

        .fa-java:before {
            content: "\F4E4";
        }

        .fa-jedi:before {
            content: "\F669";
        }

        .fa-jedi-order:before {
            content: "\F50E";
        }

        .fa-jenkins:before {
            content: "\F3B6";
        }

        .fa-jira:before {
            content: "\F7B1";
        }

        .fa-joget:before {
            content: "\F3B7";
        }

        .fa-joint:before {
            content: "\F595";
        }

        .fa-joomla:before {
            content: "\F1AA";
        }

        .fa-journal-whills:before {
            content: "\F66A";
        }

        .fa-js:before {
            content: "\F3B8";
        }

        .fa-js-square:before {
            content: "\F3B9";
        }

        .fa-jsfiddle:before {
            content: "\F1CC";
        }

        .fa-kaaba:before {
            content: "\F66B";
        }

        .fa-kaggle:before {
            content: "\F5FA";
        }

        .fa-key:before {
            content: "\F084";
        }

        .fa-keybase:before {
            content: "\F4F5";
        }

        .fa-keyboard:before {
            content: "\F11C";
        }

        .fa-keycdn:before {
            content: "\F3BA";
        }

        .fa-khanda:before {
            content: "\F66D";
        }

        .fa-kickstarter:before {
            content: "\F3BB";
        }

        .fa-kickstarter-k:before {
            content: "\F3BC";
        }

        .fa-kiss:before {
            content: "\F596";
        }

        .fa-kiss-beam:before {
            content: "\F597";
        }

        .fa-kiss-wink-heart:before {
            content: "\F598";
        }

        .fa-kiwi-bird:before {
            content: "\F535";
        }

        .fa-korvue:before {
            content: "\F42F";
        }

        .fa-landmark:before {
            content: "\F66F";
        }

        .fa-language:before {
            content: "\F1AB";
        }

        .fa-laptop:before {
            content: "\F109";
        }

        .fa-laptop-code:before {
            content: "\F5FC";
        }

        .fa-laptop-medical:before {
            content: "\F812";
        }

        .fa-laravel:before {
            content: "\F3BD";
        }

        .fa-lastfm:before {
            content: "\F202";
        }

        .fa-lastfm-square:before {
            content: "\F203";
        }

        .fa-laugh:before {
            content: "\F599";
        }

        .fa-laugh-beam:before {
            content: "\F59A";
        }

        .fa-laugh-squint:before {
            content: "\F59B";
        }

        .fa-laugh-wink:before {
            content: "\F59C";
        }

        .fa-layer-group:before {
            content: "\F5FD";
        }

        .fa-leaf:before {
            content: "\F06C";
        }

        .fa-leanpub:before {
            content: "\F212";
        }

        .fa-lemon:before {
            content: "\F094";
        }

        .fa-less:before {
            content: "\F41D";
        }

        .fa-less-than:before {
            content: "\F536";
        }

        .fa-less-than-equal:before {
            content: "\F537";
        }

        .fa-level-down-alt:before {
            content: "\F3BE";
        }

        .fa-level-up-alt:before {
            content: "\F3BF";
        }

        .fa-life-ring:before {
            content: "\F1CD";
        }

        .fa-lightbulb:before {
            content: "\F0EB";
        }

        .fa-line:before {
            content: "\F3C0";
        }

        .fa-link:before {
            content: "\F0C1";
        }

        .fa-linkedin:before {
            content: "\F08C";
        }

        .fa-linkedin-in:before {
            content: "\F0E1";
        }

        .fa-linode:before {
            content: "\F2B8";
        }

        .fa-linux:before {
            content: "\F17C";
        }

        .fa-lira-sign:before {
            content: "\F195";
        }

        .fa-list:before {
            content: "\F03A";
        }

        .fa-list-alt:before {
            content: "\F022";
        }

        .fa-list-ol:before {
            content: "\F0CB";
        }

        .fa-list-ul:before {
            content: "\F0CA";
        }

        .fa-location-arrow:before {
            content: "\F124";
        }

        .fa-lock:before {
            content: "\F023";
        }

        .fa-lock-open:before {
            content: "\F3C1";
        }

        .fa-long-arrow-alt-down:before {
            content: "\F309";
        }

        .fa-long-arrow-alt-left:before {
            content: "\F30A";
        }

        .fa-long-arrow-alt-right:before {
            content: "\F30B";
        }

        .fa-long-arrow-alt-up:before {
            content: "\F30C";
        }

        .fa-low-vision:before {
            content: "\F2A8";
        }

        .fa-luggage-cart:before {
            content: "\F59D";
        }

        .fa-lyft:before {
            content: "\F3C3";
        }

        .fa-magento:before {
            content: "\F3C4";
        }

        .fa-magic:before {
            content: "\F0D0";
        }

        .fa-magnet:before {
            content: "\F076";
        }

        .fa-mail-bulk:before {
            content: "\F674";
        }

        .fa-mailchimp:before {
            content: "\F59E";
        }

        .fa-male:before {
            content: "\F183";
        }

        .fa-mandalorian:before {
            content: "\F50F";
        }

        .fa-map:before {
            content: "\F279";
        }

        .fa-map-marked:before {
            content: "\F59F";
        }

        .fa-map-marked-alt:before {
            content: "\F5A0";
        }

        .fa-map-marker:before {
            content: "\F041";
        }

        .fa-map-marker-alt:before {
            content: "\F3C5";
        }

        .fa-map-pin:before {
            content: "\F276";
        }

        .fa-map-signs:before {
            content: "\F277";
        }

        .fa-markdown:before {
            content: "\F60F";
        }

        .fa-marker:before {
            content: "\F5A1";
        }

        .fa-mars:before {
            content: "\F222";
        }

        .fa-mars-double:before {
            content: "\F227";
        }

        .fa-mars-stroke:before {
            content: "\F229";
        }

        .fa-mars-stroke-h:before {
            content: "\F22B";
        }

        .fa-mars-stroke-v:before {
            content: "\F22A";
        }

        .fa-mask:before {
            content: "\F6FA";
        }

        .fa-mastodon:before {
            content: "\F4F6";
        }

        .fa-maxcdn:before {
            content: "\F136";
        }

        .fa-mdb:before {
            content: "\F8CA";
        }

        .fa-medal:before {
            content: "\F5A2";
        }

        .fa-medapps:before {
            content: "\F3C6";
        }

        .fa-medium:before {
            content: "\F23A";
        }

        .fa-medium-m:before {
            content: "\F3C7";
        }

        .fa-medkit:before {
            content: "\F0FA";
        }

        .fa-medrt:before {
            content: "\F3C8";
        }

        .fa-meetup:before {
            content: "\F2E0";
        }

        .fa-megaport:before {
            content: "\F5A3";
        }

        .fa-meh:before {
            content: "\F11A";
        }

        .fa-meh-blank:before {
            content: "\F5A4";
        }

        .fa-meh-rolling-eyes:before {
            content: "\F5A5";
        }

        .fa-memory:before {
            content: "\F538";
        }

        .fa-mendeley:before {
            content: "\F7B3";
        }

        .fa-menorah:before {
            content: "\F676";
        }

        .fa-mercury:before {
            content: "\F223";
        }

        .fa-meteor:before {
            content: "\F753";
        }

        .fa-microblog:before {
            content: "\F91A";
        }

        .fa-microchip:before {
            content: "\F2DB";
        }

        .fa-microphone:before {
            content: "\F130";
        }

        .fa-microphone-alt:before {
            content: "\F3C9";
        }

        .fa-microphone-alt-slash:before {
            content: "\F539";
        }

        .fa-microphone-slash:before {
            content: "\F131";
        }

        .fa-microscope:before {
            content: "\F610";
        }

        .fa-microsoft:before {
            content: "\F3CA";
        }

        .fa-minus:before {
            content: "\F068";
        }

        .fa-minus-circle:before {
            content: "\F056";
        }

        .fa-minus-square:before {
            content: "\F146";
        }

        .fa-mitten:before {
            content: "\F7B5";
        }

        .fa-mix:before {
            content: "\F3CB";
        }

        .fa-mixcloud:before {
            content: "\F289";
        }

        .fa-mizuni:before {
            content: "\F3CC";
        }

        .fa-mobile:before {
            content: "\F10B";
        }

        .fa-mobile-alt:before {
            content: "\F3CD";
        }

        .fa-modx:before {
            content: "\F285";
        }

        .fa-monero:before {
            content: "\F3D0";
        }

        .fa-money-bill:before {
            content: "\F0D6";
        }

        .fa-money-bill-alt:before {
            content: "\F3D1";
        }

        .fa-money-bill-wave:before {
            content: "\F53A";
        }

        .fa-money-bill-wave-alt:before {
            content: "\F53B";
        }

        .fa-money-check:before {
            content: "\F53C";
        }

        .fa-money-check-alt:before {
            content: "\F53D";
        }

        .fa-monument:before {
            content: "\F5A6";
        }

        .fa-moon:before {
            content: "\F186";
        }

        .fa-mortar-pestle:before {
            content: "\F5A7";
        }

        .fa-mosque:before {
            content: "\F678";
        }

        .fa-motorcycle:before {
            content: "\F21C";
        }

        .fa-mountain:before {
            content: "\F6FC";
        }

        .fa-mouse:before {
            content: "\F8CC";
        }

        .fa-mouse-pointer:before {
            content: "\F245";
        }

        .fa-mug-hot:before {
            content: "\F7B6";
        }

        .fa-music:before {
            content: "\F001";
        }

        .fa-napster:before {
            content: "\F3D2";
        }

        .fa-neos:before {
            content: "\F612";
        }

        .fa-network-wired:before {
            content: "\F6FF";
        }

        .fa-neuter:before {
            content: "\F22C";
        }

        .fa-newspaper:before {
            content: "\F1EA";
        }

        .fa-nimblr:before {
            content: "\F5A8";
        }

        .fa-node:before {
            content: "\F419";
        }

        .fa-node-js:before {
            content: "\F3D3";
        }

        .fa-not-equal:before {
            content: "\F53E";
        }

        .fa-notes-medical:before {
            content: "\F481";
        }

        .fa-npm:before {
            content: "\F3D4";
        }

        .fa-ns8:before {
            content: "\F3D5";
        }

        .fa-nutritionix:before {
            content: "\F3D6";
        }

        .fa-object-group:before {
            content: "\F247";
        }

        .fa-object-ungroup:before {
            content: "\F248";
        }

        .fa-odnoklassniki:before {
            content: "\F263";
        }

        .fa-odnoklassniki-square:before {
            content: "\F264";
        }

        .fa-oil-can:before {
            content: "\F613";
        }

        .fa-old-republic:before {
            content: "\F510";
        }

        .fa-om:before {
            content: "\F679";
        }

        .fa-opencart:before {
            content: "\F23D";
        }

        .fa-openid:before {
            content: "\F19B";
        }

        .fa-opera:before {
            content: "\F26A";
        }

        .fa-optin-monster:before {
            content: "\F23C";
        }

        .fa-orcid:before {
            content: "\F8D2";
        }

        .fa-osi:before {
            content: "\F41A";
        }

        .fa-otter:before {
            content: "\F700";
        }

        .fa-outdent:before {
            content: "\F03B";
        }

        .fa-page4:before {
            content: "\F3D7";
        }

        .fa-pagelines:before {
            content: "\F18C";
        }

        .fa-pager:before {
            content: "\F815";
        }

        .fa-paint-brush:before {
            content: "\F1FC";
        }

        .fa-paint-roller:before {
            content: "\F5AA";
        }

        .fa-palette:before {
            content: "\F53F";
        }

        .fa-palfed:before {
            content: "\F3D8";
        }

        .fa-pallet:before {
            content: "\F482";
        }

        .fa-paper-plane:before {
            content: "\F1D8";
        }

        .fa-paperclip:before {
            content: "\F0C6";
        }

        .fa-parachute-box:before {
            content: "\F4CD";
        }

        .fa-paragraph:before {
            content: "\F1DD";
        }

        .fa-parking:before {
            content: "\F540";
        }

        .fa-passport:before {
            content: "\F5AB";
        }

        .fa-pastafarianism:before {
            content: "\F67B";
        }

        .fa-paste:before {
            content: "\F0EA";
        }

        .fa-patreon:before {
            content: "\F3D9";
        }

        .fa-pause:before {
            content: "\F04C";
        }

        .fa-pause-circle:before {
            content: "\F28B";
        }

        .fa-paw:before {
            content: "\F1B0";
        }

        .fa-paypal:before {
            content: "\F1ED";
        }

        .fa-peace:before {
            content: "\F67C";
        }

        .fa-pen:before {
            content: "\F304";
        }

        .fa-pen-alt:before {
            content: "\F305";
        }

        .fa-pen-fancy:before {
            content: "\F5AC";
        }

        .fa-pen-nib:before {
            content: "\F5AD";
        }

        .fa-pen-square:before {
            content: "\F14B";
        }

        .fa-pencil-alt:before {
            content: "\F303";
        }

        .fa-pencil-ruler:before {
            content: "\F5AE";
        }

        .fa-penny-arcade:before {
            content: "\F704";
        }

        .fa-people-carry:before {
            content: "\F4CE";
        }

        .fa-pepper-hot:before {
            content: "\F816";
        }

        .fa-percent:before {
            content: "\F295";
        }

        .fa-percentage:before {
            content: "\F541";
        }

        .fa-periscope:before {
            content: "\F3DA";
        }

        .fa-person-booth:before {
            content: "\F756";
        }

        .fa-phabricator:before {
            content: "\F3DB";
        }

        .fa-phoenix-framework:before {
            content: "\F3DC";
        }

        .fa-phoenix-squadron:before {
            content: "\F511";
        }

        .fa-phone:before {
            content: "\F095";
        }

        .fa-phone-alt:before {
            content: "\F879";
        }

        .fa-phone-slash:before {
            content: "\F3DD";
        }

        .fa-phone-square:before {
            content: "\F098";
        }

        .fa-phone-square-alt:before {
            content: "\F87B";
        }

        .fa-phone-volume:before {
            content: "\F2A0";
        }

        .fa-photo-video:before {
            content: "\F87C";
        }

        .fa-php:before {
            content: "\F457";
        }

        .fa-pied-piper:before {
            content: "\F2AE";
        }

        .fa-pied-piper-alt:before {
            content: "\F1A8";
        }

        .fa-pied-piper-hat:before {
            content: "\F4E5";
        }

        .fa-pied-piper-pp:before {
            content: "\F1A7";
        }

        .fa-pied-piper-square:before {
            content: "\F91E";
        }

        .fa-piggy-bank:before {
            content: "\F4D3";
        }

        .fa-pills:before {
            content: "\F484";
        }

        .fa-pinterest:before {
            content: "\F0D2";
        }

        .fa-pinterest-p:before {
            content: "\F231";
        }

        .fa-pinterest-square:before {
            content: "\F0D3";
        }

        .fa-pizza-slice:before {
            content: "\F818";
        }

        .fa-place-of-worship:before {
            content: "\F67F";
        }

        .fa-plane:before {
            content: "\F072";
        }

        .fa-plane-arrival:before {
            content: "\F5AF";
        }

        .fa-plane-departure:before {
            content: "\F5B0";
        }

        .fa-play:before {
            content: "\F04B";
        }

        .fa-play-circle:before {
            content: "\F144";
        }

        .fa-playstation:before {
            content: "\F3DF";
        }

        .fa-plug:before {
            content: "\F1E6";
        }

        .fa-plus:before {
            content: "\F067";
        }

        .fa-plus-circle:before {
            content: "\F055";
        }

        .fa-plus-square:before {
            content: "\F0FE";
        }

        .fa-podcast:before {
            content: "\F2CE";
        }

        .fa-poll:before {
            content: "\F681";
        }

        .fa-poll-h:before {
            content: "\F682";
        }

        .fa-poo:before {
            content: "\F2FE";
        }

        .fa-poo-storm:before {
            content: "\F75A";
        }

        .fa-poop:before {
            content: "\F619";
        }

        .fa-portrait:before {
            content: "\F3E0";
        }

        .fa-pound-sign:before {
            content: "\F154";
        }

        .fa-power-off:before {
            content: "\F011";
        }

        .fa-pray:before {
            content: "\F683";
        }

        .fa-praying-hands:before {
            content: "\F684";
        }

        .fa-prescription:before {
            content: "\F5B1";
        }

        .fa-prescription-bottle:before {
            content: "\F485";
        }

        .fa-prescription-bottle-alt:before {
            content: "\F486";
        }

        .fa-print:before {
            content: "\F02F";
        }

        .fa-procedures:before {
            content: "\F487";
        }

        .fa-product-hunt:before {
            content: "\F288";
        }

        .fa-project-diagram:before {
            content: "\F542";
        }

        .fa-pushed:before {
            content: "\F3E1";
        }

        .fa-puzzle-piece:before {
            content: "\F12E";
        }

        .fa-python:before {
            content: "\F3E2";
        }

        .fa-qq:before {
            content: "\F1D6";
        }

        .fa-qrcode:before {
            content: "\F029";
        }

        .fa-question:before {
            content: "\F128";
        }

        .fa-question-circle:before {
            content: "\F059";
        }

        .fa-quidditch:before {
            content: "\F458";
        }

        .fa-quinscape:before {
            content: "\F459";
        }

        .fa-quora:before {
            content: "\F2C4";
        }

        .fa-quote-left:before {
            content: "\F10D";
        }

        .fa-quote-right:before {
            content: "\F10E";
        }

        .fa-quran:before {
            content: "\F687";
        }

        .fa-r-project:before {
            content: "\F4F7";
        }

        .fa-radiation:before {
            content: "\F7B9";
        }

        .fa-radiation-alt:before {
            content: "\F7BA";
        }

        .fa-rainbow:before {
            content: "\F75B";
        }

        .fa-random:before {
            content: "\F074";
        }

        .fa-raspberry-pi:before {
            content: "\F7BB";
        }

        .fa-ravelry:before {
            content: "\F2D9";
        }

        .fa-react:before {
            content: "\F41B";
        }

        .fa-reacteurope:before {
            content: "\F75D";
        }

        .fa-readme:before {
            content: "\F4D5";
        }

        .fa-rebel:before {
            content: "\F1D0";
        }

        .fa-receipt:before {
            content: "\F543";
        }

        .fa-record-vinyl:before {
            content: "\F8D9";
        }

        .fa-recycle:before {
            content: "\F1B8";
        }

        .fa-red-river:before {
            content: "\F3E3";
        }

        .fa-reddit:before {
            content: "\F1A1";
        }

        .fa-reddit-alien:before {
            content: "\F281";
        }

        .fa-reddit-square:before {
            content: "\F1A2";
        }

        .fa-redhat:before {
            content: "\F7BC";
        }

        .fa-redo:before {
            content: "\F01E";
        }

        .fa-redo-alt:before {
            content: "\F2F9";
        }

        .fa-registered:before {
            content: "\F25D";
        }

        .fa-remove-format:before {
            content: "\F87D";
        }

        .fa-renren:before {
            content: "\F18B";
        }

        .fa-reply:before {
            content: "\F3E5";
        }

        .fa-reply-all:before {
            content: "\F122";
        }

        .fa-replyd:before {
            content: "\F3E6";
        }

        .fa-republican:before {
            content: "\F75E";
        }

        .fa-researchgate:before {
            content: "\F4F8";
        }

        .fa-resolving:before {
            content: "\F3E7";
        }

        .fa-restroom:before {
            content: "\F7BD";
        }

        .fa-retweet:before {
            content: "\F079";
        }

        .fa-rev:before {
            content: "\F5B2";
        }

        .fa-ribbon:before {
            content: "\F4D6";
        }

        .fa-ring:before {
            content: "\F70B";
        }

        .fa-road:before {
            content: "\F018";
        }

        .fa-robot:before {
            content: "\F544";
        }

        .fa-rocket:before {
            content: "\F135";
        }

        .fa-rocketchat:before {
            content: "\F3E8";
        }

        .fa-rockrms:before {
            content: "\F3E9";
        }

        .fa-route:before {
            content: "\F4D7";
        }

        .fa-rss:before {
            content: "\F09E";
        }

        .fa-rss-square:before {
            content: "\F143";
        }

        .fa-ruble-sign:before {
            content: "\F158";
        }

        .fa-ruler:before {
            content: "\F545";
        }

        .fa-ruler-combined:before {
            content: "\F546";
        }

        .fa-ruler-horizontal:before {
            content: "\F547";
        }

        .fa-ruler-vertical:before {
            content: "\F548";
        }

        .fa-running:before {
            content: "\F70C";
        }

        .fa-rupee-sign:before {
            content: "\F156";
        }

        .fa-sad-cry:before {
            content: "\F5B3";
        }

        .fa-sad-tear:before {
            content: "\F5B4";
        }

        .fa-safari:before {
            content: "\F267";
        }

        .fa-salesforce:before {
            content: "\F83B";
        }

        .fa-sass:before {
            content: "\F41E";
        }

        .fa-satellite:before {
            content: "\F7BF";
        }

        .fa-satellite-dish:before {
            content: "\F7C0";
        }

        .fa-save:before {
            content: "\F0C7";
        }

        .fa-schlix:before {
            content: "\F3EA";
        }

        .fa-school:before {
            content: "\F549";
        }

        .fa-screwdriver:before {
            content: "\F54A";
        }

        .fa-scribd:before {
            content: "\F28A";
        }

        .fa-scroll:before {
            content: "\F70E";
        }

        .fa-sd-card:before {
            content: "\F7C2";
        }

        .fa-search:before {
            content: "\F002";
        }

        .fa-search-dollar:before {
            content: "\F688";
        }

        .fa-search-location:before {
            content: "\F689";
        }

        .fa-search-minus:before {
            content: "\F010";
        }

        .fa-search-plus:before {
            content: "\F00E";
        }

        .fa-searchengin:before {
            content: "\F3EB";
        }

        .fa-seedling:before {
            content: "\F4D8";
        }

        .fa-sellcast:before {
            content: "\F2DA";
        }

        .fa-sellsy:before {
            content: "\F213";
        }

        .fa-server:before {
            content: "\F233";
        }

        .fa-servicestack:before {
            content: "\F3EC";
        }

        .fa-shapes:before {
            content: "\F61F";
        }

        .fa-share:before {
            content: "\F064";
        }

        .fa-share-alt:before {
            content: "\F1E0";
        }

        .fa-share-alt-square:before {
            content: "\F1E1";
        }

        .fa-share-square:before {
            content: "\F14D";
        }

        .fa-shekel-sign:before {
            content: "\F20B";
        }

        .fa-shield-alt:before {
            content: "\F3ED";
        }

        .fa-ship:before {
            content: "\F21A";
        }

        .fa-shipping-fast:before {
            content: "\F48B";
        }

        .fa-shirtsinbulk:before {
            content: "\F214";
        }

        .fa-shoe-prints:before {
            content: "\F54B";
        }

        .fa-shopping-bag:before {
            content: "\F290";
        }

        .fa-shopping-basket:before {
            content: "\F291";
        }

        .fa-shopping-cart:before {
            content: "\F07A";
        }

        .fa-shopware:before {
            content: "\F5B5";
        }

        .fa-shower:before {
            content: "\F2CC";
        }

        .fa-shuttle-van:before {
            content: "\F5B6";
        }

        .fa-sign:before {
            content: "\F4D9";
        }

        .fa-sign-in-alt:before {
            content: "\F2F6";
        }

        .fa-sign-language:before {
            content: "\F2A7";
        }

        .fa-sign-out-alt:before {
            content: "\F2F5";
        }

        .fa-signal:before {
            content: "\F012";
        }

        .fa-signature:before {
            content: "\F5B7";
        }

        .fa-sim-card:before {
            content: "\F7C4";
        }

        .fa-simplybuilt:before {
            content: "\F215";
        }

        .fa-sistrix:before {
            content: "\F3EE";
        }

        .fa-sitemap:before {
            content: "\F0E8";
        }

        .fa-sith:before {
            content: "\F512";
        }

        .fa-skating:before {
            content: "\F7C5";
        }

        .fa-sketch:before {
            content: "\F7C6";
        }

        .fa-skiing:before {
            content: "\F7C9";
        }

        .fa-skiing-nordic:before {
            content: "\F7CA";
        }

        .fa-skull:before {
            content: "\F54C";
        }

        .fa-skull-crossbones:before {
            content: "\F714";
        }

        .fa-skyatlas:before {
            content: "\F216";
        }

        .fa-skype:before {
            content: "\F17E";
        }

        .fa-slack:before {
            content: "\F198";
        }

        .fa-slack-hash:before {
            content: "\F3EF";
        }

        .fa-slash:before {
            content: "\F715";
        }

        .fa-sleigh:before {
            content: "\F7CC";
        }

        .fa-sliders-h:before {
            content: "\F1DE";
        }

        .fa-slideshare:before {
            content: "\F1E7";
        }

        .fa-smile:before {
            content: "\F118";
        }

        .fa-smile-beam:before {
            content: "\F5B8";
        }

        .fa-smile-wink:before {
            content: "\F4DA";
        }

        .fa-smog:before {
            content: "\F75F";
        }

        .fa-smoking:before {
            content: "\F48D";
        }

        .fa-smoking-ban:before {
            content: "\F54D";
        }

        .fa-sms:before {
            content: "\F7CD";
        }

        .fa-snapchat:before {
            content: "\F2AB";
        }

        .fa-snapchat-ghost:before {
            content: "\F2AC";
        }

        .fa-snapchat-square:before {
            content: "\F2AD";
        }

        .fa-snowboarding:before {
            content: "\F7CE";
        }

        .fa-snowflake:before {
            content: "\F2DC";
        }

        .fa-snowman:before {
            content: "\F7D0";
        }

        .fa-snowplow:before {
            content: "\F7D2";
        }

        .fa-socks:before {
            content: "\F696";
        }

        .fa-solar-panel:before {
            content: "\F5BA";
        }

        .fa-sort:before {
            content: "\F0DC";
        }

        .fa-sort-alpha-down:before {
            content: "\F15D";
        }

        .fa-sort-alpha-down-alt:before {
            content: "\F881";
        }

        .fa-sort-alpha-up:before {
            content: "\F15E";
        }

        .fa-sort-alpha-up-alt:before {
            content: "\F882";
        }

        .fa-sort-amount-down:before {
            content: "\F160";
        }

        .fa-sort-amount-down-alt:before {
            content: "\F884";
        }

        .fa-sort-amount-up:before {
            content: "\F161";
        }

        .fa-sort-amount-up-alt:before {
            content: "\F885";
        }

        .fa-sort-down:before {
            content: "\F0DD";
        }

        .fa-sort-numeric-down:before {
            content: "\F162";
        }

        .fa-sort-numeric-down-alt:before {
            content: "\F886";
        }

        .fa-sort-numeric-up:before {
            content: "\F163";
        }

        .fa-sort-numeric-up-alt:before {
            content: "\F887";
        }

        .fa-sort-up:before {
            content: "\F0DE";
        }

        .fa-soundcloud:before {
            content: "\F1BE";
        }

        .fa-sourcetree:before {
            content: "\F7D3";
        }

        .fa-spa:before {
            content: "\F5BB";
        }

        .fa-space-shuttle:before {
            content: "\F197";
        }

        .fa-speakap:before {
            content: "\F3F3";
        }

        .fa-speaker-deck:before {
            content: "\F83C";
        }

        .fa-spell-check:before {
            content: "\F891";
        }

        .fa-spider:before {
            content: "\F717";
        }

        .fa-spinner:before {
            content: "\F110";
        }

        .fa-splotch:before {
            content: "\F5BC";
        }

        .fa-spotify:before {
            content: "\F1BC";
        }

        .fa-spray-can:before {
            content: "\F5BD";
        }

        .fa-square:before {
            content: "\F0C8";
        }

        .fa-square-full:before {
            content: "\F45C";
        }

        .fa-square-root-alt:before {
            content: "\F698";
        }

        .fa-squarespace:before {
            content: "\F5BE";
        }

        .fa-stack-exchange:before {
            content: "\F18D";
        }

        .fa-stack-overflow:before {
            content: "\F16C";
        }

        .fa-stackpath:before {
            content: "\F842";
        }

        .fa-stamp:before {
            content: "\F5BF";
        }

        .fa-star:before {
            content: "\F005";
        }

        .fa-star-and-crescent:before {
            content: "\F699";
        }

        .fa-star-half:before {
            content: "\F089";
        }

        .fa-star-half-alt:before {
            content: "\F5C0";
        }

        .fa-star-of-david:before {
            content: "\F69A";
        }

        .fa-star-of-life:before {
            content: "\F621";
        }

        .fa-staylinked:before {
            content: "\F3F5";
        }

        .fa-steam:before {
            content: "\F1B6";
        }

        .fa-steam-square:before {
            content: "\F1B7";
        }

        .fa-steam-symbol:before {
            content: "\F3F6";
        }

        .fa-step-backward:before {
            content: "\F048";
        }

        .fa-step-forward:before {
            content: "\F051";
        }

        .fa-stethoscope:before {
            content: "\F0F1";
        }

        .fa-sticker-mule:before {
            content: "\F3F7";
        }

        .fa-sticky-note:before {
            content: "\F249";
        }

        .fa-stop:before {
            content: "\F04D";
        }

        .fa-stop-circle:before {
            content: "\F28D";
        }

        .fa-stopwatch:before {
            content: "\F2F2";
        }

        .fa-store:before {
            content: "\F54E";
        }

        .fa-store-alt:before {
            content: "\F54F";
        }

        .fa-strava:before {
            content: "\F428";
        }

        .fa-stream:before {
            content: "\F550";
        }

        .fa-street-view:before {
            content: "\F21D";
        }

        .fa-strikethrough:before {
            content: "\F0CC";
        }

        .fa-stripe:before {
            content: "\F429";
        }

        .fa-stripe-s:before {
            content: "\F42A";
        }

        .fa-stroopwafel:before {
            content: "\F551";
        }

        .fa-studiovinari:before {
            content: "\F3F8";
        }

        .fa-stumbleupon:before {
            content: "\F1A4";
        }

        .fa-stumbleupon-circle:before {
            content: "\F1A3";
        }

        .fa-subscript:before {
            content: "\F12C";
        }

        .fa-subway:before {
            content: "\F239";
        }

        .fa-suitcase:before {
            content: "\F0F2";
        }

        .fa-suitcase-rolling:before {
            content: "\F5C1";
        }

        .fa-sun:before {
            content: "\F185";
        }

        .fa-superpowers:before {
            content: "\F2DD";
        }

        .fa-superscript:before {
            content: "\F12B";
        }

        .fa-supple:before {
            content: "\F3F9";
        }

        .fa-surprise:before {
            content: "\F5C2";
        }

        .fa-suse:before {
            content: "\F7D6";
        }

        .fa-swatchbook:before {
            content: "\F5C3";
        }

        .fa-swift:before {
            content: "\F8E1";
        }

        .fa-swimmer:before {
            content: "\F5C4";
        }

        .fa-swimming-pool:before {
            content: "\F5C5";
        }

        .fa-symfony:before {
            content: "\F83D";
        }

        .fa-synagogue:before {
            content: "\F69B";
        }

        .fa-sync:before {
            content: "\F021";
        }

        .fa-sync-alt:before {
            content: "\F2F1";
        }

        .fa-syringe:before {
            content: "\F48E";
        }

        .fa-table:before {
            content: "\F0CE";
        }

        .fa-table-tennis:before {
            content: "\F45D";
        }

        .fa-tablet:before {
            content: "\F10A";
        }

        .fa-tablet-alt:before {
            content: "\F3FA";
        }

        .fa-tablets:before {
            content: "\F490";
        }

        .fa-tachometer-alt:before {
            content: "\F3FD";
        }

        .fa-tag:before {
            content: "\F02B";
        }

        .fa-tags:before {
            content: "\F02C";
        }

        .fa-tape:before {
            content: "\F4DB";
        }

        .fa-tasks:before {
            content: "\F0AE";
        }

        .fa-taxi:before {
            content: "\F1BA";
        }

        .fa-teamspeak:before {
            content: "\F4F9";
        }

        .fa-teeth:before {
            content: "\F62E";
        }

        .fa-teeth-open:before {
            content: "\F62F";
        }

        .fa-telegram:before {
            content: "\F2C6";
        }

        .fa-telegram-plane:before {
            content: "\F3FE";
        }

        .fa-temperature-high:before {
            content: "\F769";
        }

        .fa-temperature-low:before {
            content: "\F76B";
        }

        .fa-tencent-weibo:before {
            content: "\F1D5";
        }

        .fa-tenge:before {
            content: "\F7D7";
        }

        .fa-terminal:before {
            content: "\F120";
        }

        .fa-text-height:before {
            content: "\F034";
        }

        .fa-text-width:before {
            content: "\F035";
        }

        .fa-th:before {
            content: "\F00A";
        }

        .fa-th-large:before {
            content: "\F009";
        }

        .fa-th-list:before {
            content: "\F00B";
        }

        .fa-the-red-yeti:before {
            content: "\F69D";
        }

        .fa-theater-masks:before {
            content: "\F630";
        }

        .fa-themeco:before {
            content: "\F5C6";
        }

        .fa-themeisle:before {
            content: "\F2B2";
        }

        .fa-thermometer:before {
            content: "\F491";
        }

        .fa-thermometer-empty:before {
            content: "\F2CB";
        }

        .fa-thermometer-full:before {
            content: "\F2C7";
        }

        .fa-thermometer-half:before {
            content: "\F2C9";
        }

        .fa-thermometer-quarter:before {
            content: "\F2CA";
        }

        .fa-thermometer-three-quarters:before {
            content: "\F2C8";
        }

        .fa-think-peaks:before {
            content: "\F731";
        }

        .fa-thumbs-down:before {
            content: "\F165";
        }

        .fa-thumbs-up:before {
            content: "\F164";
        }

        .fa-thumbtack:before {
            content: "\F08D";
        }

        .fa-ticket-alt:before {
            content: "\F3FF";
        }

        .fa-times:before {
            content: "\F00D";
        }

        .fa-times-circle:before {
            content: "\F057";
        }

        .fa-tint:before {
            content: "\F043";
        }

        .fa-tint-slash:before {
            content: "\F5C7";
        }

        .fa-tired:before {
            content: "\F5C8";
        }

        .fa-toggle-off:before {
            content: "\F204";
        }

        .fa-toggle-on:before {
            content: "\F205";
        }

        .fa-toilet:before {
            content: "\F7D8";
        }

        .fa-toilet-paper:before {
            content: "\F71E";
        }

        .fa-toolbox:before {
            content: "\F552";
        }

        .fa-tools:before {
            content: "\F7D9";
        }

        .fa-tooth:before {
            content: "\F5C9";
        }

        .fa-torah:before {
            content: "\F6A0";
        }

        .fa-torii-gate:before {
            content: "\F6A1";
        }

        .fa-tractor:before {
            content: "\F722";
        }

        .fa-trade-federation:before {
            content: "\F513";
        }

        .fa-trademark:before {
            content: "\F25C";
        }

        .fa-traffic-light:before {
            content: "\F637";
        }

        .fa-trailer:before {
            content: "\F941";
        }

        .fa-train:before {
            content: "\F238";
        }

        .fa-tram:before {
            content: "\F7DA";
        }

        .fa-transgender:before {
            content: "\F224";
        }

        .fa-transgender-alt:before {
            content: "\F225";
        }

        .fa-trash:before {
            content: "\F1F8";
        }

        .fa-trash-alt:before {
            content: "\F2ED";
        }

        .fa-trash-restore:before {
            content: "\F829";
        }

        .fa-trash-restore-alt:before {
            content: "\F82A";
        }

        .fa-tree:before {
            content: "\F1BB";
        }

        .fa-trello:before {
            content: "\F181";
        }

        .fa-tripadvisor:before {
            content: "\F262";
        }

        .fa-trophy:before {
            content: "\F091";
        }

        .fa-truck:before {
            content: "\F0D1";
        }

        .fa-truck-loading:before {
            content: "\F4DE";
        }

        .fa-truck-monster:before {
            content: "\F63B";
        }

        .fa-truck-moving:before {
            content: "\F4DF";
        }

        .fa-truck-pickup:before {
            content: "\F63C";
        }

        .fa-tshirt:before {
            content: "\F553";
        }

        .fa-tty:before {
            content: "\F1E4";
        }

        .fa-tumblr:before {
            content: "\F173";
        }

        .fa-tumblr-square:before {
            content: "\F174";
        }

        .fa-tv:before {
            content: "\F26C";
        }

        .fa-twitch:before {
            content: "\F1E8";
        }

        .fa-twitter:before {
            content: "\F099";
        }

        .fa-twitter-square:before {
            content: "\F081";
        }

        .fa-typo3:before {
            content: "\F42B";
        }

        .fa-uber:before {
            content: "\F402";
        }

        .fa-ubuntu:before {
            content: "\F7DF";
        }

        .fa-uikit:before {
            content: "\F403";
        }

        .fa-umbraco:before {
            content: "\F8E8";
        }

        .fa-umbrella:before {
            content: "\F0E9";
        }

        .fa-umbrella-beach:before {
            content: "\F5CA";
        }

        .fa-underline:before {
            content: "\F0CD";
        }

        .fa-undo:before {
            content: "\F0E2";
        }

        .fa-undo-alt:before {
            content: "\F2EA";
        }

        .fa-uniregistry:before {
            content: "\F404";
        }

        .fa-unity:before {
            content: "\F949";
        }

        .fa-universal-access:before {
            content: "\F29A";
        }

        .fa-university:before {
            content: "\F19C";
        }

        .fa-unlink:before {
            content: "\F127";
        }

        .fa-unlock:before {
            content: "\F09C";
        }

        .fa-unlock-alt:before {
            content: "\F13E";
        }

        .fa-untappd:before {
            content: "\F405";
        }

        .fa-upload:before {
            content: "\F093";
        }

        .fa-ups:before {
            content: "\F7E0";
        }

        .fa-usb:before {
            content: "\F287";
        }

        .fa-user:before {
            content: "\F007";
        }

        .fa-user-alt:before {
            content: "\F406";
        }

        .fa-user-alt-slash:before {
            content: "\F4FA";
        }

        .fa-user-astronaut:before {
            content: "\F4FB";
        }

        .fa-user-check:before {
            content: "\F4FC";
        }

        .fa-user-circle:before {
            content: "\F2BD";
        }

        .fa-user-clock:before {
            content: "\F4FD";
        }

        .fa-user-cog:before {
            content: "\F4FE";
        }

        .fa-user-edit:before {
            content: "\F4FF";
        }

        .fa-user-friends:before {
            content: "\F500";
        }

        .fa-user-graduate:before {
            content: "\F501";
        }

        .fa-user-injured:before {
            content: "\F728";
        }

        .fa-user-lock:before {
            content: "\F502";
        }

        .fa-user-md:before {
            content: "\F0F0";
        }

        .fa-user-minus:before {
            content: "\F503";
        }

        .fa-user-ninja:before {
            content: "\F504";
        }

        .fa-user-nurse:before {
            content: "\F82F";
        }

        .fa-user-plus:before {
            content: "\F234";
        }

        .fa-user-secret:before {
            content: "\F21B";
        }

        .fa-user-shield:before {
            content: "\F505";
        }

        .fa-user-slash:before {
            content: "\F506";
        }

        .fa-user-tag:before {
            content: "\F507";
        }

        .fa-user-tie:before {
            content: "\F508";
        }

        .fa-user-times:before {
            content: "\F235";
        }

        .fa-users:before {
            content: "\F0C0";
        }

        .fa-users-cog:before {
            content: "\F509";
        }

        .fa-usps:before {
            content: "\F7E1";
        }

        .fa-ussunnah:before {
            content: "\F407";
        }

        .fa-utensil-spoon:before {
            content: "\F2E5";
        }

        .fa-utensils:before {
            content: "\F2E7";
        }

        .fa-vaadin:before {
            content: "\F408";
        }

        .fa-vector-square:before {
            content: "\F5CB";
        }

        .fa-venus:before {
            content: "\F221";
        }

        .fa-venus-double:before {
            content: "\F226";
        }

        .fa-venus-mars:before {
            content: "\F228";
        }

        .fa-viacoin:before {
            content: "\F237";
        }

        .fa-viadeo:before {
            content: "\F2A9";
        }

        .fa-viadeo-square:before {
            content: "\F2AA";
        }

        .fa-vial:before {
            content: "\F492";
        }

        .fa-vials:before {
            content: "\F493";
        }

        .fa-viber:before {
            content: "\F409";
        }

        .fa-video:before {
            content: "\F03D";
        }

        .fa-video-slash:before {
            content: "\F4E2";
        }

        .fa-vihara:before {
            content: "\F6A7";
        }

        .fa-vimeo:before {
            content: "\F40A";
        }

        .fa-vimeo-square:before {
            content: "\F194";
        }

        .fa-vimeo-v:before {
            content: "\F27D";
        }

        .fa-vine:before {
            content: "\F1CA";
        }

        .fa-vk:before {
            content: "\F189";
        }

        .fa-vnv:before {
            content: "\F40B";
        }

        .fa-voicemail:before {
            content: "\F897";
        }

        .fa-volleyball-ball:before {
            content: "\F45F";
        }

        .fa-volume-down:before {
            content: "\F027";
        }

        .fa-volume-mute:before {
            content: "\F6A9";
        }

        .fa-volume-off:before {
            content: "\F026";
        }

        .fa-volume-up:before {
            content: "\F028";
        }

        .fa-vote-yea:before {
            content: "\F772";
        }

        .fa-vr-cardboard:before {
            content: "\F729";
        }

        .fa-vuejs:before {
            content: "\F41F";
        }

        .fa-walking:before {
            content: "\F554";
        }

        .fa-wallet:before {
            content: "\F555";
        }

        .fa-warehouse:before {
            content: "\F494";
        }

        .fa-water:before {
            content: "\F773";
        }

        .fa-wave-square:before {
            content: "\F83E";
        }

        .fa-waze:before {
            content: "\F83F";
        }

        .fa-weebly:before {
            content: "\F5CC";
        }

        .fa-weibo:before {
            content: "\F18A";
        }

        .fa-weight:before {
            content: "\F496";
        }

        .fa-weight-hanging:before {
            content: "\F5CD";
        }

        .fa-weixin:before {
            content: "\F1D7";
        }

        .fa-whatsapp:before {
            content: "\F232";
        }

        .fa-whatsapp-square:before {
            content: "\F40C";
        }

        .fa-wheelchair:before {
            content: "\F193";
        }

        .fa-whmcs:before {
            content: "\F40D";
        }

        .fa-wifi:before {
            content: "\F1EB";
        }

        .fa-wikipedia-w:before {
            content: "\F266";
        }

        .fa-wind:before {
            content: "\F72E";
        }

        .fa-window-close:before {
            content: "\F410";
        }

        .fa-window-maximize:before {
            content: "\F2D0";
        }

        .fa-window-minimize:before {
            content: "\F2D1";
        }

        .fa-window-restore:before {
            content: "\F2D2";
        }

        .fa-windows:before {
            content: "\F17A";
        }

        .fa-wine-bottle:before {
            content: "\F72F";
        }

        .fa-wine-glass:before {
            content: "\F4E3";
        }

        .fa-wine-glass-alt:before {
            content: "\F5CE";
        }

        .fa-wix:before {
            content: "\F5CF";
        }

        .fa-wizards-of-the-coast:before {
            content: "\F730";
        }

        .fa-wolf-pack-battalion:before {
            content: "\F514";
        }

        .fa-won-sign:before {
            content: "\F159";
        }

        .fa-wordpress:before {
            content: "\F19A";
        }

        .fa-wordpress-simple:before {
            content: "\F411";
        }

        .fa-wpbeginner:before {
            content: "\F297";
        }

        .fa-wpexplorer:before {
            content: "\F2DE";
        }

        .fa-wpforms:before {
            content: "\F298";
        }

        .fa-wpressr:before {
            content: "\F3E4";
        }

        .fa-wrench:before {
            content: "\F0AD";
        }

        .fa-x-ray:before {
            content: "\F497";
        }

        .fa-xbox:before {
            content: "\F412";
        }

        .fa-xing:before {
            content: "\F168";
        }

        .fa-xing-square:before {
            content: "\F169";
        }

        .fa-y-combinator:before {
            content: "\F23B";
        }

        .fa-yahoo:before {
            content: "\F19E";
        }

        .fa-yammer:before {
            content: "\F840";
        }

        .fa-yandex:before {
            content: "\F413";
        }

        .fa-yandex-international:before {
            content: "\F414";
        }

        .fa-yarn:before {
            content: "\F7E3";
        }

        .fa-yelp:before {
            content: "\F1E9";
        }

        .fa-yen-sign:before {
            content: "\F157";
        }

        .fa-yin-yang:before {
            content: "\F6AD";
        }

        .fa-yoast:before {
            content: "\F2B1";
        }

        .fa-youtube:before {
            content: "\F167";
        }

        .fa-youtube-square:before {
            content: "\F431";
        }

        .fa-zhihu:before {
            content: "\F63F";
        }

        .sr-only,
        .bootstrap-datetimepicker-widget table th.next::after,
        .bootstrap-datetimepicker-widget table th.prev::after,
        .bootstrap-datetimepicker-widget .picker-switch::after,
        .bootstrap-datetimepicker-widget .btn[data-action=today]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=clear]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=togglePeriod]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=showMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=showHours]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=decrementMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=decrementHours]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=incrementMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=incrementHours]::after {
            border: 0;
            clip: rect(0, 0, 0, 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }

        .sr-only-focusable:active,
        .sr-only-focusable:focus {
            clip: auto;
            height: auto;
            margin: 0;
            overflow: visible;
            position: static;
            width: auto;
        }

        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(1.6em + 0.75rem + 2px) !important;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #757575;
            line-height: calc(1.6em + 0.75rem);
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            position: absolute;
            top: 50%;
            right: 3px;
            width: 20px;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
            top: 60%;
            border-color: #343a40 transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            width: 0;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: -2px;
            position: absolute;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.6em + 0.75rem);
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-results__message {
            color: #6c757d;
        }

        .select2-container--bootstrap4 .select2-selection--multiple {
            min-height: calc(1.6em + 0.75rem + 2px) !important;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__rendered {
            box-sizing: border-box;
            list-style: none;
            margin: 0;
            padding: 0 5px;
            width: 100%;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            color: #343a40;
            border: 1px solid #bdc6d0;
            border-radius: 0.2rem;
            padding: 0;
            padding-right: 5px;
            cursor: pointer;
            float: left;
            margin-top: 0.3em;
            margin-right: 5px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: #bdc6d0;
            font-weight: bold;
            margin-left: 3px;
            margin-right: 1px;
            padding-right: 3px;
            padding-left: 3px;
            float: left;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #343a40;
        }

        .select2-container {
            display: block;
        }

        .select2-container *:focus {
            outline: 0;
        }

        .input-group .select2-container--bootstrap4 {
            flex-grow: 1;
        }

        .input-group-prepend ~ .select2-container--bootstrap4 .select2-selection {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group > .select2-container--bootstrap4:not(:last-child) .select2-selection {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .select2-container--bootstrap4 .select2-selection {
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            width: 100%;
        }

        @media (prefers-reduced-motion: reduce) {
            .select2-container--bootstrap4 .select2-selection {
                transition: none;
            }
        }

        .select2-container--bootstrap4.select2-container--focus .select2-selection {
            border-color: #a1cbef;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }

        .select2-container--bootstrap4.select2-container--focus.select2-container--open .select2-selection {
            border-bottom: none;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .select2-container--bootstrap4.select2-container--disabled .select2-selection,
        .select2-container--bootstrap4.select2-container--disabled.select2-container--focus .select2-selection {
            background-color: #e9ecef;
            cursor: not-allowed;
            border-color: #ced4da;
            box-shadow: none;
        }

        .select2-container--bootstrap4.select2-container--disabled .select2-search__field,
        .select2-container--bootstrap4.select2-container--disabled.select2-container--focus .select2-search__field {
            background-color: transparent;
        }

        select.is-invalid ~ .select2-container--bootstrap4 .select2-selection,
        form.was-validated select:invalid ~ .select2-container--bootstrap4 .select2-selection {
            border-color: #e3342f;
        }

        select.is-valid ~ .select2-container--bootstrap4 .select2-selection,
        form.was-validated select:valid ~ .select2-container--bootstrap4 .select2-selection {
            border-color: #38c172;
        }

        .select2-container--bootstrap4 .select2-dropdown {
            border-color: #ced4da;
            border-top: none;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .select2-container--bootstrap4 .select2-dropdown.select2-dropdown--above {
            border-top: 1px solid #ced4da;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        .select2-container--bootstrap4 .select2-dropdown .select2-results__option[aria-selected=true] {
            background-color: #e9ecef;
        }

        .select2-container--bootstrap4 .select2-results__option--highlighted,
        .select2-container--bootstrap4 .select2-results__option--highlighted.select2-results__option[aria-selected=true] {
            background-color: #3490dc;
            color: #f8f9fa;
        }

        .select2-container--bootstrap4 .select2-results__option[role=group] {
            padding: 0;
        }

        .select2-container--bootstrap4 .select2-results > .select2-results__options {
            max-height: 15em;
            overflow-y: auto;
        }

        .select2-container--bootstrap4 .select2-results__group {
            padding: 6px;
            display: list-item;
            color: #6c757d;
        }

        .select2-container--bootstrap4 .select2-selection__clear {
            width: 1.2em;
            height: 1.2em;
            line-height: 1.15em;
            padding-left: 0.3em;
            margin-top: 0.5em;
            border-radius: 100%;
            background-color: #6c757d;
            color: #f8f9fa;
            float: right;
            margin-right: 0.3em;
        }

        .select2-container--bootstrap4 .select2-selection__clear:hover {
            background-color: #343a40;
        }

        .sr-only,
        .bootstrap-datetimepicker-widget table th.next::after,
        .bootstrap-datetimepicker-widget table th.prev::after,
        .bootstrap-datetimepicker-widget .picker-switch::after,
        .bootstrap-datetimepicker-widget .btn[data-action=today]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=clear]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=togglePeriod]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=showMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=showHours]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=decrementMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=decrementHours]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=incrementMinutes]::after,
        .bootstrap-datetimepicker-widget .btn[data-action=incrementHours]::after {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .bootstrap-datetimepicker-widget {
            list-style: none;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu {
            display: block;
            margin: 2px 0;
            padding: 4px;
            width: 14rem;
        }

        @media (min-width: 576px) {
            .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
                width: 38em;
            }
        }

        @media (min-width: 768px) {
            .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
                width: 38em;
            }
        }

        @media (min-width: 992px) {
            .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
                width: 38em;
            }
        }

        .bootstrap-datetimepicker-widget.dropdown-menu:before,
        .bootstrap-datetimepicker-widget.dropdown-menu:after {
            content: "";
            display: inline-block;
            position: absolute;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.bottom:before {
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid #ccc;
            border-bottom-color: rgba(0, 0, 0, 0.2);
            top: -7px;
            left: 7px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.bottom:after {
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid white;
            top: -6px;
            left: 8px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.top:before {
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-top: 7px solid #ccc;
            border-top-color: rgba(0, 0, 0, 0.2);
            bottom: -7px;
            left: 6px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.top:after {
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid white;
            bottom: -6px;
            left: 7px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.float-right:before {
            left: auto;
            right: 6px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.float-right:after {
            left: auto;
            right: 7px;
        }

        .bootstrap-datetimepicker-widget.dropdown-menu.wider {
            width: 16rem;
        }

        .bootstrap-datetimepicker-widget .list-unstyled {
            margin: 0;
        }

        .bootstrap-datetimepicker-widget a[data-action] {
            padding: 6px 0;
        }

        .bootstrap-datetimepicker-widget a[data-action]:active {
            box-shadow: none;
        }

        .bootstrap-datetimepicker-widget .timepicker-hour,
        .bootstrap-datetimepicker-widget .timepicker-minute,
        .bootstrap-datetimepicker-widget .timepicker-second {
            width: 54px;
            font-weight: bold;
            font-size: 1.2em;
            margin: 0;
        }

        .bootstrap-datetimepicker-widget button[data-action] {
            padding: 6px;
        }

        .bootstrap-datetimepicker-widget .btn[data-action=incrementHours]::after {
            content: "Increment Hours";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=incrementMinutes]::after {
            content: "Increment Minutes";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=decrementHours]::after {
            content: "Decrement Hours";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=decrementMinutes]::after {
            content: "Decrement Minutes";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=showHours]::after {
            content: "Show Hours";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=showMinutes]::after {
            content: "Show Minutes";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=togglePeriod]::after {
            content: "Toggle AM/PM";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=clear]::after {
            content: "Clear the picker";
        }

        .bootstrap-datetimepicker-widget .btn[data-action=today]::after {
            content: "Set the date to today";
        }

        .bootstrap-datetimepicker-widget .picker-switch {
            text-align: center;
        }

        .bootstrap-datetimepicker-widget .picker-switch::after {
            content: "Toggle Date and Time Screens";
        }

        .bootstrap-datetimepicker-widget .picker-switch td {
            padding: 0;
            margin: 0;
            height: auto;
            width: auto;
            line-height: inherit;
        }

        .bootstrap-datetimepicker-widget .picker-switch td span {
            line-height: 2.5;
            height: 2.5em;
            width: 100%;
        }

        .bootstrap-datetimepicker-widget table {
            width: 100%;
            margin: 0;
        }

        .bootstrap-datetimepicker-widget table td,
        .bootstrap-datetimepicker-widget table th {
            text-align: center;
            border-radius: 0.25rem;
        }

        .bootstrap-datetimepicker-widget table th {
            height: 20px;
            line-height: 20px;
            width: 20px;
        }

        .bootstrap-datetimepicker-widget table th.picker-switch {
            width: 145px;
        }

        .bootstrap-datetimepicker-widget table th.disabled,
        .bootstrap-datetimepicker-widget table th.disabled:hover {
            background: none;
            color: #6c757d;
            cursor: not-allowed;
        }

        .bootstrap-datetimepicker-widget table th.prev::after {
            content: "Previous Month";
        }

        .bootstrap-datetimepicker-widget table th.next::after {
            content: "Next Month";
        }

        .bootstrap-datetimepicker-widget table thead tr:first-child th {
            cursor: pointer;
        }

        .bootstrap-datetimepicker-widget table thead tr:first-child th:hover {
            background: #e9ecef;
        }

        .bootstrap-datetimepicker-widget table td {
            height: 54px;
            line-height: 54px;
            width: 54px;
        }

        .bootstrap-datetimepicker-widget table td.cw {
            font-size: 0.8em;
            height: 20px;
            line-height: 20px;
            color: #6c757d;
        }

        .bootstrap-datetimepicker-widget table td.day {
            height: 20px;
            line-height: 20px;
            width: 20px;
        }

        .bootstrap-datetimepicker-widget table td.day:hover,
        .bootstrap-datetimepicker-widget table td.hour:hover,
        .bootstrap-datetimepicker-widget table td.minute:hover,
        .bootstrap-datetimepicker-widget table td.second:hover {
            background: #e9ecef;
            cursor: pointer;
        }

        .bootstrap-datetimepicker-widget table td.old,
        .bootstrap-datetimepicker-widget table td.new {
            color: #6c757d;
        }

        .bootstrap-datetimepicker-widget table td.today {
            position: relative;
        }

        .bootstrap-datetimepicker-widget table td.today:before {
            content: "";
            display: inline-block;
            border: solid transparent;
            border-width: 0 0 7px 7px;
            border-bottom-color: #3490dc;
            border-top-color: rgba(0, 0, 0, 0.2);
            position: absolute;
            bottom: 4px;
            right: 4px;
        }

        .bootstrap-datetimepicker-widget table td.active,
        .bootstrap-datetimepicker-widget table td.active:hover {
            background-color: #3490dc;
            color: #fff;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        }

        .bootstrap-datetimepicker-widget table td.active.today:before {
            border-bottom-color: #fff;
        }

        .bootstrap-datetimepicker-widget table td.disabled,
        .bootstrap-datetimepicker-widget table td.disabled:hover {
            background: none;
            color: #6c757d;
            cursor: not-allowed;
        }

        .bootstrap-datetimepicker-widget table td span {
            display: inline-block;
            width: 54px;
            height: 54px;
            line-height: 54px;
            margin: 2px 1.5px;
            cursor: pointer;
            border-radius: 0.25rem;
        }

        .bootstrap-datetimepicker-widget table td span:hover {
            background: #e9ecef;
        }

        .bootstrap-datetimepicker-widget table td span.active {
            background-color: #3490dc;
            color: #fff;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        }

        .bootstrap-datetimepicker-widget table td span.old {
            color: #6c757d;
        }

        .bootstrap-datetimepicker-widget table td span.disabled,
        .bootstrap-datetimepicker-widget table td span.disabled:hover {
            background: none;
            color: #6c757d;
            cursor: not-allowed;
        }

        .bootstrap-datetimepicker-widget.usetwentyfour td.hour {
            height: 27px;
            line-height: 27px;
        }

        .input-group [data-toggle=datetimepicker] {
            cursor: pointer;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .subpage-header {
            margin-top: 50px;
        }

        .subpage-main {
            margin-top: 20px;
        }

        .dropdown-toggle.active-dropdown::after {
            -webkit-transform: rotate(-90deg);
            transform: rotate(-90deg);
        }

        body {
            margin: 10px;
        }

        .wizard li > a {
            padding: 10px 12px 10px;
            margin-right: 5px;
            background: #353a40;
            color: white;
            position: relative;
            display: inline-block;
        }

        .wizard li > a:before {
            width: 0;
            height: 0;
            border-top: 20px inset transparent;
            border-bottom: 20px inset transparent;
            border-left: 20px solid #fff;
            position: absolute;
            content: "";
            top: 0;
            left: 0;
        }

        .wizard li > a:after {
            width: 0;
            height: 0;
            border-top: 20px inset transparent;
            border-bottom: 20px inset transparent;
            border-left: 20px solid #353a40;
            position: absolute;
            content: "";
            top: 0;
            right: -20px;
            z-index: 2;
        }

        .wizard li:first-child > a:before,
        .wizard li:last-child > a:after {
            border: none;
        }

        .wizard li:first-child > a {
            border-radius: 4px 0 0 4px;
        }

        .wizard li:last-child > a {
            border-radius: 0 4px 4px 0;
        }

        .wizard .badge {
            margin: 0 5px 0 18px;
            position: relative;
            top: -1px;
        }

        .wizard li:first-child .badge {
            margin-left: 0;
        }

        .wizard .current {
            background: #efefef;
            color: #000;
        }

        .wizard .current:after {
            border-left-color: #efefef;
        }

        /**
 * @author Script47 (https://github.com/Script47/Toast)
 * @description Toast - A Bootstrap 4.2+ jQuery plugin for the toast component
 * @version 0.7.1
 **/

        .toast-container {
            position: -webkit-sticky;
            position: sticky;
            z-index: 1055;
            top: 0;
        }

        .toast-wrapper {
            position: absolute;
            z-index: 1055;
            top: 0;
            right: 0;
            margin: 5px;
        }

        .toast-container > .toast-wrapper > .toast {
            min-width: 150px;
            background-color: white;
            border-top: none;
        }

        .toast-container > .toast-wrapper > .toast > .toast-header strong {
            padding-right: 20px;
        }

        .document-editor {
            border: 1px solid var(--ck-color-base-border);
            border-radius: var(--ck-border-radius);
            /* Set vertical boundaries for the document editor. */
            max-height: calc(100vh - 110px);
            /* This element is a flex container for easier rendering. */
            display: flex;
            flex-flow: column nowrap;
        }

        .document-editor__toolbar {
            /* Make sure the toolbar container is always above the editable. */
            z-index: 1;
            /* Create the illusion of the toolbar floating over the editable. */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            /* Use the CKEditor CSS variables to keep the UI consistent. */
            border-bottom: 1px solid var(--ck-color-toolbar-border);
        }

        /* Adjust the look of the toolbar inside the container. */

        .document-editor__toolbar .ck-toolbar {
            border: 0;
            border-radius: 0;
        }

        /* Make the editable container look like the inside of a native word processor application. */

        .document-editor__editable-container {
            padding: calc( 2 * var(--ck-spacing-large) );
            background: var(--ck-color-base-foreground);
            /* Make it possible to scroll the "page" of the edited content. */
            overflow-y: scroll;
        }

        .document-editor__editable-container .ck-editor__editable {
            /* Set the dimensions of the "page". */
            width: 21cm;
            min-height: 29.9cm;
            /* Keep the "page" off the boundaries of the container. */
            padding: 1cm 2cm 2cm;
            border: 1px lightgray solid;
            border-radius: var(--ck-border-radius);
            background: white;
            /* The "page" should cast a slight shadow (3D illusion). */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            /* Center the "page". */
            margin: 0 auto;
        }

        /* Set the default font for the "page" of the content. */

        .document-editor .ck-content,
        .document-editor .ck-heading-dropdown .ck-list .ck-button__label {
            font: 16px/1.6 "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        /* Adjust the headings dropdown to host some larger heading styles. */

        .document-editor .ck-heading-dropdown .ck-list .ck-button__label {
            line-height: calc( 1.7 * var(--ck-line-height-base) * var(--ck-font-size-base) );
            min-width: 6em;
        }

        /* Scale down all heading previews because they are way too big to be presented in the UI.
Preserve the relative scale, though. */

        .document-editor .ck-heading-dropdown .ck-list .ck-button:not(.ck-heading_paragraph) .ck-button__label {
            -webkit-transform: scale(0.8);
            transform: scale(0.8);
            -webkit-transform-origin: left;
            transform-origin: left;
        }

        /* Set the styles for "Heading 1". */

        .document-editor .ck-content h2,
        .document-editor .ck-heading-dropdown .ck-heading_heading1 .ck-button__label {
            font-size: 2.18em;
            font-weight: normal;
        }

        .document-editor .ck-content h2 {
            line-height: 1.37em;
            padding-top: 0.342em;
            margin-bottom: 0.142em;
        }

        /* Set the styles for "Heading 2". */

        .document-editor .ck-content h3,
        .document-editor .ck-heading-dropdown .ck-heading_heading2 .ck-button__label {
            font-size: 1.75em;
            font-weight: normal;
            color: #009dff;
        }

        .document-editor .ck-heading-dropdown .ck-heading_heading2.ck-on .ck-button__label {
            color: var(--ck-color-list-button-on-text);
        }

        /* Set the styles for "Heading 2". */

        .document-editor .ck-content h3 {
            line-height: 1.86em;
            padding-top: 0.171em;
            margin-bottom: 0.357em;
        }

        /* Set the styles for "Heading 3". */

        .document-editor .ck-content h4,
        .document-editor .ck-heading-dropdown .ck-heading_heading3 .ck-button__label {
            font-size: 1.31em;
            font-weight: bold;
        }

        .document-editor .ck-content h4 {
            line-height: 1.24em;
            padding-top: 0.286em;
            margin-bottom: 0.952em;
        }

        /* Set the styles for "Paragraph". */

        .document-editor .ck-content p {
            font-size: 1em;
            line-height: 1.63em;
            padding-top: 0.5em;
            margin-bottom: 1.13em;
        }

        /* Make the block quoted text serif with some additional spacing. */

        .document-editor .ck-content blockquote {
            font-family: Georgia, serif;
            margin-left: calc( 2 * var(--ck-spacing-large) );
            margin-right: calc( 2 * var(--ck-spacing-large) );
        }

        .fixed-top-2 {
            margin-top: 56px;
        }

        .select2 {
            width: 100%;
        }

        .grid-stack-item.card {
            border: none !important;
        }

        .grid-stack > .grid-stack-item > .grid-stack-item-content {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25em;
        }

        div.dt-button-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            background: radial-gradient(ellipse farthest-corner at center, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.7) 100%);
        }


    </style>
    @yield('additionalStyle')
</head>
<body class="@yield("bodyClass")">
@yield("content")
</body>
</html>
