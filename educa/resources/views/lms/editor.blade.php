@extends('layouts.lms')

@section('pageContent')
    <style>
        #wrapper {
            overflow-x: hidden;
        }

        #sidebar-wrapper {
            position: static;
            min-height: calc(100vh - 60px);
            max-height: calc(100vh - 60px);
            overflow: auto;
            margin-left: -15rem;
            -webkit-transition: margin .25s ease-out;
            -moz-transition: margin .25s ease-out;
            -o-transition: margin .25s ease-out;
            transition: margin .25s ease-out;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
        }

        #sidebar-wrapper {
            width: 25rem;
        }

        #page-content-wrapper {
            min-width: 100vw;
            height: calc(100vh - 60px);
            overflow: auto;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }

        body {
           font-size:  1.1rem;
        }

        .circle {
            width: 140px;
            height: 140px;
            background: #00488e;
            color: white !important;
            -moz-border-radius: 70px;
            -webkit-border-radius: 70px;
            border-radius: 70px;
            text-align: center;
        }
    </style>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-light border-right" id="sidebar-wrapper">
            <h5>{{ $course->name }}</h5>
            <h6>Inhalt</h6>
            <div class="list-group list-group-flush">
                @foreach($course->chapter as $chapter)
                    <a data-toggle="collapse" href="#collapseChapter_{{  $chapter["id"] }}" role="button" class="list-group-item list-group-item-action bg-light">{{ $chapter->name }}</a>
                    <div class="collapse @if($c_chapter == $chapter["id"]) show @endif" id="collapseChapter_{{ $chapter["id"] }}">
                        <div class="card" style="
    margin-left: 15px;
">
                            <div class="list-group list-group-flush">
                                @foreach($chapter->page as $page)
                                    @if($page["type"] == "section")
                                        <div href="#" style="background-color: #ccc;" class="list-group-item"><b>{{ $page->name }}</b></div>
                                    @else
                                <a id="{{ $chapter["id"]."_".$page["id"] }}_link" href="/lms/viewer?c={{ $coursefile }}&chapter={{ $chapter["id"] }}&page={{ $page["id"] }}" class="@if($c_chapter == $chapter["id"] and $c_page == $page["id"]) active @else bg-light @endif list-group-item list-group-item-action">@if($page["type"] == "task") <i class="fas fa-tasks"></i> @endif{{ $page->name }}</a>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- /#sidebar-wrapper -->
        <div id="page-content-wrapper">

                <div class="editor" name="description">

                {!! $pageContent !!}
                </div>
        </div>
    </div>

    <div class="modal fade" id="detailLMSModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailLMSModal_Information">Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detailLMSModal_body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fenster schließen</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additionalScript')
    <script src="/js/imageMapResizer.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script>
        $(document).ready(function() {

            var element = document.getElementById("{{ $c_chapter."_".$c_page }}_link");

            element.scrollIntoView();


            $('map').imageMapResize();
            $( window ).resize(function() {
                $('map').imageMapResize();
            });
            $(function () {
                $('[data-toggle="popover"]').popover()
            });

            $(".showText").on("click", function(e){
                e.preventDefault();
                /*
                   your code here
                */
                var text = $(this).attr('data-text');
                var example = $(this).attr('data-example');
                var title = $(this).attr('data-title');
                $('#detailLMSModal').modal('show');
                if(title === "" || title === undefined) {
                    $('#detailLMSModal_Information').html("<b>Information</b>");
                } else {
                     $('#detailLMSModal_Information').html("<b>" + title + "</b>");
                }
                $('#detailLMSModal_body').html(text);
                if(example !== "" && example !== undefined)
                {
                     $('#detailLMSModal_body').html(
                         $('#detailLMSModal_body').html() + "<br><br><b>Beispiel</b><br>" + example);
                }

                // $('#textContent').show();
                // if(title === "" || title === undefined) {
                //     $('#textContent').html(text);
                // } else {
                //     $('#textContent').html("<b>" + title + "</b><br>" + text);
                // }
                // if(example !== "" && example !== undefined)
                // {
                //     $('#textContent').html(
                //         $('#textContent').html() + "<br><br><b>Beispiel</b><br>" + example);
                // }
                });
        });
    </script>

    <script src="/ckeditor_lms/ckeditor.js"></script>
    <script>InlineEditor
            .create( document.querySelector( '.editor' ), {

                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'link',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'highlight',
                        'underline',
                        'horizontalLine',
                        '|',
                        'alignment',
                        'indent',
                        'outdent',
                        '|',
                        'fontBackgroundColor',
                        'fontFamily',
                        'fontSize',
                        'fontColor',
                        '|',
                        'imageUpload',
                        'blockQuote',
                        'insertTable',
                        'mediaEmbed',
                        'undo',
                        'redo'
                    ]
                },
                language: 'de',
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:full',
                        'imageStyle:side'
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells',
                        'tableCellProperties',
                        'tableProperties'
                    ]
                },
                licenseKey: '',

            } )
            .then( editor => {
                window.editor = editor;








            } )
            .catch( error => {
                console.error( 'Oops, something went wrong!' );
                console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                console.warn( 'Build id: fx0uivhs34um-5n9xmk8kkhpt' );
                console.error( error );
            } );
    </script>

@endsection
