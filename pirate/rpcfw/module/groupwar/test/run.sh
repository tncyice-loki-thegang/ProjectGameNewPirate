

function clear_log
{
	> /home/pirate/lcserver/log/lcserver.log
	> /home/pirate/lcserver/log/lcserver.log.wf
	> /home/pirate/rpcfw/log/rpc.log
	> /home/pirate/rpcfw/log/rpc.log.wf
	> /home/pirate/rpcfw/log/script.log
	> /home/pirate/rpcfw/log/script.log.wf
}



function op_create_battle()
{
	clear_log


	if [ -z $1 ];then
		startTime=`date +%s`
		startTime=`expr $startTime + 90`
		startTime=`date -d "1970-01-01 UTC $startTime  seconds"|awk '{print $5}'`
	else
		startTime=$1
	fi

	echo "first battle start time:$startTime"
	
	echo "refresh csv"
	cd ../scripts
	sed -i "s/,[0-9]*:[0-9]*:[0-9]*,/,$startTime,/" group_battle.csv 
	sed -n '3p' group_battle.csv
	
	php groupwar.script.php ./ ../../../data/btstore/
	php groupwarRank.script.php ./ ../../../data/btstore/
	cd -

	echo "restart php"
	cd ~/programs/php
	./run.sh 
	cd -


	echo "create battle"
	php /home/pirate/rpcfw/lib/ScriptRunner.php -f CreateGroupBattle.script.php 0 -g game102  -d pirate010
}

function op_test()
{
	
	clear_log
	php /home/pirate/rpcfw/lib/ScriptRunner.php -f TestGroupWar.php   -g game102  -d pirate002

}

function op_robot()
{
	clear_log	
	php /home/pirate/rpcfw/lib/ScriptRunner.php -f Robot.php   -g game102  -d pirate010  $1 $2 

}

function op_stop_robot()
{

	pid=`ps axu|grep ScriptRunner|grep -v grep |awk '{print $2" " }'` ;
	if [[ -z $pid ]]; then
		echo "robot not run"
	else
		echo "kill robot"
		kill -9 $pid
	fi
}

function op_to_robot()
{
	
	cd ../../../conf
	cp GroupWar.cfg.php.norm  GroupWar.cfg.php
	cd -

	cd ../scripts
	#cp group_battle_norm.csv group_battle.csv 
	php groupwar.script.php ./ ../../../data/btstore/
	php groupwarRank.script.php ./ ../../../data/btstore/
	cd -

	cd ~/programs/php
	./run.sh 
	cd -
}

function op_to_test()
{
	cd ../../../conf
	cp GroupWar.cfg.php.test  GroupWar.cfg.php
	cd -

	echo "refresh csv"
	cd ../scripts
	cp group_battle.csv.test group_battle.csv 
	php groupwar.script.php ./ ../../../data/btstore/
	php groupwarRank.script.php ./ ../../../data/btstore/
	cd -

	echo "restart php"
	cd ~/programs/php
	./run.sh 
	cd -
}

function op_usage()
{
	echo "./run.sh create 0 14:00:00";
	echo "./run.sh robot";
}


ROOT=`dirname $0`
op=$1







case $op in
		help)
		op_usage
		;;
        test)
        op_test
        ;;
        robot)
        op_robot $2 $3
        ;;
        sr)
        op_stop_robot
        ;;
		tr)
		op_to_robot 
		;;
		tt)
		op_to_test
		;;
		create)
		op_create_battle $2 
		;;
esac
