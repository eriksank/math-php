<?php
/**
	Nash product space implemented in PHP
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).

        For additional background information, refer to:
        http://en.wikipedia.org/wiki/Nash_equilibrium
*/

class ProductSpace
{
	/**
	 * For each player, the count of strategies (=choices) available.
	 * @var    array of integers
	 */
        var $counts=null;

	/**
	 * Constructs the product space.
	 * @param $counts array of integers
         * @return new product space object
	 */
        function __construct($counts)
        {
                $this->counts=$counts;
        }

	/**
	 * returns the number of counts; therefore the number of players
         * @return integer
	 */
        function playerCount()
        {
                return count($this->counts);
        }

	/**
	 * Calculates the total number of n-tuples in the product space.
         * @return integer
	 */
	function nTupleCount()
	{
		/*
			Example:
			if there are 4 players, with each respectively 2,3,8, and 5 strategies
			the nTupleCount is 2 * 3 * 8 * 5 = 240
		*/

		$nTupleCount=1;
		for($i=0;$i<$this->playerCount();$i++)
			$nTupleCount*=$this->counts[$i];
		return $nTupleCount;
	}

	/**
	 * The number of different strategy permutations after this player has made his choice 
	 * @param integer $playerNum; the player number 
         * @return integer
	 */
	function facultyForCount($playerNum)
	{
		/*
			I just call this function "facultyForCount"
                        because I don't know what the actual term is for it ;-)
                        and because it is pretty much the same as a faculty (n!)
                        with the terms in the product not based on permutational counts but 
                        rather on product-spatial counts ;-)

                        They will probably have another shitty name for this stuff.
                        I may or may not fix this function name, if someone tells me what the 
                        existing name is for it ;-)

				0	1	2	3
			for 	2	3	8	5 count counts 
				3*8*5	8*5	5       1 the faculty counts are
			 i.e.   120	40	5       1
		*/

		$n=$this->playerCount();
		if($playerNum>=$n-1) return 1;
		else return $this->counts[$playerNum+1]*$this->facultyForCount($playerNum+1);
	}

	/**
	 * The number of players who are stuck to their first choice for this index 
	 * @param integer $index; the index for the n-tuple in the product space 
         * @return integer
	 */
	function calcLeadingZeroes($index)
	{
		$position=0;
		$facultyForCount=$this->facultyForCount($position);
		while($facultyForCount>1 && $facultyForCount>$index)
		{
			$position++;
			$facultyForCount=$this->facultyForCount($position);
		}

		return $position;
	}

	/**
	 * Generates an n-tuple with a given number of players stuck to their first choice 
	 * @param integer $numberOfLeadingZeroes; the number of such players 
         * @return array, i.e. an n-tuple of player strategy numbers
	 */
	function initNTupleWitLeadingZeroes($numberOfLeadingZeroes)
	{
		$nTuple=array();
		for($i=0;$i<$numberOfLeadingZeroes;$i++)
			$nTuple[]=0;
		return $nTuple;
	}

	/**
	 * Generates the n-tuple that corresponds with a particular index for the product space 
	 * @param integer $index; the index going from 0 ..nTupleCount
         * @return array, i.e. an n-tuple of player strategy numbers
	 */
	function findNTupleByIndex($index)
	{
		/*
			Example:

			For 2,3,8, and 5 strategies and therefore nTupleCount 240
			We number the nTuples from 0 to 239
			0 mapped on (s1=1,s2=1,s3=1,s4=1)
			239 mapped on (s1=2,s2=3,s3=8,s4=5)

			index	s1 s2 s3 s4

			0	0   0  0  0
			1	0   0  0  1
			2	0   0  0  2
			3	0   0  0  3
			4	0   0  0  4
			5	0   0  1  0
			6	0   0  1  1
			7	0   0  1  2
			...
	
                        Solve it for index 83:

                        We keep going forward in the faculty counts until it is smaller than 83:

                        s1      s2      s3      s4
                        120     40      5       1

                        0       !       

                        index=83 will have 1 leading zero

                        83 (integer) divided by 40 = 2, remainder is 3. So, the digits are 0 2.
                        We take the remainder 3 and divide by 5 = 0, remainder = 3. So, the digits are now 0 2 0.
                        We take the remainder 3 and divide by 1 = 3, remainder = 0. So, the digits are now 0 2 0 3.

		*/	

		$numberOfLeadingZeroes=$this->calcLeadingZeroes($index);
		$nTuple=$this->initNTupleWitLeadingZeroes($numberOfLeadingZeroes);

		//find following digits

                $n=$this->playerCount();
		$remainder=$index;
                for($j=$numberOfLeadingZeroes; $j<$n; $j++)
                {
                        $facCount=$this->facultyForCount($j);
                        $quotient=(int)($remainder/$facCount);
                        $remainder=$remainder%$facCount;
                        $nTuple[]=$quotient;
                }

		return $nTuple;
        }

	/**
	 * Represents an nTuple as a string 
	 * @param $nTuple; the n-tuple to represent as string
         * @return string, string representation for the n-tuple
	 */
        static function nTupleToString($nTuple)
        {
                $buffer='';
                for($i=0;$i<count($nTuple);$i++)
                {
                        $buffer.=' '.$nTuple[$i];
                }
                return $buffer;
        }
}

