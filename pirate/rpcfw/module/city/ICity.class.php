<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ICity.class.php 16975 2012-03-21 02:55:32Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/city/ICity.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-03-21 10:55:32 +0800 (三, 2012-03-21) $
 * @version $Revision: 16975 $
 * @brief
 *
 **/

interface ICity
{
	/**
	 *
	 * 检测是否可以进入
	 *
	 * @param int $town_id
	 *
	 * @return boolean							目前return TRUE
	 */
	public function checkEnter($town_id);

	/**
	 *
	 * 进入城镇
	 *
	 * @param int $town_id						进入的城镇ID
	 * @param int $x							x坐标
	 * @param int $y							y坐标
	 *
	 * @return NULL
	 */
	public function enterTown($town_id, $x, $y);

	/**
	 *
	 * 离开城镇
	 *
	 * @return NULL
	 */
	public function leaveTown();

	/**
	 *
	 * 迁入城镇
	 *
	 * @param $town_id							迁入城镇的ID
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'movein_success':boolean			TRUE表示迁入成功
	 * 		'belly':int							当前的belly值,只有当迁入成功时存在
	 * }
	 * </code>
	 */
	public function moveInTown($town_id);

	/**
	 *
	 * 得到已经进入过的城镇列表
	 *
	 * @param NULL
	 *
	 * @return array(town_id)				已经进入过的城镇ID列表
	 */
	public function enterTownList();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */