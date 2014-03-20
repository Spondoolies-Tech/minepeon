#!/bin/sh

# functions copied from vladik update
no_bootsource=201
mount_fail=204

# Mount boot partition if not mounted already.
mount_boot_partition()
{
	grep -q /mnt/${bootfrom}-boot /proc/mounts ||
		mount /mnt/${bootfrom}-boot 2>/dev/null ||
			{ echo "Cannot mount /mnt/${bootfrom}-boot"; exit ${mount_fail}; }
}
umount_boot_partition(){
		umount /mnt/${bootfrom}-boot 2>/dev/null 
}

detect_boot_source()
{
	# bootfrom=<BOOTSOURCE> is always the last element of kernel
	# command line so may get it this way.
	grep -q bootfrom /proc/cmdline &&
		bootfrom=`sed 's/.*bootfrom=//' /proc/cmdline` 
}

# get external ip
external_ip(){
	ext_ip=`curl -s --head http://myexternalip.com/ | awk '/External-Ip/{print $2}'`
}

# get lan ip
lan_ip(){
	lan_ip=`ifconfig | grep -A1 eth0 | awk '/inet/{print $2}' | cut -c 6-`
	if [ -z "$lan_ip" ]; then
		lan_ip="NA"
	fi
}	

# get wan ip
wan_ip(){
	wan_ip=`ifconfig | grep -A1 wan0 | awk '/inet/{print $2}' | cut -c 6-`
	if [ -z "$wan_ip" ]; then
		wan_ip="NA"
	fi
}

board_id(){
	EEPROM_DEVICE=/sys/bus/i2c/devices/0-0050/eeprom
	board_id=`dd bs=12 skip=7 count=1 if=$EEPROM_DEVICE 2>/dev/null`
}

debug(){ 
printf "External IP: %s\n" $ext_ip
printf "Lan IP: %s\n" $lan_ip
printf "WLan IP: %s\n" $wan_ip
printf "Firmware: %s\n" $firmware
printf "Model: %s\n" $board
printf "Board ID: %s\n" $board_id
}

send_data(){ 
	s=`curl -s --include --header "Content-Type: application/json" \
	     --request PUT \
	     --data-binary "{
	    \"modelNumber\": \"$board\",
	    \"lanAddress\": \"$lan_ip\",
	    \"wanAddress\": \"$wan_ip\",
	    \"fwVersion\": \"$firmware\",
	    \"boardID\": \"$board_id\",
	}" \
	     "https://private-4745-spondapi.apiary.io/devices/registerDevice" \
		| head -1 | awk '{print $2}'`
	debug
}

main(){
	detect_boot_source
	mount_boot_partition
	board=`cat /board_ver`
	firmware=`cat /fw_ver`
	board_id
	external_ip
	lan_ip
	wan_ip
	send_data
	umount_boot_partition
	if [ $s=200 ]; then
		echo "saved"
	else
		printf "error registering machine, %s" $s | tee -a /var/log/messages
	fi
}

main
return $?
