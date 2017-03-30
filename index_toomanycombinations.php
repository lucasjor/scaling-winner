<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

require_once 'lib/dbconn.php';
require_once 'lib/functions.php';

require_once 'Personaje.php';
require_once 'Jugador.php';
require_once 'Juego.php';
require_once 'Clan.php';
require_once 'Helper.php';
require_once 'Bonus.php';

$juego = new Juego();

//Production
//$cross = array_cartesian(
//    array('a', 'b', 'c', 'd'),
//    array('e', 'f', 'g', 'h')
//);

//Equipos iniciales
$_SESSION['initial_team'][1] = array(1,6);
$_SESSION['initial_team'][2] = array(2,3);

$cross = array_cartesian(
    array('A','B','C','D'),
    array('W','X','Y','Z')
);

$currentArray = array();
$globalArrayCombined = array();

foreach($cross as $couple){
    array_push($currentArray,$couple);
    $copiaCross = $cross;
    findCombinations($couple,$currentArray,$copiaCross);
}

function findCombinations($couple,$currentArray,$cross)
{
    global $currentArray,$globalArrayCombined;

    $newCross = array();
    foreach($cross as $couple_sig){
        if(!in_array_r($couple[0], $couple_sig) && !in_array_r($couple[1], $couple_sig)){
            $newCross[] = $couple_sig;
        }
    }

    if(!empty($newCross)) {
        foreach ($newCross as $couple_sig) {
            array_push($currentArray, $couple_sig);
            findCombinations($couple_sig, $currentArray,$newCross);
        }
    } else {
        $globalArrayCombined[] = $currentArray;
    }
    array_pop($currentArray);
}
$i = 0;
$totalCombinationsCount = 0;
$availablePillzHome = 13;
$availablePillzAway = 13;
foreach ($globalArrayCombined as $record){ //Record: array(array('A','B'),array(...

    //Supongamos 4 cartas por jugador
    for($home1=1;$home1<=13;$home1++){
        for($away1=1;$away1<=13;$away1++){

            if(rand(1,4) <= 3){
                if($home1 <= $availablePillzHome
                    && $away1 <= $availablePillzAway) {
                    for ($home2 = 1; $home2 <= 13; $home2++) {
                        for ($away2 = 1; $away2 <= 13; $away2++) {

                            if (rand(1, 4) <= 1) {
                                if ($home1 + $home2 <= $availablePillzHome
                                    && $away1 + $away2 <= $availablePillzAway
                                ) {
                                    for ($home3 = 1; $home3 <= 13; $home3++) {
                                        for ($away3 = 1; $away3 <= 13; $away3++) {

                                            if (rand(1, 4) <= 1) {
                                                if($home1 + $home2 + $home3 <= $availablePillzHome
                                                    && $away1 + $away2 + $away3 <= $availablePillzAway) {

                                                    $auxRecord = array();

                                                    $auxRecord[0][0] = $record[0][0] . $home1;
                                                    $auxRecord[0][1] = $record[0][1] . $away1;
                                                    $auxRecord[1][0] = $record[1][0] . $home2;
                                                    $auxRecord[1][1] = $record[1][1] . $away2;
                                                    $auxRecord[2][0] = $record[2][0] . $home3;
                                                    $auxRecord[2][1] = $record[2][1] . $away3;
                                                    $auxRecord[3][0] = $record[3][0] . ($availablePillzHome - ($home1 + $home2 + $home3));
                                                    $auxRecord[3][1] = $record[3][1] . ($availablePillzAway - ($away1 + $away2 + $away3));

                                                    foreach ($auxRecord as $couple) {
                                                        echo implode('-', $couple);
                                                        echo ', ';
                                                        $totalCombinationsCount++;
                                                    }
                                                    echo '<br/>';

                                                } else {break;}
                                            } else {
                                                echo 'Se acabo en la TERCERA<br/>';
                                            }
                                        }
                                    }
                                } else {
                                    break;
                                }
                            } else {
                                echo 'Se acabo en la SEGUNDA<br/>';
                            }
                        }
                    }
                } else {break;}
            } else {
                echo 'Se acabo en la PRIMERA<br/>';
            }

        }
    }




//    $i++;
}

//$juego->imprimirArbol();
echo $totalCombinationsCount;


/*$personajes = array(
    new Personaje('Archibald',3,7,3),
    new Personaje('Chiro',4,8,2),
    new Personaje('Eyrik',5,8,5),
);

$yo = new Jugador($personajes,7,12);

$personajesRival = array(
    new Personaje('Otakool',3,6,4),
    new Personaje('Timber',5,6,8),
    new Personaje('Phillips',4,6,7),
);

$rival = new Jugador($personajesRival,12,8);


$porcentajes = $yo->vencerRival($rival,2);*/
