Truth-Liar-Random strategy program implemented in PHP
=====================================================

PHP program implementing a strategy for the Truth-Liar-Random problem.

1. Installation
---------------

Copy the files `truth-liar-random.php`, `truth-liar-random-mental-state.php`, and `truth-liar-random-mental-state-ja-da.php` to any location in your home folder and make them executable with:

	chmod a+x truth-liar-random.php
	chmod a+x truth-liar-random-mental-state.php
	chmod a+x truth-liar-random-mental-state-ja-da.php

Invoke the programs with:

	./truth-liar-random.php
	./truth-liar-random-mental-state.php
	./truth-liar-random-mental-state-ja-da.php


2. Description of the problem
-----------------------------

There is one Questioner, who questions the players. There are three players: Truth, Liar, and Random.

- Truth always tells the truth.
- Liar always lies.
- Random sometimes lies but sometimes may tell the truth.

Note that in this program Random does NOT give nonsensical answers. The reason for this, is that it would be possible
to detect very easily who is Random by asking the players an undecidable/uncomputable problem. If Random just spouts nonsense, he 
will be the only player who will answer to that question.

We avoid this problem by making sure Random sometimes behaves as Truth and sometimes as Liar. Therefore, Random never spouts nonsense. So, Questioner faces either two Truths and one Liar or else two Liars and one Truth.

