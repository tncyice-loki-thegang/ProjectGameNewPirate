<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IElves.class.php 36712 2013-01-22 13:57:40Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/elves/IElves.class.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-01-22 21:57:40 +0800 (二, 2013-01-22) $
 * @version $Revision: 36712 $
 * @brief 
 *  
 **/

interface IElves
{
	/**
	 * 得到精灵信息
	 * @return array
	 * <code>
	 * model_level : 使用此等级的模型
	 * exp : 经验
	 * exp_compute_time : 经验计算时间
	 * va_elves : object(
	 * 'id' => end_time,
	 * )
	 * <code>
	 */
	public function get();

	/**
	 * 给$id 的精灵增加时间
	 * @param unknown_type $id
	 * @return 'ok'
	 */
	public function icsTime($id);
	
	/**
	 * 给所有的精灵增加时间
	 * @param $arrId 所有的id
	 * @return 'ok'
	 */
	public function icsAll($arrId);
	
	/**
	 * 使用此等级的模型
	 * @return ok
	 */
	public function setModelLevel($level);
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */