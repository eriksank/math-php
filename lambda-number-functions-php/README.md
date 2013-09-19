Lambda calculus number functions implemented in PHP
====================================================

PHP library implementing lambda calculus number functions.

1. Installation
---------------

Copy the file `lambda-number-functions.php` to any location in your home folder and make it executable with:

	chmod a+x lambda-number-functions.php

Invoke the function tests with:

	./lambda-number-functions.php


2. Numbers in the lambda calculus
---------------------------------

The lambda calculus proposes - at first glance - an ideosynchratic definition for numbers. A number is function that takes two arguments:
- a function to call repeatedly 
- an argument for the first call

For example:

	$y = 3( function($x) { return $x+5; }, 9) 


The `3()` function call will repeat the application of the function supplied as its first argument `3` times. The first time it will call the function with the second argument supplied, that is, `9`. In the next function calls, the function `3()` will call the function supplied with the result from the previous function application. So, `f(9)` will returns `14`. Next, `f(14)` will return `19`. Finally, `f(19)` will return `24`. Therefore, the scripting engine will assign the value `24` to the variable `$y`.

We will elaborate further on why this definition for numbers is indeed meaningful. Let us first try to implement this in PHP. The implementation is not that straightforward, but it is possible.


3. Implementation in PHP
------------------------

