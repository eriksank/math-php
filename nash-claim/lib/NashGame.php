<?php
/**
	Nash Game implemented in PHP
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).

        For additional background information, refer to:
        http://en.wikipedia.org/wiki/Nash_equilibrium
*/
require('ProductSpace.php');

/**
	nTuple class

        static methods available for an n-tuple
        an n-tuple is an array of player strategy numbers (=choices) with n the number of players
*/
class nTuple
{
	/**
	 * Creates a copy of an n-tuple.
	 * @param array $nTuple The n-tuple to copy
         * @return a copy of the $nTuple
	 */
        static function copy($nTuple)
        {
                $copy=array();
                foreach($nTuple as $playerStrategyNum)
                {
                        $copy[]=$playerStrategyNum;
                }
                return $copy;
        }

	/**
	 * Checks if two n-tuples contain the same set of numbers.
	 * @param array $nTuple1 The first n-tuple to compare
	 * @param array $nTuple2 The second n-tuple to compare
         * @return true, if both n-tuples are equal
	 */
        static function equal($nTuple1,$nTuple2)
        {
                for($i=0;$i<count($nTuple1);$i++)
                        if($nTuple1[$i]!=$nTuple2[$i]) return false;
                return true;
        }

	/**
	 * Creates a string representation of an n-tuple.
	 * @param array $nTuple The n-tuple to represent as a string
         * @return string, representing the n-tuple
	 */
	static function toString($nTuple)
	{
		$buffer='( ';
		foreach($nTuple as $item)
		{
			$buffer.=$item.' ';
		}
		$buffer.=' )';
		return $buffer;
	}
}

/**
	nTuples class

        collection of static methods available for a collection of n-tuples
*/
class nTuples
{
	/**
	 * Checks if a collection of n-tuples contains a particular n-tuple.
	 * @param array $nTuples An array of n-tuples
	 * @param array $nTuple An n-tuple
         * @return true, if the collection of n-tuples contains the n-tuple
	 */
        static function contains($nTuples,$nTuple)
        {
                foreach($nTuples as $nTupleLocal)
                        if(nTuple::equal($nTuple,$nTupleLocal))
                                return true;
                return false;
        }

	/**
	 * Returns an array with the strategy numbers for given player, from a collection of n-tuples
         * e.g. n-tuples={(0,3),(2,1)} --> for playerNum=0, the return value is {0,2}
         *                             --> for playerNum=1, the return value is {3,1}
	 * @param array $nTuples An array of n-tuples
	 * @param integer $playerNum The player number
         * @return array of strategies for the playerNum supplied
	 */
        static function playerStrategies($nTuples,$playerNum)
        {
                $playerStrategies=array();
                foreach($nTuples as $nTuple)
                        $playerStrategies[]=$nTuple[$playerNum];
                return $playerStrategies;
        }

	/**
	 * Creates an array of n-tuple string representations.
	 * @param array $nTuples The collection of n-tuples to represent as a string
         * @return array of string, representing each an n-tuple
	 */
	static function toStringArray($nTuples)
	{
		$strings=array();
		foreach($nTuples as $i=>$nTuple)
			$strings[]=nTuple::toString($nTuple);
		return $strings;
	}

	/**
	 * Checks if two n-tuple collections are equal. 
	 * This true, if the one collection contains the items of the other, and the other way around,
	 * regardless of the order in which the items were stored
	 * @param array $nTuples1 The first collection of n-tuples to compare
	 * @param array $nTuples2 The second collections of n-tuples to compare
         * @return true, if both n-tuples are equal
	 */
        static function equal($nTuples1,$nTuples2)
        {
		$nTuples1StringArray=self::toStringArray($nTuples1);
		$nTuples2StringArray=self::toStringArray($nTuples2);

		//check if all $nTuple1 are in $nTuples2
		foreach($nTuples1StringArray as $nTupleString)
			if(!in_array($nTupleString,$nTuples2StringArray))
				return false;

		//check if all $nTuple2 are in $nTuples1
		foreach($nTuples2StringArray as $nTupleString)
			if(!in_array($nTupleString,$nTuples1StringArray))
				return false;

                return true;
        }

	/**
	 * Creates a string representation for a collection of n-tuples.
	 * @param array $nTuples The collection of n-tuples to represent as a string
         * @return string, representing the collection n-tuples
	 */
	static function toString($nTuples)
	{
		$buffer=nTuple::toString($nTuples[0]);
		foreach($nTuples as $i=>$nTuple)
			if($i>0) $buffer.=' '.nTuple::toString($nTuple);
		return $buffer;
	}
}

/**
	Payoff class

        This class combines the payoff for a player with the n-tuple for that payoff
*/
class Payoff
{
	var $payOff=null;
	var $nTuple=null;

