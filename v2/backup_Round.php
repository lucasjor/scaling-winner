<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 14/12/15
 * Time: 21:09
 */

class Round
{
    protected $_type = 'attack'; //attack or defense
    protected $_level = 0; //0,1,2,3,4
    protected $_status = ''; //lost, win, ''
    protected $_fighters = array();
    protected $_remainCards = array();
    protected $_remainPillz = array();
    protected $_usingPillz = array();
    protected $_usingFury = array();
    protected $_remainLives = array();
    protected $_activeBonuses = array();
    protected $_blockedHabilities = array();
    protected $_blockedBonuses = array();
    protected $_subRounds = array();
    protected $_parentRound = null;
    protected $_selectedRivalCard = null;
    protected $_plays = 0;
    protected $_wins = 0;

    public function __construct($options)
    {
        $this->_type = $options['type'];
        $this->_level = $options['level'];
        $this->_status = $options['status'];
        $this->_fighters = $options['fighters'];
        $this->_remainCards = $options['remainCards'];
        $this->_remainPillz = $options['remainPillz'];
        $this->_usingPillz = $options['usingPillz'];
        $this->_usingFury = $options['usingFury'];
        $this->_remainLives = $options['remainLives'];
        $this->_blockedHabilities = $options['blockedHabilities'];
        $this->_blockedBonuses = $options['blockedBonuses'];
        $this->_parentRound = $options['parentRound'];
        $this->_selectedRivalCard = isset($options['selectedRivalCard']) ? $options['selectedRivalCard'] : null;
    }

    public function play()
    {
        if($this->_level == 0){
            if($this->_selectedRivalCard){
                $this->_subRounds = $this->generateSubRoundsRivalSelected();
            } else {
                $this->_subRounds = $this->generateSubRounds($this->_type);
            }
            foreach($this->_subRounds as $subRound){
                $subRound->play();
            }
        } else {
            $this->battle();
            if($this->_status == ''){
                $this->_subRounds = $this->generateSubRounds($this->getNextType());
                foreach($this->_subRounds as $subRound){
                    $subRound->play();
                }
                $this->_subRounds = null;
            } else {
                $parentRound = ($this->getLevel() == 1) ? $this : $this->getParentRound();
                while($parentRound->getLevel() > 1){
                    $parentRound = $parentRound->getParentRound();
                }
                $parentRound->incrementPlays();
                if($this->_status == 'win'){
                    $parentRound->incrementWins();
                }
            }
        }
    }

    protected function _generateSubRounds($attackerId, $defenderId, $attackCard, $defenseCard)
    {
        $subRounds = array();
        $nextType = $this->getNextType();
        $nextRemainCards = $this->_remainCards;
        $attackKey = array_search($attackCard, $nextRemainCards[$attackerId]);
        $defenseKey = array_search($defenseCard, $nextRemainCards[$defenderId]);
        unset($nextRemainCards[$attackerId][$attackKey]);
        unset($nextRemainCards[$defenderId][$defenseKey]);

        $blockedHabilities = array(); //TODO
        $blockedBonuses = array(); //TODO

        for($attackerPillz = 0; $attackerPillz <= $this->_remainPillz[$attackerId]; $attackerPillz++) {

            $bestDefenderPillz = $this->calculateMinimumPillz(array(
                'attackCard' => $attackCard,
                'defenseCard' => $defenseCard,
                'attackPillz' => $attackerPillz
            ));

            if ($bestDefenderPillz > 0) {
                $defenderPillzPossibilities = array(
                    array('pillz' => 0, 'fury' => false),
                    array('pillz' => $bestDefenderPillz, 'fury' => false),
                    array('pillz' => $bestDefenderPillz, 'fury' => true),

                );
            } elseif ($bestDefenderPillz == 0) {
                $defenderPillzPossibilities = array(
                    array('pillz' => 0, 'fury' => false),
                    array('pillz' => 0, 'fury' => true)
                );
            } else {
                $defenderPillzPossibilities = array(
                    array('pillz' => 0, 'fury' => false)
                );
            }

            foreach ($defenderPillzPossibilities as $defenderPillzPossibility) {

                $usingPillz = array(
                    $attackerId => $attackerPillz,
                    $defenderId => $defenderPillzPossibility['pillz']
                );

                $usingFury = array(
                    $attackerId => false,
                    $defenderId => $defenderPillzPossibility['fury']
                );

                $subRounds[] = new Round(array(
                    'type' => $nextType,
                    'level' => $this->_level + 1,
                    'status' => '',
                    'fighters' => array(
                        $attackerId => $attackCard,
                        $defenderId => $defenseCard
                    ),
                    'remainCards' => $nextRemainCards,
                    'remainPillz' => $this->_remainPillz,
                    'usingPillz' => $usingPillz,
                    'usingFury' => $usingFury,
                    'remainLives' => $this->_remainLives,
                    'blockedHabilities' => $blockedHabilities,
                    'blockedBonuses' => $blockedBonuses,
                    'parentRound' => $this,
                ));

                if ($attackerPillz + 3 <= $this->_remainPillz[$attackerId]) {
                    $usingFury[$attackerId] = true;
                    $subRounds[] = new Round(array(
                        'type' => $nextType,
                        'level' => $this->_level + 1,
                        'status' => '',
                        'fighters' => array(
                            $attackerId => $attackCard,
                            $defenderId => $defenseCard
                        ),
                        'remainCards' => $nextRemainCards,
                        'remainPillz' => $this->_remainPillz,
                        'usingPillz' => $usingPillz,
                        'usingFury' => $usingFury,
                        'remainLives' => $this->_remainLives,
                        'blockedHabilities' => $blockedHabilities,
                        'blockedBonuses' => $blockedBonuses,
                        'parentRound' => $this,
                    ));
                }
            }
        }
        return $subRounds;
    }

