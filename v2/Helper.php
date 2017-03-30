<?php

class Helper
{
    public function __construct()
    {
        return $this;
    }

    public function printResults($initialRound)
    {
        foreach($initialRound->getSubrounds() as $roundLevel1){
            $fighterName = $roundLevel1->getAttackerFighter()->_name;
            $usedPillz = $roundLevel1->getAttackerUsedPillz();
            $fury = '';
            if($roundLevel1->getAttackerFury()){
                $fury = 'Fury ';
            }
            if(!isset($fighters[$fighterName][$fury.$usedPillz]['plays']) && !isset($fighters[$fighterName][$fury.$usedPillz]['wins'])){
                $fighters[$fighterName][$fury.$usedPillz]['plays'] = 0;
                $fighters[$fighterName][$fury.$usedPillz]['wins'] = 0;
            }
            $fighters[$fighterName][$fury.$usedPillz]['plays'] += $roundLevel1->getPlays();
            $fighters[$fighterName][$fury.$usedPillz]['wins'] += $roundLevel1->getWins();
        }

        $maxScore1 = 0;
        $maxScore2 = 0;
        $maxScore3 = 0;
        foreach($fighters as $fighterName => $pillzPossibility){
            foreach($pillzPossibility as $usedPillz => $summary){
                $score = round($summary['wins']/$summary['plays'], 5);
                if($score > $maxScore3){
                    if($score > $maxScore2){
                        if($score > $maxScore1){
                            $maxScore1 = $score;
                        } else {
                            $maxScore2 = $score;
                        }
                    } else {
                        $maxScore3 = $score;
                    }
                }
                echo '<span class="s'.$score.'">'.$fighterName.' - '.$usedPillz.' Pillz - '.$score."</span><br/>";
            }
        }

        return array(
            $maxScore1,
            $maxScore2,
            $maxScore3
        );
    }

}