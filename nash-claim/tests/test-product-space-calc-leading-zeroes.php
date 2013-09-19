#!/usr/bin/env php
<?php
/**
	Productspace test
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require_once('../lib/ProductSpace.php');

$productSpace=new ProductSpace(array(2,3,8,5));

for($i=0; $i<$productSpace->nTupleCount(); $i++)
{
	$numberOfLeadingZeroes=$productSpace->calcLeadingZeroes($i);
        echo "$i) leading zeroes: $numberOfLeadingZeroes\n";                
}

