<?php

$network_file_header =
"# This file describes the network interfaces available on your system
# and how to activate them. For more information, see interfaces(5).

# The loopback network interface
auto lo
iface lo inet loopback\n\n";

$wifi_section_footer =
"pre-up wpa_supplicant -B -D wext -i wlan0 -c /etc/wifi.conf;sleep 3
post-down pkill wpa_supplicant;pkill udhcpc\n";

function get_network($interface="eth0")
{
    $results = array();

    $submask = exec("ifconfig $interface | grep inet", $out);
    $submask = str_ireplace("inet addr:", "", $submask);
    $submask = str_ireplace("Mask:", "", $submask);
    $submask = trim($submask);
    $submask = explode(" ", $submask);
    $results['ipaddress'] = $submask[0];
    $results['subnet'] = $submask[4];
    $results['dhcp'] = exec('cat /etc/network/interfaces | awk "/iface eth0/{print \$4}"') == "dhcp";

    $gatewayType = shell_exec("route -n");
    $gatewayTypeRaw = explode(" ", $gatewayType);
    $results['gateway'] = $gatewayTypeRaw[42];

    $results['hostname'] = GETHOSTNAME();

    $dnsType = file('/etc/resolv.conf');
    $dnsType = str_ireplace("nameserver ", "", $dnsType);
    $results['dns1'] = $dnsType[1];
    //$dns2 = $dnsType[3];
    //$dns3 = $dnsType[4];

    return $results;
}

function set_fixed_network($settings)
{
    global $network_file_header, $wifi_section_footer;

    $network_file = $network_file_header.
    "#Static network configuration\n".
    "auto eth0\n".
    "iface eth0 inet static\n".
    "address ".$settings['0']."\n".
    "netmask ".$settings['1']."\n".
    "\n".
    "auto wlan0\n".
    "iface wlan0 inet static\n".
    "address ".$settings['4']."\n".
    "netmask ".$settings['5']."\n".
    "gateway ".$settings['6']."\n".
    $wifi_section_footer."\n".
    "gateway ".$settings['2'];

    file_put_contents("/etc/network/interfaces", $network_file);


    $resolve_file =
    "#Your static network configuration\n".
    "nameserver ".$settings['3']."\n";

    file_put_contents("/etc/resolv.conf", $resolve_file);
    network_sync();
}

function set_dhcp_network()
{
    global $network_file_header, $wifi_section_footer;

    $network_file = $network_file_header.
        "#Dynamic network configuration\n".
        "auto eth0\n".
        "iface eth0 inet dhcp\n".
        "\n".
        "auto wlan0\n".
        "iface wlan0 inet dhcp\n".
        $wifi_section_footer;

    file_put_contents("/etc/network/interfaces", $network_file);
    network_sync();
}

/*
 * didnt work, test well before doing this
function restart_network(){
	exec('/etc/init.d/S40network restart');
}
 */

function network_sync(){
	exec('/bin/sync');
}

$eth_settings = get_network("eth0");
$wlan_settings = get_network("wlan0");

//Tester
//var_dump($network_settings);
//set_dhcp_network();
/*$fixip = array();
$fixip['0'] = "192.160.1.10";
$fixip['1'] = "255.255.255.0";
$fixip['2'] = "192.160.1.1";
set_fixed_network($fixip);*/
?>
