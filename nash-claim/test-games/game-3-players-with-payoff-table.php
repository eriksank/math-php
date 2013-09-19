<?php
/**
	Productspace test game logic
        Meant to be used for 3-player games for which the payoff is simplistically
        inscribed in a table.

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('../lib/NashGame.php');

class Game3PlayersWithPayOffTable extends NashGame
{

	var $playersStrategies=null;
	var $payOffs=null;

	public function payOff($playerNum,$nTuple)
	{
		$player1Strategy=$this->playersStrategies[0][$nTuple[0]];
		$player2Strategy=$this->playersStrategies[1][$nTuple[1]];
		$player3Strategy=$this->playersStrategies[2][$nTuple[2]];
		$payOffTuple=$this->payOffs[$player1Strategy.'-'.$player2Strategy.'-'.$player3Strategy];
		return $payOffTuple[$playerNum];
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

	public function playerStrategyNum($playerNum,$strategy)
	{
		return array_search($strategy,$this->playersStrategies[$playerNum]); 
	}

}

