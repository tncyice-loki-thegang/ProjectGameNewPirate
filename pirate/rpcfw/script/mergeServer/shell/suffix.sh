#!/bin/bash

USER=$1
PASSWORD=$2
HOST=$3
DBNAME=$4

MYSQLDUMP=/home/pirate/programs/mysql/bin/mysqldump
MYSQL=/home/pirate/programs/mysql/bin/mysql

for i in `ls /home/pirate/rpcfw/script/mergeServer/doc/suffix*`; do
	$MYSQL -u$USER -p$PASSWORD -h$HOST $DBNAME < $i;
done
