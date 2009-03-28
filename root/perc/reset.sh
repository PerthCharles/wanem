#!/bin/bash
intfaces=`ifconfig -s -a |grep -v Iface|grep -v lo|cut -d " " -f1`
for i in "$intfaces" ;
do
   tc qdisc del dev $i root
   /root/nambiar/disc_new_port_int/reset_disc.sh /root/nambiar/disc_new_port_int 
done

