<?php

	/**
	* controller
	*/
	$names = array('1'=>'Sherlok', '2'=>'Watson');

	
	/**
	* view
	*/

	?>
	<html>
		<body>
			<h1>EXAMPLE FULL PAGE CACHE</h1>
			<ul>
				<?foreach ($names as $name):?>
					<li><?=$name?></li>
				<?endforeach?>
			</ul>	
		</body>
	</html>	