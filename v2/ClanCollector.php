<?php

class ClanCollector
{
    protected $_clans = array();

    public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new ClanCollector();
        }
        return $inst;
    }

    private function __construct()
    {

    }

    public function loadClan($id)
    {
        if(!isset($this->_clans[$id])){
            $data = DB::run('SELECT * FROM Clan WHERE id='.$id)->fetch();
            $clan = new Clan(array(
                'id' => $data['id'],
                'name' => $data['name'],
                'bonus' => $data['bonus']
            ));
            $this->_clans[$id] = $clan;
        }
        return $this->_clans[$id];
    }
}