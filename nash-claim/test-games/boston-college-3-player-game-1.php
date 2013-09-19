#!/usr/bin/env php
<?php
/**
        Boston College 3-player game
	Game implemented from: https://www2.bc.edu/~sonmezt/E308SL4.pdf, page 5
	Kudos to Satish Poul for pointing me to the link!

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('game-3-players-with-payoff-table.php');

class BostonGame extends Game3PlayersWithPayOffTable
{
	var $playersStrategies=array(
				 array('U','M','D') //playerNum=0, count=3
				,array('L','R') //playerNum=1, count=2
				,array('A','B') //playerNum=2, count=2
				);

	var $payOffs=array(
		'U-L-A' => array(3,2,1),
		'U-R-A'=> array(2,1,1),
                'M-L-A' => array(2,2,0),
                'M-R-A' => array(1,2,1),
                'D-L-A' => array(3,1,2),
                'D-R-A' => array(1,0,2),
                'U-L-B' => array(1,1,2),
                'U-R-B' => array(2,0,1),
                'M-L-B' => array(1,2,0),
                'M-R-B' => array(1,0,2),
                'D-L-B' => array(0,2,3),
                'D-R-B' => array(1,2,2)
        );
}

$bostonGame=new BostonGame();
$bostonGame->findSelfCounteringNTuples();
$bostonGame->dump();
$bostonGame->assertSelfCounteringNTuples(array(array('U','L','B'),array('M','L','B')));

