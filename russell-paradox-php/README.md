Russell's paradox implemented in PHP
====================================

Library implementing Russell's paradox in PHP.

1. Installation
---------------

Copy the file `russell-paradox.php` to any location in your home folder and make it executable with:

	chmod a+x russell-paradox.php

Invoke the function tests with:

	./russell-paradox.php

Note that the program does not halt. You will have to stop it by pressing `CTRL+C`.

2. Introduction
---------------

[Russell's paradox](http://en.wikipedia.org/wiki/Russell's_paradox) is the following question:

        Does a set, that contains the sets that do not contain themselves, contain itself?

3. Recursive sets
-----------------

According to its definition, a [recursive set](http://en.wikipedia.org/wiki/Recursive_set) is an interface that implements at least one function: `isMember($element)`. 

        interface RecursiveSet 
        {
                public function isMember($element);
        }

4. A Russell set
----------------

A set is a Russell set if it is a recursive set and not a member of itself:

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

We can compute Russell's paradox by instantiating the class and calling the `isMember()` function for itself:

        $russellSet=new RussellSet();
        echo $russellSet->isMember($russellSet);


5. Conclusion
-------------

The problem does not halt. The `isMember()` function keeps calling itself. The problem is indeed [undecidable](http://en.wikipedia.org/wiki/Decidability_(logic).

6. License
----------
	Copyright (c) 2012 Erik Poupaert.
	Licensed under the Library General Public License (LGPL).

