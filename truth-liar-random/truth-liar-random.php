#!/usr/bin/env php
<?php
/**
	Truth-Liar-Random
	Written by Erik Poupaert, January 2013
	Licensed under the Library General Public License (LGPL).

	This program questions three entities that are:
		Truth: always telling the truth
		Liar: always telling a lie
		Random: sometimes telling a lie, sometimes telling the truth

        For additional background information, refer to:
	http://en.wikipedia.org/wiki/The_Hardest_Logic_Puzzle_Ever
*/

/**
 * Function that always tells the truth
 * @param $realAnswer the real answer
 * @return bool, just tell you the real answer
 */
function truth($realAnswer)
{
        return $realAnswer;
}

/**
 * Function that always tells a lie
 * @param $realAnswer the real answer
 * @return bool, just tell you a lie
 */
function liar($realAnswer)
{
        return !$realAnswer;
}

/**
 * Function that sometimes tells the truth and sometimes a lie
 * @param $realAnswer the real answer
 * @return bool, either the truth or else a lie
 */
function random($realAnswer)
{
        $rnd=rand(0,1);
        if($rnd==0) return $realAnswer;
        else return !$realAnswer;        
}

/**
 * Converts booleans to something more printable than '' and '1'
 * @return string 'true' if true and 'false' if false
 */
function bool2String($bool)
{
	if($bool) return 'true'; else return 'false';
}

/**
	Players class

	A set of players of which one is Truth, one is Liar, and one is Random.
	It agrees to forward your question to any chosen player and return his answer to you.
	At any time you can tell the class who you think is Truth, Liar and Random. 
	If you got it right, the program continues executing.
	If you got it wrong, the program will crash with an error message.
*/
class Players
{
	private $players=null;

	/**
	 * Creates a new set of players
	 * @return object containing three players
	 */
	function __construct()
	{
		$this->players=array('truth','liar','random');
		shuffle($this->players);
	}

	/**
	 * Asks a contradiction to a chosen player
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @return bool, player's answer to the contradiction
	 */
	function askContradiction($playerNum)
	{
		$player=$this->players[$playerNum];
		$answer=$player(false);
		return $answer;
	}

	/**
	 * Accepts an array with identities. Crashes if these identities don't match.
	 * @param $identities an array with three identities as string. 
         *                    must be 'truth','liar', and 'random' in any chosen order.
	 */
	function assertIdentities($identities)
	{
		foreach($identities as $playerNum=>$identity)
			assert($this->players[$playerNum]==$identity);
	}
}

/**
	Questioner class

	The class follows a particular strategy in asking contradictions to the players,
	until it knows who exactly is truth, liar, and random.
*/
class Questioner
{
	var $players=null;
	var $playerIdentities=null;

	/**
	 * Creates a new questioner object
	 * @return object representing a questioner
	 */
	function __construct()
	{
		$this->players=new Players();
		$this->playerIdentities=array('unknown','unknown','unknown');
	}

	/**
	 * Translates a player number into a player index
	 * @param $playerNum player number; must be 0,1, or 2.
	 * @return string, index for the player
	 */
	function playerIndex($playerNum)
	{
		switch($playerNum)
		{
			case '0': return 'A'; 
			case '1': return 'B'; 
			case '2': return 'C'; 
		}
		throw new Exception("Invalid player number $playerNum");
	}

	/**
	 * Checks if the game has been resolved already.
	 * @return true, if the game has been resolved.
	 */
	function resolved()
	{
		foreach($this->playerIdentities as $playerIdentity)
			if(self::isIncompleteIdentity($playerIdentity)) return false;
		return true;
	}

	/**
	 * When we unmask Random, we can unrandomize truthOrRandom and liarOrRandom to just truth and liar
	 * @param $playerNum player number unmasked as being Random; must be 0,1, or 2.
	 */
	function unRandomize($playerNum)
	{
		$this->setPlayerIdentity($playerNum,'random');
		$this->replaceIdentity('truthOrRandom','truth');
		$this->replaceIdentity('liarOrRandom','liar');
	}

	/**
	 * Sets the identity for a player
	 * @param $playerNum player number; must be 0,1, or 2.
	 * @param $identity must be 'truth', 'liar' or 'random'.
	 */
	function setPlayerIdentity($playerNum,$identity)
	{
		$this->playerIdentities[$playerNum]=$identity;
		$playerIndex=$this->playerIndex($playerNum);
		echo " +setting identity of player '$playerIndex' to '$identity'\n";
	}

