<?php

class Clan
{
    public $_id;
    public $_name;
    public $_bonus;

    protected $_bonusObject = false;

    public function __construct($properties)
    {
        if(isset($properties['name']))
            $this->_name = $properties['name'];
        if(isset($properties['bonus']))
            $this->_bonus = $properties['bonus'];
        if(isset($properties['id']))
            $this->_id = $properties['id'];
    }

    public function getBonus()
    {
        if(!$this->_bonusObject){
            $helper = new Helper();
            $this->_bonusObject = $helper->cargarBonus($this->_bonus);
        }
        return $this->_bonusObject;
    }
}