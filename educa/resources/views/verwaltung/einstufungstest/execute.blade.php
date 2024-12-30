@extends('verwaltung.einstufungstest.main')

@section('pageContent')
   <style>
      .draggable {margin:5px;padding:3px;}
      .fillbar {display:inline-block;min-width:50px;min-height:20px;border-style:solid;border-width:0px;border-bottom-width:2px;}
      .aufgabenText { margin-top: 5px; font-size: 20px;}
      .draggable .fas { visibility: hidden; display:  none;}
      span .fas { padding-left: 3px;}
   </style>
   <div class="container">
   <h2>Einstufungstest: {{ $test->name }}</h2>
   <div class="card">
      <div class="card-body">
          <div class="card-title"><h4>Beschreibung</h4></div>
          {{ $test->beschreibung }}</div>
   </div>
   <form id="formaufgabe" class="form-horizontal" method="POST">
   @if(!isset($user))
      <div class="card mt-2">
         <div class="card-body">
             <div class="card-title"><h4>Allgemeine Angaben</h4></div>
            <div class="form-group">
               <label for="firstname" class="col-sm-2 control-label">Vorname</label>
               <div class="col-sm-10">
                  <input name="vorname" type="text" class="form-control" id="firstname" required>
               </div>
            </div>
            <div class="form-group">
               <label for="firstname" class="col-sm-2 control-label">Nachname</label>
               <div class="col-sm-10">
                  <input name="nachname" type="text" class="form-control" id="firstname" required>
               </div>
            </div>
            <div class="form-group">
               <label for="firstname" class="col-sm-2 control-label">E-Mail</label>
               <div class="col-sm-10">
                  <input name="email" type="email" class="form-control" id="firstname" required>
               </div>
            </div>
         </div>
      </div>
   @endif
   <div class="card">
      <div class="card-body">
          <div class="card-title"><h4>Test</h4></div>
            @csrf
         @php $counter = 0; @endphp
         @foreach($test->aufgaben as $aufgabe)
            @php
            $gabs = $gabs_all[$counter];
            $texts = $texts_all[$counter];
                    @endphp
            <h5>Aufgabe {{ $counter+1 }}</h5>

            <div id="aufgabenText_{{ $counter }}" class="aufgabenText">{!! $texts !!}</div>

            <input id="antwort_{{ $counter }}" type="hidden" name="antwort[]">

         <div id="gabs_{{ $counter+1 }}">
            @php $counter2 = 0; @endphp
            @foreach($gabs as $gab)
         <span class="draggable btn btn-primary" id="drag{{ $counter+1 }}{{ $counter2+1 }}">{{ $gab }}<i class="fas fa-times" onclick="reset(this,{{ $counter+1 }})"></i></span>
               @php $counter2++ @endphp
            @endforeach
         </div>
            @php $counter++ @endphp
         @endforeach
   </div>
      <div class="card-footer">
         <button class="btn btn-success" type="button" style="float: right; margin-left: 5px;" onmouseup="perpareAnswer();">Absenden</button>
         <a class="btn btn-danger" href="/external/einstufungstest/execute/{{ $test->id }}" style="float: right; margin-left: 5px;">Zurücksetzen</a>
         <div class="clearfix"></div>
      </div>
   </div>
   </form>
   </div>
@endsection

@section('additionalScript')
   <script src="/js/jquery-ui.min.js"></script>
   <script src="/js/touch-punch.min.js"></script>
   <script>
      function initDragAndDrop(){
         $( ".draggable" ).draggable({ revert: "invalid" });
         $( ".fillbar" ).droppable({
            drop: function( event, ui ) {
               $( this ).replaceWith(ui.draggable);
               ui.draggable.attr('class',"btn btn-default");
               ui.draggable.attr('style', '');
               ui.draggable.draggable({disabled: true});
            }
         });
   }

   function reset(element, container) {
      var id =  element.parentNode.id;
      var text = element.parentNode.innerHTML;
      var newId =Math.random().toString(36).substr(2, 9);
      element.parentNode.parentNode.replaceChild(createElementFromHTML('<div id="div' + newId + '" class="fillbar">_</div>'),document.getElementById(id));
      document.getElementById("gabs_" + container).append(createElementFromHTML('  <span class="draggable btn btn-primary" id="drag' + newId + '">' +
              text +
              '         </span>'));
      initDragAndDrop();
   }
      function createElementFromHTML(htmlString) {
         var div = document.createElement('div');
         div.innerHTML = htmlString.trim();

         // Change this to div.childNodes to support multiple top-level nodes
         return div.firstChild;
      }
      function perpareAnswer() {
         var counter = {{ $counter }};
         for (var i = 0; i < counter; i++) {
            var textClean = document.getElementById("aufgabenText_" + i).innerText.replace(/ +(?= )/g,'');
            document.getElementById("antwort_" + i).value = textClean;
         }
          $("#formaufgabe").validate({
              lang: 'de',
              errorElement: 'span',
              errorPlacement: function (error, element) {
                  error.addClass('invalid-feedback');
                  element.closest('.form-group').append(error);
              },
              highlight: function (element, errorClass, validClass) {
                  $(element).addClass('is-invalid');
              },
              unhighlight: function (element, errorClass, validClass) {
                  $(element).removeClass('is-invalid');
              }
          });
          $("#formaufgabe").submit();
      }
      initDragAndDrop();
   </script>
@endsection
