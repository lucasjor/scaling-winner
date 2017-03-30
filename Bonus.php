<?php

class Bonus
{
    public $_id;
    public $_name;
    public $_code;

    public function __construct($properties)
    {
        if(isset($properties['name']))
            $this->_name = $properties['name'];
        if(isset($properties['code']))
            $this->_code = $properties['code'];
        if(isset($properties['id']))
            $this->_id = $properties['id'];
    }
}