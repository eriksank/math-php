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
				 array('left','right') //playerNum=0, count=2
				,array('left','right') //playerNum=1, count=2
				);

	        $this->payOffs=array(
		'left-left' => array(100,100),
		'left-right' => array(0,0),
		'right-left' => array(0,0),
		'right-right' => array(100,100));
        }
}

$coordinationGame=new CoordinationGame();
$coordinationGame->findSelfCounteringNTuples();
$coordinationGame->dump();
$coordinationGame->assertSelfCounteringNTuples(array(array('left','left'),array('right','right')));

