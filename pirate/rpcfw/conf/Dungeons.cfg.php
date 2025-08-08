<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Dungeons.cfg.php 16403 2012-03-14 02:37:05Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/conf/Dungeons.cfg.php $
 * @author $Author: HaopingBai $(dh0000@babeltime.com)
 * @date $Date: 2012-03-14 10:37:05 +0800 (三, 2012-03-14) $
 * @version $Revision: 16403 $
 * @brief
 *
 **/
class DungeonsConfig
{
	//table name
	const DB_USER_DUNGEONS = 't_user_dungeons';
	const DB_DUNGEONS = 't_dungeons';
	//副本信息
	static $DungeonsInfo	=	array	(
        '11010'=>array(
            'name'=>'美索不达米亚之森',
            'intro'=>'介绍',
            'levelRequire'=>5,
            'types'=>array(1,2),
            'pointIds'=>array(11011),
            'backAreaId'=>11000,
        ),
        '12010'=>array(
            'name'=>'底格里斯之源',
            'intro'=>'介绍',
            'levelRequire'=>20,
            'types'=>array(1,2),
            'pointIds'=>array(12011,12012),
            'backAreaId'=>12000,
        ),
        '13010'=>array(
            'name'=>'幼发拉底河',
            'intro'=>'介绍',
            'levelRequire'=>35,
            'types'=>array(1,2),
            'pointIds'=>array(13011,13012),
            'backAreaId'=>13000,
        ),
        '14010'=>array(
            'name'=>'伊甸园禁地',
            'intro'=>'介绍',
            'levelRequire'=>50,
            'types'=>array(1,2),
            'pointIds'=>array(14011,14012,14013),
            'backAreaId'=>14000,
        ),
        '15010'=>array(
            'name'=>'神秘金字塔',
            'intro'=>'介绍',
            'levelRequire'=>60,
            'types'=>array(1,2),
            'pointIds'=>array(15011,15012,15013),
            'backAreaId'=>15000,
        ),
        '16010'=>array(
            'name'=>'空中花园遗迹',
            'intro'=>'介绍',
            'levelRequire'=>70,
            'types'=>array(1,2),
            'pointIds'=>array(16011,16012,16013,16014),
            'backAreaId'=>16000,
        ),
        '16020'=>array(
            'name'=>'汉莫拉比王宫',
            'intro'=>'介绍',
            'levelRequire'=>90,
            'types'=>array(1,2),
            'pointIds'=>array(16021,16022,16023,16024),
            'backAreaId'=>16000,
        ),
        '16030'=>array(
            'name'=>'马杜克神庙',
            'intro'=>'介绍',
            'levelRequire'=>105,
            'types'=>array(1,2),
            'pointIds'=>array(16031,16032,16033,16034),
            'backAreaId'=>16000,
        ),
	);
	//据点信息
	static $DungeonsPoints	=	array	(
		'11011'=>array(
			'name'=>'据点1',
			'intro'=>'据点1介绍',
			'monsters'=>array(1,2),
			'monstersExp'=>11111,
			'monstersH'=>array(1,2),
			'monstersHExp'=>22222,
			'hadMystic'=>1,
			'mysticBoss'=>array(1,2),
			'mysticProbability'=>10,//%
			'backAreaId'=>11000,
		),
	);
	//据点掉落表
	static $DungeonsPointsFallTable	=	array	(
		'11011'=>array(
            'allWeight'	=>	500,
            'newItems'	=>	 array(
                array(
                'name'	=>	'钥匙碎片',
                'bind'=>0,
                'id'	=>	50100,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片1',
                'bind'=>0,
                'id'	=>	50101,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片2',
                'bind'=>0,
                'id'	=>	50102,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片3',
                'bind'=>0,
                'id'	=>	50103,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片4',
                'bind'=>0,
                'id'	=>	50104,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片5',
                'bind'=>0,
                'id'	=>	50105,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片6',
                'bind'=>0,
                'id'	=>	50106,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片7',
                'bind'=>0,
                'id'	=>	50107,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片8',
                'bind'=>0,
                'id'	=>	50108,
                'num'	=>	1,
                'weight'	=>	50),
                array(
                'name'	=>	'碎片9',
                'bind'=>0,
                'id'	=>	50109,
                'num'	=>	1,
                'weight'	=>	50),
            ),
		),
	);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