    public function generateSubRounds($nextType)
    {
        $subRounds = array();
        if($nextType == 'attack'){
            foreach($this->_remainCards[1] as $attackCard){
                foreach($this->_remainCards[2] as $defenseCard){

                    $nextRemainCards = $this->_remainCards;
                    $attackKey = array_search($attackCard, $nextRemainCards[1]);
                    $defenseKey = array_search($defenseCard, $nextRemainCards[2]);
                    unset($nextRemainCards[1][$attackKey]);
                    unset($nextRemainCards[2][$defenseKey]);

                    $blockedHabilities = array(); //TODO
                    $blockedBonuses = array(); //TODO

                    for($myPillz = 0; $myPillz <= $this->_remainPillz[1]; $myPillz++){

                        $bestRivalPillz = $this->calculateMinimumPillz(array(
                            'attackCard' => $attackCard,
                            'defenseCard' => $defenseCard,
                            'attackPillz' => $myPillz
                        ));

                        if($bestRivalPillz > 0){
                            $rivalPillzPossibilities = array(
                                array('pillz' => 0, 'fury' => false),
                                array('pillz' => $bestRivalPillz, 'fury' => false),
                                array('pillz' => $bestRivalPillz, 'fury' => true),

                            );
                        } elseif($bestRivalPillz == 0) {
                            $rivalPillzPossibilities = array(
                                array('pillz' => 0, 'fury' => false),
                                array('pillz' => 0, 'fury' => true)
                            );
                        } else {
                            $rivalPillzPossibilities = array(
                                array('pillz' => 0, 'fury' => false)
                            );
                        }

                        foreach($rivalPillzPossibilities as $rivalPillzPossibility){

                            $usingPillz = array(
                                1 => $myPillz,
                                2 => $rivalPillzPossibility['pillz']
                            );

                            $usingFury = array(
                                1 => false,
                                2 => $rivalPillzPossibility['fury']
                            );

                            $subRounds[] = new Round(array(
                                'type' => $nextType,
                                'level' => $this->_level+1,
                                'status' => '',
                                'fighters' => array(
                                    1 => $attackCard,
                                    2 => $defenseCard
                                ),
                                'remainCards' => $nextRemainCards,
                                'remainPillz' => $this->_remainPillz,
                                'usingPillz' => $usingPillz,
                                'usingFury' => $usingFury,
                                'remainLives' => $this->_remainLives,
                                'blockedHabilities' => $blockedHabilities,
                                'blockedBonuses' => $blockedBonuses,
                                'parentRound' => $this,
                            ));

                            if($myPillz + 3 <= $this->_remainPillz[1]){
                                $usingFury[1] = true;
                                $subRounds[] = new Round(array(
                                    'type' => $nextType,
                                    'level' => $this->_level+1,
                                    'status' => '',
                                    'fighters' => array(
                                        1 => $attackCard,
                                        2 => $defenseCard
                                    ),
                                    'remainCards' => $nextRemainCards,
                                    'remainPillz' => $this->_remainPillz,
                                    'usingPillz' => $usingPillz,
                                    'usingFury' => $usingFury,
                                    'remainLives' => $this->_remainLives,
                                    'blockedHabilities' => $blockedHabilities,
                                    'blockedBonuses' => $blockedBonuses,
                                    'parentRound' => $this,
                                ));
                            }
                        }
                    }
                }
            }
        } else {
            //TYPE IS DEFENSE
            foreach($this->_remainCards[2] as $attackCard){
                foreach($this->_remainCards[1] as $defenseCard){

                    $nextRemainCards = $this->_remainCards;
                    $attackKey = array_search($attackCard, $nextRemainCards[2]);
                    $defenseKey = array_search($defenseCard, $nextRemainCards[1]);
                    unset($nextRemainCards[2][$attackKey]);
                    unset($nextRemainCards[1][$defenseKey]);

                    $blockedHabilities = array(); //TODO
                    $blockedBonuses = array(); //TODO

                    for($hisPillz = 0; $hisPillz <= $this->_remainPillz[2]; $hisPillz++){

                        $bestOwnPillz = $this->calculateMinimumPillz(array(
                            'attackCard' => $attackCard,
                            'defenseCard' => $defenseCard,
                            'attackPillz' => $hisPillz
                        ));

                        if($bestOwnPillz > 0){
                            $ownPillzPossibilities = array(
                                array('pillz' => 0, 'fury' => false),
                                array('pillz' => $bestOwnPillz, 'fury' => false),
                                array('pillz' => $bestOwnPillz, 'fury' => true),

                            );
                        } elseif($bestOwnPillz == 0) {
                            $ownPillzPossibilities = array(
                                array('pillz' => 0, 'fury' => false),
                                array('pillz' => 0, 'fury' => true)
                            );
                        } else {
                            $ownPillzPossibilities = array(
                                array('pillz' => 0, 'fury' => false)
                            );
                        }

                        foreach($ownPillzPossibilities as $ownPillzPossibility){

                            $usingPillz = array(
                                1 => $ownPillzPossibility['pillz'],
                                2 => $hisPillz
                            );

                            $usingFury = array(
                                1 => $ownPillzPossibility['fury'],
                                2 => false
                            );

                            $subRounds[] = new Round(array(
                                'type' => $nextType,
                                'level' => $this->_level+1,
                                'status' => '',
                                'fighters' => array(
                                    1 => $defenseCard,
                                    2 => $attackCard
                                ),
                                'remainCards' => $nextRemainCards,
                                'remainPillz' => $this->_remainPillz,
                                'usingPillz' => $usingPillz,
                                'usingFury' => $usingFury,
                                'remainLives' => $this->_remainLives,
                                'blockedHabilities' => $blockedHabilities,
                                'blockedBonuses' => $blockedBonuses,
                                'parentRound' => $this,
                            ));

                            if($hisPillz + 3 <= $this->_remainPillz[2]){
                                $usingFury[2] = true;
                                $subRounds[] = new Round(array(
                                    'type' => $nextType,
                                    'level' => $this->_level+1,
                                    'status' => '',
                                    'fighters' => array(
                                        1 => $defenseCard,
                                        2 => $attackCard
                                    ),
                                    'remainCards' => $nextRemainCards,
                                    'remainPillz' => $this->_remainPillz,
                                    'usingPillz' => $usingPillz,
                                    'usingFury' => $usingFury,
                                    'remainLives' => $this->_remainLives,
                                    'blockedHabilities' => $blockedHabilities,
                                    'blockedBonuses' => $blockedBonuses,
                                    'parentRound' => $this,
                                ));
                            }
                        }
                    }
                }
            }
        }
        return $subRounds;
    }

