PHP library for John F. Nash's 'Equilibrium points in n-person games', Mathematics p.48-49, vol 36, 1950
========================================================================================================

1. Installation
---------------

Copy the folder to your home folder.

Invoke the tests with:

        cd test-games
        ./test-all.php


2. "n" means 2,3,4,5 or more
----------------------------
Von Neumann had already proposed a simplistic solution for 2-player games by using his simpler minimax strategy -- before John Nash came with [his more general solution](http://web.mit.edu/linguistics/events/iap07/Nash-Eqm.pdf).

I originally thought that the reason why none of the mathematics literature discusses real Nash equilibria showing 3-player or 4-player games, was because they just wanted to use simple examples for didactic purposes. So, I did not pay further attention to the problem, until I discovered this paper from 2005:

[Three-Player Games Are Hard](http://www.eecs.berkeley.edu/~christos/papers/3players.pdf)

The number of computations in the algorithm in use in this library varies with the n-tuple count, which is approximately m^n, with m the number of strategies when all players can use the same strategies and with n the number of players. Three-player games take m^3 computations to exhaustively search the product space. That is not particularly hard.

I have finally managed to locate a similar program that computes Nash equilibria: [www.gambit-project.org](http://www.gambit-project.org). The software also handles 3+ player games.

In addition to locating the equilibrium points, Gambit also seems to deal with successive player moves. One strategy then consists of several moves. The equilibrium computation itself is not affected, but it does require a preliminary step to compute the strategies, before computing the equilibrium itself. I still need to investigate this further, because the original Nash specs do not particularly elaborate on this.

They write in the program documentation that "Analyzing large games may become infeasible surprisingly quickly". Since the algorithm lends itself very well to parallel computation and to subdividing the product-space search amongst a large number of CPUs, however, it should be possible to perform computations in a cloud setting with hundreds or even thousands of cores. This should allow for finding equilibria in relatively large games. If Gambit implements computationally more efficient methods, however, it could still end first. It could mean that it is possible to skip n-tuples in the search. If this is really the case, I should probably skip them too ;-) Preferably without affecting distributability of the search algorithm. I have not yet compared performance. 

Gambit does not seem to offer the possibility to split up the product space for the purpose of parallel computation. I have added this feature to my own program. A full enumeration runs between `0` and `$this->productSpace->nTupleCount()-1`. It is perfectly possible to subdivide the search, for example, into 100 sub-searches and distribute each individual search across 100 different cores that will each search independently from each other: 

        $game->findSelfCounteringNTuplesInSegmentOfProductSpace($indexStart,$indexEnd)

Gambit also seems to insist on payoff tables, while payoff functions are much more flexible. For example, it may be possible to compute a 10-player prisoner's dilemma with Gambit, but it will require specifying a table with 2^10=1024 rows.

There is a repository of games included in the Gambit sources. I may be able to use it to verify my own program. I still need to investigate this possibility.

For the sake of the argument, here is a 5-player game equilibrium computation with payoff function (and not a payoff table):

	var $playersStrategies=array(
				 array(5,3) //playerNum=0, count=2
				,array(5,3,9) //playerNum=1, count=3
				,array(5,3,9,10,17,8,4,11) //playerNum=2, count=8
				,array(5,3,8,4,11) //playerNum=3, count=4
				,array(11,9,4,8) //playerNum=3, count=4
				);

	public function payOff($playerNum,$nTuple)
	{
                /* silly ranking based on most different from the average
                   aka: largest deviation from the mean */

                $sum=0;
                foreach($nTuple as $playerNumLocal=>$strategyNum)
                        $sum+=$this->playersStrategies[$playerNumLocal][$nTuple[$playerNumLocal]];

                $average=$sum/count($nTuple);
                $playerStrategy=$this->playersStrategies[$playerNum][$nTuple[$playerNum]];
                $deviation=sqrt(pow($average-$playerStrategy,2));
                return $deviation;
	}

The solution is:

        number of n-tuples: 960
        number of self-countering n-tuples: 2
        ( 3 3 17 3 11  )
        ( 3 3 17 11 4  )


It may not be readily possible to verify the equilibrium points for this game by comparing this program's output to the output produced by Gambit, because Gambit does not allow for payoff functions but only for payoff tables. I am working on other things at the moment, but I hope to revisit this library program soon, time permitting. Nash's n-person game theory is definitely in the top five of my favourite mathematical theories.

For particular payoff functions, it could be possible to avoid a brute-force enumeration and engage in a directional search. However, attempting to avoid a full enumeration is beyond the scope of this library, since such attempts are too related to the choice of payoff function. It may also be possible under special circumstances to skip entire segments in the product space by investigating the characteristics for particular index numbers. If you can reasonably demonstrate that such index segment could never contain a self-countering n-tuple, you can safely skip it.

_Note:_ Concerning the following statement in Nash's original paper: "By using the continuity of the pay-off functions we see that the
graph of the mapping is closed." I wonder why the pay-off function needs to be continuous? I actually think the whole thing will even work with entirely discrete pay-off functions. But then again, I leave that to others to figure out. John Nash may have had another reason to say this, while this reason may simply escape me. I happily use discrete functions and it all still works fine. Kakutani's fixed-point theorem only requires that every countering n-tuple is again a valid n-tuple in the product space. This is always the case, if a player is allowed to choose any strategy regardless of what strategy the other players may have chosen. I seriously wonder what the continuity of the payoff functions may have to do with Kakutani's theorem? There is no requirement for the payoff functions besides just being computable. Therefore, the reason for the continuity requirement keeps escaping me ...

I seriously enjoyed reading Nash's 1950 blog post. His specs are really fun to implement; unlike the unintelligible, unimplementable, Russell-Whitehead verbiage they harass us with nowadays. Nash's paper is eminently readable for a relatively complex statement to make. David Gale had a seriously good idea when telling John Nash to use Kakutani. His mathematical reduction to Kakutani is incredibly simple! I wonder why John Nash never wrote another thing like that? I would really like to implement that one too!

3. License
----------
	Copyright (c) 2012 Erik Poupaert.
	Licensed under the Library General Public License (LGPL).

