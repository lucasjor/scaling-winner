<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 14/12/15
 * Time: 21:09
 */

class Juego
{
    public $_tree = array();

    public function simularJugada2vs2($record, $debug, $simulationNumber) //FUNCIONANDO sin que te maten en la primer ronda
    {
        $pillzJugador1 = 13;
        $pillzJugador2 = 13;
        $vidasJugador1 = 20;
        $vidasJugador2 = 20;

        $helper = new Helper();
//                Production
        $personajeJugador1 = $helper->cargarPersonaje($record[0][0]);
        $personajeJugador2 = $helper->cargarPersonaje($record[0][1]);

        $victorias = 0;
        $derrotas = 0;

        $victoriasPorPillz = array(); //Para el resumen


        for($pillzJugador1PrimeraRonda=0;$pillzJugador1PrimeraRonda<=$pillzJugador1;$pillzJugador1PrimeraRonda++){ //Por cada alternativa de pillz que yo tengo
            $victoriasPorPillz[$pillzJugador1PrimeraRonda] = 0;
            $jugadasPorPillz[$pillzJugador1PrimeraRonda] = 0;

            for($pillzJugador2PrimeraRonda=0;$pillzJugador2PrimeraRonda<=$pillzJugador2;$pillzJugador2PrimeraRonda++){ //Por cada alternativa de pillz que tiene el otro
                $vidasJugador1AfterPrimeraBatalla = $vidasJugador1;
                $vidasJugador2AfterPrimeraBatalla = $vidasJugador2;
                $resultado = $this->simularPelea($personajeJugador1,$personajeJugador2,$pillzJugador1PrimeraRonda,$pillzJugador2PrimeraRonda);

                if($resultado['victoria']){ //Si gano yo
                    $vidasJugador2AfterPrimeraBatalla = $vidasJugador2 - $resultado['damageToLoser']; //Le saco vida al otro

                } else { //Si pierdo
                    $vidasJugador1AfterPrimeraBatalla = $vidasJugador1 - $resultado['damageToLoser']; //Me saco vida
                }

                //-------Aca simulo la segunda batalla-----------
                $pillzJugador1AfterPrimerBatalla = $pillzJugador1 - $pillzJugador1PrimeraRonda; //Actualizo cantidad de pillz
                $pillzJugador2AfterPrimerBatalla = $pillzJugador2 - $pillzJugador2PrimeraRonda; //Actualizo cantidad de pillz

                $personajeJugador1SegundaRonda = $helper->cargarPersonaje($record[1][0]); //Actualizo personajes
                $personajeJugador2SegundaRonda = $helper->cargarPersonaje($record[1][1]); //Actualizo personajes

                $pillzJugador1SegundaRonda = $pillzJugador1AfterPrimerBatalla; //Porque en la ultima ronda no te guardas nada
                $pillzJugador2SegundaRonda = $pillzJugador2AfterPrimerBatalla; //Porque en la ultima ronda no te guardas nada

//                for($pillzJugador1SegundaRonda=0;$pillzJugador1SegundaRonda<=$pillzJugador1AfterPrimerBatalla;$pillzJugador1SegundaRonda++){ //Por cada alternativa de pillz que yo tengo


//                    for($pillzJugador2SegundaRonda=0;$pillzJugador2SegundaRonda<=$pillzJugador2AfterPrimerBatalla;$pillzJugador2SegundaRonda++){ //Por cada alternativa de pillz que tiene el otro

                        $jugadasPorPillz[$pillzJugador1PrimeraRonda]++;
                        $vidasJugador1AfterSegundaBatalla = $vidasJugador1AfterPrimeraBatalla;
                        $vidasJugador2AfterSegundaBatalla = $vidasJugador2AfterPrimeraBatalla;

                        $resultadoSegundaRonda = $this->simularPelea($personajeJugador1SegundaRonda,$personajeJugador2SegundaRonda,$pillzJugador1SegundaRonda,$pillzJugador2SegundaRonda);

                        if($resultadoSegundaRonda['victoria']){ //Si gano yo
                            $vidasJugador2AfterSegundaBatalla = $vidasJugador2AfterPrimeraBatalla - $resultadoSegundaRonda['damageToLoser']; //Le saco vida al otro

                            if($vidasJugador1AfterSegundaBatalla >= $vidasJugador2AfterSegundaBatalla){
//                                echo 'GANE </br>';
                                $victorias++;
                                $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                            } else {
//                                echo 'PERDI </br>';
                                $derrotas++;
                            }

                        } else { //Si pierdo
                            $vidasJugador1AfterSegundaBatalla = $vidasJugador1AfterPrimeraBatalla - $resultadoSegundaRonda['damageToLoser']; //Me saco vida

                            if($vidasJugador1AfterSegundaBatalla < $vidasJugador2AfterSegundaBatalla){
//                                echo 'PERDI </br>';
                                $derrotas++;
                            } else {
//                                echo 'GANE </br>';
                                $victorias++;
                                $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                            }
//                        }

//                        echo '<hr>';
//                    }
                }

            }
        }

        if(!isset($this->_tree[$personajeJugador1->_name])){
            $this->_tree[$personajeJugador1->_name] = array();
        }
        for($i=0;$i<=$pillzJugador1;$i++){
            echo $personajeJugador1->_name.' - '.$i.' Pillz : '.$victoriasPorPillz[$i].'/'.$jugadasPorPillz[$i].'<br>';

            $this->_tree[$personajeJugador1->_name][$i][$simulationNumber] = array('victorias' => 0, 'jugadas' =>0);
            $this->_tree[$personajeJugador1->_name][$i][$simulationNumber]['victorias'] += $victoriasPorPillz[$i];
            $this->_tree[$personajeJugador1->_name][$i][$simulationNumber]['jugadas'] += $jugadasPorPillz[$i];
        }

        echo 'Total <b>Victorias</b>:'.$victorias.'</br>';
        echo 'Total <b>Derrotas</b>:'.$derrotas.'</br>';
    }

