<?php
	
	define('FPCTIME', 300); //cache time
	define('FPCDIR', 'cache/ex_include/');
	
	require '../fpcache.php';

	echo fpc_save_include('start.php');
