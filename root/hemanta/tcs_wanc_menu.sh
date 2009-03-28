#!/bin/bash

#Host to be pinged
#HOSTS="172.19.140.79"
#HOSTS=$1

#No of packets to be sent in one ping call
COUNT=1
#SIZE=66000
#size=64

#Interval of analysis of results in seconds
INTERVAL=5

#First call to awk file, 1 means FIRST CALL to the AWK Script is true
FIRST_CALL=1

#Number of ICMP packets sent in each duration/interval
ICMP_SENT=0
#clear
#echo
#echo
#echo -n "Enter Target Machine's IP Address : "
#read TARGET_IP
TARGET_IP=$1

echo "TCS WANALYSER RESULTS" > "/tmp/tcs_wanc_report.csv"
echo "..................................................." >> "/tmp/tcs_wanc_report.csv"
echo >> "/tmp/tcs_wanc_report.csv"
echo "Remote host IP: $TARGET_IP" >> "/tmp/tcs_wanc_report.csv"
#echo ".................................................."
#ping $TARGET > "check.dmp"
var=`ping -c $COUNT $TARGET_IP| grep "100%"`
if [ -z "$var" ]
then
	echo "Remote host IP,$TARGET_IP,"
	/root/hemanta/tcs_wanc_main.sh $TARGET_IP 
	/root/hemanta/tcs_bw_main.sh $TARGET_IP
	rm -f /tmp/*.dmp
else
   	echo "0"
	#SIZE=66000
fi