	/**
	 * Constructs a payoff object.
	 * @param integer $payOff The player payoff for the n-tuple
         *       The payOff must be of some ordered kind of type
         *       such as integers or floats or even strings
         *       It must be possible to evaluate the expression $payOff1 < $payOff2 in
         *       terms of TRUE or FALSE.
	 * @param array $nTuple The n-tuple
         * @return Payoff object
	 */
	function __construct($payOff,$nTuple)
	{
		$this->payOff=$payOff;
		$this->nTuple=$nTuple;
	}

	/**
	 * Represents the payoff object as a string.
         * @return string
	 */
	function toString()
	{
		return '<'.nTuple::toString($this->nTuple).' ['.$this->payOff.']>';
	}
}

/**
	Payoffs class

        This class contains a collection of player payoffs.
*/
class PayOffs
{
	var $items=null;

	/**
	 * Constructs a payoffs object.
         * @return Payoffs object
	 */
	function __construct()
	{
		$this->items=array();
	}

	/**
	 * Finds the highest payoff in a collection of payoffs.
         * @return float (or integer or whatever), the highest Payoff value
	 */
        function highestPayOff()
        {
                $highest=$this->items[0]->payOff;
                foreach($this->items as $item)
                        if($item->payOff>$highest)
                                $highest=$item->payOff;
                return $highest;
        }

	/**
	 * Finds the subset of n-tuples that have a particular payoff.
	 * @param integer $payOff The payoff that each n-tuple must have
         * @return array of n-tuples
	 */
        function nTuplesForPayOff($payOff)
        {
                $nTuples=array();
                foreach($this->items as $item)
                        if($item->payOff==$payOff)
                                $nTuples[]=$item->nTuple;
                return $nTuples;
        }

	/**
	 * Finds the subset of n-tuples that all have the highest payoff.
         * @return array of n-tuples
	 */
	function nTuplesForHighestPayOff()
	{
		return $this->nTuplesForPayOff($this->highestPayOff());
	}

	/**
	 * Converts the collection of n-tuples to a string representation.
         * @return string, representing the n-tuples
	 */
	function toString()
	{
		$buffer=$this->items[0]->toString();
		foreach($this->items as $i=>$payOff)
			if($i>0) $buffer.=' '.$payOff->toString();
		return $buffer;
	}

}

/**
	NashGame class

        This class carries out the computations required to locate the
        self-countering n-tuple, that is, the notorious Nash Equilibrium.
        It does a full/brute-force enumeration of the product space in n-tuples.
        For each n-tuple, it computes the the player's best alternative strategies,
        creates a sub-product space from these strategies, and enumerates
        this sub-product space to obtain the countering n-tuples. If the n-tuple
        is member of its own countering n-tuples, it is a self-countering n-tuple
        and therefore a Nash Equilibrium.
*/
abstract class NashGame
{
	var $debug=false;

	/**
	 * The main product space
	 * @var  $productSpace
	 */
        var $productSpace=null;
	/**
	 * The self-countering n-tuples located
	 * @var  $selfCounteringNTuples
	 */
        var $selfCounteringNTuples=null;

	/**
	 * The real game must be able to tell us how many players are involved.
         * @return integer, the number of players
	 */
	abstract function playerCount();
	/**
	 * The real game must be able to tell us for any player how many strategies (choices) he has.
	 * @param integer $playerNum The player's number, going from 0..n-1
         * @return integer, the number of strategies (=choices) for the player
	 */
	abstract function playerStrategyCount($playerNum);
	/**
	 * The real game must be able to tell us what the payoff is for a player given the choices of all players.
	 * @param integer $playerNum The player's number for whom we need the payoff, going from 0..n-1
	 * @param array $nTuple The strategies (=choices) made by all players
         * @return integer, float or anything that has some kind of order ($a<$b) representing a payoff
	 */
	abstract function payOff($playerNum,$nTuple);

	/**
	 * Initializes the product space.
         * Retrieves player count and for each player the strategy count and supplies this to the product
         * space, which will assist us in do a full enumeration of n-tuples
	 */
        function initProductSpace()
        {
                $counts=array();
                for($i=0;$i<$this->playerCount();$i++)
                {
                        $counts[]=$this->playerStrategyCount($i);
                }
                $this->productSpace=new ProductSpace($counts);
        }

	/**
	 * We create a new payoff object, containing payoff value and n-tuple
         * for an alternative player strategy (=choice)
	 * @param integer $playerNum The player's number for whom we are computing the payoff, going from 0..n-1
	 * @param array $nTuple The strategies (=choices) made by all players
	 * @param int $alternativeStrategyNum The number of the alternative strategy (=choice)
         * @return integer, float or anything that has some kind of order ($a<$b) representing a payoff
	 */
	function playerPayOff($playerNum,$nTuple,$alternativeStrategyNum)
	{
                //copy to avoid overwriting the existing n-tuple
                $nTupleAlternative=nTuple::copy($nTuple);
                $nTupleAlternative[$playerNum]=$alternativeStrategyNum;
                //request the real game to inform us about what the payoff value is
                $payOff=$this->payOff($playerNum,$nTupleAlternative);
		return new PayOff($payOff,$nTupleAlternative);
	}

