<?php

interface ICrystal
{
	public function getInfo();
	
	public function summon();
	
	public function getResource();
	
	public function onClickLvUp($type);
	
	public function lvUp($type);
	
	public function lvUpByGold();
}