    public function simularJugada1vs1($record, $debug) //FUNCIONANDO
    {
        $pillzJugador1 = 1;
        $pillzJugador2 = 1;
        $vidasJugador1 = 6;
        $vidasJugador2 = 6;



//        $record[0][0] = new Personaje('Timber', 5, 6, 4);
//        $record[0][1] = new Personaje('Hugo', 4, 7, 3);
//
//        $personajeJugador1 = $record[0][0];
//        $personajeJugador2 = $record[0][1];

//                Production
        $personajeJugador1 = $this->cargarPersonaje($record[0][0]);
        $personajeJugador2 = $this->cargarPersonaje($record[0][1]);

        $victorias = 0;
        $derrotas = 0;

        $victoriasPorPillz = array(); //Para el resumen


        for($pillzJugador1PrimeraRonda=0;$pillzJugador1PrimeraRonda<=$pillzJugador1;$pillzJugador1PrimeraRonda++){ //Por cada alternativa de pillz que yo tengo
            $victoriasPorPillz[$pillzJugador1PrimeraRonda] = 0;
            $jugadasPorPillz[$pillzJugador1PrimeraRonda] = 0;

            for($pillzJugador2PrimeraRonda=0;$pillzJugador2PrimeraRonda<=$pillzJugador2;$pillzJugador2PrimeraRonda++){ //Por cada alternativa de pillz que tiene el otro
                $jugadasPorPillz[$pillzJugador1PrimeraRonda]++;
                $vidasJugador1AfterPrimeraBatalla = $vidasJugador1;
                $vidasJugador2AfterPrimeraBatalla = $vidasJugador2;
                $victoria = $this->simularPelea($personajeJugador1,$personajeJugador2,$pillzJugador1PrimeraRonda,$pillzJugador2PrimeraRonda);

                if($victoria){ //Si gano yo
                    $vidasJugador2AfterPrimeraBatalla = $vidasJugador2 - $personajeJugador1->_damage; //Le saco vida al otro

                    if($vidasJugador1AfterPrimeraBatalla >= $vidasJugador2AfterPrimeraBatalla){
//                        echo 'GANE </br>';
                        $victorias++;
                        $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                    } else {
//                        echo 'PERDI </br>';
                        $derrotas++;
                    }

                } else { //Si pierdo
                    $vidasJugador1AfterPrimeraBatalla = $vidasJugador1 - $personajeJugador2->_damage; //Me saco vida

                    if($vidasJugador1AfterPrimeraBatalla < $vidasJugador2AfterPrimeraBatalla){
//                        echo 'PERDI </br>';
                        $derrotas++;
                    } else {
//                        echo 'GANE </br>';
                        $victorias++;
                        $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                    }
                }

//                echo '<hr>';
            }
        }

        for($i=0;$i<=$pillzJugador1;$i++){
            echo $personajeJugador1->_name.' - '.$i.' Pillz : '.$victoriasPorPillz[$i].'/'.$jugadasPorPillz[$i].'<br>';
        }

        echo 'Total <b>Victorias</b>:'.$victorias.'</br>';
        echo 'Total <b>Derrotas</b>:'.$derrotas.'</br>';
    }

