
@section('pageContent')
   <!-- <iframe id="chatWindow" src="https://chat.educa-portal.de/group/{{ $chat_id }}?layout=embedded" style="border:none; width:100%; height: calc( 100% - 60px)">

    </iframe> -->
@endsection


@section('additionalScript')
    <script>
        window.onload = function () {
            document.getElementById('chatWindow').contentWindow.postMessage({
                externalCommand: 'login-with-token',
                token: '{{ $token }}'
            }, '*');
        };
        window.addEventListener('message', function(e) {
            console.log(e.data.eventName); // event name
            console.log(e.data.data); // event data
            if(e.data.eventName == "status-changed" && e.data.data == "online")
            {
                document.getElementById('chatWindow').contentWindow.postMessage({
                    externalCommand: 'go',
                    path: 'group/{{ $chat_id }}'
                }, '*');
            }
        });
    </script>
@endsection
