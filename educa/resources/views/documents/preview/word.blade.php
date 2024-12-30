@extends('app')

@section('content')
    @include('documents.preview.head')
    <div id="doc">
    </div>

@endsection

@section('additionalScript')
    <!--polyfills for IE11-->
    <script crossorigin src="https://unpkg.com/core-js-bundle@3.3.2/minified.js"></script>
    <!--dependencies-->
    <script crossorigin src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
    <!--library-->
    <script src="/js/docx-preview.js"></script>


    <script>

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '{{ $schema }}/dokument/{{ $dokument->id }}/download', true);
        xhr.responseType = 'blob';
        xhr.onload = function(e) {
            if (this.status == 200) {
                var myBlob = this.response;
                loadDocx(myBlob)
                // myBlob is now the blob that the object URL pointed to.
            }
        };
        xhr.send();

        function loadDocx(file) {
            var container = document.getElementById("doc");

            docx.renderAsync(file, container, null, {
                debug: true,
                experimental: true
            }).then(function(x){ console.log(x); });
        }
    </script>

@endsection