    public function generateSubRoundsRivalSelected()
    {
        $subRounds = array();

        $attackCard = $this->_selectedRivalCard;
        foreach($this->_remainCards[1] as $defenseCard){

            $subRounds = array_merge($subRounds, $this->_generateSubRounds(2, 1, $attackCard, $defenseCard));

        }
        return $subRounds;
    }

    public function calculateMinimumPillz($options)
    {
        if($this->_type == 'attack' && $this->_level > 0){
            $defenderPlayer = 1;
        } else {
            $defenderPlayer = 2;
        }

        $attackCard = CardCollector::getInstance()->loadCard($options['attackCard']);
        $defenseCard = CardCollector::getInstance()->loadCard($options['defenseCard']);

        $attackerPower = $attackCard->getPower();
        $attackerAttack = ($options['attackPillz'] + 1) * $attackerPower;

        $defenderPower = $defenseCard->getPower();
        $best = floor($attackerAttack / $defenderPower);

        if($this->_remainPillz[$defenderPlayer] < $best){
            return 0;
        }
        return $best;
    }

    private function battle()
    {
        if($this->_type == 'attack'){
            $attackerPlayer = 1;
            $defenderPlayer = 2;
        } else {
            $attackerPlayer = 2;
            $defenderPlayer = 1;
        }

        //Begin Validations
        if($this->_usingPillz[$attackerPlayer] > $this->_remainPillz[$attackerPlayer]
        || $this->_usingPillz[$defenderPlayer] > $this->_remainPillz[$defenderPlayer]){
            Throw new Exception('Insufficient Pillz');
        }
        //End validations


        //Begin data initialization
        $attackCard = CardCollector::getInstance()->loadCard($this->_fighters[$attackerPlayer]);
        $defenseCard = CardCollector::getInstance()->loadCard($this->_fighters[$defenderPlayer]);

        $powerVariationAttacker = array();
        $powerVariationDefender = array();
        $attackVariationAttacker = array();
        $attackVariationDefender = array();

        //Temporarly commented for testing
//        if(!isset($this->_blockedHabilities[1]) || !$this->_blockedHabilities[1]){
//            $powerVariationAttacker[] = $attackCard->getHability()->increasesOwnPower();
//            $attackVariationAttacker[] = $attackCard->getHability()->increasesOwnAttack();
//            $powerVariationDefender[] = $attackCard->getHability()->decreasesRivalPower();
//            $attackVariationDefender[] = $attackCard->getHability()->decreasesRivalAttack();
//        }
//        if(!isset($this->_blockedHabilities[2]) || !$this->_blockedHabilities[2]) {
//            $powerVariationAttacker[] = $defenseCard->getHability()->decreasesRivalPower();
//            $attackVariationAttacker[] = $defenseCard->getHability()->decreasesRivalAttack();
//            $powerVariationDefender[] = $defenseCard->getHability()->increasesOwnPower();
//            $attackVariationDefender[] = $defenseCard->getHability()->increasesOwnAttack();
//        }
//
//        if(!isset($this->_blockedBonuses[1]) || !$this->_blockedBonuses[1]){
//            $powerVariationAttacker[] = $attackCard->getBonus()->increasesOwnPower();
//            $attackVariationAttacker[] = $attackCard->getBonus()->increasesOwnAttack();
//            $powerVariationDefender[] = $attackCard->getBonus()->decreasesRivalPower();
//            $attackVariationDefender[] = $attackCard->getBonus()->decreasesRivalAttack();
//        }
//        if(!isset($this->_blockedBonuses[2]) || !$this->_blockedBonuses[2]) {
//            $powerVariationAttacker[] = $defenseCard->getBonus()->decreasesRivalPower();
//            $attackVariationAttacker[] = $defenseCard->getBonus()->decreasesRivalAttack();
//            $powerVariationDefender[] = $defenseCard->getBonus()->increasesOwnPower();
//            $attackVariationDefender[] = $defenseCard->getBonus()->increasesOwnAttack();
//        }

        //Powers calculation
        $attackerPower = $attackCard->getPower();
        $defenderPower = $defenseCard->getPower();

        foreach($powerVariationAttacker as $powerVariation){
            if(isset($powerVariation['limit']) && isset($powerVariation['limit_value']) &&
                (
                    ($powerVariation['limit'] == 'min' && ($attackerPower+$powerVariation['value'] < $powerVariation['limit_value']))
                    || $powerVariation['limit'] == 'max' && ($attackerPower+$powerVariation['value'] > $powerVariation['limit_value'])
                ))
            {
                $attackerPower = $powerVariation['limit_value'];

            } else {
                $attackerPower += $powerVariation['value'];
            }
        }

        foreach($powerVariationDefender as $powerVariation){
            if(isset($powerVariation['limit']) && isset($powerVariation['limit_value']) &&
                (
                    ($powerVariation['limit'] == 'min' && ($defenderPower+$powerVariation['value'] < $powerVariation['limit_value']))
                    || $powerVariation['limit'] == 'max' && ($defenderPower+$powerVariation['value'] > $powerVariation['limit_value'])
                ))
            {
                $defenderPower = $powerVariation['limit_value'];

            } else {
                $defenderPower += $powerVariation['value'];
            }
        }
        ///////////////////////////

        //Attacks calculation
        $attackerAttack = ($this->_usingPillz[$attackerPlayer] + 1) * $attackerPower;
        $defenderAttack = ($this->_usingPillz[$defenderPlayer] + 1) * $defenderPower;

        foreach($attackVariationAttacker as $attackVariation){
            if(isset($attackVariation['limit']) && isset($attackVariation['limit_value']) &&
                (
                    ($attackVariation['limit'] == 'min' && ($attackerAttack+$attackVariation['value'] < $attackVariation['limit_value']))
                    || $attackVariation['limit'] == 'max' && ($attackerAttack+$attackVariation['value'] > $attackVariation['limit_value'])
                ))
            {
                $attackerAttack = $attackVariation['limit_value'];

            } else {
                $attackerAttack += $attackVariation['value'];
            }
        }

        foreach($attackVariationDefender as $attackVariation){
            if(isset($attackVariation['limit']) && isset($attackVariation['limit_value']) &&
                (
                    ($attackVariation['limit'] == 'min' && ($defenderAttack+$attackVariation['value'] < $attackVariation['limit_value']))
                    || $attackVariation['limit'] == 'max' && ($defenderAttack+$attackVariation['value'] > $attackVariation['limit_value'])
                ))
            {
                $defenderAttack = $attackVariation['limit_value'];

            } else {
                $defenderAttack += $attackVariation['value'];
            }
        }
        ///////////////////////

        //Remove used pillz
        $this->_remainPillz[$attackerPlayer] -= $this->_usingPillz[$attackerPlayer];
        $this->_remainPillz[$defenderPlayer] -= $this->_usingPillz[$defenderPlayer];

        //Remove attack draw
        if($attackerAttack == $defenderAttack){
            if($attackCard->_level < $defenseCard->_level){
                $defenderAttack++;
            } else {
                $attackerAttack++;
            }
        }

        $furyDamageAttacker = $this->_usingFury[$attackerPlayer] ? 2 : 0;
        $furyDamageDefender = $this->_usingFury[$defenderPlayer] ? 2 : 0;

        if($attackerAttack > $defenderAttack){
            $this->_remainLives[$defenderPlayer] -= $attackCard->_damage + $furyDamageAttacker;
            if($this->_remainLives[$defenderPlayer] < 0){
                $winner = 'attacker';
            }
        } else {
            $this->_remainLives[$attackerPlayer] -= $defenseCard->_damage + $furyDamageDefender;
            if($this->_remainLives[$attackerPlayer] < 0){
                $winner = 'defender';
            }
        }
        if(!isset($winner) && $this->_status == '' && empty($this->_remainCards[$attackerPlayer])){
            if($this->_remainLives[$attackerPlayer] > $this->_remainLives[$defenderPlayer]){
                $winner = 'attacker';
            } elseif($this->_remainLives[$defenderPlayer] > $this->_remainLives[$attackerPlayer]){
                $winner = 'defender';
            } else {
                $winner = 'draw';
            }
        }

        if(isset($winner)){
            if(($winner == 'attacker' && $attackerPlayer == 1)
                || ($winner == 'defender' && $attackerPlayer == 2)){
                $this->_status = 'win';
            } elseif (($winner == 'attacker' && $attackerPlayer == 2)
                || $winner == 'defender' && $attackerPlayer == 1) {
                $this->_status = 'lost';
            } elseif($winner == 'draw') {
                $this->_status = 'draw';
            }
        }

    }

    public function getLevel()
    {
        return $this->_level;
    }

    public function getParentRound()
    {
        return $this->_parentRound;
    }

    public function getSubrounds()
    {
        return $this->_subRounds;
    }

    public function getPlays()
    {
        return $this->_plays;
    }

    public function getWins()
    {
        return $this->_wins;
    }

    public function getAttackerFighter()
    {
        return CardCollector::getInstance()->loadCard($this->_fighters[1]);
    }

    public function getAttackerUsedPillz()
    {
        return $this->_usingPillz[1];
    }

    public function getAttackerFury()
    {
        return $this->_usingFury[1];
    }

    public function incrementPlays()
    {
        $this->_plays++;
    }

    public function incrementWins()
    {
        $this->_wins++;
    }

    public function getNextType()
    {
        if($this->_level == 0){
            return $this->_type;
        }
        if($this->_type == 'attack'){
            return 'defense';
        }
        return 'attack';
    }


}