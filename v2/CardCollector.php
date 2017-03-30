<?php

class CardCollector
{
    protected $_cards = array();

    public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new CardCollector();
        }
        return $inst;
    }

    private function __construct()
    {

    }

    public function loadCard($id)
    {
        if(!isset($this->_cards[$id])){
            $data = DB::run('SELECT * FROM Personaje WHERE id='.$id)->fetch();
            $personaje = new Personaje(array(
                'name' => $data['name'],
                'level' => $data['level'],
                'power' => $data['power'],
                'damage' => $data['damage'],
                'id' => $data['id'],
                'clan' => $data['clan'],
                'isLead' => $data['is_lead']
            ));
            $this->_cards[$id] = $personaje;
        }
        return $this->_cards[$id];
    }
}