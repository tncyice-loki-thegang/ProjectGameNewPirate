<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Festival.class.php 31113 2012-11-15 10:57:45Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/festival/Festival.class.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2012-11-15 18:57:45 +0800 (四, 2012-11-15) $
 * @version $Revision: 31113 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : Festival
 * Description : 节日活动对外接口实现类
 * Inherit     : Festival
 **********************************************************************************************************************/
class Festival implements IFestival
{
    private $uid;

    /* 
	 * 构造函数
	 */
    public function __construct()
    {
    	$this->uid = RPCContext::getInstance()->getUid();
    }

	/* (non-PHPdoc)
	 * @see IFestival::getFestivalUserInfo()
	 */
	public function getFestivalUserInfo()
	{
		Logger::debug('Festival::getFestivalUserInfo start.');
		// 是否是节日活动
		$ret = FestivalLogic::checkFestivalDate(Util::getTime());
		if (Empty($ret))
		{
			Logger::debug('today is not festival');
			return false;
		}

		// 翻牌活动是否开启
		if($ret[FestivalDef::FESTIVAL_FLOPCARD_ONOFF] == 1)
		{
			// 取得用户节日信息
			$info = FestivalLogic::getFestival($this->uid, $ret);
		}
		else 
		{
			Logger::debug('the flopcard game is off.');
			$info = array('times' => 0, 'point' => 0);
		}
		Logger::debug('Festival::getFestivalUserInfo end.');
		return $info;
	}

	/* (non-PHPdoc)
	 * @see IFestival::flopCards()
	 */
	public function flopCards($cardId)
	{
		Logger::debug('Festival::flopCards start.');
		// 是否是节日活动
		$ret = FestivalLogic::checkFestivalDate(Util::getTime());
		if (Empty($ret))
		{
			Logger::debug('today is not festival');
			return array('ret' => 'noFestival');
		}
		// 翻牌活动是否开启
		if($ret[FestivalDef::FESTIVAL_FLOPCARD_ONOFF] == 0)
		{
			Logger::debug('the flopcard game is off.');
			return array('ret' => 'noFestival');
		}

		// 翻牌次数检查
		$ret = FestivalLogic::checkFlopCardsTimes($this->uid, $ret);
		if (!$ret)
		{
			Logger::debug('no flopcard times');
			return array('ret' => 'noTimes');
		}

		// 随机牌取得,返回牌的信息和前端显示用的装备模板id
		$arrayCards = FestivalLogic::getRandCard($this->uid);
		// 翻牌次数+1
		FestivalLogic::updateFlopCardsResult($this->uid);
		// 奖励更新
		$bagInfo = FestivalLogic::updateFpCardsRetToUser($this->uid, 
										$arrayCards['cardInfo'][$cardId],
										$arrayCards['cardId'][$cardId]);
		Logger::debug('Festival::flopCards end.');
		return array('ret' => 'ok',
					 'cardsInfo' => $arrayCards['cardId'],
					 'bag' => $bagInfo);
	}

	/* (non-PHPdoc)
	 * @see IFestival::getExchangePoint()
	 */
	public function getExchangePoint()
	{
		Logger::debug('Festival::getExchangePoint start.');
		// 是否是节日活动，返回值是活动增益表的值
		$festivalMall = FestivalLogic::checkFestivalMallDate();
		if (Empty($festivalMall))
		{
			Logger::debug('today is not festival.');
			return 'noFestival';
		}
		$ret = FestivalLogic::getExchangePoint($festivalMall);
		Logger::debug('ret = %s', $ret);
		Logger::debug('Festival::getExchangePoint end.');
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see IFestival::exchangeItem()
	 */
	public function exchangeItem($exItemId)
	{
		Logger::debug('Festival::exchangeItem start.');
		if(EMPTY($exItemId))
		{
			Logger::warning('Err para:exItemId, %s!', $exItemId);
			return 'err';
		}
		
		// 是否是节日活动，返回值是活动增益表的值
		$festivalMall = FestivalLogic::checkFestivalMallDate();
		if (Empty($festivalMall))
		{
			Logger::debug('today is not festival.');
			return 'noFestival';
		}
		$ret = FestivalLogic::exchangeItem($exItemId, $festivalMall);
		Logger::debug('Festival::exchangeItem end.');
		return $ret;
	}
	
	public function getFeedbackUserInfo()
	{
		return array();
	}
	
	public function getAreadyBuyInfo()
	{
		return array();
	}
	
	public function buyCard()
	{
		return array();
	}
	
	function sellCards()
	{
		return array();
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */