#!/bin/bash
echo -n "Stopping All...................."
/etc/init.d/apache2 stop >/dev/null 2>&1
echo "ok"
var=`ifconfig -a -s|grep -v Iface|grep -v lo|cut -d  " " -f1`
if [ -z $var ];
then
   echo "No network Interface found..... Exiting"
   exit;
fi
rm -f eth_setup.sh
./eth_setup $var
if [ -f eth_setup.sh ];
then
  echo -n "IP Address Setting ......."
  route del default
  ./eth_setup.sh
fi  
./do_putty.sh
clear
echo "ok"
echo -n "Network settings...... "
echo 1 > /proc/sys/net/ipv4/ip_forward
echo 0 > /proc/sys/net/ipv4/conf/default/send_redirects
echo 0 > /proc/sys/net/ipv4/conf/all/send_redirects
for i in $var; 
do
  intf="/proc/sys/net/ipv4/conf/$i/send_redirects";
  echo 0 > $intf
done
echo "ok"
echo -n "Apache Start........"
/etc/init.d/apache2 start > /dev/null 2>&1
echo "ok"
rm -f eth_setup.sh

echo "Reset Successful";
exit;
