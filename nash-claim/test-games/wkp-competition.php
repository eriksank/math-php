#!/usr/bin/env php
<?php
/**
        Simple 2-player minimax-style game transscribed from Wikipedia
        Actually, only good for testing Von Neumann's theory.
        But ok, lacking better examples, I'll use it to test Nash's theory.

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('game-2-players-with-payoff-table.php');

class CompetitionGame extends Game2PlayersWithPayOffTable
{
        function __construct()
        {
	        $this->playersStrategies=array(
				 array(0,1,2,3) //playerNum=0, count=4
				,array(0,1,2,3) //playerNum=1, count=4
				);

	        $this->payOffs=array(
		'0-0' => array(0,0),
		'0-1' => array(2,-2),
		'0-2' => array(2,-2),
		'0-3' => array(2,-2),
		'1-0' => array(-2,2),
		'1-1' => array(1,1),
		'1-2' => array(3,-1),
		'1-3' => array(3,-1),
		'2-0' => array(-2,2),
		'2-1' => array(-1,3),
		'2-2' => array(2,2),
		'2-3' => array(4,0),
		'3-0' => array(-2,2),
		'3-1' => array(-1,3),
		'3-2' => array(0,4),
		'3-3' => array(3,3)
                );
        }
}

$competitionGame=new CompetitionGame();
$competitionGame->findSelfCounteringNTuples();
$competitionGame->dump();
$competitionGame->assertSelfCounteringNTuples(array(array(0,0)));

