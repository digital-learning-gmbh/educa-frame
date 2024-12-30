@extends('beautymail::templates.widgets')

@section('content')

    @include('beautymail::templates.widgets.newfeatureStart')

    <h4 class="secondary"><strong>Herzlich willkommen in Ihrem Praxisportal-StuPla,</strong></h4>
    <p>die Schnittstelle in der Kommunikation zwischen der Berufsfachschule für Pflege der F+U Rhein-Main-Neckar gGmbH und Ihnen als Ausbildungsbetrieb. </p>
    <p>Hier erfassen Sie die An- und Abwesenheiten Ihrer Auszubildenden zur Pflegefachfrau, zum Pflegefachmann. Sie füllen einfach und bequem Protokolle oder andere Dokumente aus. Auch die Noteneingabe in den verschiedenen Pflichteinsätzen erfolgt digital.</p>
    <p>So behalten Sie die Aktivitäten und den Ausbildungsverlauf  Ihrer Auszubildenden jederzeit im Blick.</p>

    <p>Wenn Sie weitere Zugangscodes für das Praxisportal benötigen oder Rückfragen haben, wenden Sie sich bitte per Email an Nicole Seiler (nicole.seiler@fuu.de)</p>


    @include('beautymail::templates.widgets.newfeatureEnd')


    @include('beautymail::templates.widgets.articleStart')

    <h4 class="secondary"><strong>Wie können Sie sich anmelden?</strong></h4>
    <p><a href="https://fuu.stupla.online">https://fuu.stupla.online</a></p>
    <p>Benutzername: <b>{{ $kontakt->email }}</b></p>
    <p>Passwort: <b>{{ $password }}</b></p>

    @include('beautymail::templates.widgets.articleEnd')

@stop
