#!/usr/bin/env php
<?php
/**
        Boston College 3-player game
	Game implemented from: https://www2.bc.edu/~sonmezt/E308SL4.pdf, page 17
	Kudos to Satish Poul for pointing me to the link!

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('game-3-players-with-payoff-table.php');

class BostonGame extends Game3PlayersWithPayOffTable
{
	var $playersStrategies=array(
				 array('U','D') //playerNum=0, count=2
				,array('L','R') //playerNum=1, count=2
				,array('A','B') //playerNum=2, count=2
				);

	var $payOffs=array(
		'U-L-A' => array(5,5,1),
		'U-R-A'=> array(2,1,3),
                'D-L-A' => array(4,7,6),
                'D-R-A' => array(1,8,5),
                'U-L-B' => array(0,2,2),
                'U-R-B' => array(4,4,4),
                'D-L-B' => array(1,1,1),
                'D-R-B' => array(3,7,1)
        );
}

$bostonGame=new BostonGame();
$bostonGame->findSelfCounteringNTuples();
$bostonGame->dump();
$bostonGame->assertSelfCounteringNTuples(array(array('U','R','B')));

