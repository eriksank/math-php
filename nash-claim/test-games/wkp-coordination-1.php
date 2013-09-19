#!/usr/bin/env php
<?php
/**
        Simple 2-player minimax-style game transscribed from Wikipedia:
        http://en.wikipedia.org/wiki/Nash_equilibrium

        Actually, only good for illustrating or testing Von Neumann's theory.
        But ok, lacking better examples, I'll use it to test Nash's theory.

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('game-2-players-with-payoff-table.php');

class CoordinationGame extends Game2PlayersWithPayOffTable
{
        function __construct()
        {
	        $this->playersStrategies=array(
				 array('A','B') //playerNum=0, count=2
				,array('A','B') //playerNum=1, count=2
				);

	        $this->payOffs=array(
		        'A-A' => array(4,4),
		        'A-B' => array(1,3),
		        'B-A' => array(3,1),
		        'B-B' => array(2,2));
        }
}

$coordinationGame=new CoordinationGame();
$coordinationGame->findSelfCounteringNTuples();
$coordinationGame->dump();
$coordinationGame->assertSelfCounteringNTuples(array(array('A','A'),array('B','B')));

