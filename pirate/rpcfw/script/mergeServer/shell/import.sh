#!/bin/bash

USER=$1
PASSWORD=$2
HOST=$3
DBNAME=$4
FILE=$5

#导入数据表
MYSQLDUMP=/home/pirate/programs/mysql/bin/mysqldump
MYSQL=/home/pirate/programs/mysql/bin/mysql

dblist=`$MYSQL -u$USER -p$PASSWORD -h$HOST -e "show databases;" | grep $DBNAME`

dbexist=0
if [ -n "$dblist" ]; then
	echo -e "$DBNAME has in DB $HOST!\n";
	while true;
	do
		echo "please input y/Y to affirm! n/N to exit!";
		read p;
		if [ $p = 'y' -o $p = 'Y' ]; then
			dbexist=1
			break;
		elif [ $p = 'n' -o $p = 'N' ]; then
			exit 1;
		else
			continue;
		fi
	done;
fi

if [ $dbexist -eq 0 ]; then
	echo "CREATE DATABASE $DBNAME ON DB $HOST!";
	$MYSQL -u$USER -p$PASSWORD -h$HOST -e "create database if not exists $DBNAME;";
	$MYSQL -u$USER -p$PASSWORD -h$HOST $DBNAME < $FILE
	echo "CREATE DATABASE $DBNAME ON DB $HOST DONE!";
	echo "MODIFY $DBNAME START!";
	for i in `ls /home/pirate/rpcfw/script/mergeServer/doc/modify*`; do
		echo "$MYSQL -u$USER -p$PASSWORD -h$HOST $DBNAME < $i;";
		$MYSQL -u$USER -p$PASSWORD -h$HOST $DBNAME < $i;
	done
	echo "MODIFY $DBNAME DONE!";
fi

echo "ADD TMP TABLE FOR MERGE!"
for i in `ls /home/pirate/rpcfw/script/mergeServer/doc/tmp*`; do
	echo "$MYSQL -u$USER -p$PASSWORD -h$HOST $DBNAME < $i;";
	$MYSQL -u$USER -p$PASSWORD -h$HOST $DBNAME < $i;
done
echo "ADD TMP TABLE FOR MERGE DONE!"