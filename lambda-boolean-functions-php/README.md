Lambda calculus boolean functions implemented in PHP
====================================================

PHP library implementing lambda calculus boolean functions.

1. Installation
---------------

Copy the file `lambda-boolean-functions.php` to any location in your home folder and make it executable with:

	chmod a+x lambda-boolean-functions.php

Invoke the function tests with:

	./lambda-boolean-functions.php


2. TRUE and FALSE
-----------------

`TRUE` and `FALSE` are traditionally implemented by mapping them on two arbitrary values. Typically:

	TRUE=1 and FALSE=0      C style
	TRUE=0 and FALSE=1      Shell style
	TRUE=-1 and FALSE=0     Basic style

Other values are ordinarily considered `TRUE` or `FALSE` depending on the one or the other arbitrary calculation rule. For example, "Zero is true and everything else is false" (Shell style), or "Zero is false and everything else is true." (C style). As you can see, it is absolutely not a problem to define `TRUE` and `FALSE` in diametrically opposed ways.

The lambda calculus works differently. It adamantly insists on the idea that `TRUE` and `FALSE` are arbitrarily different functions.


`TRUE` is a function that returns its first argument as its result. In PHP:

	/** implementation for: _TRUE_ := λx.λy.x */

	function _TRUE_($a,$b)
	{
		return $a;
	}


`FALSE` is a function that returns its second argument as its result. In PHP:


	/** implementation for: _FALSE_ := λx.λy.y */

	function _FALSE_($a,$b)
	{
		return $b;
	}


For example, the answer to the question `_TRUE_('john', 'peter')` is `john`. Now you are probably thinking "You cannot mean this. This is absurd!" I must insist, however, that the answer to the truth of the question about `john` and `peter` is `john`, simply because you mentioned `john` first. If you supply two alternatives in your question, the first alternative is always the true answer and the second alternative the false one. So, indeed, you decide entirely by yourself what is the truth. Just mention what you prefer, first.

You may think that the idea of determining truth and falsehood arbitrarily by essentially leaving the answer to the person asking the question is a problem. It is absolutely not. On the contrary, this is the only correct way to determine the truth.

The definitions for `TRUE` and `FALSE` in the lambda calculus satisfy two very important constraints:

	For if the judgement is analytical, be it affirmative or negative, its truth must always
	be recognizable by means of the principle of contradiction. [...] We must therefore hold
	the principle of contradiction to be the universal and fully sufficient Principle of all
	analytical cognition.

	Critique of Pure Reason (German: Kritik der reinen Vernunft) by Immanuel Kant, 1781, Königsberg.


There is only one law in cognition: All statements are true until you can point out a contradiction in them. So, yes, indeed, if someone says he saw gremlings eating his sandwich, the statement will remain true, until you can finally point out a contradiction in one of his later statements.


The second important constraint is that no formal reasoning system is allowed to claim the truth of its own statements:


	For any formal effectively generated theory T including basic arithmetical truths
	and also certain truths about formal provability, if T includes a statement of
	its own consistency then T is inconsistent.

	Gödel's Second Incompleteness Theorem


So, the only acceptable answer to the question "Is New York a city in the USA?" looks like this:


	function ($a,$b) { return $a;}


The only acceptable answer to the question "Is New York a city in China" looks like this:


	function ($a,$b) { return $b;}


These are the real answers. The Lambda calculus even refrains from naming these functions `_TRUE_` and `_FALSE_` because these names could confuse you as to what the real answers are. The real answers are the functions given above.

Let's repeat the question and the answer to make sure the point came across correctly. The answer to the question "Is New York a city in the USA?" is not:

	TRUE


It is:


	function ($a,$b) { return $a;}


We can indeed refer to the function implementation with a name, but the symbol is definitely not its real nature. It is just a potentially confusing symbol.



3. AND, OR, and NOT
-------------------

People who see a problem in accepting any non-contradictory statement as the truth, may think that any arbitrary set of statements will end up being true. This is not the case at all.

The answer to the truth of the combination of statements `statement1 AND statement2` is:

	statement1      statement2      statement1 and statement2

	_FALSE_       	_FALSE_         _FALSE_
	_FALSE_         _TRUE_          _FALSE_
	_TRUE_         	_FALSE_        	_FALSE_
	_TRUE_         	_TRUE_         	_TRUE_