The first obstacle to overcome lies in the PHP parser. The sequence of tokens ` 3(` is not a valid sequence in PHP. The token `(` is simply not a member of the follow set for the (integer) token `3`. This problem cannot be fixed without adjusting the PHP grammar definition, that you can find [here](https://github.com/php/php-src/blob/master/Zend/zend_language_parser.y). I do not think there is any scripting engine around that accepts number functions. Fixing this, would actually be a relatively minor hack to the PHP scripting engine or to any of these engines.

But then again, let us work around the problem instead. Let us use `_3_(` instead of `3(`. The symbol `_3_` is a valid (function) identifier in PHP as defined in the PHP token identification rules that you can find [here](https://github.com/php/php-src/blob/master/Zend/zend_language_scanner.l). Therefore, we could try to reform our expression to:

	$y = _3_( function($x) { return $x+5; }, 9) 

Now, we are hitting a new obstacle. The function `_3_` is not defined. We also do not desire to define all possible integer numbers as functions in the PHP symbol table. The solution would be to intercept the error flow in PHP, and attempt to resolve the unknown identifier `_3_` as a number function.

Unfortunately, anything that would elaborate the following strategy does not work:

	// --> PHP Fatal error: Call to undefined function _3_() 
	//	The error handler is not willing to trap the fatal error

		function lambdaErrorHandler($errno, $errstr, $errfile, $errline)
		{
			echo "ERROR: $errno\n";
		}
		set_error_handler("lambdaErrorHandler",E_ALL);


Fortunately, PHP offers a solution by using a class. 

When calling a static method in a class, the PHP scripting engine offers the possibility to intercept such call with the `__callStatic()` method. If we detect that the function called matches the pattern `_n_($arg1)` or `_n_($arg1,$arg2)`, the caller is trying to apply a number function. From there, we can redirect the call to the function `_x_($x,$f,$g)` which will implement the lambda calculus number function logic by repeatedly applying the result of the function `$f` to its argument `$g`, all of that `$x` times:

	class repeat
	{
		public static function __callStatic($name,$arguments)
		{
			if(!preg_match('/_([0-9]*)_/',$name,$matches))
				throw new Exception("invalid number function: $name");
			$number=$matches[1];
			if(count($arguments)<1)
				throw new Exception("number function '$name' must be called with at least 1 argument");
			$f=$arguments[0];
			if(count($arguments)>1) $g=$arguments[1]; else $g=null;
			return self::_x_($number,$f,$g);
		}

		static function _x_($x,$f,$g=null)
		{	
			$result=$g;
			for($i=0; $i<$x; $i++)
			{
		                if(is_callable($f))
		        	        $result=$f($result);
		                else
		                        throw new Exception("function $f not callable");
			}
			return $result;
		}
	}


The following function application now works properly:


	$y=repeat::_3_( function ($x) { return $x+5; } ,9);


4. Interpretation of lambda number functions
--------------------------------------------

From [Betrand Russell](http://en.wikipedia.org/wiki/Bertrand_Russell)'s [type theory](http://en.wikipedia.org/wiki/Type_theory) we understand that the number `3` does not really exist in reality. What we see are `3 apples` or `3 bicycles` but never the number `3` itself. `Threeness` must be applied to something to become meaningful. The simplicity and at the same time the rigor of the lambda calculus will inadvertently start exposing this truth. Anything that must be applied through `β-reduction` in order to be meaningful can obviously only be a function. Therefore, numbers are functions.

The simplest use of the `3()` function is calling it with a function that does not require any arguments:

	3(apple)

The second parameter can be omitted. Calling `3(apple)` amounts to stating the type function `apple()` three times. In PHP:

	repeat::_3_(function() { echo "this is an apple\n"; });

This amounts to pretty much the meaning of `3 apples` in daily life. We count `3` times something visible in front which belongs to the type (or category) of `apple`.

In other words, a type is itself also a function, that we can use to categorize things.

The lambda calculus allows, however, for supplying an argument to the type function, clearly suggesting that types/categories themselves are variable functions.

This seems to suggest that our view on the `appleness` of an apple may depend on the previous apple we saw. Therefore, the order in which we see things, may affect our opinion about the next things we see. Since it may be dangerous to do this, we will ordinarily try to suppress this characteristic of types. Repeated application of a type function, aka, [function iteration](http://en.wikipedia.org/wiki/Iterated_function) which allows for variability should be implemented with great care.


5. Usefulness in programming
----------------------------

The following expression would definitely look natural to use:

	3( echo "hello" );

No programmer would misunderstand the expression above, I guess. The lambda calculus seems to suggest that iterations can naturally be expressed by using number functions.


6. Implementing TRUE and FALSE through number functions
-------------------------------------------------------

`TRUE` and `FALSE` can be implemented by calling the `1`,`2`,`3`,`4` function or actually any number function as following:

	_TRUE_=function($a,$b)
	{
		return repeat::_1_(function($a) {return $a; },$a);	
	}

	_FALSE_=function($a,$b)
	{
		return repeat::_1_(function($a) {return $a; },$b);	
	}

`TRUE` and `FALSE` are definitely not number functions themselves in the lambda calculus. Mapping `TRUE` to a number and `FALSE` to all other numbers, or the other way around, does not seem to fit the rigourous but surprisingly simple logic of the lambda calculus.

7. Generating numbers
---------------------

It would not be possible to reach the number `√2` without using the function `√`. In fact, the existence of most numbers is just the result of the one or the other function application. For example, we can reach the number `2` by calling the `successor` function for the number `1`. From there, we can call the same function in order to reach `3` and so on:

	3 = successor(successor(successor(0)))

There is a problem, however. We cannot possibly call the `successor` function in order to reach the number `0`. What function should we call? Calling the successor function for `-1` obviously just moves the problem around without actually solving it. Originally, Alonzo Church recognized that fact that `Zero` is not truly computable. According to the lambda calculus, the answer to the question `Zero` amounts to not doing anything, including not answering the question:

>In Church's original lambda calculus, the formal parameter of a lambda expression was required to occur at least once in the function body, which made the above definition of 0 impossible.

`Zero` is equivalent to `nothing`, and `nothing` is an original pre-existing thing. It is simply the starting point. It did not come into being through application or computation. `Zero` is only a virtual number, designating the absence of a number. `Zero` does not exist in reality because its nature is to designate non-existence, which of course cannot truly exist amidst existence. `Zero` points back all the way to the paradox of the initial starting point, in which it would have existed.

Another problem occurs in defining the `successor` function itself. It is not possible to define the `successor` function itself without mentioning the number `1` in its implementation. Therefore, the number `One` is also not computable. Its computation through the `successor` function requires that it exists first. `One` is simply the `unit`, that is, the benchmark for `anything`.

According to the [Church-Turing thesis](http://en.wikipedia.org/wiki/Church-Turing_thesis), we can generate [all numbers]((http://en.wikipedia.org/wiki/Church_numeral) by function application. However, this story is not applicable out-of-the-box to `Zero` and `One`. `Zero` and `One` really seem to have a special status. They are true implementation constants. 

But then again, as soon as we have managed to define `Zero` and `One` as constants, they also become valid number functions. Consequently, the numbers `Zero` and `One` have a dual nature of both function and constant.

8. Unreachable numbers
----------------------

We could easily make the mistake to think that all numbers can be reached through computation. [Gödel's Incompleteness Theorem](http://en.wikipedia.org/wiki/G%C3%B6del's_incompleteness_theorems) guarantees that the lambda calculus, which indeed makes use of basic arithmetic, is incapable of ever generating particular unreachable numbers. These numbers exist but cannot be represented by the lambda calculus and therefore also not by a Turing or Von Neumann machine, that is, by the computers we use, unless we introduce them as constants.

A typical example for this, is the number `infinite`. We do not accept to keep calculating with for example the result of a division by zero, because the next calculation steps will almost inevitably fail to become meaningful. After their introduction, computations on the number `infinite` and `minus infinite` never move back to ordinary numbers by function application. 

There seem to exist exit points out of this reality. One such door can be opened, simply by dividing by zero. It does not seem to be possible to come back, though. Computation would simply be trapped there with no option to return.

[Benecerraf](http://en.wikipedia.org/wiki/Paul_Benacerraf) [argues](http://plato.stanford.edu/entries/philosophy-mathematics/) that an adequate account of truth in mathematics implies the existence of abstract mathematical objects, but that such objects are epistemologically inaccessible because they are causally inert and beyond the reach of sense perception. Therefore, there are things that truly exist but we are simply incapable of seeing. This is inevitably the worst limitation of the Church-Turing-Von Neumann machine and probably of ourselves as well.

In terms of computation, extending the power of the Turing-Church-Von Neumann machine may be possible but will at the very least require additional foundational constants. It could be extremely difficult to work with them. It is not sure that it is possible to reach these numbers by computation on ordinary numbers. It may also not be possible to reach back the ordinary numbers -- which we need for meaningful interpretation -- by computation on such new foundational constants.

I suspect that such new, additional foundational constants can be actually be reached by what we assume to be invalid function applications, such as division by zero. These new numbers do not map to anything in reality. Reaching such numbers amounts to some extent to thinking the unthinkable and still finding logic in such madness. In fact, long-term exposure to the unthinkable may even be a bit dangerous.

Our world is built on the foundational constants of `Zero` and `One`. Given the fact that `Zero` is defined as the absense of any `One`, `Zero` may only be a virtual foundational constant. The only real foundational constant seems to be `One`.

There are other such foundational constants possible. Our world also contains doors to these other realities but we are simply incapable of understanding what we see there. Long-term exposure to the impossible to understand, is in fact an issue in itself. Therefore, a journey through unreachable numbers and then successfully coming back to the real world with a meaningful interpretation, would amount to a seriously impressive breakthrough. I suspect, however, that it may turn out to be impossible.

9. License
----------
	Copyright (c) 2012 Erik Poupaert.
	Licensed under the Library General Public License (LGPL).

