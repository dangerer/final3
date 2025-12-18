<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
// --------------------
// Grunddaten
// --------------------
include_once 'preise.php';



/*$name_besteller   = $_POST['name']   ?? '';
$email  = $_POST['email']  ?? '';
$tickets = $_POST['tickets'] ?? [];*/

$name_besteller=saubern("name","text");
$email=saubern("email","email");
$tickets = saubern("tickets","text");

$ip=getUserIpAddr();
		


if (!isset($_POST['agb'])) {
    die('AGB müssen akzeptiert werden.');
}

// Ticketpreise


// Rechnungsnummer (einfach, eindeutig)
$rechnungsdatum = date('d.m.Y');

// --------------------
// Bestellung aufbereiten
// --------------------
$gesamt = 0;
$positionenHTML = '';


$ticketListeHTML = '<ul>';

foreach ($ticketsDef as $ticket) {
    [$key, $name, $preis] = $ticket;

    $anzahl = isset($_POST['tickets'][$key]) ? (int)$_POST['tickets'][$key] : 0;

    if ($anzahl > 0) {
        $summe  = $anzahl * $preis;
        $gesamt += $summe;

        $ticketListeHTML .= "
 <li>$name – $anzahl × {$preis} € = <strong>{$summe} €</strong></li>
        ";
    }
}

$db = new mysqli("localhost", "dbu_zeltfest-arnreit-at", "revn%!x&Pc9T!%yT","db_zeltfest-arnreit-at") or die("Connection Failed");
$sql="INSERT INTO bestellungen (name, email, adresse, anzahl, ip, tstamp, datum ) VALUES ";
$sql.="('".$name_besteller."', '".$email."', '".$ticketListeHTML."', '0', '".$ip."', '".time()."', '".date('d.m.Y H:i:s', time())."')";

if ($db->query($sql) === TRUE) {
		    $insert_id = $db->insert_id; // Hier wird die insert_id abgeholt
            //echo "Neuer Datensatz erfolgreich angelegt. Die ID ist: " . $insert_id;
		} else {
            $insert_id = 800;
		  //echo "Error: " . $sql . "<br>" . $db->error.$db->errno;
}



$ticketListeHTML .= '';


// --------------------
// Zahlungsdaten
// --------------------
// --------------------
// Zahlungsdaten
// --------------------
$empfaenger = "Union Arnreit";
$iban = "AT673441000006613673";
$verwendungszweck = "Tickets Final3, Rechnungsnummer $insert_id";

// EPC / SEPA QR-Code (korrektes Format)

$qrData =
    "BCD\n" .
    "002\n" .
    "1\n" .
    "SCT\n" .
    $empfaenger . "\n" .
    $iban . "\n" .
    "EUR" . number_format($gesamt, 2, '.', '') . "\n" .
    "\n" .
    $verwendungszweck;

$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);


// --------------------
// E-Mail (HTML)
// --------------------
$subject = "Rechnung – Final3 am 21. - 22. Februar in Rohrbach";

$emailHTML = "
<html>
<body style=\"font-family: Arial, sans-serif; color:#333;\">";
    

$rechnungstext="<p class='d-print-none'>Sehr geehrte/r ".$name_besteller.",</p>

<p class='d-print-none'>vielen Dank für Ihre Bestellung der Tickets für das <strong>Final3 am 21. - 22. Februar in Rohrbach, Bezirkssporthalle</strong>.</p>
<p class='mb-3 text-print-right'><b>
ÖTSU Arnreit</b><br /><b>
Partenreit 26 <br />A-4121 Arnreit</b>
    </p>
<h3>Rechnung Nr.:$insert_id vom $rechnungsdatum</h3>
  ".$ticketListeHTML."
<li style='list-style-type: none;'><strong>Gesamtbetrag:  ".$gesamt."  €</strong></li></ul>
<h3>Zahlungsinformationen</h3>
<p>
Bitte überweisen Sie den Gesamtbetrag auf folgendes Konto:
</p><p>
<strong>Empfänger:</strong> $empfaenger<br>
<strong>IBAN:</strong> $iban<br>
<strong>Verwendungszweck:</strong> $verwendungszweck
</p>";

    $rechnungstextplain=$rechnungstext;
    $rechnungstext.="<p>
        Alternativ können Sie den folgenden QR-Code für die Überweisung verwenden:
    </p>

    <p>
        <img src=\"$qrUrl\" alt=\"QR-Code Zahlung\">
    </p>

    <p class='d-print-none'>
        Nach Zahlungseingang erhalten Sie Ihr Ticket innerhalb von <strong>3 Werktagen</strong>
        elektronisch per E-Mail. <br><a href='https://www.final3.at/'>nähere Infos zum Final3</a>
    </p>";
