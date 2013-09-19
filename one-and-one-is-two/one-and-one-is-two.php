#!/usr/bin/env php
<?php
/**
*	One and one is two
*
*       For additional background information, refer to:
*       http://en.wikipedia.org/wiki/Peano_axioms
*
*	Written by Erik Poupaert, February 2013
*	Licensed under the General Public License (GPL).
*/

/**
 * Rename argument in expression
 * example: renameArg('axdf','a','z') becomes 'zxdf'
 * @param string $expression
 * @param string $symbolReplaced
 * @param string $symbolNew
 * @return string
 */
function renameArg($expression,$symbolReplaced,$symbolNew)
{
	return str_replace($symbolReplaced,$symbolNew,$expression);
}

/**
 * Applies a number function to an argument
 * example: apply('s(s(x))','s(x)') becomes 's(s(s(x)))'
 * @param string $function
 * @param string $argument
 * @return string
 */
function apply($function,$argument,$debug=true)
{
        if($function=='') $applied=$argument; 
	else $applied=renameArg($function,'x',$argument);
	if($debug) output(2,"[apply]: function '$function' applied to '$argument' is '$applied'");
        return $applied;
}

/* Axiomatically defined claims about zero and one */
$definedExpressionsBySymbol=array('0' => '','1' => 's()');
$definedFunctionsBySymbol=array('0' => '', '1'=> 's(x)');
$definedSymbolsByExpression=array('' => '0', 's()'=> '1');

/**
 * Looks up the expression associated with a symbol
 * example: lookup('2') returns 's(s())'
 * @param string $symbol
 * @return string
 */
function lookupExpressionBySymbol($symbol,$debug=true)
{
	global $definedExpressionsBySymbol;
        $expression=$definedExpressionsBySymbol[$symbol];
	if($debug) output(2,"[lookup]: the expression for '$symbol' is '$expression'");
        return $expression;
}

/**
 * Looks up the symbol associated with an expression
 * example: lookup('s(s())') returns '2' 
 * @param string $expression
 * @return string
 */
function lookupSymbolByExpression($expression,$debug=true)
{
	global $definedSymbolsByExpression;
        $symbol=$definedSymbolsByExpression[$expression];
	if($debug) output(2,"[lookup]: the symbol for '$expression' is '$symbol'");
        return $symbol;
}

/**
 * Looks up the function associated with a symbol
 * example: lookup('2') returns 's(s(x))' 
 * @param string $symbol
 * @return string
 */
function lookupFunctionBySymbol($symbol,$debug=true)
{
	global $definedFunctionsBySymbol;
        $function=$definedFunctionsBySymbol[$symbol];
	if($debug) output(2,"[lookup]: the function for '$symbol' is '$function'");
        return $function;
}

/**
 * Declares that a symbol is the successor of another symbol
 * example: declareSuccessor('3','2') declares that '3' is the successor of '2' 
 * @param string $symbolNext
 * @param string $symbolPrevious
 * @return string
 */
function declareSuccessor($symbolNext,$symbolPrevious)
{
	global $definedExpressionsBySymbol;
	global $definedSymbolsByExpression;
	global $definedFunctionsBySymbol;

        //no symbol can be a successor to itself
        if($symbolNext==$symbolPrevious)
                throw new exception("cannot declare '$symbolNext' as a successor to itself");

        //zero cannot be the successor to any number
        if($symbolNext=='0')
                throw new exception("cannot declare '$symbolNext' as a successor to any number");


	//Expression
	$expressionPrevious=lookupExpressionBySymbol($symbolPrevious,false);
	$expressionNext=apply('s(x)',$expressionPrevious,false);

        //duplicate successor
        if(array_key_exists($expressionNext,$definedSymbolsByExpression))
                throw new exception("'$symbolPrevious' already has a successor");

	//function
	$functionPrevious=lookupFunctionBySymbol($symbolPrevious,false);
        $functionNext=apply('s(x)',$functionPrevious,false);

	//store
	$definedExpressionsBySymbol[$symbolNext]=$expressionNext;
	$definedFunctionsBySymbol[$symbolNext]=$functionNext;
	$definedSymbolsByExpression[$expressionNext]=$symbolNext;
}

/**
 * Outputs a message
 * @param string $level multiple of 4 spaces to prefix to the message
 * @param string $message the message itself
 * @return nothing
 */
function output($level,$message)
{
	for($i=0; $i<$level;$i++) echo "    ";
	echo "$message\n";
}

/**
 * Computes the sum of two numbers
 * 'a+b'=lookupSymbolByExpression(apply(lookupFunctionBySymbol(a),lookupExpressionBySymbol(b))
 * @param string $a first number
 * @param string $b second number
 * @return $a+$b
 */
function sum($a,$b)
{
	output(0,"=== computing '$a'+'$b' ===");
	output(1,"processing '$a'");
	$functionForA=lookupFunctionBySymbol($a);
	output(1,"processing '$b'");
	$expressionForB=lookupExpressionBySymbol($b);
	output(1,"combining results");
	$resultingExpression=apply($functionForA,$expressionForB);
	$symbolForResult=lookupSymbolByExpression($resultingExpression);
	output(0,"Therefore, '$a'+'$b'='$symbolForResult'");
	output(0,'');

	return $symbolForResult;
}

/**
 * Outputs the symbols declared
 * @return nothing
 */
function outputSymbols()
{
	global $definedExpressionsBySymbol;
	global $definedFunctionsBySymbol;
	output(0,'Symbols defined:');
	foreach($definedExpressionsBySymbol as $symbol=>$expression)
        {
                $function=lookupFunctionBySymbol($symbol,false);
		output(1,"'$symbol' is expression '$expression' and function '$function'");
        }
	output(0,'');
}

/**
 * MAIN PROGRAM
 */

//declaration of successors
declareSuccessor('2','1');
declareSuccessor('3','2');
declareSuccessor('4','3');
declareSuccessor('5','4');

outputSymbols();

//calculations
sum('2','3');
sum('0','1');
sum('0','4');
sum('4','0');
sum('1','1');

