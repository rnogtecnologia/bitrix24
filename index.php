<?php
	session_start();

	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

	define('PROJECT', 'bitrix24/'); 
	define('CONTROLLERS', 'app/controllers/');	
	define('VIEWS', 'app/views/');
	define('MODELS', 'app/models/');
	define('HELPERS', 'system/helpers/');	
	define('LIBRARY', '/'.PROJECT.'/system/library/');	
	define('DIR_LIBRARY', getcwd() . '/system/library/');
	define('NFE', '/'.PROJECT.'/system/nfe/');	
	define('DIR_NFE', getcwd() . '/system/nfe/');	
	define('CTE', '/'.PROJECT.'/system/cte/');	
	define('DIR_CTE', getcwd() . '/system/cte/');	
	define('ASSETS', PROJECT.'assets/');
	define('TITLE', 'Br24');
	define('ROOT', PROJECT);
	define('HTTP_SERVER', 'http://www.rnog.com.br/');

	require_once('system/system.php');
	require_once('system/controller.php');
	require_once('system/model.php');

	function __autoload( $file ){
		if ( file_exists( MODELS . $file . '.php') )
			require_once( MODELS . $file . '.php');
		else if ( file_exists( HELPERS . $file . '.php') )
			require_once( HELPERS . $file . '.php');
		else
			die("Houve um erro. Model ou Helper nao encontrado. (".$file.")");
	}

	$start = new System;
	$start->run();