    public function simularJugada3vs3($record, $debug) //FUNCIONANDO sin que se mueran en rondas intermedias
    {
        $pillzJugador1 = 3;
        $pillzJugador2 = 3;
        $vidasJugador1 = 10;
        $vidasJugador2 = 10;



        $record[0][0] = new Personaje('Nadia', 5, 6, 4);
        $record[0][1] = new Personaje('Hugo', 4, 7, 3);

        $record[1][0] = new Personaje('Juana', 5, 6, 4);
        $record[1][1] = new Personaje('Mauricio', 4, 7, 3);

        $record[2][0] = new Personaje('Erica', 5, 6, 4);
        $record[2][1] = new Personaje('Omar', 4, 7, 3);

        $personajeJugador1 = $record[0][0];
        $personajeJugador2 = $record[0][1];

//                Production
//                $personajeJugador1 = cargarPersonaje($record[0][0]);
//                $personajeJugador2 = cargarPersonaje($record[0][1]);

        $victorias = 0;
        $derrotas = 0;

        $victoriasPorPillz = array(); //Para el resumen


        for($pillzJugador1PrimeraRonda=0;$pillzJugador1PrimeraRonda<=$pillzJugador1;$pillzJugador1PrimeraRonda++){ //Por cada alternativa de pillz que yo tengo
            $victoriasPorPillz[$pillzJugador1PrimeraRonda] = 0;
            $jugadasPorPillz[$pillzJugador1PrimeraRonda] = 0;

            for($pillzJugador2PrimeraRonda=0;$pillzJugador2PrimeraRonda<=$pillzJugador2;$pillzJugador2PrimeraRonda++){ //Por cada alternativa de pillz que tiene el otro
                $vidasJugador1AfterPrimeraBatalla = $vidasJugador1;
                $vidasJugador2AfterPrimeraBatalla = $vidasJugador2;
                $victoria = $this->simularPelea($personajeJugador1,$personajeJugador2,$pillzJugador1PrimeraRonda,$pillzJugador2PrimeraRonda);

                if($victoria){ //Si gano yo
                    $vidasJugador2AfterPrimeraBatalla = $vidasJugador2 - $personajeJugador1->_damage; //Le saco vida al otro

                } else { //Si pierdo
                    $vidasJugador1AfterPrimeraBatalla = $vidasJugador1 - $personajeJugador2->_damage; //Me saco vida
                }

                //-------Aca simulo la segunda batalla-----------
                $pillzJugador1AfterPrimerBatalla = $pillzJugador1 - $pillzJugador1PrimeraRonda; //Actualizo cantidad de pillz
                $pillzJugador2AfterPrimerBatalla = $pillzJugador2 - $pillzJugador2PrimeraRonda; //Actualizo cantidad de pillz

                $personajeJugador1SegundaRonda = $record[1][0]; //Actualizo personajes
                $personajeJugador2SegundaRonda = $record[1][1]; //Actualizo personajes

                for($pillzJugador1SegundaRonda=0;$pillzJugador1SegundaRonda<=$pillzJugador1AfterPrimerBatalla;$pillzJugador1SegundaRonda++){ //Por cada alternativa de pillz que yo tengo


                    for($pillzJugador2SegundaRonda=0;$pillzJugador2SegundaRonda<=$pillzJugador2AfterPrimerBatalla;$pillzJugador2SegundaRonda++){ //Por cada alternativa de pillz que tiene el otro

                        $vidasJugador1AfterSegundaBatalla = $vidasJugador1AfterPrimeraBatalla;
                        $vidasJugador2AfterSegundaBatalla = $vidasJugador2AfterPrimeraBatalla;

                        $victoriaSegundaRonda = $this->simularPelea($personajeJugador1SegundaRonda,$personajeJugador2SegundaRonda,$pillzJugador1SegundaRonda,$pillzJugador2SegundaRonda);

                        if($victoriaSegundaRonda){ //Si gano yo
                            $vidasJugador2AfterSegundaBatalla = $vidasJugador2AfterPrimeraBatalla - $personajeJugador1SegundaRonda->_damage; //Le saco vida al otro

                        } else { //Si pierdo
                            $vidasJugador1AfterSegundaBatalla = $vidasJugador1AfterPrimeraBatalla - $personajeJugador2SegundaRonda->_damage; //Me saco vida

                        }

                        //-------Aca simulo la tercer batalla-----------
                        $pillzJugador1AfterSegundaBatalla = $pillzJugador1AfterPrimerBatalla - $pillzJugador1SegundaRonda; //Actualizo cantidad de pillz
                        $pillzJugador2AfterSegundaBatalla = $pillzJugador2AfterPrimerBatalla - $pillzJugador2SegundaRonda; //Actualizo cantidad de pillz

                        $personajeJugador1TerceraRonda = $record[2][0]; //Actualizo personajes
                        $personajeJugador2TerceraRonda = $record[2][1]; //Actualizo personajes

                        for($pillzJugador1TerceraRonda=0;$pillzJugador1TerceraRonda<=$pillzJugador1AfterSegundaBatalla;$pillzJugador1TerceraRonda++){ //Por cada alternativa de pillz que yo tengo


                            for($pillzJugador2TerceraRonda=0;$pillzJugador2TerceraRonda<=$pillzJugador2AfterSegundaBatalla;$pillzJugador2TerceraRonda++){ //Por cada alternativa de pillz que tiene el otro

                                $jugadasPorPillz[$pillzJugador1PrimeraRonda]++;
                                $vidasJugador1AfterTerceraBatalla = $vidasJugador1AfterSegundaBatalla;
                                $vidasJugador2AfterTerceraBatalla = $vidasJugador2AfterSegundaBatalla;

                                $victoriaTerceraRonda = $this->simularPelea($personajeJugador1TerceraRonda,$personajeJugador2TerceraRonda,$pillzJugador1TerceraRonda,$pillzJugador2TerceraRonda);

                                if($victoriaTerceraRonda){ //Si gano yo
                                    $vidasJugador2AfterTerceraBatalla = $vidasJugador2AfterSegundaBatalla - $personajeJugador1TerceraRonda->_damage; //Le saco vida al otro

                                    if($vidasJugador1AfterTerceraBatalla >= $vidasJugador2AfterTerceraBatalla){
                                        echo 'GANE / '.$vidasJugador1AfterPrimeraBatalla.'-'.$vidasJugador1AfterSegundaBatalla.'-'.$vidasJugador1AfterTerceraBatalla.' / '.$vidasJugador2AfterPrimeraBatalla.' - '.$vidasJugador2AfterSegundaBatalla.'-'.$vidasJugador2AfterTerceraBatalla.'</br>';
                                        $victorias++;
                                        $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                                    } else {
                                        echo 'PERDI / '.$vidasJugador1AfterPrimeraBatalla.'-'.$vidasJugador1AfterSegundaBatalla.'-'.$vidasJugador1AfterTerceraBatalla.' / '.$vidasJugador2AfterPrimeraBatalla.' - '.$vidasJugador2AfterSegundaBatalla.'-'.$vidasJugador2AfterTerceraBatalla.'</br>';
                                        $derrotas++;
                                    }

                                } else { //Si pierdo
                                    $vidasJugador1AfterTerceraBatalla = $vidasJugador1AfterSegundaBatalla - $personajeJugador2TerceraRonda->_damage; //Me saco vida

                                    if($vidasJugador1AfterTerceraBatalla < $vidasJugador2AfterTerceraBatalla){
                                        echo 'PERDI / '.$vidasJugador1AfterPrimeraBatalla.'-'.$vidasJugador1AfterSegundaBatalla.'-'.$vidasJugador1AfterTerceraBatalla.' / '.$vidasJugador2AfterPrimeraBatalla.' - '.$vidasJugador2AfterSegundaBatalla.'-'.$vidasJugador2AfterTerceraBatalla.'</br>';
                                        $derrotas++;
                                    } else {
                                        echo 'GANE / '.$vidasJugador1AfterPrimeraBatalla.'-'.$vidasJugador1AfterSegundaBatalla.'-'.$vidasJugador1AfterTerceraBatalla.' / '.$vidasJugador2AfterPrimeraBatalla.' - '.$vidasJugador2AfterSegundaBatalla.'-'.$vidasJugador2AfterTerceraBatalla.'</br>';
                                        $victorias++;
                                        $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                                    }
                                }

                                echo '<hr>';
                            }
                        }
                    }
                }

            }
        }

        for($i=0;$i<=$pillzJugador1;$i++){
            echo $record[0][0]->_name.' - '.$i.' Pillz : '.$victoriasPorPillz[$i].'/'.$jugadasPorPillz[$i].'<br>';
        }

        echo 'Total <b>Victorias</b>:'.$victorias.'</br>';
        echo 'Total <b>Derrotas</b>:'.$derrotas.'</br>';
    }

