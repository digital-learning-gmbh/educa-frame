@extends('layouts.lms')

@section('pageContent')
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
    <style>
        #left,
        #right,
        #middle,
        #drag4 {
            min-height: 200px;
            background-color: #eee;
        }
        .dropper {
            margin: 5px;
            padding: 4px;
        }
        .gu-mirror {
            position: fixed !important;
            margin: 0 !important;
            z-index: 9999 !important;
            opacity: 0.8;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
            filter: alpha(opacity=80);
        }
        .gu-hide {
            display: none !important;
        }
        .gu-unselectable {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }
        .gu-transit {
            opacity: 0.2;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=20)";
            filter: alpha(opacity=20);
        }
    </style>
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
                    <a href="/lms/viewer?c={{ $coursefile }}&chapter={{  $chapter["id"] }}&page=1" role="button" class="list-group-item list-group-item-action bg-light">{{ $chapter->name }}</a>
                    <div class="collapse @if($c_chapter == $chapter["id"]) show @endif" id="collapseChapter_{{ $chapter["id"] }}">
                        <div class="card" style="
    margin-left: 15px;
">
                            <div class="list-group list-group-flush">
                                @foreach($chapter->page as $page)
                                    @if($page["type"] == "section")
                                        <div style="background-color: #ccc;" class="list-group-item"><b>{{ $page->name }}</b></div>
                                    @else
                                        <a id="{{ $chapter["id"]."_".$page["id"] }}_link" href="/lms/viewer?c={{ $coursefile }}&chapter={{ $chapter["id"] }}&page={{ $page["id"] }}" class="@if($c_chapter == $chapter["id"] and $c_page == $page["id"]) active @else bg-light @endif list-group-item list-group-item-action">
                                            @if($page["type"] == "task") <i class="fas fa-tasks"></i>
                                            @elseif($page["type"] == "remember") <i class="fas fa-lightbulb"></i>
                                            @elseif($page["type"] == "check") <i class="far fa-check-square"></i>
                                            @endif
                                                {{ $page->name }}</a>
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
        <!--            <h2 class="m-3">{{ $realPage->name }}</h2> -->
            {!! $pageContent !!}
        </div>
    </div>

    <div class="modal fade" id="detailLMSModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
    <script src="/js/jquery.maphilight.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js'></script>
    <script>
        $(document).ready(function() {

            var element = document.getElementById("{{ $c_chapter."_".$c_page }}_link");

            element.scrollIntoView();


            $("map[name='image-map']").imageMapResize();
            $( window ).resize(function() {
                $("map[name='image-map']").imageMapResize();
            });
            $(function () {
                $('[data-toggle="popover"]').popover()
            });

            $('.img-fluid:visible').maphilight();


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
                    $('#detailLMSModal_Information').html("");
                } else {
                    $('#detailLMSModal_Information').html("<b>" + title + "</b>");
                }
                $('#detailLMSModal_body').html(text);
                if(example !== "" && example !== undefined)
                {
                    $('#detailLMSModal_body').html(
                        $('#detailLMSModal_body').html() + "<br><br><b>Beispiel</b><br>" + example);
                }

                $(".show2HideModal").on("click", function(e){

                    $('#detailLMSModal').modal('hide');
                    var show = $(this).attr('data-show');
                    var hide = $(this).attr('data-hide');
                    $('#' + show).show("slow");
                    $('#' + hide).hide("slow");
                });

            });


            $(".show2Hide").on("click", function(e){
                e.preventDefault();
                var show = $(this).attr('data-show');
                var hide = $(this).attr('data-hide');
                $('#' + show).show("slow");
                $('#' + hide).hide("slow");setTimeout(function(){
                window.dispatchEvent(new Event('resize'));
                if(!$('#image2').hasClass('maphilighted')) {
                    $("map[name='image-map2']").imageMapResize();
                    $('#image2:visible').maphilight();
                }
                if(!$('#image3').hasClass('maphilighted')) {
                    $("map[name='image-map3']").imageMapResize();
                    $('#image3:visible').maphilight();
                }
                if(!$('#image4').hasClass('maphilighted')) {
                    $("map[name='image-map4']").imageMapResize();
                    $('#image4:visible').maphilight();
                }
                if(!$('#image5').hasClass('maphilighted')) {
                    $("map[name='image-map5']").imageMapResize();
                    $('#image5:visible').maphilight();
                }
                },1000);

            });

            $(".showElement").on("click", function(e){
                var show = $(this).attr('data-show');
                $('#' + show).show("slow");
            });

            var left = 'left';
            var right = 'right';
            var middle = 'middle';
            var drag4 = 'drag4';
            dragula([document.getElementById(left), document.getElementById(right), document.getElementById(middle), document.getElementById(drag4), document.getElementById('drag5'), document.getElementById('drag6'), document.getElementById('drag7'), document.getElementById('drag8')]);
        });

        function clickShowText(te)
        {

            /*
               your code here
            */
            var text = $(te).attr('data-text');
            var example = $(te).attr('data-example');
            var title = $(te).attr('data-title');
            $('#detailLMSModal').modal('show');
            if(title === "" || title === undefined) {
                $('#detailLMSModal_Information').html("");
            } else {
                $('#detailLMSModal_Information').html("<b>" + title + "</b>");
            }
            $('#detailLMSModal_body').html(text);
            if(example !== "" && example !== undefined)
            {
                $('#detailLMSModal_body').html(
                    $('#detailLMSModal_body').html() + "<br><br><b>Beispiel</b><br>" + example);
            }
            $(".show2HideModal").on("click", function(e){

                $('#detailLMSModal').modal('hide');
                var show = $(this).attr('data-show');
                var hide = $(this).attr('data-hide');
                $('#' + show).show("slow");
                $('#' + hide).hide("slow");
            });
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
        }

        function mark(id, correct)
        {
            $('#'+ id).removeClass('border-success');
            $('#'+ id).removeClass('border-danger');
            if($('#' + id).index() == correct  || ($.isArray(correct) && correct.includes($('#' + id).index())))
            {
                $('#'+ id).addClass('border-success');
                return  1;
            } else {
                $('#'+ id).addClass('border-danger');
                return 0;
            }
        }

        function select(id, correct)
        {
            $('#'+ id).removeClass('border-success');
            $('#'+ id).removeClass('border-danger');
            if($('#' + id).val() == correct)
            {
                $('#'+ id).addClass('border-success');
                return  1;
            } else {
                $('#'+ id).addClass('border-danger');
                return 0;
            }
        }

        function markElement(id, correct)
        {
            $('#'+ id).removeClass('border-success');
            $('#'+ id).removeClass('border-danger');
            if($('#' + id).parent().attr('id') == correct)
            {
                $('#'+ id).addClass('border-success');
                return 1;
            } else {
                $('#'+ id).addClass('border-danger');
                return 0;
            }
        }

        function radioCheck(id, correct)
        {
            var object = $("[name='" + id + "']");
            object.removeClass('is-invalid');
            object.removeClass('is-valid');
            object.addClass('is-invalid');
            $('input[name=' + id + '][value="' +  correct + '"]').removeClass('is-invalid');
            $('input[name=' + id + '][value="' +  correct + '"]').addClass('is-valid');
        }

        function multiCheck(id, correct)
        {
            var object = $("[id='" + id + "']");
            object.removeClass('is-invalid');
            object.removeClass('is-valid');
            if((correct ==  'checked' && object.is(':checked') ) || (correct !=  'checked' && !object.is(':checked') )  ) {
                object.addClass('is-valid');
                return 1;
            } else {
                object.addClass('is-invalid');
                return 0;
            }
        }
    </script>
@endsection
