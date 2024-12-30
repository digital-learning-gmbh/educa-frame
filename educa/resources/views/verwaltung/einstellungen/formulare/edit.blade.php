@extends('verwaltung.main')
@section('additionalStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
@endsection
@section('siteContent')
    <h3>{{ __('Formulare') }}</h3>
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">Formular: {{ $formular->name }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Bearbeiten eines Formulars</h6>
            <div style="margin-bottom: 3px;">
                <div class="float-right">
                    <select class="form-control" onchange="changeRevision(this.options[this.selectedIndex].value);">
                        @foreach($formular->revisions as $revision)
                            <option value="{{ $revision->id }}" @if($selectedRevision->id == $revision->id) selected @endif>{{ $revision->number }}. Version</option>
                        @endforeach
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <form class="form-horizontal" method="POST" onsubmit="setDataOnSubmit()">
                {{ csrf_field() }}
                <input required class="form-control col-6" placeholder="Name des Formulars" value="{{ $formular->name }}" name="formular_name">
            <div id="editor">
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <a href="/verwaltung/einstellungen/formulare" class="btn btn-white">Abbrechen</a>
                    <button type="submit" class="btn btn-success">Speichern</button>
                </div>
            </div>
                <input name="data" id="dataInput" type="hidden" style="display: none;" value="{{ $selectedRevision->data }}">
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="uploadProgress" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Lade Dateien hoch</h4>
                </div>
                <div class="modal-body">
                    <p>Bitte warten, bis alle Dateien hochgeladen worden sind.</p>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('additionalScript')
    <script>
        function changeRevision(id) {
            window.location.href = "/verwaltung/einstellungen/formulare/{{ $formular->id }}/edit?revision=" + id;
        }
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
            var fields = [{
                label: 'Video',
                attrs: {
                    type: 'video'
                },
                icon: '📹'
                },
                {
                    label: 'Bild',
                    attrs: {
                        type: 'image'
                    },
                    icon: '📷'
                },
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
                video: function (fieldData) {
                    return {
                        field: '<div id="' + fieldData.name + '">' +
                            '<video controls="" width="100%">\n' +
                            '  <source src="/formulare/{{ $formular->id }}/getFile?video=' +fieldData.id + '" type="video/mp4">\n' +
                            '  Your browser does not support HTML5 video.\n' +
                            '</video>' +
                            '</div>',
                        onRender: function () {
                            console.log(fieldData);
                        }
                    };
                },
                image: function (fieldData) {
                    return {
                        field: '<div id="' + fieldData.name + '">' +
                            '  <img style="max-width: 100%;" src="/formulare/{{ $formular->id }}/getFile?image=' +fieldData.id + '">' +
                            '</div>',
                        onRender: function () {

                        }
                    };
                },
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
