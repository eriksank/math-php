<?php
/**
	Productspace test game logic
        Meant to be used for 2-player games for which the payoff is simplistically
        inscribed in a table.

        This should allow to reuse all the examples for the Von Neumann 2-player minimax stuff.
        I can't find anybody who actually did a 3-player minimax example.
        Probably, that minimax stuff does not even work for 3-player games. Ha ha ha ha ;-)
        I won't waste my time figuring that out. I've got better things to do. Really.

        I really cannot understand why anybody would want to deal with that minimax stuff
        when the real Nash stuff is so much better and even simpler for n>2 players.

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('../lib/NashGame.php');

class Game2PlayersWithPayOffTable extends NashGame
{

	var $playersStrategies=null;
	var $payOffs=null;

	public function payOff($playerNum,$nTuple)
	{
		$player1Strategy=$this->playersStrategies[0][$nTuple[0]];
		$player2Strategy=$this->playersStrategies[1][$nTuple[1]];
		$payOffTuple=$this->payOffs[$player1Strategy.'-'.$player2Strategy];
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

