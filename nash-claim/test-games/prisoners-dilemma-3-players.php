#!/usr/bin/env php
<?php
/**
        Prisoner's dilemma with 3 players.
 
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('game-3-players-with-payoff-table.php');

class DilemmaGame extends Game3PlayersWithPayOffTable
{
	var $playersStrategies=array(
				 array('cooperate','betray') //playerNum=0, count=2
				,array('cooperate','betray') //playerNum=1, count=2
				,array('cooperate','betray') //playerNum=2, count=2
				);

	var $payOffs=array(
		'cooperate-cooperate-cooperate' => array(2,2,2),
		'cooperate-cooperate-betray'=> array(0,0,3),
                'cooperate-betray-cooperate' => array(0,3,0),
                'cooperate-betray-betray' => array(0,3,3),
                'betray-cooperate-cooperate' => array(3,0,0),
                'betray-cooperate-betray' => array(3,0,3),
                'betray-betray-cooperate' => array(3,3,0),
                'betray-betray-betray' => array(1,1,1)
        );
}

$dilemmaGame=new DilemmaGame();
$dilemmaGame->findSelfCounteringNTuples();
$dilemmaGame->dump();
$dilemmaGame->assertSelfCounteringNTuples(array(array('betray','betray','betray')));

