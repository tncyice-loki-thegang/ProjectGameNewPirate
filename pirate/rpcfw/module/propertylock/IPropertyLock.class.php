<?php

interface IPropertyLock
{
	public function getStatus();

	public function setStatus();
	
	public function initPassword($pass1, $pass2, $ques, $ans);
	
	public function unlock($pass, $type);
	
	public function questionReset($ques, $ans);
	
	public function reset($oldPass, $pass1, $pass2);
}
