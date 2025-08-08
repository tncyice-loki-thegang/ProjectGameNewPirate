#!/bin/sh

PHPPATH=/home/pirate/programs/php

if [ ! -d $PHPPATH ]; then
	echo "PHPPATH:$PHPPATH is not exist!";
	exit 1;
fi

killall -9 php-fpm
listen=`grep "^listen\s*=" $PHPPATH/etc/php-fpm.conf | awk '{print $3}'`
if [ -z "$listen" ]; then
	echo "CAN NOT find php listen info!";
	exit 1;
fi 

php_exists=1
for i in `seq 0 25`; do 
	sleep 1;
	php_exists=`/bin/netstat -anpt 2>/dev/null | grep "$listen" | wc -l` 
	if [ $php_exists -eq 0 ]; then
		break;
	fi
	if [ $i -eq 5 ]; then
		killall -9 php-fpm
	fi
	sleep 4;
done

if [ $php_exists -ne 0 ]; then
	echo "PHP kill failed!";
	exit 1;
fi

$PHPPATH/sbin/php-fpm
