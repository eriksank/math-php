#!/usr/bin/env php
<?php
/**
        Prisoner's dilemma with n players.
 
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require('../lib/NashGame.php');

define('NUMBER_OF_PLAYERS',10);

define('COOPERATE',0);
define('BETRAY',1);

define('COOPERATE_REPR','cooperate');
define('BETRAY_REPR','betray');

define('PAYOFF_COOPERATE_WITH_BETRAYERS',0);
define('PAYOFF_EVERYBODY_BETRAYS',1);
define('PAYOFF_EVERYBODY_COOPERATES',2);
define('PAYOFF_BETRAY_WITH_COOPERATORS',3);


class DilemmaGame extends NashGame
{

	public function payOff($playerNum,$nTuple)
	{
                $cooperators=0;
                $betrayers=0;
                foreach($nTuple as $playerStrategyNum)
                        if($playerStrategyNum==COOPERATE) $cooperators++;
                        else $betrayers++;

                //everybody betrays
                if($cooperators==0) return PAYOFF_EVERYBODY_BETRAYS;
                //everybody cooperates
                else if($betrayers==0) return PAYOFF_EVERYBODY_COOPERATES;
                //some cooperators and some betrayers: mixed situation
                else
                {
                        $playerStrategyNum=$nTuple[$playerNum];
                        //the cooperator loses
                        if($playerStrategyNum==COOPERATE) return PAYOFF_COOPERATE_WITH_BETRAYERS;
                        //the betrayer wins
                        else return PAYOFF_BETRAY_WITH_COOPERATORS;
                }                
	}

	public function playerCount()
	{
		return NUMBER_OF_PLAYERS; //choose whatever number you like
	}	

	public function playerStrategyCount($playerNum)
	{
                return 2; //0=cooperate, 1=betray, so strategy count=2 for all players
	}

	public function nTupleIndexesToStrategies($nTuple)
	{
		$strategies=array();
		foreach($nTuple as $playerNum=>$strategyNum)
                        if($strategyNum==COOPERATE)
                                $strategies[]=COOPERATE_REPR;
                        else $strategies[]=BETRAY_REPR;
		return $strategies;
	}
}

$dilemmaGame=new DilemmaGame();
$dilemmaGame->findSelfCounteringNTuples();
$dilemmaGame->dump();

