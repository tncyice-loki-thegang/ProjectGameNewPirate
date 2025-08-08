<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BackendManager.php 21207 2012-05-24 08:21:04Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/BackendManager.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-05-24 16:21:04 +0800 (四, 2012-05-24) $
 * @version $Revision: 21207 $
 * @brief
 *
 **/

class BackendManager extends BaseScript
{

	private function usage()
	{

		echo "usage: btscript BackendManager.php maskBackend|unmaskBackend|addBackend [wegiht]\n";
	}

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$argc = count ( $arrOption );
		if ($argc < 1)
		{
			echo "not enough arguments\n";
			$this->usage ();
			return;
		}

		$method = $arrOption [0];
		if ($method != 'maskBackend' && $method != 'unmaskBackend' && $method != 'addBackend')
		{
			echo "invalid method\n";
			$this->usage ();
			return;
		}

		if ($argc > 1)
		{
			$weight = intval ( $arrOption [1] );
		}
		else
		{
			$weight = 0;
		}

		if ($method == 'addBackend' && empty ( $weight ))
		{
			echo "weight can't be zero for add\n";
			$this->usage ();
			return;
		}

		$arrGroup = $this->parseConfig ( ScriptConf::PHPPROXY_CONF );
		foreach ( $arrGroup as $group )
		{

			try
			{
				$proxy = new PHPProxy ( 'lcserver' );
				$arrModule = $proxy->getModuleInfo ( 'lcserver', $group );
				$proxy = new RPCProxy ( $arrModule ['host'], $arrModule ['port'] );
				$ret = $proxy->$method ( ScriptConf::PRIVATE_HOST, $weight );
				echo sprintf ( "%s on %s:%d %d return %s\n", $method, $arrModule ['host'],
						$arrModule ['port'], $weight, $ret );
			}
			catch ( Exception $e )
			{
				$ret = $e->getMessage ();
				echo sprintf ( "%s on %s return %s\n", $method, $group, $ret );
			}

		}
	}

	private function parseConfig($file)
	{

		$config = simplexml_load_file ( $file );
		$arrGroup = array ();
		foreach ( $config->module as $module )
		{
			if ($module->name != 'lcserver')
			{
				continue;
			}
			$arrGroup [] = strval ( $module->group );
		}
		return $arrGroup;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */