<?php

function DtoOx($num){
	$OX = array(
	0=>0,
	1=>1,
	2=>2,
	3=>3,
	4=>4,
	5=>5,
	6=>6,
	7=>7,
	8=>8,
	9=>9,
	10=>'a',
	11=>'b',
	12=>'c',
	13=>'d',
	14=>'e',
	15=>'f',
	);
	$str=array();
	while ($num!=0) {
		$v=$num%16;
		$num=(int)($num/16);
		$str[]=$v;
	}
	$str=array_reverse($str);
	foreach ($str as $key => &$value) {
		if (array_key_exists($value, $OX)) {
			$value=$OX[$value];
		}
	}
	return $str;
}
var_dump(DtoOx(99999));
$str="多人模式";
?>