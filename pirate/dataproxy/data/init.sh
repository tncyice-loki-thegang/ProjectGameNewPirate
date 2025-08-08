#!/bin/sh

ROOT=`dirname $0`
db=$1
if [ "$db" = "" ]; then
        echo "usage: sh $0 database"
        exit 0
fi

if [ -d "$ROOT/$db" ]; then
        echo "$db alreay exists"
        exit
fi

mkdir $ROOT/$db

for i in `grep name $ROOT/../conf/id.xml|cut -d">" -f2|cut -d"<" -f 1`; do
        touch $ROOT/$db/$i
done
