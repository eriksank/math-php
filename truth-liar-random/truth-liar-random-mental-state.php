#!/usr/bin/env php
<?php
/**
	Truth-Liar-Random-MentalState
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
* Must be in the enclosing scope of both the random() function and the players class
*
*/
$mentalState=null;

function randomizeMentalState()
{
        global $mentalState;
        $rnd=rand(0,1);
        if($rnd==0) $mentalState=false;
        else $mentalState=true;
}

/**
 * Function that sometimes tells the truth and sometimes a lie
 * @param $realAnswer the real answer
 * @return bool, either the truth or else a lie
 */
function random($realAnswer)
{
        global $mentalState;
        
        if($mentalState) $answer=$realAnswer;
        else $answer=!$realAnswer;
        randomizeMentalState();
        return $answer;
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
	var $players=null;

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
	 * Asks a player if he is Random: externally visible
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @return bool, player's answer
	 */
	public function areYouRandom($playerNum)
	{
                $answer=$this->isThisPlayerRandom($playerNum,$playerNum);
		return $answer;
	}

	/**
	 * Asks a player if he is Random: true answer, not externally visible
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @return bool, player's answer
	 */
	private function areYouRandomInternal($playerNum)
	{
		$player=$this->players[$playerNum];
                if($player=='random') $realAnswer=true; else $realAnswer=false;
		return $realAnswer;
	}

	/**
	 * Asks a player if another player is Random: externally visible; may be false answer
	 * @param $playerNumTo the index for the player to ask the question to: 0,1, or 2.
	 * @param $playerNumAbout the index for the player to ask the question about: 0,1, or 2.
	 * @return bool, the answer
	 */
        public function isThisPlayerRandom($playerNumTo,$playerNumAbout)
        {
		$playerTo=$this->players[$playerNumTo];
		$playerAbout=$this->players[$playerNumAbout];
                if($playerAbout=='random') $realAnswer=true; else $realAnswer=false;
                $answer=$playerTo($realAnswer);
                return $answer;
        }

	/**
	 * Asks a player if he is about to tell the truth; not externally visible; real answer
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @return bool, the answer
	 */
	private function areYouAboutToTellTheTruth($playerNum)
	{
                global $mentalState;
		$player=$this->players[$playerNum];
                switch($player)
                {
                        case 'random': $realAnswer=$mentalState; break;
                        case 'truth': $realAnswer=true; break;
                        case 'liar': $realAnswer=false; break;
                }
                return $realAnswer;
        }

	/**
	 * Asks a player if he is about to tell a lie; not externally visible; real answer
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @return bool, the answer
	 */        
	private function areYouAboutToTellALie($playerNum)
	{
                global $mentalState;
		$player=$this->players[$playerNum];
                switch($player)
                {
                        case 'random': $realAnswer=!$mentalState; break;
                        case 'truth': $realAnswer=false; break;
                        case 'liar': $realAnswer=true; break;
                }
                return $realAnswer;
        }

	/**
	 * Asks a player if he random and about to tell the truth; externally visible; could be false answer
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @return bool, the answer
	 */        
	function areYouRandomAndAreYouAboutToTellTheTruth($playerNum)
	{
		$player=$this->players[$playerNum];
                $realAnswer=$this->areYouRandomInternal($playerNum) && $this->areYouAboutToTellTheTruth($playerNum);
                $answer=$player($realAnswer);
		return $answer;              
        }

	/**
	 * Asks a player if he random and about to tell a lie; externally visible; could be false answer
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @return bool, the answer
	 */        
	function areYouRandomAndAreYouAboutToTellALie($playerNum)
	{
		$player=$this->players[$playerNum];
                $realAnswer=$this->areYouRandomInternal($playerNum) && $this->areYouAboutToTellALie($playerNum);
                $answer=$player($realAnswer);
		return $answer;              
        }

	/**
	 * Accepts an array with identities. Crashes if these identities don't match.
	 * @param $identities an array with three identities as string. Must be 'truth','liar', and 'random' in any chosen order.
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
                randomizeMentalState();
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
	 * Outputs the fact that a question was asked to a player and an answer was given by the player
	 * @param $playerNum the index for the player: 0,1, or 2.
	 * @param $question the question asked.
	 * @param $answer the answer given.
	 */        
        function outputQA($playerNum,$question,$answer)
        {
                $playerIdentity=$this->playerIdentities[$playerNum];
	        $playerIndex=$this->playerIndex($playerNum);
	        $answerString=bool2String($answer);
	        echo "Asked '$question' to player '$playerIndex' ".
		        "with identity '$playerIdentity', answer is '$answerString'\n";
        }

	/**
	 * Questions a player whether he is random and telling the truth
	 * @param $playerNum player number; must be 0,1, or 2.
	 * @return bool, the answer
	 */
	function areYouRandomAndAreYouAboutToTellTheTruth($playerNum)
	{
                $answer=$this->players->areYouRandomAndAreYouAboutToTellTheTruth($playerNum);
                $this->outputQA($playerNum,__FUNCTION__,$answer);
                return $answer;
        }

	/**
	 * Questions a player whether he is random and telling a lie
	 * @param $playerNum player number; must be 0,1, or 2.
	 * @return bool, the answer
	 */
	function areYouRandomAndAreYouAboutToTellALie($playerNum)
	{
                $answer=$this->players->areYouRandomAndAreYouAboutToTellALie($playerNum);
                $this->outputQA($playerNum,__FUNCTION__,$answer);
                return $answer;
        }

        var $player_A_Identity=array(
	          'false-false' => 'truth'
	        , 'true-true' => 'liar'
	        , 'true-false' => 'random');

	/**
	 * Questions the first player
	 */
        function firstQuestion()
        {
                $answer1truth=$this->areYouRandomAndAreYouAboutToTellTheTruth(0);
                $answer1lie=$this->areYouRandomAndAreYouAboutToTellALie(0);
                $player_A_identity=$this->player_A_Identity[bool2string($answer1truth).'-'.bool2string($answer1lie)];
                $this->setPlayerIdentity(0,$player_A_identity);
                return $player_A_identity;
        }

        var $firstAnswerHandlers=array(
	          'truth' => 'player_A_isTruth'
	        , 'liar' => 'player_A_isLiar'
	        , 'random' => 'player_A_isRandom');

	/**
	 * Questions a player whether another player is Random
	 * @param $playerNumTo the player answering the question; must be 0,1, or 2.
	 * @param $playerNumAbout the player whom the question is about; must be 0,1, or 2.
	 * @return bool, the answer
	 */
        function isThisPlayerRandom($playerNumTo,$playerNumAbout)
        {
	        $playerIndexAbout=$this->playerIndex($playerNumAbout);
                $answer=$this->players->isThisPlayerRandom($playerNumTo,$playerNumAbout);
                $this->outputQA($playerNumTo,__FUNCTION__." about player $playerIndexAbout",$answer);
                return $answer;
        }

	/**
	 * Asks player B about player A
	 * @param $willHeTelltheTruth yes, if player A is Truth; no if he is Liar.
	 */
        function ask_player_A_about_player_B($willHeTelltheTruth)
        {
                if($willHeTelltheTruth==true) $otherPlayer='liar';
                else $otherPlayer='truth';

                $answer=$this->isThisPlayerRandom(0,1);
                if($answer==$willHeTelltheTruth)
                {
                        $this->setPlayerIdentity(1,'random');
                        $this->setPlayerIdentity(2,$otherPlayer);
                }
                else
                {
                        $this->setPlayerIdentity(1,$otherPlayer);
                        $this->setPlayerIdentity(2,'random');
                }                
        }

	/**
	 * Handles the case in which player A is Truth
	 */
        function player_A_isTruth()
        {
                $this->ask_player_A_about_player_B(true);
        }

	/**
	 * Handles the case in which player A is Liar
	 */
        function player_A_isLiar()
        {
                $this->ask_player_A_about_player_B(false);
        }

	/**
	 * Handles the case in which player A is Random
	 */
        function player_A_isRandom()
        {
                //ask question to player B
                $answer=$this->areYouRandom(1);
                if($answer==true)
                {
                        $this->setPlayerIdentity(1,'liar');
                        $this->setPlayerIdentity(2,'truth');
                }
                else
                {
                        $this->setPlayerIdentity(1,'truth');
                        $this->setPlayerIdentity(2,'liar');
                }
        }

	/**
	 * Questions a player whether he is Random
	 * @param $playerNum the player answering the question; must be 0,1, or 2.
	 * @return bool, the answer
	 */
        function areYouRandom($playerNum)
        {
                $answer=$this->players->areYouRandom($playerNum);
                $this->outputQA($playerNum,__FUNCTION__,$answer);
                return $answer;
        }
                        
	/**
	 * Main function: executes the resolution strategy.
	 */
	function resolve()
	{
                $player_A_identity=$this->firstQuestion();
		echo " STATE:".$this->state()."\n";
                $handler=$this->firstAnswerHandlers[$player_A_identity];
                $this->$handler();
		echo " STATE:".$this->state()."\n";
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

