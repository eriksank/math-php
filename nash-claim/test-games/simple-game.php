#!/usr/bin/env php
<?php
/**
        Sample "whatever" Nash Game with 5 players.
        I thought 3 was already too hard?

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('../lib/NashGame.php');

class SampleNashGame extends NashGame
{
	var $playersStrategies=array(
				 array(5,3) //playerNum=0, count=2
				,array(5,3,9) //playerNum=1, count=3
				,array(5,3,9,10,17,8,4,11) //playerNum=2, count=8
				,array(5,3,8,4,11) //playerNum=3, count=4
				,array(11,9,4,8) //playerNum=3, count=4
				);

	public function payOff($playerNum,$nTuple)
	{
                /* silly ranking based on most different from the average
                   aka: largest deviation from the mean */

                $sum=0;
                foreach($nTuple as $playerNumLocal=>$strategyNum)
                        $sum+=$this->playersStrategies[$playerNumLocal][$nTuple[$playerNumLocal]];

                $average=$sum/count($nTuple);
                $playerStrategy=$this->playersStrategies[$playerNum][$nTuple[$playerNum]];
                $deviation=sqrt(pow($average-$playerStrategy,2));
                return $deviation;
	}

	public function playerCount()
	{
		return count($this->playersStrategies);
	}	

	public function playerStrategyCount($playerNum)
	{
		return count($this->playersStrategies[$playerNum]);
	}

	public function playerStrategy($playerNum,$strategyNum)
	{
		return $this->playersStrategies[$playerNum][$strategyNum];
	}

}

$sampleNashGame=new SampleNashGame();
$sampleNashGame->findSelfCounteringNTuples();
$sampleNashGame->dump();

