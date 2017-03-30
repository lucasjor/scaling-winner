<?php

function in_array_r($item , $array){
    return preg_match('/"'.$item.'"/i' , json_encode($array));
}

function array_cartesian() {
    $_ = func_get_args();
    if(count($_) == 0)
        return array(array());
    $a = array_shift($_);
    $c = call_user_func_array(__FUNCTION__, $_);
    $r = array();
    foreach($a as $v)
        foreach($c as $p)
            $r[] = array_merge(array($v), $p);
    return $r;
}