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
				 array('L','R') //playerNum=0, count=2
				,array('KK','KU','UU','UK') //playerNum=1, count=4
				);

	        $this->payOffs=array(
		'L-KK' => array(3,1),
		'L-KU' => array(3,1),
		'L-UU' => array(1,3),
		'L-UK' => array(1,3),
		'R-KK' => array(2,1),
		'R-KU' => array(0,0),
		'R-UU' => array(0,0),
		'R-UK' => array(2,1)
                );
        }
}

$coordinationGame=new CoordinationGame();
$coordinationGame->findSelfCounteringNTuples();
$coordinationGame->dump();
$coordinationGame->assertSelfCounteringNTuples(array(array('L','UU'),array('R','UK')));

