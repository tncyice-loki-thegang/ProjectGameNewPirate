<?php

interface IGuildSkill
{
	public function getAllGuildTechLv();
	public function plusGuildTechLv($id);
	public function getBellyPurchaseTimes();	
	public function PurchaseTechPoint($type);
}