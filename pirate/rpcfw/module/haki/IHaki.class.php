<?php

interface IHaki
{
	public function gethakiInfo();
	
	public function hakiInfo();	
	
	public function trial($type);	
	
	public function allTrial($type);
		
	public function allGoldTrial($type);
		
	public function addProperty($htid, $propertys);
	
	public function convert($htid);
	
	// public function hakiReturn($htid, $hakiInfo);
	
	// public function levelupHakiScene();
	
	// public function notifyOpenUi();
}
