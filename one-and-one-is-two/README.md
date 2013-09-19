"One and one is two" program implemented in PHP
===============================================

PHP program using Peano's axioms for natural numbers to prove in a mathematical sense that 1+1=2.
For additional background information, refer to:

http://en.wikipedia.org/wiki/Peano_axioms


1. Installation
---------------

Copy the files `one-and-one-is-two.php` to any location in your home folder, and if needed, make it executable with:

	chmod a+x one-and-one-is-two.php

Invoke the program with:

	./one-and-one-is-two.php


2. What is mathematical 'proof' for a statement
-----------------------------------------------

A statement is 'proven' when all untruth in the statement can only be the consequence of the untruth in the axioms to which it has been reduced. We will now demonstrate that any untruth in the statement '1+1=2' can only be the result of untruth in Peano's axioms. In other words, we will demonstrate that the statement '1+1=2' does not introduce any original untruth.


3. The function apply()
-----------------------

The function `apply(expression,argument)` replaces every `x` in the `expression` by the `argument`. Examples:

        apply('f(x)','a') returns 'f(a)'
        apply('axb','z') returns 'azb'

Note that we have no choice as to how to implement `apply('',argument)`. It must return `argument` and not `''`. This simplistic `apply()` function is powerful enough for the purpose of applying the `successor()` function.


4. The table of symbols, expressions, and functions
---------------------------------------------------

We declare the following entries axiomatically:

        symbol  expression      function
        '0'     ''              ''
        '1'     's()'           's(x)'

Zero is not the successor to any number. The expression for zero is `''`.
One is the successor to zero. The expression for one is `s()`.


5. Defining additional numbers
------------------------------

We define each additional number as the successor to a previous number:

                                     symbol  expression         function

        declareSuccessor('2','1')    '2'     's(s())'           's(s(x))'
        declareSuccessor('3','2')    '3'     's(s(s()))'        's(s(s(x)))'
        declareSuccessor('4','3')    '4'     's(s(s(s())))'     's(s(s(s(x))))'

We can declare as many numbers as we want. We can use any symbol for a number. For example:

        declareSuccessor('FIV','4')    'FIV'     's(s(s(s(s()))))'     's(s(s(s(s(x)))))'

This would be perfectly valid. Instead of `5`, our table would contain the symbol `FIV` as the successor of `4`.

Note that we should not declare:

- a number as a successor to itself
- a number as a successor to a number that already has a successor
- `0` as a successor to any number 


6. Lookup functions
-------------------

We define the following lookup functions that operate on the symbol-expression-function table:

###6.1. lookupExpressionBySymbol(symbol)

The function returns the expression associated with a symbol. Examples:

        invocation                      returns
        ----------                      -------
        lookupExpressionBySymbol('1')   's()'
        lookupExpressionBySymbol('2')   's(s())'
        lookupExpressionBySymbol('0')   ''

###6.2. lookupSymbolByExpression(expression)

The function returns the symbol associated to an expression. It is the inverse function of the previous function. Examples:

        invocation                              returns
        ----------                              -------
        lookupSymbolByExpression('s()')         '1'
        lookupSymbolByExpression('s(s())')      '2'
        lookupSymbolByExpression('')            '0'

###6.3. lookupFunctionBySymbol(symbol)

The function returns the function associated with a symbol. Examples:

        invocation                      returns
        ----------                      -------
        lookupExpressionBySymbol('1')   's(x)'
        lookupExpressionBySymbol('2')   's(s(x))'
        lookupExpressionBySymbol('0')   ''

As you can see by checking the previous lookup function, the expression is just the function applied to `zero`, that is, to `''`.


7. The sum function
-------------------

Now, we can implement the function `sum(a,b)` function as:

        lookupSymbolByExpression( apply( lookupFunctionBySymbol( a ),lookupExpressionBySymbol( b ) ) )

Here, we use Peano's axiom that says that:

        if a=s^a(), a(x)=s^a(x), b=s^b(), and b(x)=s^b(x) then a+b=s^a(s^b()), simplified: a+b=a(b())

The `sum()` function has now entirely been implemented as a combination of `lookup()` and `apply()`. The table used for looking up the expressions and functions, is constructed in accordance with Peano's axioms and defines the number symbols that we want to use. The results are therefore computed axiomatically, since the operation reduces each `sum(a,b)` statement to the basic definitions and axioms in the table. The result therefore constitutes 'proof' in a mathematical meaning.


8. Proof that 1+1=2
-------------------

For sum(1,1) the program computes:

        === computing '1'+'1' ===
            processing '1'
                [lookup]: the function for '1' is 's(x)'
            processing '1'
                [lookup]: the expression for '1' is 's()'
            combining results
                [apply]: function 's(x)' applied to 's()' is 's(s())'
                [lookup]: the symbol for 's(s())' is '2'
        Therefore, '1'+'1'='2'


9. Other examples
-----------------

        === computing '2'+'3' ===
            processing '2'
                [lookup]: the function for '2' is 's(s(x))'
            processing '3'
                [lookup]: the expression for '3' is 's(s(s()))'
            combining results
                [apply]: function 's(s(x))' applied to 's(s(s()))' is 's(s(s(s(s()))))'
                [lookup]: the symbol for 's(s(s(s(s()))))' is '5'
        Therefore, '2'+'3'='5'

        === computing '0'+'1' ===
            processing '0'
                [lookup]: the function for '0' is ''
            processing '1'
                [lookup]: the expression for '1' is 's()'
            combining results
                [apply]: function '' applied to 's()' is 's()'
                [lookup]: the symbol for 's()' is '1'
        Therefore, '0'+'1'='1'


10. License
----------
	Copyright (c) 2013 Erik Poupaert.
	Licensed under the General Public License (GPL).

