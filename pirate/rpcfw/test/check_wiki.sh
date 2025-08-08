#!/usr/bin/env bash

# Time-stamp: <2012-07-26 15:06:59 Thursday by idyll>

# @version 1.0
# @author hylan

if [ $# -lt 1 ]; then
	echo "usage: sh check_wiki.sh 30052"
	exit 0
fi


echo check $1
echo "conenect to 192.168.1.192 ...."

mkdir -p /tmp/curl
curl -c /tmp/curl/cook.txt -F username=lanhongyu -F password=aaaa http://192.168.1.192:3000/login > /dev/null 2>&1

echo  "grep res:"

curl -b /tmp/curl/cook.txt http://192.168.1.192:3000/projects/pirate/wiki/Pirate_server_desc 2>/dev/null |grep -o "[^a-zA-Z]game$1[^0-9]\|[^a-zA-Z]pirate$1[^0-9]"
