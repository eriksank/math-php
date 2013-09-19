#!/usr/bin/env php
<?php
/**
	Lambda calculus number functions implemented in PHP
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).

        This function implements lambda calculus number functions in PHP

        For additional background information, refer to:
        http://en.wikipedia.org/wiki/Lambda_calculus
        http://en.wikipedia.org/wiki/Alonzo_Church
*/

//-----------
//echo 3(5);
//-----------
// --> syntax error, unexpected '(', expecting ',' or ';'
//It is impossible to apply a function to a number, because the parser will reject the expression

//-----------
//_3_(5);
//-----------
// --> PHP Fatal error: Call to undefined function _3_() 
//
//	The error handler is not willing to trap the fatal error
//	function lambdaErrorHandler($errno, $errstr, $errfile, $errline)
//	{
//		echo "ERROR: $errno\n";
//	}
//	set_error_handler("lambdaErrorHandler",E_ALL);

//solution: dummy class 'repeat'
//which translates a call to _12_($a,$b) as _x_(12,$a,$b)

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

repeat::_7_(
                function ($x) { echo "$x\n"; return $x+1; }
        ,3);

repeat::_25_(
                function ($x) { echo "$x\n"; return $x-1; }
        ,100);

$y=repeat::_3_(
                function ($x) { return $x+5; }
        ,9);
echo "y is $y\n";

repeat::_3_(function() { echo "this is an apple\n"; });

