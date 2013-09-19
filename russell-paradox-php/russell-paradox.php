#!/usr/bin/env php
<?php
/**
	Russell's paradox implemented in PHP
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).

        This function implements Russell's paradox in PHP

        For additional background information, refer to:
        http://en.wikipedia.org/wiki/Russell's_paradox
*/


/**
*       A recursive set is a set, defined exclusively by its
*       membership function. It only needs to be able to answer
*       if an element is member of the set or not.
*       http://en.wikipedia.org/wiki/Recursive_set
*/
interface RecursiveSet 
{
        public function isMember($element);
}

/**
*       A Russell set is a set of sets whose elements do
*       not contain themselves
*/
class RussellSet implements RecursiveSet
{
        function isMember($element)
        {
                if($element instanceof RecursiveSet)
                {
                        //only true if the element is not a 
                        //member of itself
                        return !$element->isMember($element);
                }
                else
                {
                        //it's not a recursive set, so it is also not a member
                        // of this set
                        return false;
                }
        }
}

/*
*       Is the Russell set member of the Russell set itself?
*       Undecidable. The algorithm keeps calling itself with
*       the same arguments. It never halts.
*/
$russellSet=new RussellSet();
echo $russellSet->isMember($russellSet); //undecidable

