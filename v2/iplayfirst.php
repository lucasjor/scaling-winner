<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('memory_limit','1024M');
set_time_limit(60);

require_once 'lib/dbconn.php';
require_once 'lib/functions.php';

require_once 'Personaje.php';
require_once 'Round.php';
require_once 'CardCollector.php';
require_once 'ClanCollector.php';
//require_once 'Clan.php';
require_once 'Helper.php';
//require_once 'Bonus.php';

echo date('H:i:s')."<br/><br/>";

//Production
//$cross = array_cartesian(
//    array('a', 'b', 'c', 'd'),
//    array('e', 'f', 'g', 'h')
//);

//Equipos iniciales
//$_SESSION['initial_team'][1] = array(1,6);
//$_SESSION['initial_team'][2] = array(2,3);

$initialCards = array(
    1 => array(
        1,2,3
    ),
    2 => array(
        5,6,7
    )
);

$initialPillz = array(
    1 => 9,
    2 => 11
);

$initialLives = array(
    1 => 12,
    2 => 11
);

$initialType = 'attack';

$initialOptions = array(
    'type' => $initialType,
    'level' => 0,
    'status' => '',
    'fighters' => array(),
    'remainCards' => $initialCards,
    'remainPillz' => $initialPillz,
    'usingPillz' => array(),
    'usingFury' => array(),
    'remainLives' => $initialLives,
    'blockedHabilities' => array(),
    'blockedBonuses' => array(),
    'parentRound' => null,
);

$initialRound = new Round($initialOptions);
$initialRound->play();

$fighters = array();

$helper = new Helper();
$maxScores = $helper->printResults($initialRound);

echo "<br/><br/>".date('H:i:s');


?>

<script type="text/javascript">

    elements = document.getElementsByClassName("s<?php echo $maxScores[2]; ?>");
    for (var i = 0; i < elements.length; i++) {
        elements[i].style.color="orange";
    }

    elements = document.getElementsByClassName("s<?php echo $maxScores[1]; ?>");
    for (var i = 0; i < elements.length; i++) {
        elements[i].style.color="brown";
    }

    elements = document.getElementsByClassName("s<?php echo $maxScores[0]; ?>");
    for (var i = 0; i < elements.length; i++) {
        elements[i].style.color="red";
    }

</script>