$rechnungstextplain.="
Nach Zahlungseingang erhalten Sie Ihr Ticket innerhalb von <strong>3 Werktagen</strong>
elektronisch per E-Mail.

Mit freundlichen Grüßen
Ihr Final3-Team
 ";
   $emailHTML.=$rechnungstext;

    $emailHTML.="<p>Mit freundlichen Grüßen<br>
    Ihr Final3-Team</p>


</body>
</html>
";

// --------------------
// Header
// --------------------
$boundary = md5(time());
$headers  = "From: Anmeldung Final3 <info@zeltfest-arnreit.at>\r\n";
$headers .= "Reply-To: info@zeltfest-arnreit.at\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
$headers .=  "Cc: dietmar.angerer@exabis.at\r\n";




$message = "--$boundary\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$message .= strip_tags($rechnungstextplain) . "\r\n\r\n";

$message .= "--$boundary\r\n";
$message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
$message .= $emailHTML . "\r\n";
$message .= "--$boundary--";

// Mail an Besteller
mail($email, $subject, $message, $headers);

// --------------------
// Website-Ausgabe
// --------------------
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Rechnung Final3 2026</title>
 <link href="css/bootstrap5-3-8/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="styles.css">
 <!-- Favicon -->
    <link rel="icon" type="image/png" href="Final3_CMYK_F3-frei.png">

    <!-- Für moderne Browser und PWA-Unterstützung -->
    <link rel="apple-touch-icon" sizes="180x180" href="Final3_CMYK_F3-frei.png">
    <link rel="icon" type="image/png" sizes="32x32" href="Final3_CMYK_F3-frei.png">
    <link rel="icon" type="image/png" sizes="16x16" href="Final3_CMYK_F3-frei.png">
    <link rel="manifest" href="/site.webmanifest">
</head>
<body class="container my-4">
    
    <div class="d-flex justify-content-center flex-column align-items-center my-4 d-print-none ">
        <div class="h-image">
            <a href="https://zeltfest-arnreit.at/final3/">
            <img src="logo.png" class="img-fluid event-logo" alt="Final3 Logo">
            </a>
        </div>
    </div>
  
  
<div class="container-fluid my-4 mt-5"> 

    <div class="card card-rechnung">
        <div class="card-body">
            <h1 class="mb-3 d-print-none finale-title mb-5">Vielen Dank für Ihre Bestellung!</h1>

            <p>
                <?php echo $rechnungstext ?>
            </p>
 
            <p class="d-print-none">
                Sie erhalten in Kürze auch eine E-Mail mit der Rechnung und den Zahlungsinformationen.
                Nach Zahlungseingang wird Ihnen Ihr Ticket innerhalb von
                <strong>3 Werktagen elektronisch</strong> übermittelt.
            </p>

            <p class="d-print-none">
                Sollten Sie keine E-Mail erhalten, überprüfen Sie bitte Ihren
                <strong>Spam-Ordner</strong>.
                Bei weiteren Fragen wenden Sie sich bitte an
                <a href="mailto:kontakt@union-arnreit.at">kontakt@union-arnreit.at</a>.
            </p>

            <p class="mt-4">
                <strong>Rechnungsnummer:</strong> <?= $insert_id ?>
            </p>
            <p>
            <button class="d-print-none btn btn-primary btn-lg w-100 mt-4" onclick="window.print()" class="btn btn-secondary">
            Rechnung drucken</button></p>
        </div>
    </div>

</body>
</html>
<?php
function saubern($wert,$type){
		if (empty($_POST[$wert])) return "";
		if ($type=="text") return preg_replace('/[^a-zA-Z0-9\/_äüöÄÜÖß -]/', '', $_POST[$wert]);
		else if ($type=="email") {return preg_replace('/[^a-zA-Z0-9_\.@äüöÄÜÖ-]/', '', $_POST[$wert]);	}
		else if ($type=="url"){return preg_replace('/[^a-zA-Z0-9_\.\/:-]/', '', $_POST[$wert]);}
		else if ($type=="datum"){return preg_replace('/[^0-9\.]/', '', $_POST[$wert]);}
		else if ($type=="username"){return preg_replace('/[^a-zA-Z0-9_\/-]/', '', $_POST[$wert]);}
		else if ($type=="zahl"){ return intval($_POST[$wert]);}
	}
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
    ?>
