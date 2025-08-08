<?php

class GemMatrix implements IGemMatrix
{
	private $uid;
	
	public function __construct()
	{
		if (!EnSwitch::isOpen(SwitchDef::GEM_MATRIX))
        {
        	Logger::warning('gem_matrix switch is not open');
        	throw new Exception('fake');
        }
		$this->uid = RPCContext::getInstance()->getUid();
	}
	
	public function getInfo()
	{
		$info = GemMatrixLogic::getInfo($this->uid);
		$user = EnUser::getUserObj();
			
		$ret = array(
					'id' => $info['level'], 
					'score' => $info['score'], 
					'count' => $info['count'], 
					'gemexp' => $user->getGemExp(), 
					'elite' => $info['elite'],
					'matrix' => $info['va_info']['matrix'],
					'lucky' => $info['va_info']['lucky'],
		);		
		return $ret;
	}
	
	public function getScore()
	{
		$info = GemMatrixLogic::getInfo($this->uid);
		$ret = array('score' => $info['score'], 'elite' => $info['elite']);
		return $ret;
	}
	
	public function explode($type, $pos)
	{		
		return GemMatrixLogic::explode($this->uid, $type, $pos);
	}
	
	public function levelUp()
	{
		return GemMatrixLogic::levelUp($this->uid);
	}
}
