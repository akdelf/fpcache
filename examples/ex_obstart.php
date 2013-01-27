<?php

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
	
	echo $content;

	fpc_save($content);