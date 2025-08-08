<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: IFormation.class.php 14113 2012-02-17 06:51:49Z YangLiu $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/formation/IFormation.class.php $
 * @author $Author: YangLiu $(lanhongyu@babeltime.com)
 * @date $Date: 2012-02-17 14:51:49 +0800 (五, 2012-02-17) $
 * @version $Revision: 14113 $
 * @brief 
 *  
 **/

interface IFormation
{
	/**
	 * 得到用户的所有阵型
	 * @return array
	 * <code>{
	 * array{
	 * fid => 
	 * array{
	 * 'fid' : '阵型id',
	 * 'level': 等级
	 * 'hid1' : '此位置的英雄id',
	 * 'hid2' : '此位置的英雄id',
	 * 'hid3' : '此位置的英雄id',
	 * 'hid4' : '此位置的英雄id',
	 * 'hid5' : '此位置的英雄id',
	 * 'hid6' : '此位置的英雄id',
	 * 'hid7' : '此位置的英雄id',
	 * 'hid8' : '此位置的英雄id',
	 * 'hid9' : '此位置的英雄id'
	 * }
	 * }
	 * }</code>
	 */
	public function getAllFormation();

	/**
	 * 得到阵型信息
	 * @param int $fid							阵型ID
	 * 
	 * @return array
	 * <code>{
	 * fid => 
	 * array{
	 * 'fid' : '阵型id',
	 * 'level': 等级
	 * 'hid1' : '此位置的英雄id',
	 * 'hid2' : '此位置的英雄id',
	 * 'hid3' : '此位置的英雄id',
	 * 'hid4' : '此位置的英雄id',
	 * 'hid5' : '此位置的英雄id',
	 * 'hid6' : '此位置的英雄id',
	 * 'hid7' : '此位置的英雄id',
	 * 'hid8' : '此位置的英雄id',
	 * 'hid9' : '此位置的英雄id'
	 * }
	 * }</code>
	 */
	public function getFormationInfoByID($fid);

	/**
	 * 保存用户阵型
	 * @param int $fid							阵型ID
	 * @param array $formation					新更换的阵型信息
	 * <code>{
	 * array{
	 * 'hid1' : '此位置的英雄id',
	 * 'hid2' : '此位置的英雄id',
	 * 'hid3' : '此位置的英雄id',
	 * 'hid4' : '此位置的英雄id',
	 * 'hid5' : '此位置的英雄id',
	 * 'hid6' : '此位置的英雄id',
	 * 'hid7' : '此位置的英雄id',
	 * 'hid8' : '此位置的英雄id',
	 * 'hid9' : '此位置的英雄id'
	 * }
	 * }</code>
	 * @return
	 */
	public function changeCurFormation($fid, $formation);

	/**
	 * 使用此阵型作为默认阵型
	 * @param int $fid							阵型ID
	 * @param array $formation					新更换的阵型信息
	 * @return  string 
	 * 'ok'
	 * other : err
	 */
	public function setCurFormation($fid, $formation);

	/**
	 * 升级阵型
	 * @param int $fid							阵型ID
	 * @return string
	 * 'err' : 升级失败
	 * 'ok' ：成功
	 */
	public function plusFormationLv($fid);

	/**
	 * 获取阵型能力
	 * @param int $fid							阵型ID
	 * @param array $formation					新更换的阵型信息
	 * <code>{
	 * array{
	 * 属性ID => 加成值
	 * }
	 * }</code>
	 */
	public function getFormationAttr($fid);
	
	public function evolution($fid);
	
	public function refreshAttr($fid);
	// commitAttr
	// evolution
	// getFormationBench
	// getCdEndTime
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */