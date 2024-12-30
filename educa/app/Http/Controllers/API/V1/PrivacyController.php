<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;

class PrivacyController extends ApiController
{
    
    public function getPrivacyAgreement(Request $request)
    {
        $html_text = '<div class="et_pb_code_inner"><h1>Impressum</h1>

        <h2>Angaben gemäß § 5 TMG</h2>
        <p>Digital Learning GmbH<br>
        Am Sportplatz 4<br>
        37115 Duderstadt</p> 
        
        <p>Handelsregister: HRB 206304<br>
        Registergericht: Göttingen</p> 
        
        <p><strong>Vertreten durch:</strong><br>
        Benjamin Ledel</p> 
        
        <h2>Kontakt</h2>
        <p>Telefon: +(49) 5527 914843<br>
        E-Mail: kontakt@schule-plus.com</p> 
        
        <h2>Verbraucher­streit­beilegung/Universal­schlichtungs­stelle</h2>
        <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p> 
        
        <h3>Haftung für Inhalte</h3><p>Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.</p>  <p>Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.</p> <h3>Haftung für Links</h3><p>Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar.</p>  <p>Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.</p> <h3>Urheberrecht</h3><p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet.</p>  <p>Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.</p> 
        
        <p>Quelle: <a href="https://www.e-recht24.de">eRecht24</a></p> </div>';

        return $this->createJsonResponse( "ok", false, 200, [ "title" => "Datenschutzerklärung", "html_text" => $html_text  ]);
    }

    public function accept(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);
        $user->agreedPrivacy = true;
        $user->save();

        $educaMasterData = new EducaMasterdataController();
        return $educaMasterData->getCurrentUser($request);
    }
}
