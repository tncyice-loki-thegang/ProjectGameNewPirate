<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SortByFieldFunc.class.php 23277 2012-07-05 05:05:41Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/SortByFieldFunc.class.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-07-05 13:05:41 +0800 (四, 2012-07-05) $
 * @version $Revision: 23277 $
 * @brief 
 *  
 **/


/**
 * 
 * 根据数组的字段排序
usage: 
$arrRet = array(array('uid'=>7, 'upgrade_time'=>3, 'level'=>5),
				array('uid'=>2, 'upgrade_time'=>3, 'level'=>5),
				array('uid'=>8, 'upgrade_time'=>3, 'level'=>5),
				array('uid'=>3, 'upgrade_time'=>1, 'level'=>5));

$objCmp = new SortByFieldFunc(array('upgrade_time'=>1, 'uid'=>1));
$res = usort($arrRet, array($objCmp, 'cmp'));
//var_dump($res);
var_dump($arrRet);
 
 * Enter description here ...
 * @author idyll
 *
 */

class SortByFieldFunc
{
	//array(field => 1:升序, field=>-1 : 降序)
	//
	private $arrField = null;
	
	const ASC = 1;
	const DESC = -1;
	
	
	public function __construct($arrField)
	{
		$this->arrField = $arrField;
	}

	public function cmp($a, $b)
	{
		foreach($this->arrField as $fieldName => $asc)
		{
			if ($a[$fieldName] > $b[$fieldName])
			{
				return $asc;
			}
			else if ($a[$fieldName] < $b[$fieldName])
			{
				return -$asc;				
			}
			//== continue;
		}
		//equal
		return 0;		
	}
}



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */