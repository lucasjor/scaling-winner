<?php

class Jugador
{
    public $_pillz;
    public $_vida;
    public $_personajes;

    public function __construct($personajes, $vida, $pillz)
    {
        $this->_personajes = $personajes;
        $this->_vida = $vida;
        $this->_pillz = $pillz;
    }

    public function vencerRival($rival, $personajeSeleccionado)
    {
        $victorias = array();
        foreach($this->_personajes as $personaje){
            $name = $personaje->_name;
            $victorias[$name] = 0;

            $personaje->jugarContra($rival,$personajeSeleccionado);
        }
    }

    public function elegirPersonaje()
    {

    }
}