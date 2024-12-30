@extends('verwaltung.einstufungstest.main')

@section('pageContent')
    <div class="container">
    <h4>Verfügbare Einstufungstests</h4>
    @foreach($available as $test)
        <div class="row">
            <div class="col-md-6">
       <div class="card mt-2">
           <div class="card-body">
               <div class="card-title"><h2>{{ $test->name }}</h2></div>
               {{ $test->beschreibung }}</div>
           <div class="card-footer">
               <a href="/external/einstufungstest/execute/{{ $test->id }}" class="btn btn-success" style="float: right;">Einstufungstest beginnen <i class="fas fa-arrow-alt-circle-right"></i></a>
               <div class="clearfix"></div>
           </div>
       </div>
            </div>
        </div>
   @endforeach
    </div>
@endsection
