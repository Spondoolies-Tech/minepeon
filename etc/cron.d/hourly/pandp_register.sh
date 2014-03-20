#!/bin/sh

# functions
no_bootsource=201
detect_boot_source()
{
# modified from Vladiks script to use /tmp/ if no bootsource found!!! 
	# bootfrom=<BOOTSOURCE> is always the last element of kernel
	# command line so may get it this way.
	bootfrom=`find /mnt -name uImage | head -1 | sed 's/-.*//;s/^.*\///'`

	if [ -z "$bootfrom" ]; then
		bootfrom="/tmp"	
	fi
}
detect_boot_source

# get external ip

ext_ip=`curl -s --head http://myexternalip.com/ | awk '/External-Ip/{print $2}'`

# get lan ip
lan_ip=`ifconfig | grep -A1 eth0 | awk '/inet/{print $2}' | cut -c 6-`
if [ -z "$lan_ip" ]; then
	lan_ip="NA"
fi
	

# get wan ip
wan_ip=`ifconfig | grep -A1 wan0 | awk '/inet/{print $2}' | cut -c 6-`
if [ -z "$wan_ip" ]; then
	wan_ip="NA"
fi
 

# get firmware version
# instructions from Vladik
firmware=`mkimage -l /mnt/${bootfrom}-boot/uImage | grep 'Image Name' | sed 's/.*:\s\+//'`

# get model number
# script from Vladik
EEPROM_DEVICE=/sys/bus/i2c/devices/0-0050/eeprom
#board=`dd bs=12 skip=7 count=1 if=$EEPROM_DEVICE 2>/dev/null`
board=`cat /board_ver`

# 
printf "External IP: %s\n" $ext_ip
printf "Lan IP: %s\n" $lan_ip
printf "WLan IP: %s\n" $wan_ip
printf "Board Serial Number: %s\n" $board
printf "Firmware: %s\n" $firmware

 
s=`curl -s --include --header "Content-Type: application/json" \
     --request PUT \
     --data-binary "{
    \"modelNumber\": \"ABCD12345\",
    \"lanAddress\": \"$lan_ip\",
    \"wanAddress\": \"$wan_ip\",
    \"fwVersion\": \"$firmware\",
    \"minerName\": \"$board\",
}" \
     "https://private-4745-spondapi.apiary.io/devices/registerDevice" \
	| head -1 | awk '{print $2}'`

if [ $s=200 ]; then
	echo "saved"
else
	printf "error registering machine, %s" $s | tee -a /var/log/messages
fi