	/**
	 * We create the full list of payoffs for one player, given a particular n-tuple
	 * @param integer $playerNum The player's number for whom we are computing the payoff, going from 0..n-1
	 * @param array $nTuple The strategies (=choices) made by all players
         * @return PayOffs object
	 */
        function playerPayOffs($playerNum,$nTuple)
        {
                $playerPayoffs=new Payoffs();
                for($strategyNum=0;$strategyNum<$this->playerStrategyCount($playerNum);$strategyNum++)
	               	$playerPayoffs->items[]=$this->playerPayOff($playerNum,$nTuple,$strategyNum);
                return $playerPayoffs;
        }

	/**
	 * Given a particular n-tuple, we compute for one player the list of equivalent best strategies
         * all yielding the same maximum payoff
	 * @param integer $playerNum The player's number for whom we are computing the payoff, going from 0..n-1
	 * @param array $nTuple The strategies (=choices) made by all players
         * @return array of equivalent best strategies (=choices) for the player
	 */
	function bestPlayerStrategies($playerNum,$nTuple)
	{
                $playerPayOffs=$this->playerPayOffs($playerNum,$nTuple);
		if($this->debug) echo "payoffs for player $playerNum: ".$playerPayOffs->toString()."\n";
                $counteringNTuplesForPlayer=$playerPayOffs->nTuplesForHighestPayOff();
                $bestPlayerStrategies=nTuples::playerStrategies($counteringNTuplesForPlayer,$playerNum);
		if($this->debug) echo "highest: ".nTuple::toString($bestPlayerStrategies)."\n";                
                return $bestPlayerStrategies;
	}

	/**
	 * Given a particular n-tuple, we compute for all players the list of equivalent best strategies
         * all yielding per player the same maximum payoff
	 * @param array $nTuple The strategies (=choices) made by all players
         * @return array of equivalent best strategies (=choices) for the players, one item per player containing
         * what is best for that particular player
	 */
	function bestPlayersStrategies($nTuple)
	{
                $bestPlayersStrategies=array();
                for($playerNum=0;$playerNum<$this->playerCount();$playerNum++)
                {
                        $bestPlayerStrategies=$this->bestPlayerStrategies($playerNum,$nTuple);
                        $bestPlayersStrategies[]=$bestPlayerStrategies;
                }
                return $bestPlayersStrategies;
	}

	/**
	 * The best strategies (=choices) per player span their own sub-product space
         * Their complete enumeration is the list of countering n-tuples for a particular n-tuple
	 * @param array $bestPlayersStrategies one array per player with his alternative, equivalent best choices
         * @return array enumerating all n-tuples for the sub-product space of best strategies
	 */
        function counteringNTuples($bestPlayersStrategies)
        {
                $counts=array();
                for($i=0;$i<count($bestPlayersStrategies);$i++)
                        $counts[]=count($bestPlayersStrategies[$i]);

                $bestNTuples=array();
                $subProductSpace=new ProductSpace($counts);
                for($i=0;$i<$subProductSpace->nTupleCount();$i++)
                {
                        $nTuple=$subProductSpace->findNTupleByIndex($i);
                        $bestNTuple=array();
                        foreach($nTuple as $playerNum=>$bestStrategyIndex)
                                $bestNTuple[]=$bestPlayersStrategies[$playerNum][$bestStrategyIndex];
                        $bestNTuples[]=$bestNTuple;
                }
                return $bestNTuples;
        }

	/**
	 * We enumerate the complete product space and compute the countering n-tuples per n-tuple
         * If the countering n-tuples contain the n-tuple itself, it is a self-countering n-tuple
         * aka, a Nash equilibrium.
	 */

	function findSelfCounteringNTuples()
	{
                $this->initProductSpace();
                $this->findSelfCounteringNTuplesInSegmentOfProductSpace(0,$this->productSpace->nTupleCount());

                //The Popper-compliant claim for a Nash Equilibrium:
                //Feel free to crash and burn here, if not one self-countering n-tuple was found.
                assert(count($this->selfCounteringNTuples)>0);
        }

