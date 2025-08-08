<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Astrolabe.class.php 39846 2013-03-04 10:47:57Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/Astrolabe.class.php $
 * @author $Author: HongyuLan $(yangwenhai@babeltime.com)
 * @date $Date: 2013-03-04 18:47:57 +0800 (一, 2013-03-04) $
 * @version $Revision: 39846 $
 * @brief 
 *  
 **/


class Astrolabe implements IAstrolabe
{
	private $m_uid;
	public function __construct()
	{
		$this->m_uid = RPCContext::getInstance()->getUid();
		if ( empty($this->m_uid) )
		{
			Logger::FATAL('Astrolabe.__construct invalid uid:%d', $this->m_uid);
			throw new Exception('fake');
		}
	}
	
	/*
	 *前端请求初始化信息 :
	 *获得主星盘等级、
	 *当前装备的天赋星盘id、
	 *该天赋星盘内星座等级 、
	 *星灵石数量、
	 *贝里购买灵石剩余次数、
	 *vip购买灵石剩余次数、
	 *各个天赋星盘的状态
	 *普通技能状态
	 */
	public function askInitInfo ()
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askInitInfo err astrolabe is not open');
			throw new Exception('fake');
		}
		
		$retary=array();$exptime =0;
		//获得主星盘等级和主星盘增加的属性信息
		$valbasast=AstrolabeLogic::getBasicAstInfo($this->m_uid);
		if (empty($valbasast))
		{
			//如果为空，表明是玩家第一次打开界面，则进行初始化的操作
		    $retary=AstrolabeLogic::initAstInfo($this->m_uid);
		}
		else 
		{
			//主星盘等级
			$level=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL];
			$retary[AstrolabeDef::ASTROLABE_RET_BASAST_LEV ]=$level;
			
			//获得各个天赋星盘的状态（未激活、可以激活、已经激活），即检查星序表的第一个星的状态
			$retstat=AstrolabeLogic::getcurAllAstStatus($this->m_uid);
			$retary[AstrolabeDef::ASTROLABE_RET_AST_STATUS]=$retstat;
			
			//策划是不是更新了天赋星盘表
			AstrolabeLogic::checkIsUpdateTalentAstTable($this->m_uid,$retstat);
			
			//上次领取经验的时间
			$exptime=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME];
		}
		
		//获得当前装备的天赋星盘id，普通技能状态、以及该星盘内星座的等级、状态
		$retidskill=AstrolabeLogic::getCurTalentAstInfo($this->m_uid);
		
		//普通技能状态
		$skillstat=$retidskill[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS];
		$retary[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS ]=$skillstat;
		
		//当前装备的天赋星盘id
		$retTalentId=$retidskill[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID];
		$retary[AstrolabeDef::ASTROLABE_RET_TALAST_ID ]=$retTalentId;
		
		//当前装备的天赋星盘内的各个星座等级、状态
		$retdata=AstrolabeLogic::getConsLevStatIncurTalentAst($this->m_uid,$retTalentId);
		$retary[AstrolabeDef::ASTROLABE_RET_CONS_STATUS ]=$retdata[AstrolabeDef::ASTROLABE_RET_CONS_STATUS];
		$retary[AstrolabeDef::ASTROLABE_RET_CONS_LEV ]=$retdata[AstrolabeDef::ASTROLABE_RET_CONS_LEV];;
		
		//星灵石相关信息
		$retstone=AstrolabeLogic::updateStoneCountInfo($this->m_uid);
		$stonenum=$retstone[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM];
		$bellycount=$retstone[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT];
		$vipcount=$retstone[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT];
		
		$stone=AstrolabeLogic::getStoneInfo($this->m_uid);
		$retary[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM]=$stonenum;
		$retary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT]=$bellycount;
		$retary[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT]=$vipcount;
		
		//能不能领取经验（每天玩家可以领一次经验）
		$retary['can_get_exp']=0;
		$mainastlevel=$retary[AstrolabeDef::ASTROLABE_RET_BASAST_LEV ] ;
		if (($exptime > 0 && !Util::isSameDay($exptime))|| $exptime==0)
		{
			$retary['can_get_exp']=1;
		}
		
		return  $retary;
	}

	/*
	 * 升级主星盘
	 */
	public function askLevelUpMain ()
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askLevelUpMain err astrolabe is not open');
			throw new Exception('fake');
		}
		
		$retAry=array();
		$user = EnUser::getUserObj();
		//先获取玩家的星灵石
		$stone=AstrolabeLogic::getStoneInfo($this->m_uid);
		$stonNum=$stone[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM];

		//先获得当前主星盘的等级
		$valbasast=AstrolabeLogic::getBasicAstInfo($this->m_uid);
		$level=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL];
		$curlevelupexp=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ALL_EXP];
	
		//去主星序表里找对应的星座id
		$stage=ceil (($level+1)/AstrolabeDef::ASTROLABE_AST_MAIN_STAGE );
		$index=($level)%AstrolabeDef::ASTROLABE_AST_MAIN_STAGE ;//数组下标从0开始 (从0升到1 找第一个，从10升到11找第11个)
		
		//是不是已经升到最高级了
		$arytraymain=btstore_get()->ASTROLABE_TRAYMAIN;
		if ($stage > count($arytraymain))
		{
			Logger::debug('askLevelUpMain reach max stage err curstage:%s',$stage);
			return $retAry;
		}
		$trayids=$arytraymain[$stage];
		if (($index+1) > count($trayids))
		{
			Logger::debug('askLevelUpMain reach max index err! curlevel:%s',$level);
			return $retAry;
		}
		$consid=intval($trayids[$index]);
		if (empty($consid))
		{
			Logger::debug('askLevelUpMain consid err');
			return $retAry;
		}
		//通过该星座找到对应的限制条件,并进行检查（星灵石够不够，主角等级够不够）
		++$level;$needexp=0;
		$uerlevel=$user->getMasterHeroLevel();
		if (!AstrolabeLogic::checkLevelUpCondition($this->m_uid,$consid,$stonNum,$level,$uerlevel,$needexp))
		{
			return $retAry;
		}
		$exp= ($stonNum-$needexp);
		$exp=$exp< 0?0:$exp;
		$curlevelupexp=$curlevelupexp+$needexp;
		
		//条件检查通过，升级
		if ( !AstrolabeLogic::levelUpMainAst($this->m_uid,$level,$exp,$curlevelupexp))
		{
			Logger::debug('askLevelUpMain update err');
			return  $retAry;
		}
		
		$retAry['stone_num']=$exp;
		$retAry['basic_ast_lev']=$level;
		
		//升级后可能会触发其他星座的激活条件，这里将这些状态返回
		$ret=AstrolabeLogic::getAstConsStatusByLevulUp($this->m_uid,0);
		$retAry['activeTray']=$ret['activeTray'];
		$retAry['activeStart']=$ret['activeStart'];
		
		//更新相应的天赋星盘状态(其实没啥用，星盘的状态是由星盘第一个星座的状态决定的)
		//AstrolabeLogic::updateTalentAstStatus($this->m_uid, $ret['activeTray']);
		
		//今天能不能领取经验
		$retAry['can_get_exp']=0;
		$exptime=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME];
		if (($exptime > 0 && !Util::isSameDay($exptime))|| $exptime==0 )
		{
			$retAry['can_get_exp']=1;
		}
		
		//主星盘升级后通知任务系统
		TaskNotify::operate(TaskOperateType::AST_LEVELUP);
		
		//战斗优化
		EnUser::modifyBattleInfo();
		
		//var_dump($retAry);
		return $retAry;
	}
	
	
	/*
	 * 升级天赋星座
	 */
	public function askLevelUpCons ($consid)
	{
		
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askLevelUpCons err astrolabe is not open');
			throw new Exception('fake');
		}
		
		$retAry=array();
		$user = EnUser::getUserObj();
		
		//是不是天赋星座
		$ismain=intval(btstore_get()->ASTROLABE_STARS[$consid]['isMain']);
		if ($ismain)
		{
			Logger::warning('askLevelUpCons ismain err uid:%s consid:%s',$this->m_uid,$consid);
			return $retAry;
		}
		
	    //该星座是不是被激活了,如果可以激活则激活
		$retdata=AstrolabeLogic::getConsStatus($this->m_uid,array($consid));
		if ($retdata[$consid] == AstrolabeDef::ASTROLABE_CONSTE_STATUS_CAN_ACTIVE)
		{
			AstrolabeLogic::activeTalentCons($this->m_uid,$consid);
			$retdata[$consid]= AstrolabeDef::ASTROLABE_CONSTE_STATUS_OK_ACTIVE;
		}
		if ($retdata[$consid] != AstrolabeDef::ASTROLABE_CONSTE_STATUS_OK_ACTIVE)
		{
			Logger::warning('askLevelUpCons isnot active uid:%s consid:%s curstat:%s',$this->m_uid,$consid,$retdata[$consid]);
			return $retAry;
		}
			
		//获得该星座当前等级
		$retConsLevel=AstrolabeLogic::getConsLevel($this->m_uid,array($consid));
		$level=$retConsLevel[$consid];
		
		//是不是达到等级上限了
		$maxlevel=intval(btstore_get()->ASTROLABE_STARS[$consid]['astLevelLimit']);
		if ($level >= $maxlevel)
		{
			Logger::debug('askLevelUpCons LevelLimit err uid:%s consid:%s curlevel:%s maxlevel:%s',$this->m_uid,$consid,$level,$maxlevel);
			return $retAry;
		}
		
		//获取玩家的星灵石
		$stone=AstrolabeLogic::getStoneInfo($this->m_uid);
		$stonNum=$stone[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM];
		
		//通过该星座找到对应的限制条件,并进行检查（星灵石够不够，主角等级够不够）
		++$level;$needexp=0;
		$uerlevel=$user->getMasterHeroLevel();
		if (!AstrolabeLogic::checkLevelUpCondition($this->m_uid,$consid,$stonNum,$level,$uerlevel,$needexp))
		{
			Logger::debug('askLevelUpCons check condition err uid:%s consid:%s stonNum:%s curlevel:%s',$this->m_uid,$consid,$stonNum,$level);
			return $retAry;
		}
		//获得当前所有升级总经验
		$curlevelupexp=AstrolabeLogic::getConsCurLevlUpExp($this->m_uid,$consid);
		
		//条件检查通过，升级
		$exp= ($stonNum-$needexp);
		$exp=$exp< 0?0:$exp;
		$curlevelupexp=$curlevelupexp+$needexp;
		if ( !AstrolabeLogic::LevelUpTalentCons($this->m_uid,$consid,$level,$exp,$curlevelupexp))
		{
			Logger::debug('askLevelUpCons levelup err uid:%s consid:%s',$this->m_uid,$consid);
			return  $retAry;
		}
		
		$retAry['stone_num']=$exp;
		$retAry['newLevel']=$level;
		
		//升级后可能会触发其他星座的激活条件，这里将这些状态返回
		$ret=AstrolabeLogic::getAstConsStatusByLevulUp($this->m_uid,$consid);
		$retAry['activeTray']=$ret['activeTray'];
		$retAry['activeStart']=$ret['activeStart'];
		
		//更新相应的天赋星盘状态(其实没啥用，星盘的状态是由星盘第一个星座的状态决定的)
		//AstrolabeLogic::updateTalentAstStatus($this->m_uid, $ret['activeTray']);
		
		//战斗优化
		EnUser::modifyBattleInfo();
		
		//升级后检查有没有普通技能被激活
		$retskill=AstrolabeLogic::checkSkillStatusByLevulUp($this->m_uid,$consid,$level);
		$retAry['skill_stat']=$retskill;
		//var_dump($retAry);
		return  $retAry;
	}
	
	/*
	 * 请求切换天赋星盘
	 */
	public function askSwitchTalentAst ($astid)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askSwitchTalentAst err astrolabe is not open');
			throw new Exception('fake');
		}
		
		//检查该星盘的状态，如果可以激活，则激活
		$ret=AstrolabeLogic::getTalentAstInfo($this->m_uid,$astid);
		$status=$ret[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS];
		if ($status==AstrolabeDef::ASTROLABE_AST_STATUS_CAN_ACTIVE)
		{
			AstrolabeLogic::activeTalentAst($this->m_uid,$astid);
		}
		//由星盘id找到该星盘下的所有星座的状态、等级
		$conids=array();$ConsStat=array();$retConsLevel=array();$aryids=array();
		$tmpids=btstore_get()->ASTROLABE_TRAYDOWNER[$astid];
		foreach ($tmpids as $id)
		{
			$aryids[]=intval($id);
		}
		if (!empty($aryids))
		{
			$ConsStat=AstrolabeLogic::getConsStatus($this->m_uid, $aryids);
			$retConsLevel=AstrolabeLogic::getConsLevel($this->m_uid, $aryids);
		}
		//天赋星盘的状态由该星盘第一个星座的状态决定，看看能不能激活
		$baginfo=array();
		if ($ConsStat[$tmpids[0]] >= AstrolabeDef::ASTROLABE_CONSTE_STATUS_CAN_ACTIVE &&
			$status<=AstrolabeDef::ASTROLABE_AST_STATUS_CAN_ACTIVE)
		{
			//增加条件检查，扣除物品
			$consid=$tmpids[0];
			$bag = BagManager::getInstance()->getBag();
			$items=btstore_get()->ASTROLABE_STARS[$consid]['costitems'];
			foreach ($items as $itemid=>$itemcount)
			{
				if ($itemid > 0 && $itemcount > 0 )
				{
					if(!$bag->deleteItembyTemplateID($itemid, $itemcount))
					{
						Logger::warning('askSwitchTalentAst err, item:%d num:%d consid:%d', $itemid,$itemcount,$consid);
						throw new Exception('fake');
					}
				}
			}
			$baginfo=$bag->update();
			AstrolabeLogic::activeTalentAst($this->m_uid,$astid);
		}
		
		//按策划要求，现在只是自动激活第一颗星，其他的星的激活放到升级操作里
		if ($ConsStat[$tmpids[0]] == AstrolabeDef::ASTROLABE_CONSTE_STATUS_CAN_ACTIVE)
		{
			AstrolabeLogic::activeTalentCons($this->m_uid,$tmpids[0]);
			$ConsStat[$tmpids[0]]=AstrolabeDef::ASTROLABE_CONSTE_STATUS_OK_ACTIVE;
		}
		//如果这些星座的状态可以激活，则激活
		/*foreach ($ConsStat as $id => $stat)
		{
			if ($stat==AstrolabeDef::ASTROLABE_CONSTE_STATUS_CAN_ACTIVE)
			{
				AstrolabeLogic::activeTalentCons($this->m_uid,$id);
				$ConsStat[$id]=AstrolabeDef::ASTROLABE_CONSTE_STATUS_OK_ACTIVE;
			}
		}
		*/
		$retary[AstrolabeDef::ASTROLABE_RET_CONS_STATUS]=$ConsStat;
		$retary[AstrolabeDef::ASTROLABE_RET_CONS_LEV ]=$retConsLevel;
		
		//普通技能状态
		$retary['skill_stat']=$ret[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS];
		//背包状态
		$retary['baginfo']=$baginfo;
		
		return $retary;
	}
	
	/*
	 * 装备天赋星盘
	*/
	public function askEquipTalentAst ($astid)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askEquipTalentAst err astrolabe is not open');
			throw new Exception('fake');
		}
		
		$retAry=array();
		//检查该星盘当前的状态，只有处于激活状态的天赋星盘才能装备上
		$ret=AstrolabeLogic::getTalentAstInfo($this->m_uid,$astid);
		$status=$ret[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS];
		if ($status!=AstrolabeDef::ASTROLABE_AST_STATUS_OK_ACTIVE)
		{
			Logger::warning('askEquipTalentAst status err uid:%s stat:%s',$this->m_uid,$status);
			return  $retAry;
		}
		$retidskill=AstrolabeLogic::getCurTalentAstInfo($this->m_uid);
	    $curid=$retidskill[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID];
	    if ($curid==$astid)
	    {
	    	Logger::warning('askEquipTalentAst curid == astid err uid:% $astid:%s',$this->m_uid,$astid);
	    	return  $retAry;
	    }
	    //更改当前装备id
		if ($curid > 0 && $curid != $astid)
		{
			$stat=AstrolabeDef::ASTROLABE_AST_STATUS_OK_ACTIVE;
			AstrolabeLogic::setTalentAstEquipInfo($this->m_uid,$curid,$stat);
		}
		//把新id装备上
		if ($curid != $astid )
		{
			$stat=AstrolabeDef::ASTROLABE_AST_STATUS_EQUIPED;
			AstrolabeLogic::setTalentAstEquipInfo($this->m_uid,$astid,$stat);
		}
		
		//战斗优化
		EnUser::modifyBattleInfo();
		
		//返回值
		$retAry['talent_ast_id']=$astid;
		$retAry['skill_stat']=$ret[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS];
		
		//var_dump($retAry);
		return $retAry;
	}
	
	/*
	 * 卸下天赋星盘
	*/
	public function askUnEquipTalentAst ($astid)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askUnEquipTalentAst err astrolabe is not open');
			throw new Exception('fake');
		}
		$ret=AstrolabeLogic::getTalentAstInfo($this->m_uid,$astid);
		$status=$ret[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_STATUS];
		if ($status!=AstrolabeDef::ASTROLABE_AST_STATUS_EQUIPED)
		{
			Logger::debug('askUnEquipTalentAst status err uid:%s status:%s',$this->m_uid,$status);
			return ;
		}
		$stat=AstrolabeDef::ASTROLABE_AST_STATUS_OK_ACTIVE;
		AstrolabeLogic::setTalentAstEquipInfo($this->m_uid,$astid,$stat);
		
		//战斗优化
		EnUser::modifyBattleInfo();
		
	}
	
	/*
	 * 请求买星灵石
	*/
	public function askBuyStone ($type)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askBuyStone err astrolabe is not open');
			throw new Exception('fake');
		}
        $retary=array();$blflag=true;
		$retAry=AstrolabeLogic::updateStoneCountInfo($this->m_uid);		
		$retnum=$retAry[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM];
		$bellycount=$retAry[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT];
		$vipcount=$retAry[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT];
		
		AstrolabeLogic::buyStone($this->m_uid,$type,$retnum,$bellycount,$vipcount,$blflag);
		
		$retary[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM]=$retnum;
		$retary[AstrolabeDef::ASTROLABE_SQL_FIELD_BELLY_COUNT]=$bellycount;
		$retary[AstrolabeDef::ASTROLABE_SQL_FIELD_VIP_COUNT]=$vipcount;
		
		//和前端约定，如果失败返回null
		if ($blflag)return $retary;
	}
	
	/*
	 * 请求获取今天的经验
	*/
	public function askObtainTodayExp()
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE))
		{
			Logger::warning('Astrolabe.askObtainTodayExp err astrolabe is not open');
			throw new Exception('fake');
		}
		
		$retary=array();
		$valbasast=AstrolabeLogic::getBasicAstInfo($this->m_uid);
		if (empty($valbasast))
		{
			return $retary;
		}
		$mainlevel=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_LEVEL];
		$exptime=$valbasast[0][AstrolabeDef::ASTROLABE_SQL_FIELD_AST_EXP_TIME];
		//是不是可以领取
		if ( $exptime > 0 && Util::isSameDay($exptime) )
		{
			return $retary;
		}
	
		//按策划要求，增加经验的公式为INT【 【1014000* ln(200+主星盘等级)-2470*(200+主星盘等级) +499070-1014000*ln(201)】/10】*10 
		//INT【 【1228000* ln(200+主星盘等级)-2990*(200+主星盘等级) +604140-1228000*ln(201)】/10】*10
		$userObj = EnUser::getUserObj($this->m_uid);
		$userlevel = $userObj->getMasterHeroLevel();
		$hero = $userObj->getMasterHeroObj();
		$val=0;
		//原来的公式加个判断，当主星盘等级大于等于200的时候经验=253200+ (主星盘等级-200)*80
		if ($mainlevel>=200)
		{
			$val=253200+($mainlevel-200)*80;
		}
		else
		{
			$val=intval(( 1228000*log(200+$mainlevel)-2990*(200+$mainlevel)+604140-1228000*log(201) )/10);
			$val=$val*10;
		}
		$exp= $val;
		$exp=($exp< 0 )?0:$exp;
		if ( !AstrolabeLogic::updateAstExpTime($this->m_uid))
		{
			return $retary;
		}
		//增加经验
		$userObj->addExp($exp);
		$userObj->update ();
		$curlevel=$userObj->getMasterHeroLevel();
		$retary['curuserlevel']=$curlevel;
		$retary['add_exp']= $exp;//增加的经验
		$retary['cur_exp']= $hero->getExp();//当前的经验，因为增加经验后可能会升级，前端没法没法自己算。
	
		//领取星盘祝福的时候，计入活跃度次数
		EnActive::addAstroAbeExp();
		
		EnFestival::addAstroPoint();
		
		return $retary;
	}
	
	/*
	 * 获得星盘功能当前的主星盘属性加成,返回值为array(属性id=>属性值)
	*/
	public static function getCurMainAstAttr ($uid)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE,$uid))
		{
			return array();
		}
		return AstrolabeLogic::getCurMainAstAttr($uid);
	}
	
	/*
	 * 获得星盘功能当前的天赋盘属性加成,返回值为array(属性id=>属性值)
	 * 
	*/
	public static function getCurTalentAstAttr ($uid)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE,$uid))
		{
			return array();
		}
		return AstrolabeLogic::getCurTalentAstAttr($uid);
	}
	
	/*
	 * 获得星盘功能当前的普通技能id,如果没有则为0
	*/
	public static function getCurCommonSkillId ($uid)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE,$uid))
		{
			return 0;
		}
		//先获得当前装备的天赋星盘id，以及该星盘的状态，对应普通技能的状态
		$retidskill=AstrolabeLogic::getCurTalentAstInfo($uid);
		$astid=$retidskill[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID];
		$skillstat=$retidskill[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_SKILL_STATUS];
		//返回该状态
		if ($astid > 0 && isset(btstore_get()->ASTROLABE_STARGIFT[$astid]) &&
			$skillstat==AstrolabeDef::ASTROLABE_AST_SKILL_STATUS_OK_ACTIVE ) 
		{
			$skillid=intval(btstore_get()->ASTROLABE_STARGIFT[$astid]['normalatt']);
			return $skillid;
		}
		return 0;
	}
	/**
	 * 指定一个uid，返回该uid当前装备的天赋星盘id（注意，是已经装备的天赋星盘id）
	 * @param int $uid
	 * @return int   0获取失败，或者当前没有，
	 */
	public static function getCurTalentAstId ($uid)
	{
		//星盘功能是不是开启了
		if (!EnSwitch::isOpen(SwitchDef::ASTROLABE,$uid))
		{
			return 0;
		}
		//先获得当前装备的天赋星盘id，以及该星盘的状态，对应普通技能的状态
		$retidskill=AstrolabeLogic::getCurTalentAstInfo($uid);
		$astid=$retidskill[AstrolabeDef::ASTROLABE_SQL_FIELD_AST_ID];
		return $astid;
	}
	
	
	/*
	 * 获得玩家当前的星灵石个数
	 */
	public static function getCurStone($uid)
	{
		$stone=AstrolabeLogic::getStoneInfo($uid);
		$stonNum=$stone[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM];
		return $stonNum;
	}
	
	/*
	 * 给某个玩家增加星灵石，如果$stoneNum < 0 则为减
	*/
	public static function addStone($uid,$stoneNum)
	{
		$stoneNum = intval($stoneNum);
		
		//有可能数据库里没这个玩家的记录，看需不需要做下初始化
		$retAry=AstrolabeLogic::updateStoneCountInfo($uid);		
		$curnum=$retAry[AstrolabeDef::ASTROLABE_SQL_FIELD_STONE_NUM];
		$num=$stoneNum+$curnum;
		$num= ($num<0)?0:$num;
		
		return AstrolabeLogic::addStone($uid,$num);
	}
	
	/*
	 * 将各个星盘的、星座的等级将为初始值，并将经验返回给玩家
	*/
	public  function  resetTalentAst($astid,$costid)
	{
		//是不是天赋星座
		if(!isset(btstore_get()->ASTROLABE_TRAYDOWNER[$astid]))
		{
			Logger::fatal('Astrolabe.resetTalentAst err astid:%d',$astid);
			return array();
		}
		$cost=null;
		switch ($costid)
		{
			case 1:
				$cost='reset_cost1';
				break;
			case 2:
				$cost='reset_cost2';
				break;
			case 3:
				$cost='reset_cost3';
				break;
			default:
				$cost=null;
				break;
		}
		if ($cost==null)
		{
			Logger::fatal('Astrolabe.resetTalentAst err costid:%d',$costid);
			return array();
		}
		if(!isset(btstore_get()->ASTROLABE_STONE[$cost]))
		{
			Logger::fatal('Astrolabe.resetTalentAst err cost:%d',$cost);
			return array();
		}
		
		$ret =AstrolabeLogic::resetTalentAst($this->m_uid,$astid,$cost);
		if (empty($ret))return $ret;
		
		//再次检测该星盘内所有星星的状态
		$consids=array();
		$ids = btstore_get()->ASTROLABE_TRAYDOWNER[$astid];
		foreach ($ids as $val)
		{
				$consids[]=intval($val);
		}
		$retConsStat=AstrolabeLogic::getConsStatus($this->m_uid, $consids);
		$ret['consstatus']=$retConsStat;
		return $ret;
	}
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */