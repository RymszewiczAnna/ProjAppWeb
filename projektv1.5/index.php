include('cfg.php');
<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);


if ($_GET['idp'] == '') {
    $strona = 'html/glowna.html';
} elseif ($_GET['idp'] == 'chiny') {
    $strona = 'html/chiny.html';
} elseif ($_GET['idp'] == 'tajwan') {
    $strona = 'html/tajwan.html';
} elseif ($_GET['idp'] == 'usa') {
    $strona = 'html/usa.html';
} elseif ($_GET['idp'] == 'inne') {
    $strona = 'html/inne.html';
} elseif ($_GET['idp'] == 'kontakt') {
    $strona = 'html/contact.html';
} elseif ($_GET['idp'] == 'filmy') {
    $strona = 'html/filmy.html';
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Najdłuższe mosty świata</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body onload="startclock()">

<header>
    <h1>Najdłuższe mosty świata</h1>
</header>

<nav>
    <ul>
        <form method="POST" name="background">
            <li><input type="button" value="żółty" onclick="changeBackground('#FFF000')"></li>
            <li><input type="button" value="czarny" onclick="changeBackground('#000000')"></li>
            <li><input type="button" value="biały" onclick="changeBackground('#FFFFFF')"></li>
            <li><input type="button" value="zielony" onclick="changeBackground('#00FF00')"></li>
            <li><input type="button" value="niebieski" onclick="changeBackground('#0000FF')"></li>
            <li><input type="button" value="pomarańczowy" onclick="changeBackground('#FF8000')"></li>
            <li><input type="button" value="szary" onclick="changeBackground('#c0c0c0')"></li>
            <li><input type="button" value="czerwony" onclick="changeBackground('#FF0000')"></li>
        </form>
    </ul>
    <ul>
    <li><a href="index.php?idp=">Strona Główna</a></li>
    <li><a href="index.php?idp=chiny">Chiny</a></li>
    <li><a href="index.php?idp=tajwan">Tajwan</a></li>
    <li><a href="index.php?idp=usa">USA</a></li>
    <li><a href="index.php?idp=inne">Inne</a></li>
    <li><a href="index.php?idp=kontakt">Kontakt</a></li>
    <li><a href="index.php?idp=filmy">Filmy</a></li>
</ul>
</nav>

<nav>
    <div id="zegarek"></div>
    <div id="data"></div>
</nav>

<nav>
    <div id="animacjaTestowa1" class="test-block">Kliknij, a się powiększę</div>
    <script>
        $("#animacjaTestowa1").on("click", function() {
            $(this).animate({
                width: "500px",
                opacity: 0.4,
                fontSize: "3em",
                borderWidth: "10px"
            }, 1500);
        });
    </script>
    <div id="animacjaTestowa2" class="test-block">
        Najedź kursorem, a się powiększę
    </div>
    <script>
        $("#animacjaTestowa2").on({
            "mouseover": function() {
                $(this).animate({
                    width: 300
                }, 800);
            },
            "mouseout": function() {
                $(this).animate({
                    width: 200
                }, 800);
            }
        });
    </script>
    <div id="animacjaTestowa3" class="test-block">
        Kliknij abym urósł
    </div>
    <script>
        $("#animacjaTestowa3").on("click", function() {
            if (!$(this).is(":animated")) {
                $(this).animate({
                    width: "+=" + 50,
                    height: "+=" + 10,
                    opacity: "-=" + 0.1,
                    duration: 3000 //inny sposób deklaracji czasu trwania animacji
                });
            }
        });
    </script>
</nav>

<section>
    <?php
    if (file_exists($strona)) {
        include($strona);
    } else {
        echo "Strona nie istnieje.";
    }
    ?>
</section>

<footer>
    <p></p>
</footer>

</body>
</html>