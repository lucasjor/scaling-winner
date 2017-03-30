<?php

class Personaje
{
    public $_power;
    public $_damage;
    public $_name;
    public $_level;
    public $_clanId;
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
            $this->_clanId = $properties['clan'];
        if(isset($properties['damage']))
            $this->_damage = $properties['damage'];
        if(isset($properties['isLead']))
            $this->_isLead = $properties['isLead'];
        if(isset($properties['id']))
            $this->_id = $properties['id'];

        return $this;
    }
    
    public function isBonusActive($team)
    {
        $personajesDelClan = 0;
        foreach($_SESSION['initial_team'][$team] as $personajeId){
            $helper = new Helper();
            $personaje = $helper->cargarPersonaje($personajeId);
            
            if($personaje->_id != $this->_id
                && $personaje->_clanId == $this->_clanId){
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
            $this->_clanObject = ClanCollector::getInstance()->loadClan($this->_clanId);
        }
        return $this->_clanObject;
    }

    public function getPower()
    {
        return $this->_power;
    }
}