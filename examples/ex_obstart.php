<?php

	define('FPCTIME', 300); //cache time

	require '../fpcache.php';

	ob_start();

	?>
	<html>
		<body>
			<h1>EXAMPLE FULL PAGE CACHE</h1>
		</body>
	</html>	

	<?php

	$content = trim(ob_get_contents());
	ob_end_clean();	

	
	# file_put_contents(FPCFILE, $result); //code version

	fpc_save($content); // function short version 

