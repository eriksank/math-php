#!/usr/bin/env php
<?php
/**
        Script to run all tests

	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
function execPHP($script)
{
        system("php $script");
}

$scriptsExcluded=array(
		'.','..',
		'test-all.php', //this script
		'game-2-players-with-payoff-table.php', //facilitating parent class
		'game-3-players-with-payoff-table.php' //facilitating parent class
);

$thisDir=opendir(dirname(__FILE__));
while(false!==$file=readdir($thisDir))
{
	if(!in_array($file,$scriptsExcluded))
	{
		echo "-----------------------------------------------------------\n";
		echo "EXECUTING GAME: $file\n";
		echo "-----------------------------------------------------------\n";
		execPHP($file);
	}
}

