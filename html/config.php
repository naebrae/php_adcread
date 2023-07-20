<?php

$username='phpread';
$password='Passw0rd';

$netscaler["0"]['URL'] = "https://podman.lab.home:9443/nitro/v1";
$netscaler["1"]['URL'] = "https://podman.lab.home:8443/nitro/v1";
$netscaler["0"]['label'] = "CPX_0";
$netscaler["1"]['label'] = "CPX_1";

$netscalerAttrs["L"] = "name,effectivestate,servicetype,ipv46,port,lbmethod,persistencetype,timeout";
$netscalerAttrs["C"] = "name,curstate,servicetype,type,ipv46,port,lbvserver";
$netscalerAttrs["S"] = "name,state,ipaddress,domain";
$netscalerAttrs["G"] = "servicegroupname,state,servicetype,servicegroupname,servicegroupeffectivestate,svrstate,cip,cipheader";

?>
