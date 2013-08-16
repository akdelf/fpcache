<?php


	define('FPCDIR', 'cache/');
	require '../fpcache.php';

	$key = 'my_arr';
	
	if ($arr = fpc_array($key, 300)){
		echo "cache: \n\n";
		print_r($arr);
	}	
	else {
		$arr = array('1', '2', '3', '4');
		fpc_array($key, $arr);
		echo "original: \n\n";
		print_r($arr);
	}