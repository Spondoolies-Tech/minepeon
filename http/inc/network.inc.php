<?php

$network_file_header =
"# This file describes the network interfaces available on your system
# and how to activate them. For more information, see interfaces(5).

# The loopback network interface
auto lo
iface lo inet loopback\n\n";


function get_network($interface)
{
    $results = array();

    $submask = exec("ifconfig $interface | grep inet", $out);
    $submask = str_ireplace("inet addr:", "", $submask);
    $submask = str_ireplace("Mask:", "", $submask);
    $submask = trim($submask);
    $submask = explode(" ", $submask);
    $results['ipaddress'] = $submask[0];
    $results['subnet'] = $submask[4];

    $gatewayType = shell_exec("route -n");
    $gatewayTypeRaw = explode(" ", $gatewayType);
    $results['gateway'] = $gatewayTypeRaw[42];

    $results['hostname'] = GETHOSTNAME();

    $dnsType = file('/etc/resolv.conf');
    $dnsType = str_ireplace("nameserver ", "", $dnsType);
    $results['dns1'] = $dnsType[2];
    //$dns2 = $dnsType[3];
    //$dns3 = $dnsType[4];

    return $results;
}

function set_fixed_network($settings)
{
    global $network_file_header;

    $network_file = $network_file_header.
    "#Your static network configuration\n".
    "iface eth0 inet static\n".
    "address ".$settings['ipaddress']."\n".
    "netmask ".$settings['netmask']."\n".
    "gateway ".$settings['gateway']."\n";

    file_put_contents("/opt/minepeon/nettesting", $network_file);
}

function set_dhcp_network()
{
    global $network_file_header;

    $network_file = $network_file_header.
        "#Dynamic network configuration\n".
        "auto eth0\n".
        "iface eth0 inet dhcp\n";

    file_put_contents("/opt/minepeon/nettesting", $network_file);
}

$network_settings = get_network("eth0");
//Tester
//var_dump($network_settings);
set_dhcp_network();
/*$fixip = array();
$fixip['ipaddress'] = "192.160.1.10";
$fixip['netmask'] = "255.255.255.0";
$fixip['gateway'] = "192.160.1.1";
set_fixed_network($fixip);*/
?>