#!/bin/sh

source ~/.bashrc

for i in 20 21 22 24 34 42 43 44 45 46 47; do
	for k in 23 26 27 28 41 48 49 50 52 53 55 56 58; do
		echo "btscript BackendManager.php maskBackend 192.168.3.$k 192.168.3.$i"
		read p
		btscript BackendManager.php maskBackend 192.168.3.$k 192.168.3.$i
	done
	echo "-----------------------mask done----------------------------------------------"

	for k in 23 26 27 28 41 48 49 50 52 53 55 56 58; do
		echo "btscript BackendManager.php unmaskBackend 192.168.3.$k 192.168.3.$i"
		read p
		btscript BackendManager.php unmaskBackend 192.168.3.$k 192.168.3.$i
	done
	echo "-----------------------unmask done----------------------------------------------"
done