	/**
	 * Sets the identity for a player to truthOrRandom
	 * @param $playerNum player number; must be 0,1, or 2.
	 */
	function setPlayerIdentityTruthOrRandom($playerNum)
	{
		$this->setPlayerIdentity($playerNum,'truthOrRandom');
	}

	/**
	 * Sets the identity for a player to liarOrRandom
	 * @param $playerNum player number; must be 0,1, or 2.
	 */
	function setPlayerIdentityLiarOrRandom($playerNum)
	{
		$this->setPlayerIdentity($playerNum,'liarOrRandom');
	}

	function outputIneffectiveness($playerNum)
	{
		echo " +question ineffective\n";
	}

	/**
		(state - answer) combinations ==> action
	 */
	var $handlers=array(
		 'unknown-false' => 'setPlayerIdentityTruthOrRandom'
		,'unknown-true' => 'setPlayerIdentityLiarOrRandom'
		,'truthOrRandom-true' => 'unRandomize'
		,'truthOrRandom-false' => 'outputIneffectiveness'
		,'liarOrRandom-true' => 'outputIneffectiveness'
		,'liarOrRandom-false' => 'unRandomize'
	);

	/**
	 * Questions a player
	 * @param $playerNum player number; must be 0,1, or 2.
	 * @param $playerIdentity the player's current identity.
	 */
	function questionPlayer($playerNum,$playerIdentity)
	{
	        $answer=$this->players->askContradiction($playerNum);
	        //output answer with current state for player
	        $answerString=bool2String($answer);
	        $playerIndex=$this->playerIndex($playerNum);
	        echo "Asked contradiction to player '$playerIndex' ".
		        "with identity '$playerIdentity', answer is '$answerString'\n";
	        //process action for identity-answer combination
	        $handler=$this->handlers[$playerIdentity.'-'.bool2String($answer)];
	        $this->$handler($playerNum);
        }

	/**
	 * Questioning round.
	 */
	function doQuestioningRound()
	{
		echo "\n== NEW QUESTIONING ROUND ==\n\n";

		foreach($this->playerIdentities as $playerNum=>$playerIdentity)
			if(!$this->resolved())
                                if(!self::isFinalIdentity($playerIdentity))
                                        $this->questionPlayer($playerNum,$playerIdentity);

		echo "\nSTATE: ".$this->state()."\n";
	}

	/**
	 * @param $identity
	 * @return true, if identity is incomplete
	 */
        static function isIncompleteIdentity($identity)
        {
                if(in_array($identity, array('unknown','liarOrRandom','truthOrRandom'))) return true;
                return false;
        }

	/**
	 * @param $identity
	 * @return true, if identity is final
	 */
        static function isFinalIdentity($identity)
        {
                if(in_array($identity, array('truth','liar','random'))) return true;
                return false;
        }

	/**
	 * @param $identity
	 * @return int, number of players with a particular identity
	 */
        function countIdentities($identity)
        {
                $count=0;
		foreach($this->playerIdentities as $playerIdentity)
                        if($playerIdentity==$identity) $count++;
                return $count;
        }

	/**
	 * @param $identity1 identity to replace
	 * @param $identity2 identity to replace identity1 with
	 */
        function replaceIdentity($identity1,$identity2)
        {
		foreach($this->playerIdentities as $playerNum=>$playerIdentity)
                        if($playerIdentity==$identity1)
				$this->setPlayerIdentity($playerNum,$identity2);
        }

	/**
         * In the first round, we will always be able to detect either liar or truth
	 */
        function resolveFirstRound()
        {
                if($this->countIdentities('truthOrRandom')==1) $this->replaceIdentity('truthOrRandom','truth');
                if($this->countIdentities('liarOrRandom')==1) $this->replaceIdentity('liarOrRandom','liar');
        }

	/**
	 * Main function: executes the resolution strategy.
	 */
	function resolve()
	{
                //first round
		$this->doQuestioningRound();
		$this->resolveFirstRound();

                //follow-on rounds
		while(!$this->resolved())
			$this->doQuestioningRound();

                //check if the identities have correctly been detected
		$this->players->assertIdentities($this->playerIdentities);
	}

	/**
	 * Shows questioners state
	 * @return string representing the current state of the player identities
	 */
	function state()
	{
		return '( '.$this->playerIdentities[0].' '.$this->playerIdentities[1].' '.$this->playerIdentities[2].' )';
	}
}

$questioner=new Questioner();
$questioner->resolve();

