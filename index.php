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
    array('1', '6'),
    array('2', '3')
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
foreach ($globalArrayCombined as $record){
    foreach ($record as $couple){
        echo implode('-',$couple);
        echo ', ';
    }
    echo '<br>';
    $resultados = $juego->simularJugada2vs2($record, false, $i);
    echo '<br>';
//    if($i > 300)
//        die();
    $i++;
}

$juego->imprimirArbol();

echo count($globalArrayCombined);


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
