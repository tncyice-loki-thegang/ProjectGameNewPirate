#!/bin/sh

killall -9 memcached
sleep 1
bin/memcached -d -l 127.0.0.1 -m 2048
