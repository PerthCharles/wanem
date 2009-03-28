#!/bin/bash
trap "" SIGINT SIGTERM SIGSTOP SIGTSTP
var=`ifconfig -a -s|grep -v Iface|grep -v lo|cut -d  " " -f1`
#echo $var
if [ -z $var ];
then
   echo "No network Interface found..... Exiting"
   init 0;	
fi
for i in $var;
do
         ifconfig $i down > /dev/null 2>&1
done
echo -n "Do you want to configure all interfaces via DHCP(y/n): "
read choice
if [ "$choice" != "y" -a "$choice" != "Y" ];
then
     rm -f root/eth_setup.sh
     root/eth_setup $var
    if [ -f eth_setup.sh ];
    then
        echo -n "IP Address Setting ......."
        \rm -rf tempf 
        ./eth_setup.sh >tempf 2>&1
       if [ -s tempf ];
         then
           echo "failed"
      else
           echo "ok"
       fi
    \rm -rf tempf 

    fi   
root/do_putty.sh
clear
fi
echo -n "Network settings ...... "
echo 1 > /proc/sys/net/ipv4/ip_forward
echo 0 > /proc/sys/net/ipv4/conf/default/send_redirects
echo 0 > /proc/sys/net/ipv4/conf/all/send_redirects
echo 1 > /proc/sys/net/ipv4/ip_no_pmtu_disc
echo "ok"
echo -n " MTU=1500 settings ...... "

for i in $var; 
do
  intf="/proc/sys/net/ipv4/conf/$i/send_redirects";
  echo 0 > $intf
  ifconfig $i mtu 1500
done

echo "ok"
echo -n "Apache Start........"
/etc/init.d/apache2 start > /dev/null 2>&1
echo "ok"
echo -n "SSH start ..........."
/etc/init.d/ssh start > /dev/null 2>&1
echo "ok"
rm -f eth_setup.sh
echo -n "Adding  user perc...  for remote logins via SSH....."
useradd  -s /bin/bash perc
\cp -rf root/perc /home
chown -R perc /home/perc
chgrp -R perc /home/perc
chown root /home/perc/dosu
chgrp root /home/perc/dosu
chmod u+s /home/perc/dosu
root/useradd.exp 123entsysperc >/dev/null
echo "ok"
echo "Added user perc.... Password for perc"
passwd perc 
rm -f ptemp
rm -f .bash_profile
echo " = = = = = = = = = = = = = = = = = = = = = = = = ="
echo "Initialization Successful";
echo "A shell will be given for WANem Administration."
echo "Check the status of Wanem."
echo "Type help to get the list of commands"
echo "Access the WANem from any machine by http://<IP of this machine>/WANem"
echo " = = = = = = = = = = = = = = = = = = = = = = = = ="
echo ""
echo ""
root/wanem.sh

