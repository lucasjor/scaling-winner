<?php

class Helper
{
    public function __construct()
    {
        return $this;
    }

    public function cargarPersonaje($id)
    {
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
        return $personaje;
    }

    public function cargarClan($id)
    {
        $data = DB::run('SELECT * FROM Clan WHERE id='.$id)->fetch();
        $clan = new Clan(array(
            'id' => $data['id'],
            'name' => $data['name'],
            'bonus' => $data['bonus']
        ));
        return $clan;
    }

    public function cargarBonus($id)
    {
        $data = DB::run('SELECT * FROM Bonus WHERE id='.$id)->fetch();
        $bonus = new Bonus(array(
            'id' => $data['id'],
            'name' => $data['name'],
            'code' => $data['code']
        ));
        return $bonus;
    }
}