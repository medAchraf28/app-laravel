#!/bin/bash

#echo $2;

# Check if the file exists, delete it and reload pmta

if [ -f $1 ]; then
   #rm -rf $1
   #echo "$1 is removed"
   #pmta_cmd=`sudo /etc/init.d/pmta restart`
   #echo "PMTA Reload: $pmta_cmd"

   sudo -S chmod -R 777 $1

   if [[ "$2" == "bounce" ]]; then
   	echo "type,bounceCat,timeLogged,timeQueued,orig,rcpt,dsnAction,dsnStatus,dsnDiag,vmta,jobId,envId,queue" > $1 
	echo "bounce file is empty now" ;

   else 
	echo "type,timeLogged,orig,rcpt,vmta,jobId,envId,header_emp-Id" > $1 
	echo "delivered file is empty now" ;
   fi

else
   echo "not found" ;

fi
