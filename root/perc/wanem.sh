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
	 echo "shutdown -- Shutdown the System"
	 echo "restart -- Restart the System"
	 echo "status -- Check the status of Network settings and services"
	 echo "wanemreset -- Reset WANem settings from here if GUI is very slow"
	 echo "assign -- Assign an IPAddress to a device usage:assign <ipaddr> <dev>"
	 echo "quit -- Quit the shell"
	 echo 
         ;;

   about) more /var/www/WANem/About.txt
         ;;
              
   reset)
         ./dosu ./wanem_init.sh
	 ;;
   shutdown)
         ./dosu init 0
	 ;;
   restart)
         ./dosu init 6
	 ;;
   clear)
         clear
	 ;;
   assign*)
        ip=`echo $str|cut -d " " -f2`
        dev=`echo $str|cut -d " " -f3`
        ./dosu ./ip_dev.sh $ip $dev
	;;
         
   quit)
	      exit 0
	 ;;
   status)
       clear
       echo "IP Settings"
       echo "========================================================================="
       ifconfig -a
       echo "========================================================================="
       echo "Route Settings"
       echo "========================================================================="
       route -n
       echo "========================================================================="
       tempstr=`ps -el|grep apache2`
       echo -n "Apache ..... "
       if [ -z "$tempstr" ];
       then
          echo "down";
	  echo "WANem won't be accessed, reset to start  it first"
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
       ./dosu ./reset.sh >/dev/null 2>&1
       ;;
   "")
        ;;
   *)
        echo "Invalid Command...."
	echo "Type help to get a list of commands"
	;;
   esac
 done
 

        
       
