<?php

interface IAppleFactory
{
	public function getInfo();
	
	public function compose($item_temp_id);
}
