#!/usr/bin/env php
<?php
/**
	Lambda calculus boolean functions implemented in PHP
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).

        This function implements lambda calculus boolean functions in PHP

        1. _TRUE_
        2. _FALSE_
        3. _AND_
        4. _OR_
        5. _NOT_

        For additional background information, refer to:
        http://en.wikipedia.org/wiki/Lambda_calculus
        http://en.wikipedia.org/wiki/Alonzo_Church

*/

/** implementation for: _TRUE_ := λx.λy.x */

function _TRUE_($a,$b)
{
        return $a;
}

/** implementation for: _FALSE_ := λx.λy.y */

function _FALSE_($a,$b)
{
        return $b;
}

/** implementation for: _AND_ := λp.λq.p q p */

function _AND_($a,$b)
{
        return $a($b,$a);
}

/** implementation for: _OR_ := λp.λq.p p q */

function _OR_($a,$b)
{
        return $a($a,$b);
}

/**

alternative implementation for _NOT_ 

The traditional implementation is:

        NOT := λp.λa.λb.p b a

This implementation takes 3 parameters.
This is unsuitable.
The implementation should only take 1 parameter.

*/

function _NOT_($a)
{
        //we could also implement this in full anonymous lambda style
        //by repeating the full implementation of _FALSE_ and _TRUE in the
        //function call
        return $a('_FALSE_','_TRUE_');       
};

function output($msg)
{
        if($msg instanceof Closure)
        {
                $f=new ReflectionFunction($msg);
                echo $f->getDocComment()."\n";
        }
        else
        {
                echo "$msg\n";
        }
}

                                        //Expected output
output('---AND---');
output(_AND_('_TRUE_','_FALSE_'));      //FALSE
output(_AND_('_TRUE_','_TRUE_'));       //TRUE
output(_AND_('_FALSE_','_TRUE_'));      //FALSE
output(_AND_('_FALSE_','_FALSE_'));     //FALSE

output('---OR---');
output(_OR_('_TRUE_','_FALSE_'));       //TRUE
output(_OR_('_TRUE_','_TRUE_'));        //TRUE
output(_OR_('_FALSE_','_TRUE_'));       //TRUE
output(_OR_('_FALSE_','_FALSE_'));      //FALSE

output('---NOT---');
output(_NOT_('_TRUE_'));                //FALSE
output(_NOT_('_FALSE_'));               //TRUE

//anonymous style

//PHP apparently refuses to return the function definition, 
//So, we will return the function's doc comment

output
(       _AND_
        ( 
                /** TRUE */     function($a,$b){return $a;}, 
                /** FALSE */    function($a,$b){return $b;}
        )
);

