<?php
	/**
	* Full Page in files
	* cache headers in browser and nginx tag
	* config default
	*/
	
	
	/** 
	* config define	
	*/
	if (!defined('FPCTIME'))
		define('FPCTIME', 1800); //по умoлчанию 30 минут
	
	
	if ( FPCTIME !== 0 and sizeof($_POST) == 0 or FPCTIME == -1 ) {
												
		/**
		* - nginx + headers
		*/
		if (FPCTIME == -1) 
			$htime = 1200;
		else
			$htime = FPCTIME;
		
		header("X-Accel-Expires: $htime"); // tag for nginx
		header("Cache-Control: max-age=$htime"); // tag browser
		header("Expires: ".gmdate("D, d M Y H:i:s", time()+$htime)." GMT");
			
		/**
		* default cache directory
		*/
		if (!defined('FPCDIR'))
			define('FPCDIR', $_SERVER['DOCUMENT_ROOT'].'cache/');
			
		
		/**	определяем файл */
		if (isset($_SERVER['REQUEST_URI'])) {
			$fpc_uri = trim($_SERVER['REQUEST_URI']); 
			$fpc_uri = trim($fpc_uri,'/');
		}
		else
			$fpc_uri = '';
		
		if ($fpc_uri == '')
			$fpc_uri = 'index';	//mainpage

				/** GET params */
		if ($_SERVER['QUERY_STRING'] !== ''){ 
			$fpc_query = strpos($fpc_uri, '?');
			$fpc_uri = substr($fpc_uri, 0, $fpc_query);	
			$fpcache = $fpc_uri.'.html?'.$_SERVER['QUERY_STRING'];
		}
		else
			$fpcache = $fpc_uri.'.html';
		
		define('FPCFILE', FPCDIR.'html/'.$fpcache); // cache file 
			
		if (file_exists(FPCFILE)) { 
								
			if (FPCTIME == -1) {
				echo file_get_contents(FPCFILE);
				exit;
			}
			
			$fp_endtime = filemtime(FPCFILE) + FPCTIME;
			if ($fp_endtime > $_SERVER['REQUEST_TIME']){
				echo file_get_contents(FPCFILE); // выводим файл кеша и обрываем выполнение скрипта
				exit;
			}
		}
	}


	else { 
		/** не кешировать */
		header("X-Accel-Expires: 0");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
		header("Pragma: no-cache"); // HTTP/1.1 
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
	}
	
	
	




	/**
	* create cache directory
	*/
	function fpc_dir($dir){
		
		if (!is_dir($dir)){
			if (!mkdir($dir, 0777, True))
				return False;
		}
		return True;	
	}
	
	
	/**
	* write cache file
	*/
	function fpc_save($content = '', $key = ''){
		
		fpc_dir(dirname(FPCFILE));
		return file_put_contents(FPCFILE, $content);
	
	}
	

	// save page in html
	function fpc_save_include($include, $print = True) {
			
		ob_start();
			include($include);
			$content = trim(ob_get_contents());
		ob_end_clean();	
		
		if ($print)
			echo $content;
		
		return fpc_save($content, $print); //saved cache;
	}
	

	function fpc_array($key, $value, $test = False) {
		
		// create directory
		$ex_dir = FPCDIR.'export/';
		fpc_dir($ex_dir);
		// cache file
		$fcache = FPCDIR.'export/'.md5($key).'.json';
		if (is_array($value)) {  
			return file_put_contents($fcache, json_encode($value));
		}	 
		
		elseif (is_int($value)) {
			
			if (file_exists($fcache) && filemtime($fcache)+$value > $_SERVER['REQUEST_TIME']){
				if ($test)
					echo "FPCACHE\CACHE:\n\n";
				return json_decode(file_get_contents($fcache), True);
			}
		}
		
		if ($test)
			echo "FPCACHE\ORIGINAL:\n\n";
		return null;
	}
	

	function fpc_file($key, $time = 3600) {
		
		$fcache = FPCDIR.'blocks/'.$key.'.html';
		
		if (filemtime($fpiece)+$time > $_SERVER['REQUEST_TIME'] && file_exists($fcache))
			return file_get_contents($fcache); 
		else {
			
			ob_start();
			include($file);
			$result = trim(ob_get_contents());
			ob_end_clean();
			
			file_put_contents($fpiece, $result);
			return $result;
		}
	
	}