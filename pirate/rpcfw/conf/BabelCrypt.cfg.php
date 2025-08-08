<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: BabelCrypt.cfg.php 17906 2012-04-01 07:01:19Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-17-20/conf/BabelCrypt.cfg.php $
 * @author $Author: HongyuLan $(hoping@babeltime.com)
 * @date $Date: 2012-04-01 15:01:19 +0800 (Sun, 01 Apr 2012) $
 * @version $Revision: 17906 $
 * @brief 加解密相关的配置
 *
 **/
class BabelCryptConf
{

	const METHOD = 'des';

	const KEY = 'BabelTime777';

	const IV = '32210967';

	//验证登录时候用
	const PlayHashKey = "2012#B@belPir@te#0410";
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */