<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IFestival.class.php 31045 2012-11-14 08:27:05Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/IFestival.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-14 16:27:05 +0800 (三, 2012-11-14) $
 * @version $Revision: 31045 $
 * @brief 
 *  
 **/

/**********************************************************************************************************************
 * Class       : IFestival
 * Description : 节日活动对外接口实现类
 * Inherit     : IFestival
 **********************************************************************************************************************/
interface IFestival
{
	/**
	 * 获取用户的节日活动信息
	 * 
	 * @return array <code> : {
	 * times:翻牌次数,
	 * point:当前积分
	 * }
	 * </code>
	 */
	function getFestivalUserInfo();	

	/**
	 * 翻牌
	 * 
	 * @param int $cardId 选择的牌
	 * 
	 * @return array <code> : {
	 * ret:noFestival,									非节日
	 * 	   noTimes,										没有翻牌次数
	 *     ok											翻牌结果OK
	 * cardInfo:
	 * {
	 * 		card1:
	 * 			  {
	 *				  cardId:int						翻牌奖励表中的ID
	 *				  item_template_id:int            	物品模板ID
	 *				  item_num:int                    	物品数量
	 * 			  }
	 * 		card2:
	 * 			  {
 	 *				  cardId:int						翻牌奖励表中的ID
	 *				  item_template_id:int            	物品模板ID
	 *				  item_num:int                    	物品数量
	 * 			  }
	 * 		card3:
	 * 			  {
	 *				  cardId:int						翻牌奖励表中的ID
	 *				  item_template_id:int            	物品模板ID
	 *				  item_num:int                    	物品数量
	 * 			  }
	 * 		card4:
	 * 			  {
	 *				  cardId:int						翻牌奖励表中的ID
	 *				  item_template_id:int            	物品模板ID
	 *				  item_num:int                    	物品数量
	 * 			  }
	 * 		card5:
	 * 			  {
	 *				  cardId:int						翻牌奖励表中的ID
	 *				  item_template_id:int            	物品模板ID
	 *				  item_num:int                    	物品数量
	 * 			  }
 	 * },
	 * items
	 * {
	 * 		bag：@see IBag::receiveItem
	 * }
	 * }
	 * </code>
	 */
	function flopCards($cardId);

	/**
	 * 节日商城-获取当前积分
	 * 
	 * @return $point									当前用户积分
	 *     
	 */
	function getExchangePoint();
	
	/**
	 * 节日商城-物品兑换
	 * 
	 * @param int $exItemId								兑换的装备ID
	 * 
	 * @return array <code> : {
	 * ret:
	 *     ok											兑换成功
	 *     err											兑换失败
	 *     noFestival,									非节日
	 * 	   noPoint,										没有积分
	 * 
	 * items
	 * {
	 * 		bag：@see IBag::receiveItem
	 * }
	 */
	function exchangeItem($exItemId);
	
	function getFeedbackUserInfo();
	
	function getAreadyBuyInfo();
	
	function buyCard();
	
	function sellCards();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */