<?php

interface IDepositPlan
{
	public function getDepositPlanInfo();
	public function buyDepositPlan($id, $num);
	public function receivePrize($pos);
}
