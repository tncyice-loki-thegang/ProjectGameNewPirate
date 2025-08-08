<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: NewServer.php 24762 2012-07-26 02:59:22Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-0-99/optool/NewServer.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-07-26 10:59:22 +0800 (Thu, 26 Jul 2012) $
 * @version $Revision: 24762 $
 * @brief
 *
 **/
class NewServer extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		require_once (ROOT . '/optool/conf/Server.cfg.php');

		//处理lcserver的配置文件
		$config = simplexml_load_file ( ServerConf::LCSERVER_CONFIG );

		$group = $config->xpath ( '/root/server/group' );
		$group [0] [0] = ServerConf::LCSERVER_GROUP;

		$db = $config->xpath ( '/root/server/db_name' );
		$db [0] [0] = ServerConf::DATAPROXY_DB_NAME;

		$publicHost = $config->xpath ( '/root/server/public/listen_address' );
		$publicHost [0] [0] = ServerConf::LCSERVER_PUBLIC_HOST;

		$publicPort = $config->xpath ( '/root/server/public/listen_port' );
		$publicPort [0] [0] = ServerConf::LCSERVER_PUBLIC_PORT;

		$privateHost = $config->xpath ( '/root/server/private/listen_address' );
		$privateHost [0] [0] = ServerConf::LCSERVER_PRIVATE_HOST;

		$privatePort = $config->xpath ( '/root/server/private/listen_port' );
		$privatePort [0] [0] = ServerConf::LCSERVER_PRIVATE_PORT;

		$taskDpHost = $config->xpath ( '/root/server/task/db_host' );
		$taskDpHost [0] [0] = ServerConf::DATAPROXY_LISTEN_HOST;

		$taskDpPort = $config->xpath ( '/root/server/task/db_port' );
		$taskDpPort [0] [0] = ServerConf::DATAPROXY_LISTEN_PORT;

		$config->asXML ( ServerConf::LCSERVER_CONFIG . '.new' );

		//处理phpproxy
		$config = simplexml_load_file ( ServerConf::PHPPROXY_CONFIG );

		$arrModule = $config->xpath ( '/root/module' );
		foreach ( $arrModule as $module )
		{
			switch ($module->name)
			{
				case 'lcserver' :
					$host = ServerConf::LCSERVER_PRIVATE_HOST;
					$port = ServerConf::LCSERVER_PRIVATE_PORT;
					break;
				case 'data' :
					$host = ServerConf::DATAPROXY_LISTEN_HOST;
					$port = ServerConf::DATAPROXY_LISTEN_PORT;
					break;
				default :
					continue 2;
			}
			$module [0]->group = ServerConf::LCSERVER_GROUP;
			$hostObj = $module->xpath ( 'instances/instance/host' );
			$hostObj [0] [0] = $host;
			$module [0]->port = $port;
		}

		$config->asXML ( ServerConf::PHPPROXY_CONFIG . '.new' );

		echo "done\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
