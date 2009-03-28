#!/bin/bash
trap "" SIGINT SIGTERM SIGSTOP SIGTSTP
while [ 1 ];
do
   echo -n "WANemControl@PERC>"
   read str;
   case "$str" in
   help)
         echo "help -- Displays this help."
	 echo "about -- About WANem"
	 echo "clear -- Clear the Screen"
	 echo "reset -- Reset the network setting and services"
	 echo "nat -- Help for enabling WANem to work across subnets."
	 echo "shutdown -- Shutdown the System"
	 echo "restart -- Restart the System"
	 echo "status -- Check the status of Network settings and services"
	 echo "wanemreset -- Reset WANem settings from here if GUI is very slow"
	 echo "assign -- Assign a device to a host. usage: assign IPAddr device"
	 echo "exit2shell -- Go to shell."
	 echo "wanem -- Return to WANem shell."
	 echo 
         ;;
   nat*)
         command=`echo $str|cut -d " " -f2`
   	 case "$command" in
	 show)
	 	ifs=`/sbin/iptables -t nat -L -v | awk '/MASQUERADE/{print $7;}'`
		if [ $ifs ]
		then
			echo -n "NAT enabled interface(s) in WANem :"
			for i in "$ifs" ;
			do
				echo $i
			done
		else
			echo "No NAT enabled interface in WANem"
		fi
		;;
	 add)
         	if=`echo $str|cut -d " " -f3`
       	 	interfaces=`ifconfig -s -a |grep -v Iface|grep -v lo|cut -d " " -f1`
	 	natif=`/sbin/iptables -t nat -L -v | awk '/MASQUERADE/{print $7;}'|cut -d " " -f1`

		if echo "$interfaces"|grep -q "$if"
		# 'if' found in 'interfaces'
		then
			if echo "$natif"|grep -q "$if"
			# 'if' found in 'natif'
			then
				echo "$if is already NAT enabled."
			else
				#add rules
	 			/sbin/iptables -t nat -A POSTROUTING -o $if -j MASQUERADE > /dev/null 2>&1
			fi
		else
			echo "$if is not a valid interface name."
		fi
   	 	;;
	 del)
         	if=`echo $str|cut -d " " -f3`
       	 	interfaces=`ifconfig -s -a |grep -v Iface|grep -v lo|cut -d " " -f1`
	 	natif=`/sbin/iptables -t nat -L -v | awk '/MASQUERADE/{print $7;}'|cut -d " " -f1`

		if echo "$interfaces"|grep -q "$if"
		# 'if' found in 'interfaces'
		then
			if echo "$natif"|grep -q "$if"
			# 'if' found in 'natif'
			then
				#delete rule
	 			/sbin/iptables -t nat -D POSTROUTING -o $if -j MASQUERADE > /dev/null 2>&1 	 
			else
				echo "$if is already NAT disabled."
			fi
		else
			echo "$if is not a valid interface name."
		fi
   	 	;;
	 help)
	 	echo
	 	echo "Use the following commands to use WANem across multiple subnets:"
	 	echo "nat add <interface-name> 	:  Enable network address translation (nat) on this interface"
		echo "nat del <interface-name> 	:  Disable network address translation (nat) on this interface"
		echo "nat show 			:  List nat enabled interfaces"
		echo "nat help 			:  nat help"
		echo
	 	;;
	 *)
	 	echo "nat: invalid option"
		echo "Try 'nat help' for more information."
		;;
	 esac
	 ;;
   assign*)
        ip=`echo $str|cut -d " " -f2`
        dev=`echo $str|cut -d " " -f3`
        root/ip_dev.sh $ip $dev
	;;
  about)
        more /var/www/WANem/About.txt
	;;
   reset)
         cd /root
         ./reset_wanem.sh
	 cd ..
	 ;;
   shutdown)
         init 0
	 ;;
   restart)
         init 6
	 ;;
   clear)
         clear
	 ;;
   #dontquit)
   #      read -s pw;
   #	 if [ "$pw" = "rmdahod@perc" ]; 
   #	 then
   #	      exit 0
   #	 fi     
   #	 ;;
   exit2shell)
       echo "Type 'wanem' to return to WANem console"	
       exit 0
       ;;	
   status)
       clear
       echo "IP Settings"
       echo "========================================================================="
       ifconfig -a|more
       echo "========================================================================="
       echo -n "Press any key to continue"
       read -s x
       clear
       echo "Route Settings"
       echo "========================================================================="
       route -n
       echo "========================================================================="
       tempstr=`ps -el|grep apache2`
       echo -n "Apache ..... "
       if [ -z "$tempstr" ];
       then
          echo "down";
	  echo "WANem can't be accessed, reset to start  it first"
       else
          echo "running";
       fi
       tempstr=`ps -el|grep sshd`
       echo -n "SSH Server ..... "
       if [ -z "$tempstr" ];
       then
	  echo "down";
       else 
	  echo "up"
       fi
       echo -n "Enter IP Address to test reachability(q to skip):";
       read ip;
       if [ $ip != "q" ];
       then
        
          tempvar=`ping -c 1 $ip|grep loss|cut -d "," -f3|cut -d " " -f2`
	  if [ -z $tempvar ]
	  then
	     echo "Wrong IPAddress...."
	  else
              if [ $tempvar = "0%" ]
              then
	           echo "$ip reachable...."
              else
                   echo "$ip not reachable.... check network settings"
              fi
	   fi   
       fi	  
       ;;
   wanemreset)
       intfaces=`ifconfig -s -a |grep -v Iface|grep -v lo|cut -d " " -f1`
       for i in $intfaces ;
       do
         tc qdisc del dev $i root > /dev/null 2>&1
	 /root/nambiar/disc_new_port_int/reset_disc.sh /root/nambiar/disc_new_port_int $i
       done
       ;;
   "")
        ;;
   *)
        echo "Invalid Command...."
	echo "Type help to get a list of commands"
	;;
   esac
 done
 

        
       
