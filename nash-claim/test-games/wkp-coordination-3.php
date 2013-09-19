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
				 array('A','B','C') //playerNum=0, count=3
				,array('A','B','C') //playerNum=1, count=3
				);

	        $this->payOffs=array(
		'A-A' => array(0,0),
		'A-B' => array(25,40),
		'A-C' => array(5,10),
		'B-A' => array(40,25),
		'B-B' => array(0,0),
		'B-C' => array(5,15),
		'C-A' => array(10,5),
		'C-B' => array(15,5),
		'C-C' => array(10,10)
                );
        }
}

$coordinationGame=new CoordinationGame();
$coordinationGame->findSelfCounteringNTuples();
$coordinationGame->dump();
$coordinationGame->assertSelfCounteringNTuples(array(array('A','B'),array('B','A'),array('C','C')));

