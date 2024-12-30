@section('pageContent')
    <div class="container gedf-wrapper">
    <div class="row">
    <div class="col-12 gedf-main">

        <form action="/social/group/{{ $group->id }}/{{ $activeReiter->count }}/createBeitrag" method="POST" id="beitragform">
            @csrf
            <div class="card gedf-card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="posts-tab" data-toggle="tab" href="#posts" role="tab" aria-controls="posts" aria-selected="true"><i class="fas fa-bullhorn"></i> Ankündigung</a>
                        </li>

                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                            <div class="form-group">
                                <label class="sr-only" for="message">post</label>
                                <textarea required class="form-control" name="message" id="message" rows="3" placeholder="Verfasse hier einen Beitrag..."></textarea>
                            </div>
                        </div>
                        Bild(er) anhängen:
                        <div id="dropzone" class="upload-drop-zone"></div>
                    </div>
                    <div class="btn-toolbar justify-content-between">
                        <div class="btn-group">
                            <button onclick="submitPost()" id="submitbutton" class="btn btn-primary">Beitrag erstellen</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @foreach($activeReiter->beitrags() as $beitrag)
        <div class="card gedf-card" id="btr{{ $beitrag->id }}">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mr-2">
                            <img class="rounded-circle" width="45" src="/api/image/cloud/?cloud_id={{ $beitrag->cloudid }}&size=45" alt="">
                        </div>
                        <div class="ml-2">
                            <div class="h5 m-0">{{ \App\CloudID::findOrFail($beitrag->cloudid)->displayName }}</div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <div class="text-muted h7 mb-2"><div class="float-right"><i class="fa fa-clock"></i> {{ (new \Illuminate\Support\Carbon(new \DateTime($beitrag->created_at)))->diffForHumans() }} </div></div>
                <p class="card-text">
                    {!! $beitrag->content !!}
                </p>
                @foreach($beitrag->media as $media)
                <a href="/storage/{{ explode("/", $media->disk_name)[1] }}" target="_blank">
                    <img src="/storage/{{ explode("/", $media->disk_name)[1] }}" class="img-thumbnail">
                </a>
                @endforeach
            </div>
            <div class="card-footer">
                {{ count($beitrag->likes) }}
                <form class="card-link" style="display:inline" id="like{{ $beitrag->id }}" action="/social/group/{{ $group->id }}/{{ $activeReiter->count }}/likeBeitrag" method="POST">
                    @csrf
                    <a class="card-link" href="#" onclick="document.getElementById('like{{ $beitrag->id }}').submit()"><i class="far fa-thumbs-up"></i> Gefällt mir @if($beitrag->likes->contains($cloudid))
                            nicht mehr
                        @endif</a>
                    <input type="hidden" name="beitragid" value="{{ $beitrag->id }}">
                </form>
                <a href="#comment{{ $beitrag->id }}" class="card-link" data-toggle="collapse" aria-expanded="false" aria-controls="comment{{ $beitrag->id }}"><i class="fa fa-comment"></i> Kommentar</a>
                <a href="#btr{{ $beitrag->id }}" class="card-link beitragbutton"><i class="fas fa-share-square"></i> Link zum Beitrag</a>
                <div class="collapse" id="comment{{ $beitrag->id }}">
                    <form class="pt-3" action="/social/group/{{ $group->id }}/{{ $activeReiter->count }}/createComment" method="POST">
                        @csrf
                        <input type="hidden" name="beitragid" value="{{ $beitrag->id }}">
                        <div class="form-group">
                            <label class="sr-only" for="comment">Kommentar</label>
                            <textarea required class="form-control editor" name="comment" id="comment" rows="3" placeholder="Verfasse hier einen Kommentar..."></textarea>
                        </div>
                        <div class="btn-toolbar justify-content-between">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">Kommentar erstellen</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if(sizeof($beitrag->comments) > 0)
            <div class="group-footer">

                @foreach($beitrag->comments as $comment)
                <div class="group-comment">
                    <div class="float-left">
                        <img class="rounded-circle" src="/api/image/cloud/?cloud_id={{ $comment->cloudid }}&size=45" alt="">
                    </div>
                    <div class="media-body">
                        {{ \App\CloudID::findOrFail($comment->cloudid)->displayName }}:
                        <br>
                        {{ $comment->content }}
                        <br>
                        <small class="text-muted">{{ (new \Illuminate\Support\Carbon(new \DateTime($comment->created_at)))->diffForHumans() }}</small>
                    </div>
                </div>
                @endforeach

            </div>
            @endif
        </div>
        @endforeach
    </div>
    </div>

</div>

<style>
    .upload-drop-zone {
        height: 200px;
        border-width: 2px;
        margin-bottom: 20px;
        color: #ccc;
        border-style: dashed;
        border-color: #ccc;
        line-height: 200px;
        text-align: center
    }
    .upload-drop-zone.drop {
        color: #222;
        border-color: #222;
    }
</style>
@endsection
@section('additionalScript')
    <script src="/ckeditor_inline/ckeditor.js"></script>
    <script>ClassicEditor
            .create( document.querySelector( '#message' ), {

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
        $( document ).ready(function() {
            var dropzoneOptions = {
                dictDefaultMessage: 'Klicke hier oder lege hier Bilder ab, um sie zu dem Beitrag hinzuzufügen.',
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 10000,
                url : '/social/group/{{ $group->id }}/{{ $activeReiter->count }}/createBeitrag',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            };
            window.newDropzone = new Dropzone("#dropzone", dropzoneOptions);
            $("#dropzone").addClass("dropzone");
            Dropzone.forElement('#dropzone').on('sending', function(file, xhr, formData){
                formData.append('message', window.editor.getData());
                formData.append("_token", "{{ csrf_token() }}");
            });
            Dropzone.forElement('#dropzone').on("complete", function (file) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    location.reload();
                }
            });
        });
        function submitPost() {
            var d = Dropzone.forElement('#dropzone');
            if(d.files.length == 0)
            {
                $('#beitragform').submit();
            } else {
                d.processQueue();
            }
        }
        $("#submitbutton").click(function(e){
            e.preventDefault()
        });
        $(".beitragbutton").click(function(e){
            e.preventDefault();
            location.hash = $(this).attr('href');
            var copyText = window.location.href;
            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);
            document.execCommand('copy');
            $.toast({
                title: 'Link kopiert!',
                subtitle: 'jetzt',
                content: 'Der Link zum Beitrag wurde in die Zwischenablage kopiert.',
                type: 'success',
                delay: 5000
            });
        });
    </script>

@endsection
