#!/bin/sh

BTSCRIPT=/home/pirate/bin/btscript
FILE=$1
if [ "$FILE" = "" -o ! -e $FILE ]; then
	echo "$FILE does not exist"
	exit 0
fi

shift 

for i in /home/pirate/rpcfw/conf/gsc/*; do 
	group=`basename $i`
	$BTSCRIPT $group $FILE $@ >/dev/null 2>&1
done