You can find more background information about the problem here: [http://en.wikipedia.org/wiki/The_Hardest_Logic_Puzzle_Ever](http://en.wikipedia.org/wiki/The_Hardest_Logic_Puzzle_Ever).


3. First solution: truth-liar-random.php
----------------------------------------
This is the solution strategy when:

- The players cannot know who the other players are without actually asking them
- Random does not know if he is lying or not

Questioner solves the problem by asking each player a contradiction.

- Truth will answer `false`
- Liar will answer `true`
- Random will answer either `false` or `true`

The status of each player will first move from `unknown` to `truthOrRandom` or `liarOrRandom`.
The players will remain in this status until one player finally reveals that he is `Random`.

- `truthOrRandom`: by answering true to a contradiction. This means that the player has revealed that he is `Random`.
- `liarOrRandom`: by answering false to a contradiction. This means that the player has revealed that he is `Random`.

The problem is therefore solved by repeatedly facing the players with a contradiction until the contradiction becomes effective in revealing who is `Random`.

	In other words, we detect who is the contradictory player, Random, through his inconsistency
	in dealing with contradictions.


###First round and follow-on rounds

The first round will always result in resolving one identity:

        truthOrRandom liarOrRandom liarOrRandom ==>  truth liarOrRandom liarOrRandom
        liarOrRandom truthOrRandom truthOrRandom  ==> liar truthOrRandom truthOrRandom

Questioner will question only the two remaining players in the follow-on rounds.


###Kind of questions to ask

After looking at the source code, it becomes clear that this kind of strategy would work by asking any `known-true` or any `known-false` question. A contradiction is a good question for this strategy, because there is not the slightest doubt as to the fact that it is `known-false`. Any other `known-false` question can always be reduced to a contradiction of some sort. With a `known-true` question, however, there is always the risk that you think that the answer is true, while in reality it is false.


###Number of steps

Regardless of what questions Questioner asks, during the first round it will only be possible to reveal the identity of one player. The output of the two other players will be exactly the same because for all purposes these players are exactly the same. In the next questioning rounds, it will not be possible to distinguish between the players, for as long as they keep giving exactly the same answers. 

Changing the question does not help. If Questioner asked one of both players if the player is Random or if the other player is Random, the answer to the question is irrelevant. The player could be Random while he is in the Liar state. The player could also be telling the truth.

It is simply not possible to solve the problem in a fixed number of steps. Questioner will have to wait until the randomness governing the behaviour of `Random` changes his attitude and in that way reveals his identity. Questioner will discover who is Random, simply because Random will eventually end up giving a different answer to exactly the same question as before. Discovering this, inevitably requires minimum two questions to Random.

This solution strategy can be represented as a regular expression in contradictions asked to players A, B, and C:

        ABC ((A|B)|(AB|AC|BC)+(A|B|C)?)

The strategy has two strategy paths. Only after the fourth question it will become clear what path has been taken:

        ABC (A|B)
        ABC (AB|AC|BC)+(A|B|C)?

The minimum number of questions to ask is four: one question to Truth, one question to Liar, and two questions to Random. There is no maximum for the number of questions to ask. However, higher counts are increasingly unlikely.

###Example output

	$ ./truth-liar-random.php 

	== NEW QUESTIONING ROUND ==

	Asked contradiction to player 'A' with identity 'unknown', answer is 'false'
	 +setting identity of player 'A' to 'truthOrRandom'
	Asked contradiction to player 'B' with identity 'unknown', answer is 'true'
	 +setting identity of player 'B' to 'liarOrRandom'
	Asked contradiction to player 'C' with identity 'unknown', answer is 'false'
	 +setting identity of player 'C' to 'truthOrRandom'

	STATE: ( truthOrRandom liarOrRandom truthOrRandom )
	 +setting identity of player 'B' to 'liar'

	== NEW QUESTIONING ROUND ==

	Asked contradiction to player 'A' with identity 'truthOrRandom', answer is 'false'
	 +question ineffective
	Asked contradiction to player 'C' with identity 'truthOrRandom', answer is 'true'
	 +setting identity of player 'C' to 'random'
	 +setting identity of player 'A' to 'truth'

	STATE: ( truth liar random )


4. Second solution, with 3 or 4 questions
-----------------------------------------
If a player can know when another player is Random, without asking any question to Random, the situation becomes different. A first question could be: Are you Random?

The answers will always be one of the following combinations

	count(yes)=1, count(no)=2 ==> the one saying yes is Liar
	count(yes)=2, count(no)=1 ==> the one saying no is Truth

The details for this are:

	#   A, B, C
	(0) no,no,no  	--> not possible, requires Liar to be lying
	(1) no,no,yes--> automatically Liar
	(2) no,yes-->Liar,no
	(3) no-->Truth,yes,yes
	(4) yes-->Liar,no,no
	(5) yes,no-->Truth,yes
	(6) yes,yes,no-->automatically Truth
	(7) yes,yes,yes --> not possible, requires Truth to be lying

Questioner can save himself to ask one question in cases (1) and (6). In cases (2) and (4) it will take one additional question to Liar about one other player to resolve the issue. Liar will lie. So if Liar says in case (2) that player A is not Random, then player A is random. From there, player C must be Truth. The same applies to case (4). In cases (3) and (5), it will take one additional question to Truth about one other player to resolve the issue.

The number of questions is then:

						Num. questions	Additional question to
	(1) no,no [yes implied] 		2+1=3		Liar
	(2) no,yes,no	 			3+1=4		Liar
	(3) no,yes,yes				3+1=4		Truth
	(4) yes,no,no				3+1=4		Liar
	(5) yes,no,yes				3+1=4		Truth
	(6) yes,yes,[no implied] 		2+1=3		Truth

Questioner can find the answer using just 3 questions in cases (1) and (6). In the other cases, Questioner will need 4 questions. Just knowing who the other players is cuts down the number of questions to ask, but not entirely to 3. This becomes possible, however, if Random knows if he is about to lie or tell the truth and can answer questions about that.

_Note:_ I have not written the program for this second solution, because it is just a simpler version of the third solution.


5. Third solution, with 3 questions: truth-liar-random-mental-state.php
-----------------------------------------------------------------------
Imagine that Random can know his mental state and therefore whether he is about to lie or to tell the truth. In this solution, the players also needs to know if another player is Random without asking him. To the questions, (1) Are you Random and are you about to tell the truth? (2) Are you Random and are you about to tell a lie? the players would answer:

        				Are you Random and		Are you Random and
					about to tell the truth?	about to tell a lie?

	Truth				no					no
	Liar				yes					yes
	Random about to tell the Truth	yes					no
	Random about to tell a lie	yes					no


So, it requires two questions to player A, to figure out who he is.

	no,no --> Truth
	yes,yes --> Liar
	yes,no --> Random

        no,yes --> impossible

Upon identifying Truth or Liar, one question to player A "Is player B Random?" will identify all players and will have solved the problem in 3 questions. Upon identifying Random, however, it will not be possible to use Random's next answer about any other player, because he could answer anything. So, Questioner should ask the next question to player B: "Are you Random?". If he says yes, he is Liar. Otherwise, he is Truth. 


###Example output

        $ ./truth-liar-random-mental-state.php 
        Asked 'areYouRandomAndAreYouAboutToTellTheTruth' to player 'A' with identity 'unknown', answer is 'true'
        Asked 'areYouRandomAndAreYouAboutToTellALie' to player 'A' with identity 'unknown', answer is 'false'
         +setting identity of player 'A' to 'random'
         STATE:( random unknown unknown )
        Asked 'areYouRandom' to player 'B' with identity 'unknown', answer is 'false'
         +setting identity of player 'B' to 'truth'
         +setting identity of player 'C' to 'liar'
         STATE:( random truth liar )


        $ ./truth-liar-random-mental-state.php 
        Asked 'areYouRandomAndAreYouAboutToTellTheTruth' to player 'A' with identity 'unknown', answer is 'false'
        Asked 'areYouRandomAndAreYouAboutToTellALie' to player 'A' with identity 'unknown', answer is 'false'
         +setting identity of player 'A' to 'truth'
         STATE:( truth unknown unknown )
        Asked 'isThisPlayerRandom about player B' to player 'A' with identity 'truth', answer is 'true'
         +setting identity of player 'B' to 'random'
         +setting identity of player 'C' to 'liar'
         STATE:( truth random liar )


6. Fourth solution, the ja-da complication: truth-liar-random-mental-state-ja-da.php
------------------------------------------------------------------------------------
Players now answer with `ja` or `da`. Questioner does not know, however, if `ja` means `yes` and `da` means `false` or the other way around. Each player makes up his own mind about that. This is less of a problem than it may look like:

	Would you say `ja` to a `true` statement?
	if ja=true and da=false ==> answer:ja
	if ja=false and da=true ==> answer:ja

	Would you say `ja` to a `false` statement?
	ja=true and da=false ==> answer:da
	ja=false and da=true ==> answer:da

Therefore, it does not matter what `ja` and `da` precisely mean, because, indeed, according to the lambda calculus:

	true(a,b)=a=false(b,a)
	false(a,b)=b=true(b,a)

The madness in the logic then becomes:

	if ja=true and da=false ==> ja(true,da)=ja(true)=ja=true
	if ja=false and da=true ==> ja(false,da)=ja(false)=da=true

We can conclude the mad logic with:

	ja(true statement) = ja, regardless of what ja and da mean
	ja(false statement) = da, regardless of what ja and da mean

The answers to the questions will look like this:

					Would you say 'ja' to:		Would you say 'ja' to:
        				Are you Random and		Are you Random and
					about to tell the truth?	about to tell a lie?

	Truth				no-->da					no-->da
	Liar				yes-->ja				yes-->ja
	Random about to tell the Truth	yes-->ja				no-->da
	Random about to tell a lie	yes-->ja				no-->da

So, we can figure out with these two questions to player A who he is:

	da,da --> Truth
	ja,ja --> Liar
	ja,da --> Random

	da,ja --> impossible


###Example output

        $ ./truth-liar-random-mental-state-ja-da.php 
        Asked 'wouldYouSayJaIfYouWereRandomAndAboutToTellTheTruth' to player 'A' with identity 'unknown', answer is 'da'
        Asked 'wouldYouSayJaIfYouWereRandomAndAboutToTellALie' to player 'A' with identity 'unknown', answer is 'da'
         +setting identity of player 'A' to 'truth'
         STATE:( truth unknown unknown )
        Asked 'wouldYouSayJaIfThisPlayerWereRandom about player B' to player 'A' with identity 'truth', answer is 'da'
         +setting identity of player 'B' to 'liar'
         +setting identity of player 'C' to 'random'
         STATE:( truth liar random )

        $ ./truth-liar-random-mental-state-ja-da.php 
        Asked 'wouldYouSayJaIfYouWereRandomAndAboutToTellTheTruth' to player 'A' with identity 'unknown', answer is 'da'
        Asked 'wouldYouSayJaIfYouWereRandomAndAboutToTellALie' to player 'A' with identity 'unknown', answer is 'da'
         +setting identity of player 'A' to 'truth'
         STATE:( truth unknown unknown )
        Asked 'wouldYouSayJaIfThisPlayerWereRandom about player B' to player 'A' with identity 'truth', answer is 'ja'
         +setting identity of player 'B' to 'random'
         +setting identity of player 'C' to 'liar'
         STATE:( truth random liar )


7. License
----------
	Copyright (c) 2012 Erik Poupaert.
	Licensed under the Library General Public License (LGPL).