As soon as you have chosen `_TRUE_` or `_FALSE_` as the answers to a `statement1` and to another `statement2`, be careful what you start saying about `statement1 _AND_ statement2`. You are no longer free to make arbitrary claims about the combined statement. The lambda calculus is perfectly capable of verifying your combined statement with the following function:

	/** implementation for: _AND_ := λp.λq.p q p */

	function _AND_($a,$b)
	{
		return $a($b,$a);
	}

						//Expected output

	output(_AND_('_TRUE_','_FALSE_'));      //FALSE
	output(_AND_('_TRUE_','_TRUE_'));       //TRUE
	output(_AND_('_FALSE_','_TRUE_'));      //FALSE
	output(_AND_('_FALSE_','_FALSE_'));     //FALSE


`$a` is the `_TRUE_` or `_FALSE_` function that you have chosen as an answer to the first statement. Next, `$b` is the function you have chosen as an answer to the second statement. From there, you are no longer free to choose an answer to the statement `statement1 AND statement2`. It is the system that will tell you the answer.


The combination `statement1 OR statement2` will have the following answers:


	statement1      statement2      statement1 or statement2

	_FALSE_        	_FALSE_        	_FALSE_
	_FALSE_       	_TRUE_          _TRUE_
	_TRUE_         	_FALSE_        	_TRUE_
	_TRUE_         	_TRUE_         	_TRUE_


It is implemented as following:


	/** implementation for: _OR_ := λp.λq.p p q */

	function _OR_($a,$b)
	{
		return $a($a,$b);
	}

						//Expected output

	output(_OR_('_TRUE_','_FALSE_'));       //TRUE
	output(_OR_('_TRUE_','_TRUE_'));        //TRUE
	output(_OR_('_FALSE_','_TRUE_'));       //TRUE
	output(_OR_('_FALSE_','_FALSE_'));      //FALSE


And now comes the most dangerous function, the `_NOT_` function:


	/**

	alternative implementation for _NOT_ 

	The traditional implementation is:

		NOT := λp.λa.λb.p b a

	The traditional implementation takes 3 parameters, which is not really suitable.
	The following implementation only takes 1 parameter.

	*/

	function _NOT_($a)
	{
		//we could also implement this in full anonymous lambda style
		//by repeating the full implementation of _FALSE_ and _TRUE_ in the
		//function call
		return $a('_FALSE_','_TRUE_');       
	}

						//Expected output

	output(_NOT_('_TRUE_'));		//FALSE
	output(_NOT_('_FALSE_'));		//TRUE



The `_NOT_` function is the basis for all cognition.


A set of statements `$a` will be in `contradiction` as soon as we claim the truth of `$a` while the function `_NOT_($a)` also returns the `_TRUE_` function. The notion of `contradiction` is the ONLY but also the absolutely sufficient basis for rejecting a set of statements. Every logical combination of statements may never yield such contradiction.


Some people do not realize how hard it is to keep making statements -- about the gremlins that ate their sandwich -- before hitting a dreaded contradiction. This is why even skillfull liars will avoid lying, if it causes them no damage to tell the truth:


	The most dangerous of all falsehoods is a slightly distorted truth. 
	― Georg Christoph Lichtenberg, The Waste Books


If a theory, that is, a set of statements, does not collapse in a spectacular contradiction, it is simply not false:


	You can recognize a small truth because its opposite is a falsehood.
	The opposite of a great truth is another truth. 
	― Niels Bohr


The following statements are not a contradiction:


	_TRUE_('lisa','lisa')
	--> lisa
	_FALSE_('lisa','lisa')
	--> lisa


These statements are not a contradiction but an ambiguity. It is not possible to detect a contradiction without calling the `_NOT_` function. A contradiction must always look like this:


	echo $x
	--> function ($a,$b) { return $a;} //TRUE
	echo _NOT_($x)
	--> function ($a,$b) { return $a;} //ALSO TRUE


If the answer to `$x` is `function ($a,$b) { return $a;}`, the answer to `_NOT_($x)` must be `function ($a,$b) { return $b;}`. Any other way to detect a contradiction is incorrect.

Ambiguities may look like contradictions, but they are not. Bohr's remark simply rephrases an inevitable consequence of the Church-Turing theorem concerning the [Entscheidungsproblem](http://en.wikipedia.org/wiki/Entscheidungsproblem). 


4. Conclusion
-------------

The definitions for `TRUE` and `FALSE` are definitely much more solidly defined in the lambda calculus than in implementations in which these concept are mapped to arbitrary constants along with a few arbitrary rules to deal with the fallout of such choice.


5. License
----------
	Copyright (c) 2012 Erik Poupaert.
	Licensed under the Library General Public License (LGPL).