	/**
	 * We enumerate a segment of the product space and compute the countering n-tuples per n-tuple
	 * This support for segmentation should allow for distributing the search across a large number of cores
         * If the countering n-tuples contain the n-tuple itself, it is a self-countering n-tuple
         * aka, a Nash equilibrium.
	 */
	function findSelfCounteringNTuplesInSegmentOfProductSpace($indexStart,$indexEnd)
	{
                if($this->productSpace==null) $this->initProductSpace();
                $this->selfCounteringNTuples=array();

		if($this->debug)
			echo "Possible strategies: ".$this->productSpace->nTupleCount()."\n";

                for($i=$indexStart;$i<$indexEnd;$i++)
                {
                        $nTuple=$this->productSpace->findNTupleByIndex($i);

			if($this->debug)
	                        echo "strategy $i) ".ProductSpace::nTupleToString($nTuple)."\n";

                        $bestPlayersStrategies=$this->bestPlayersStrategies($nTuple);
                        $counteringNTuples=$this->counteringNTuples($bestPlayersStrategies);

                        if($this->debug) 
                                echo "countering nTuples:".nTuples::toString($counteringNTuples)."\n";

                        if(nTuples::contains($counteringNTuples,$nTuple))  
                        {
                                $this->selfCounteringNTuples[]=$nTuple;
                                if($this->debug) echo "==>self-countering\n";
                        }
                        else
                        {
                                if($this->debug) echo "==>not self-countering\n";
                        }
                }
	}

	/**
         * Non-mandatory abstract function retrieving the strategy for a strategy index number
	 * @param integer $playerNum The player's number going from 0..n-1
	 * @param int $strategyNum The number of the strategy (=choice)
         * @return string: the strategy for the strategyNum

	 */
	public function playerStrategy($playerNum,$strategyNum)
	{
		//this is the answer if the child class does not provide an answer
		return "unknown"; 
	}

	/**
         * Converts n-tuple indexes to the actual strategies.
	 * @param array $nTuple The strategies (=choices) made by all players as indexes
         * @return array of actual strategies
	 */
	public function nTupleIndexesToStrategies($nTuple)
	{
		$strategies=array();
		foreach($nTuple as $playerNum=>$strategyNum)
			$strategies[]=$this->playerStrategy($playerNum,$strategyNum);
		return $strategies;
	}

	/**
         * Non-mandatory abstract function retrieving the strategy number for a strategy
	 * @param integer $playerNum The player's number going from 0..n-1
	 * @param int $strategy The strategy (=choice)
         * @return integer: the strategyNum for the strategy

	 */
	public function playerStrategyNum($playerNum,$strategy)
	{
		//this is the answer if the child class does not provide an answer
		return -1; 
	}

	/**
         * Converts n-tuple of strategies to indexes
	 * @param array $strategies The strategies (=choices) made by all players
         * @return n-tuple
	 */
	public function nTupleStrategiesToIndexes($strategies)
	{
		$nTuple=array();
		foreach($strategies as $playerNum=>$strategy)
			$nTuple[]=$this->playerStrategyNum($playerNum,$strategy);
		return $nTuple;
	}

	/**
         * Converts n-tuples of strategies to n-tuples of indexes
	 * @param array $strategiesArray The collecton of strategies tuples (=choices) made by all players
         * @return array of n-tuples
	 */
	public function nTupleStrategiesArrayToIndexes($strategiesArray)
	{
		$nTuples=array();
		foreach($strategiesArray as $strategies)
			$nTuples[]=$this->nTupleStrategiesToIndexes($strategies);
		return $nTuples;
	}

	/**
	 * For output purposes, we lookup the payoffs for an n-tuple
	 * @param array $nTuple The strategies (=choices) made by all players
         * @return array of player payoffs for the n-tuple
	 */
        function playersPayOffs($nTuple)
        {
                $payOffs=array();
                foreach($nTuple as $playerNum=>$strategyNum)
			$payOffs[]=$this->payOff($playerNum,$nTuple);
                return $payOffs;
        }

	function assertSelfCounteringNTuples($strategyNTuples)
	{
		$nTuples=$this->nTupleStrategiesArrayToIndexes($strategyNTuples);
		assert(nTuples::equal($this->selfCounteringNTuples,$nTuples));	
	}

	/**
         * Outputs n-tuple count and self-countering n-tuples with payoffs to stdout.
	 */
        public function dump()
        {
                $nTupleCount=$this->productSpace->nTupleCount();
                $eqCount=count($this->selfCounteringNTuples);
                echo "number of n-tuples: $nTupleCount\n";
                echo "number of self-countering n-tuples: $eqCount\n";
                foreach($this->selfCounteringNTuples as $nTuple)
                {
	                $playerStrategies=$this->nTupleIndexesToStrategies($nTuple);
	                $nTupleString=nTuple::toString($playerStrategies);
			$payOffs=$this->playersPayOffs($nTuple);
			$payOffsString=nTuple::toString($payOffs);
	                echo "$nTupleString payoffs: $payOffsString\n";
                }
        }

}

