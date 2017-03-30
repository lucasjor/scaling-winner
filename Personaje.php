<?php

class Personaje
{
    public $_power;
    public $_damage;
    public $_name;
    public $_level;
    public $_clan;
    public $_isLead;
    public $_id;

    protected $_clanObject = false;

    public function __construct($properties)
    {
        if(isset($properties['name']))
            $this->_name = $properties['name'];
        if(isset($properties['level']))
            $this->_level = $properties['level'];
        if(isset($properties['power']))
            $this->_power = $properties['power'];
        if(isset($properties['clan']))
            $this->_clan = $properties['clan'];
        if(isset($properties['damage']))
            $this->_damage = $properties['damage'];
        if(isset($properties['isLead']))
            $this->_isLead = $properties['isLead'];
        if(isset($properties['id']))
            $this->_id = $properties['id'];

        return $this;
    }

    public function jugarContraSegundo($rival, $personajeSeleccionado, $yo)
    {
        $rivalCopia = new Jugador($rival->_personajes, $rival->_vida, $rival->_pillz);
        $yoCopia = new Jugador($yo->_personajes, $yo->_vida, $yo->_pillz);
        $personaje = $rival->_personajes[$personajeSeleccionado];

        for($i=1;$i<=$this->_pillz;$i++){
            $resultados = $this->peleaContra($rivalCopia,$personaje,$i,$yoCopia,2);
        }
    }

    public function peleaContra($rival,$personaje,$pillz,$yo,$turno)
    {
        $victorias = 0;
        $derrotas = 0;
        for($i=1;$i<=$rival->_pillz;$i++){
            if(($this->_power * $pillz) > ($personaje->_power * $i)){
                $rival->_vida -= $this->_damage;
                if($rival->_vida <= 0) {
                    $victorias++;
                } else {
                    if($turno == 2) {
                        $yo->elegirPersonaje();
                    } else {
                        $rival->elegirPersonaje();
                    }
                }
            } elseif(($this->_power * $pillz) < ($personaje->_power * $i)){
                $yo->_vida -= $personaje->_damage;
                if($yo->_vida <= 0) {
                    $derrotas++;
                } else {
                    $yo->jugarContraPrimero();
                }
                $derrotas++;
            } elseif($this->_level > $personaje->_level){
                $victorias++;
            } else {
                $derrotas++;
            }
        }

        return array(
            'victorias' => $victorias,
            'derrotas' => $derrotas
        );
    }
    
    public function isBonusActive($team)
    {
        $personajesDelClan = 0;
        foreach($_SESSION['initial_team'][$team] as $personajeId){
            $helper = new Helper();
            $personaje = $helper->cargarPersonaje($personajeId);
            
            if($personaje->_id != $this->_id
                && $personaje->_clan == $this->_clan){
                $personajesDelClan++;
            }
        }
        if($personajesDelClan >= 1) { //TODO cambiar por 2 cuando usemos equipos de 4 cartas
            return true;
        }

        return false;
    }

    public function getClan()
    {
        if(!$this->_clanObject){
            $helper = new Helper();
            $this->_clanObject = $helper->cargarClan($this->_clan);
        }
        return $this->_clanObject;
    }
}