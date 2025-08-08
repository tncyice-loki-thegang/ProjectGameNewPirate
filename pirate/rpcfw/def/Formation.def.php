<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Formation.def.php 16414 2012-03-14 02:48:18Z HaopingBai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Formation.def.php $
 * @author $Author: HaopingBai $(lanhongyu@babeltime.com)
 * @date $Date: 2012-03-14 10:48:18 +0800 (三, 2012-03-14) $
 * @version $Revision: 16414 $
 * @brief 
 *  
 **/

class FormationDef
{
	public static $HERO_FORMATION_FIELDS = array(
    	'fid',
		'level',
		'hid1', 
		'hid2', 
		'hid3', 
		'hid4', 
		'hid5', 
		'hid6', 
		'hid7', 
		'hid8', 
		'hid9', 
		'layer'
    );
    
    public static $HERO_FORMATION_KEYS = array(
		'hid1', 
		'hid2', 
		'hid3', 
		'hid4', 
		'hid5', 
		'hid6', 
		'hid7', 
		'hid8', 
		'hid9' 
    );
	
	public static $EVOLUTION_ITEM_NEED_NUM = array(1,1,2,2,2,2,2,2,2,2,3,3,3,4,4,4,5,5,6,6,7,7,7,7,7,8,8,8,9,9,9,10,10,10,10);
	
	public static $EVOLUTION_EXPERIENCE_NEED_NUM = array(10000,10000,20000,20000,20000,20000,20000,20000,20000,20000,30000,30000,30000,40000,40000,40000,50000,50000,60000,60000,70000,70000,70000,80000,80000,80000,90000,90000,90000,100000,100000,100000,110000,110000,110000);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */