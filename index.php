<?php
// anmeldung.php
include_once 'preise.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Tickets für das Final3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Eigene Styles -->
    <link rel="stylesheet" href="styles.css">

    <!-- JS Bibliothek (SweetAlert2 für UX) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

     <!-- Favicon -->
    <link rel="icon" type="image/png" href="Final3_CMYK_F3-frei.png">

    <!-- Für moderne Browser und PWA-Unterstützung -->
    <link rel="apple-touch-icon" sizes="180x180" href="Final3_CMYK_F3-frei.png">
    <link rel="icon" type="image/png" sizes="32x32" href="Final3_CMYK_F3-frei.png">
    <link rel="icon" type="image/png" sizes="16x16" href="Final3_CMYK_F3-frei.png">
    <link rel="manifest" href="/site.webmanifest">

</head>
<body>
<div class="container my-4" style="max-width:600px">

    <div class="text-center mb-4">
        <img src="logo.png" class="img-fluid event-logo" alt="Final3 Logo">
    </div>

    <h1 class="text-center mb-4 finale-title">Tickets für das Final3</h1>

    <form id="ticketForm" action="anmeldung_bestaetigung.php" method="post">
        <div class="row">
            <div class="col-12 mb-3 form-narrow">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-12 mb-3 form-narrow">
                <label class="form-label">E-Mail *</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
        </div>

        <h4 class="mt-4">Tickets auswählen</h4>

        <div id="tickets">
            <!-- Ticket Zeilen -->
            <?php

            foreach ($ticketsDef as $t) {
                echo '<div class="row align-items-center mb-2 ticket-row" data-price="'.$t[2].'">
                        <div class="col-4 col-md-3 d-flex align-items-center gap-2 qty-wrapper">
                            <input type="number" min="0" max="20" value="0" name="tickets['.$t[0].']" class="form-control ticket-qty text-center">
                            <button type="button" class="btn btn-sm qty-plus">+</button>
                            <button type="button" class="btn btn-sm qty-minus">−</button>
                        </div>
                        <div class="col-8 col-md-9">'.$t[1].' ('.$t[2].' €)</div>
                      </div>';
            }
            ?>
        </div>
            <div class="form-check mt-4 form-narrow">
    <input class="form-check-input" type="checkbox" value="1" id="agb" name="agb">
    <label class="form-check-label" for="agb">
        Ich akzeptiere die <a href="agb.php" target="_blank">AGB</a>
    </label>
</div>

        <div class="card mt-4">
            <div class="card-body">
                <h5>Warenkorb</h5>
                <div id="cart"></div>
                <strong>Gesamtsumme: <span id="total">0</span> €</strong>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">Tickets bestellen</button>
    </form>
    <div class="text-center mb-4"><br /><a href="datenschutz.php">Datenschutzerklärung</a></div>
</div>

<script src="script.js"></script>
</body>
</html>