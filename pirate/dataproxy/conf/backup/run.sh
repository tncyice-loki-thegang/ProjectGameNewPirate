#!/bin/sh

killall -9 supervisor.dataproxy dataproxy
bin/supervisor.dataproxy bin/dataproxy >log/supervisor.log 2>&1 &
