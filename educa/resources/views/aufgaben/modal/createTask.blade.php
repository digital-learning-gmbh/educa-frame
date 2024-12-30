@extends('layouts.aufgaben')

@section('additionalStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
@endsection
@section('pageContent')
    <style>
        .row {
            display: flex !important;
        }
    </style>
    <div class="container">
        <div class="card" style="margin-top: 20px;">
            <div class="card-body">
                <h5 class="card-title">Neue Aufgabe</h5>
                <form method="POST">
                    @csrf
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-2 col-form-label"><i class="fas fa-pencil-alt"></i> Titel</label>
                        <div class="col-sm-10">
                            <input name="title" type="text" class="form-control" placeholder="Titel hinzufügen" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-2 col-form-label"><i class="fas fa-list-ul"></i> Aufgabenstellung</label>
                        <div class="col-sm-10">
                <textarea class="editor" name="description">
                &lt;p>Details zu dieser Aufgabe hinzufügen&lt;/p>
                </textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><i class="fas fa-calendar-week"></i> Zeitbegrenzung</label>
                        <div class="col-sm-4">
                            <div class="input-group date" id="datepicker11" data-target-input="nearest">
                                <input required id="besuchsdatum" name="begin" type="text" class="form-control datetimepicker-input" data-target="#datepicker11"/>
                                <div class="input-group-append" data-target="#datepicker11" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2" style="text-align: center;">
                            <i class="fas fa-arrow-right" style="font-size: 26px;"></i>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                <input required name="end" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker2"/>
                                <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><i class="fas fa-users"></i> Gruppe</label>
                        <div class="col-sm-10">
                            <select class="select2" multiple name="gruppe[]" id="gruppe" required>

                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><i class="fas fa-clipboard-check"></i> Abgabeformat</label>
                        <div class="col-sm-10">
                            <select id="format" class="select2" name="format" onchange="changeFormat();">
                                <option value="no" selected>Keine Abgabe erforderlich</option>
                                <option value="text">Antworttext</option>
                                <option value="file">Dokumente</option>
                                <option value="digitalesBlatt">Digitales Blatt</option>
                            </select>
                        </div>
                    </div>

                        <div class="form-group"   id="digitalesBlatt" style="display: none; width: 100%;">
                        <div id="editor" style="width: 100%;" >

                        </div></div>

                    <a href="/calendar" class="btn btn-secondary">Abbrechen</a>
                    <button type="submit"  class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section("additionalScript")
    <style>

        .card {
            background-color: #f7f7f7;
        }
        .form-wrap.form-builder .frmb {
            min-height: 200px !important;
        }
    </style>
    <script>
        function changeFormat()
        {
            if($('#format').val() == "digitalesBlatt")
            {
                $('#digitalesBlatt').show();
                window.formBuilder.actions.clearFields();
            } else {
                $('#digitalesBlatt').hide();
            }
        }
    </script>
    <script src="/ckeditor_inline/ckeditor.js"></script>
    <script>ClassicEditor
            .create( document.querySelector( '.editor' ), {

                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'link',
                        '|',
                        'fontColor',
                        'fontFamily',
                        'fontSize',
                        '|',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'indent',
                        'outdent',
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
                        'mergeTableCells'
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
                console.warn( 'Build id: s8z9nfdk7w4t-xpu28fnnqx3z' );
                console.error( error );
            } );
    </script>
    <script>
        $("#tags").select2({
            tags: true,
            theme: 'bootstrap4',
        });

        $("#gruppe").select2({
            minimumInputLength: 0,
            theme: 'bootstrap4',
            ajax: {
                url: "/api/search/group",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                }
            },
        });
    </script>
    <script>
        function clipboard() {
            /* Get the text field */
            var copyText = document.getElementById("link");

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");
        }
    </script>

    <script src="/js/formbuilder/jquery-ui.min.js"></script>
    <script src="/js/formbuilder/dist/form-builder.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    <script src="/js/jSignature/jSignature.min.js"></script>
    <script>


        function setDataOnSubmit() {
            $('#dataInput').val(window.formBuilder.actions.getData('json', false));

            return false;
        }

        function fileFormChange(mainId,url, type) {
            $('#uploadProgress').modal('show');
            $('#' + mainId + '_status').remove();
            const files = $( '#' + type + '-' + mainId)[0].files;
            const formData = new FormData();

            for (let i = 0; i < files.length; i++) {
                let file = files[i];

                formData.append('file', file);
            }
            formData.append('_token', $('input[name="_token"]').val());
            formData.append('type', type);

            const id = $('#name-' + mainId).val();
            formData.append('id', id);

            fetch(url, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
                .catch(error => alert('Error:', error))
                .then(response =>{
                    $('#uploadProgress').modal('hide');
                    if(response.status == 1)
                    {
                        $( '<p class="alert alert-success" id="' + mainId + '_status">Erfolgreich hochgeladen</p>' ).insertBefore( '#file-' + mainId );
                    } else {
                        $( '<p class="alert alert-danger" id="' + mainId + '_status">Fehler beim Upload</p>' ).insertBefore( '#file' + mainId );
                    }
                });
        }

        jQuery(function ($) {
            var fields = [
                {
                    label: 'Star Rating',
                    attrs: {
                        type: 'starRating'
                    },
                    icon: '🌟'
                },
                {
                    label: 'Unterschrift',
                    attrs: {
                        type: 'signature'
                    },
                    icon: '🖊️'
                }
            ];
            var templates = {
                starRating: function(fieldData) {
                    return {
                        field: '<div id="'+fieldData.name+'">' + '</div>',
                        onRender: function() {
                            $(document.getElementById(fieldData.name)).rateYo({rating: 3.6});
                        }
                    };
                },
                signature: function(fieldData) {
                    return {
                        field: '<div id="'+fieldData.name+'">' + '</div>',
                        onRender: function() {
                            $(document.getElementById(fieldData.name)).jSignature();
                            //$(document.getElementById(fieldData.name)).rateYo({rating: 3.6});
                        }
                    };
                }
            };
            var disableFields = ['autocomplete', 'button', 'hidden', 'file'];


            var options = {
                disabledAttrs: ["className", "style","access", "name", "value"],
                i18n: {
                    locale: 'de-DE'
                },
                disabledActionButtons: ['clear', 'save', 'data'],
                disableFields: disableFields,
                disabledSubtypes: {
                    text: ['password'],
                },
                fields: fields,//[],
                templates: templates,
                disableHTMLLabels: true,
                typeUserAttrs: {
                    video: {
                        video: {
                            label: 'Video-Datei',
                            type: 'file',
                            url: '/formulare/1/edit/upload',
                            value: "",
                        }
                    },
                    image: {
                        image: {
                            label: 'Bild-Datei',
                            type: 'file',
                            url: '/formulare/1/edit/upload',
                            value: "",
                        }
                    }
                },
                onOpenFieldEdit: function(editPanel) {
                    $(".fld-video").change(function(){
                        console.log($(this));
                        var id = $(this).attr("id").replace("video-","");
                        fileFormChange(id, $(this).attr("url"), 'video');
                    });
                    $(".fld-image").change(function(){
                        var id = $(this).attr("id").replace("image-","");
                        fileFormChange(id, $(this).attr("url"),'image');
                    });
                },
                formData: $('#dataInput').val()
            };

            window.formBuilder = $(document.getElementById('editor')).formBuilder(options);
        });
    </script>
@endsection