    public function simularJugada4vs4($record, $debug) //FUNCIONANDO sin que se mueran en rondas intermedias
    {
        $pillzJugador1 = 3;
        $pillzJugador2 = 3;
        $vidasJugador1 = 20;
        $vidasJugador2 = 20;



        $record[0][0] = new Personaje('Nadia', 5, 6, 4);
        $record[0][1] = new Personaje('Hugo', 4, 7, 3);

        $record[1][0] = new Personaje('Juana', 5, 6, 4);
        $record[1][1] = new Personaje('Mauricio', 4, 7, 3);

        $record[2][0] = new Personaje('Erica', 5, 6, 4);
        $record[2][1] = new Personaje('Omar', 4, 7, 3);

        $record[3][0] = new Personaje('Keila', 5, 6, 4);
        $record[3][1] = new Personaje('Quique', 4, 7, 3);

        $personajeJugador1 = $record[0][0];
        $personajeJugador2 = $record[0][1];

//                Production
//                $personajeJugador1 = cargarPersonaje($record[0][0]);
//                $personajeJugador2 = cargarPersonaje($record[0][1]);

        $victorias = 0;
        $derrotas = 0;

        $victoriasPorPillz = array(); //Para el resumen


        for($pillzJugador1PrimeraRonda=0;$pillzJugador1PrimeraRonda<=$pillzJugador1;$pillzJugador1PrimeraRonda++){ //Por cada alternativa de pillz que yo tengo
            $victoriasPorPillz[$pillzJugador1PrimeraRonda] = 0;
            $jugadasPorPillz[$pillzJugador1PrimeraRonda] = 0;

            for($pillzJugador2PrimeraRonda=0;$pillzJugador2PrimeraRonda<=$pillzJugador2;$pillzJugador2PrimeraRonda++){ //Por cada alternativa de pillz que tiene el otro
                $vidasJugador1AfterPrimeraBatalla = $vidasJugador1;
                $vidasJugador2AfterPrimeraBatalla = $vidasJugador2;
                $victoria = $this->simularPelea($personajeJugador1,$personajeJugador2,$pillzJugador1PrimeraRonda,$pillzJugador2PrimeraRonda);

                if($victoria){ //Si gano yo
                    $vidasJugador2AfterPrimeraBatalla = $vidasJugador2 - $personajeJugador1->_damage; //Le saco vida al otro

                } else { //Si pierdo
                    $vidasJugador1AfterPrimeraBatalla = $vidasJugador1 - $personajeJugador2->_damage; //Me saco vida
                }

                //-------Aca simulo la segunda batalla-----------
                $pillzJugador1AfterPrimerBatalla = $pillzJugador1 - $pillzJugador1PrimeraRonda; //Actualizo cantidad de pillz
                $pillzJugador2AfterPrimerBatalla = $pillzJugador2 - $pillzJugador2PrimeraRonda; //Actualizo cantidad de pillz

                $personajeJugador1SegundaRonda = $record[1][0]; //Actualizo personajes
                $personajeJugador2SegundaRonda = $record[1][1]; //Actualizo personajes

                for($pillzJugador1SegundaRonda=0;$pillzJugador1SegundaRonda<=$pillzJugador1AfterPrimerBatalla;$pillzJugador1SegundaRonda++){ //Por cada alternativa de pillz que yo tengo


                    for($pillzJugador2SegundaRonda=0;$pillzJugador2SegundaRonda<=$pillzJugador2AfterPrimerBatalla;$pillzJugador2SegundaRonda++){ //Por cada alternativa de pillz que tiene el otro

                        $vidasJugador1AfterSegundaBatalla = $vidasJugador1AfterPrimeraBatalla;
                        $vidasJugador2AfterSegundaBatalla = $vidasJugador2AfterPrimeraBatalla;

                        $victoriaSegundaRonda = $this->simularPelea($personajeJugador1SegundaRonda,$personajeJugador2SegundaRonda,$pillzJugador1SegundaRonda,$pillzJugador2SegundaRonda);

                        if($victoriaSegundaRonda){ //Si gano yo
                            $vidasJugador2AfterSegundaBatalla = $vidasJugador2AfterPrimeraBatalla - $personajeJugador1SegundaRonda->_damage; //Le saco vida al otro

                        } else { //Si pierdo
                            $vidasJugador1AfterSegundaBatalla = $vidasJugador1AfterPrimeraBatalla - $personajeJugador2SegundaRonda->_damage; //Me saco vida

                        }

                        //-------Aca simulo la tercer batalla-----------
                        $pillzJugador1AfterSegundaBatalla = $pillzJugador1AfterPrimerBatalla - $pillzJugador1SegundaRonda; //Actualizo cantidad de pillz
                        $pillzJugador2AfterSegundaBatalla = $pillzJugador2AfterPrimerBatalla - $pillzJugador2SegundaRonda; //Actualizo cantidad de pillz

                        $personajeJugador1TerceraRonda = $record[2][0]; //Actualizo personajes
                        $personajeJugador2TerceraRonda = $record[2][1]; //Actualizo personajes

                        for($pillzJugador1TerceraRonda=0;$pillzJugador1TerceraRonda<=$pillzJugador1AfterSegundaBatalla;$pillzJugador1TerceraRonda++){ //Por cada alternativa de pillz que yo tengo


                            for($pillzJugador2TerceraRonda=0;$pillzJugador2TerceraRonda<=$pillzJugador2AfterSegundaBatalla;$pillzJugador2TerceraRonda++){ //Por cada alternativa de pillz que tiene el otro
                                $vidasJugador1AfterTerceraBatalla = $vidasJugador1AfterSegundaBatalla;
                                $vidasJugador2AfterTerceraBatalla = $vidasJugador2AfterSegundaBatalla;

                                $victoriaTerceraRonda = $this->simularPelea($personajeJugador1TerceraRonda,$personajeJugador2TerceraRonda,$pillzJugador1TerceraRonda,$pillzJugador2TerceraRonda);

                                if($victoriaTerceraRonda){ //Si gano yo
                                    $vidasJugador2AfterTerceraBatalla = $vidasJugador2AfterSegundaBatalla - $personajeJugador1TerceraRonda->_damage; //Le saco vida al otro


                                } else { //Si pierdo
                                    $vidasJugador1AfterTerceraBatalla = $vidasJugador1AfterSegundaBatalla - $personajeJugador2TerceraRonda->_damage; //Me saco vida

                                }

                                //-------Aca simulo la cuarta batalla-----------
                                $pillzJugador1AfterTerceraBatalla = $pillzJugador1AfterSegundaBatalla - $pillzJugador1TerceraRonda; //Actualizo cantidad de pillz
                                $pillzJugador2AfterTerceraBatalla = $pillzJugador2AfterSegundaBatalla - $pillzJugador2TerceraRonda; //Actualizo cantidad de pillz

                                $personajeJugador1CuartaRonda = $record[3][0]; //Actualizo personajes
                                $personajeJugador2CuartaRonda = $record[3][1]; //Actualizo personajes

                                for($pillzJugador1CuartaRonda=0;$pillzJugador1CuartaRonda<=$pillzJugador1AfterTerceraBatalla;$pillzJugador1CuartaRonda++){ //Por cada alternativa de pillz que yo tengo


                                    for($pillzJugador2CuartaRonda=0;$pillzJugador2CuartaRonda<=$pillzJugador2AfterTerceraBatalla;$pillzJugador2CuartaRonda++){ //Por cada alternativa de pillz que tiene el otro

                                        $jugadasPorPillz[$pillzJugador1PrimeraRonda]++;
                                        $vidasJugador1AfterCuartaBatalla = $vidasJugador1AfterTerceraBatalla;
                                        $vidasJugador2AfterCuartaBatalla = $vidasJugador2AfterTerceraBatalla;

                                        $victoriaCuartaRonda = $this->simularPelea($personajeJugador1CuartaRonda,$personajeJugador2CuartaRonda,$pillzJugador1CuartaRonda,$pillzJugador2CuartaRonda);

                                        if($victoriaCuartaRonda){ //Si gano yo
                                            $vidasJugador2AfterCuartaBatalla = $vidasJugador2AfterTerceraBatalla - $personajeJugador1CuartaRonda->_damage; //Le saco vida al otro

                                            if($vidasJugador1AfterCuartaBatalla >= $vidasJugador2AfterCuartaBatalla){
                                                if($debug)
                                                    echo 'GANE / '.$vidasJugador1AfterCuartaBatalla.' / '.$vidasJugador2AfterCuartaBatalla.'</br>';
                                                $victorias++;
                                                $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                                            } else {
                                                if($debug)
                                                    echo 'PERDI / '.$vidasJugador1AfterCuartaBatalla.' / '.$vidasJugador2AfterCuartaBatalla.'</br>';
                                                $derrotas++;
                                            }

                                        } else { //Si pierdo
                                            $vidasJugador1AfterCuartaBatalla = $vidasJugador1AfterTerceraBatalla - $personajeJugador2CuartaRonda->_damage; //Me saco vida

                                            if($vidasJugador1AfterCuartaBatalla < $vidasJugador2AfterCuartaBatalla){
                                                if($debug)
                                                    echo 'PERDI / '.$vidasJugador1AfterCuartaBatalla.' / '.$vidasJugador2AfterCuartaBatalla.'</br>';
                                                $derrotas++;
                                            } else {
                                                if($debug)
                                                    echo 'GANE / '.$vidasJugador1AfterCuartaBatalla.' / '.$vidasJugador2AfterCuartaBatalla.'</br>';
                                                $victorias++;
                                                $victoriasPorPillz[$pillzJugador1PrimeraRonda]++;
                                            }
                                        }

//                                        echo '<hr>';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        for($i=0;$i<=$pillzJugador1;$i++){
            echo $record[0][0]->_name.' - '.$i.' Pillz : '.$victoriasPorPillz[$i].'/'.$jugadasPorPillz[$i].'<br>';
        }

        echo 'Total <b>Victorias</b>:'.$victorias.'</br>';
        echo 'Total <b>Derrotas</b>:'.$derrotas.'</br>';
    }

    public function simularPelea($personajeJugador1,$personajeJugador2,$pillzJugador1,$pillzJugador2)
    {
        $resultado = array();
        $restarPuntosAPersonaje1 = array();
        $restarPuntosAPersonaje2 = array();

        $personajeJugador1Info['power'] = $personajeJugador1->_power;
        $personajeJugador2Info['power'] = $personajeJugador2->_power;

        //Chequeamos bonuses que modifiquen el power
        if($personajeJugador1->isBonusActive(1)){
            $bonusId = $personajeJugador1->getClan()->getBonus()->_code;
            switch($bonusId){
                case 'power-plus-2':
                    $personajeJugador1Info['power'] += 2;
                    break;
                case 'opp-power-minus-2-min-1':
                    $restarPoderAPersonaje2['cantidad'] = 2;
                    $restarPoderAPersonaje2['min'] = 1;
                    break;
                default:
                    break;
            }
        }
        if($personajeJugador2->isBonusActive(1)){
            $bonusId = $personajeJugador2->getClan()->getBonus()->_code;
            switch($bonusId){
                case 'power-plus-2':
                    $personajeJugador2Info['power'] += 2;
                    break;
                case 'opp-power-minus-2-min-1':
                    $restarPoderAPersonaje1['cantidad'] = 2;
                    $restarPoderAPersonaje1['min'] = 1;
                    break;
                default:
                    break;
            }
        }

        //Aplicamos los descuentos de poder pertinentes
        if(isset($restarPoderAPersonaje2['cantidad'])){
            $personajeJugador2Info['power'] -= $restarPoderAPersonaje2['cantidad'];
            if($personajeJugador2Info['power'] < $restarPoderAPersonaje2['min']){
                $personajeJugador2Info['power'] = $restarPoderAPersonaje2['min'];
            }
        }
        if(isset($restarPoderAPersonaje1['cantidad'])){
            $personajeJugador1Info['power'] -= $restarPoderAPersonaje1['cantidad'];
            if($personajeJugador1Info['power'] < $restarPoderAPersonaje1['min']){
                $personajeJugador1Info['power'] = $restarPoderAPersonaje1['min'];
            }
        }

//        echo 'Los poderes son '.$personajeJugador1Info['power'].'-'.$personajeJugador2Info['power'].'<br/>';

        $puntosPersonajeJugador1 = $personajeJugador1Info['power'] * ($pillzJugador1+1);
        $puntosPersonajeJugador2 = $personajeJugador2Info['power'] * ($pillzJugador2+1);

        //Chequeamos bonuses que modifiquen ataques
        if($personajeJugador1->isBonusActive(1)){
            $bonusId = $personajeJugador1->getClan()->getBonus()->_code;
            switch($bonusId){
                case 'attack-plus-8':
                    $puntosPersonajeJugador1 += 8;
                    break;
                case 'opp-attack-minus-12-min-8':
                    $aux = array();
                    $aux['cantidad'] = 12;
                    $aux['min'] = 8;
                    $restarPuntosAPersonaje2[] = $aux;
                    break;
                case 'opp-attack-minus-10-min-3':
                    $aux = array();
                    $aux['cantidad'] = 10;
                    $aux['min'] = 3;
                    $restarPuntosAPersonaje2[] = $aux;
                    break;
                case 'opp-attack-minus-8-min-3':
                    $aux = array();
                    $aux['cantidad'] = 8;
                    $aux['min'] = 3;
                    $restarPuntosAPersonaje2[] = $aux;
                    break;
                default:
                    break;
            }
        }
        if($personajeJugador2->isBonusActive(1)){
            $bonusId = $personajeJugador2->getClan()->getBonus()->_code;
            switch($bonusId){
                case 'attack-plus-8':
                    $puntosPersonajeJugador2 += 8;
                    break;
                case 'opp-attack-minus-12-min-8':
                    $aux = array();
                    $aux['cantidad'] = 12;
                    $aux['min'] = 8;
                    $restarPuntosAPersonaje1[] = $aux;
                    break;
                case 'opp-attack-minus-10-min-3':
                    $aux = array();
                    $aux['cantidad'] = 10;
                    $aux['min'] = 3;
                    $restarPuntosAPersonaje1[] = $aux;
                    break;
                case 'opp-attack-minus-8-min-3':
                    $aux = array();
                    $aux['cantidad'] = 8;
                    $aux['min'] = 3;
                    $restarPuntosAPersonaje1[] = $aux;
                    break;
                default:
                    break;
            }
        }

        //Aplicamos los descuentos de ataque pertinentes
        if(count($restarPuntosAPersonaje2)){ //TODO hay que ordenar los arrays, para que si habilidad y bonus estan presentes se ordenen de la mejor manera
            foreach($restarPuntosAPersonaje2 as $resta){
                $personajeJugador2Info['power'] -= $resta['cantidad'];
                if($personajeJugador2Info['power'] < $resta['min']){
                    $personajeJugador2Info['power'] = $resta['min'];
                }
            }
        }
        if(count($restarPuntosAPersonaje1)){ //TODO hay que ordenar los arrays, para que si habilidad y bonus estan presentes se ordenen de la mejor manera
            foreach($restarPuntosAPersonaje1 as $resta){
                $personajeJugador1Info['power'] -= $resta['cantidad'];
                if($personajeJugador1Info['power'] < $resta['min']){
                    $personajeJugador1Info['power'] = $resta['min'];
                }
            }
        }

//        echo 'Puntos '.$personajeJugador1->_name.': '.$puntosPersonajeJugador1.' - '.$pillzJugador1.' Pillz</br>';
//        echo 'Puntos '.$personajeJugador2->_name.': '.$puntosPersonajeJugador2.' - '.$pillzJugador2.' Pillz</br>';

        if($puntosPersonajeJugador1 > $puntosPersonajeJugador2){
            $resultado['victoria'] = true;
            $personajeGanador = $personajeJugador1;
            $personajePerdedor = $personajeJugador2;
//            echo 'Gana <b>'.$personajeJugador1->_name.'</b></br></br>';
        } elseif ($puntosPersonajeJugador2 > $puntosPersonajeJugador1) {
            $personajeGanador = $personajeJugador2;
            $personajePerdedor = $personajeJugador1;
//            echo 'Gana <b>'.$personajeJugador2->_name.'</b></br></br>';
            $resultado['victoria'] = false;
        } else {
            if($personajeJugador1->_level < $personajeJugador2->_level){
                $personajeGanador = $personajeJugador1;
                $personajePerdedor = $personajeJugador2;
//                echo 'Gana <b>'.$personajeJugador1->_name.'</b></br></br>';
                $resultado['victoria'] = true;
            } else {
                $personajeGanador = $personajeJugador2;
                $personajePerdedor = $personajeJugador1;
//                echo 'Gana <b>'.$personajeJugador2->_name.'</b></br></br>';
                $resultado['victoria'] = false;
            }
        }

        $resultado['damageToLoser'] = $personajeGanador->_damage;
        //Chequeamos bonuses que modifiquen el daño
        if($personajeGanador->isBonusActive(1)){
            $bonusId = $personajeGanador->getClan()->getBonus()->_code;
            switch($bonusId){
                case 'damage-plus-2':
                    $resultado['damageToLoser'] += 2;
                    break;
                default:
                    break;
            }
        }

        if($personajePerdedor->isBonusActive(1)){
            $bonusId = $personajePerdedor->getClan()->getBonus()->_code;
            switch($bonusId){
                case 'opp-damage-minus-2-min-1':
                    $resultado['damageToLoser'] -= 2;
                    if($resultado['damageToLoser'] < 1){
                        $resultado['damageToLoser'] = 1;
                    }
                    break;
                default:
                    break;
            }
        }


        return $resultado;
    }

    public function imprimirArbol()
    {
        foreach($this->_tree as $name => $pillzData){
            foreach($pillzData as $pillz => $playData){
                $victorias = 0;
                $jugadas = 0;
                foreach($playData as $victoriasYJugadas){
                    $victorias += $victoriasYJugadas['victorias'];
                    $jugadas += $victoriasYJugadas['jugadas'];
                }
                $efficiency = ($victorias/$jugadas)*100;
                echo $name.' - '.$pillz.' Pillz : '.$victorias.'/'.$jugadas.' - '.$efficiency.'%<br>';
            }
        }
    }
}