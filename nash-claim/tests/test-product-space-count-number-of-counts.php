#!/usr/bin/env php
<?php
/**
	Productspace test
	Written by Erik Poupaert, December 2012
	Licensed under the Library General Public License (LGPL).
*/
require_once('../lib/ProductSpace.php');

function dumpProductSpace($counts)
{
	$productSpace=new ProductSpace($counts);
	echo "product space: ".$productSpace->nTupleCount()."\n";

	for($i=0; $i<$productSpace->countNumberOfCounts(); $i++)
	{
		echo $productSpace->facultyForCount($i)."\n";
	}
}

dumpProductSpace(array(2,3));
dumpProductSpace(array(2,3,8,5));
dumpProductSpace(array(5,5,5));

