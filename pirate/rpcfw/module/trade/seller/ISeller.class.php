<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: ISeller.class.php 5027 2011-09-20 02:22:43Z HaopingBai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/trade/seller/ISeller.class.php $
 * @author $Author: HaopingBai $(jhd@babeltime.com)
 * @date $Date: 2011-09-20 10:22:43 +0800 (二, 2011-09-20) $
 * @version $Revision: 5027 $
 * @brief
 *
 **/

interface ISeller
{
	/**
	 *
	 * 购买
	 * @param int $seller_id
	 * @param int $item_template_id
	 * @param int $item_num
	 *
	 * @return array
	 */
	public function buy($seller_id, $item_template_id, $item_num);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */