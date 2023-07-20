<?php
session_start();
if (isset($_SESSION['netscaler']))
{
  $defaultNetscaler = strtoupper($_SESSION['netscaler']);
}
else 
{
  $defaultNetscaler = "0";
}
?>
<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css"/>
<script src="sorttable.js"></script>
</head>
<body>
<br>
<br>
<br>
<div class="sidenav">
  <a href="<?php echo $_SESSION['home']; ?>">Home</a>
  <a href="javascript:history.go(-1)">Back</a>
</div>

<div class="main">
<div class="lblist">
<table>
<?php

include('config.php');

$opts = array('http'=>array('header'=>"X-NITRO-USER: $username\r\nX-NITRO-PASS: $password\r\n"), 'ssl' => array('verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=>true));

if (isset($_GET['lbvserver'])) 
{
  $lbvserver = $_GET['lbvserver'];
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/lbvserver_binding/$lbvserver";
  
  $ctx = stream_context_create($opts);
  $json=file_get_contents($data_url, false, $ctx );
  $data = json_decode($json);

  echo "<title>LoadBalance Info</title>\n";
  echo "<tr><th>Virtual Server</th><th>Service Group</th><th>State</th><th>Type</th><th>IP Address</th><th>Port</th></tr>\n";
  foreach ($data->{'lbvserver_binding'} as $lbvserver)
  {
    echo "  <tr class='boldtr'><td>".$lbvserver->{'name'}."</td></tr>\n";
    foreach ($lbvserver->{'lbvserver_servicegroupmember_binding'} as $servicegroupmember)
    {
      echo "  <tr><td></td><td><a href=info.php?servicegroup=".$servicegroupmember->{'servicegroupname'}.">".$servicegroupmember->{'servicegroupname'}."</td><td>".$servicegroupmember->{'curstate'}."</td><td>".$servicegroupmember->{'servicetype'}."</td><td>".$servicegroupmember->{'ipv46'}."</td><td>".$servicegroupmember->{'port'}."</td></tr>\n";
    }
  }
}
elseif (isset($_GET['csvserver'])) 
{
  $csvserver = $_GET['csvserver'];
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/csvserver_binding/$csvserver";
  
  $ctx = stream_context_create($opts);
  $json=file_get_contents($data_url, false, $ctx );
  $data = json_decode($json);

  echo "<title>ContentSwitch Info</title>\n";
  echo "<tr><th>Content Switch</th><th>Service Policy</th><th>Service Group</th></tr>\n";
  foreach ($data->{'csvserver_binding'} as $csvserver)
  {
    echo "  <tr class='boldtr'><td>".$csvserver->{'name'}."</td></tr>\n";
    foreach ($csvserver->{'csvserver_lbvserver_binding'} as $lbvserver)
    {
      echo "  <tr><td></td><td></td><td><a href=info.php?lbvserver=".$lbvserver->{'lbvserver'}.">".$lbvserver->{'lbvserver'}."</a></td></tr>\n";
    }
    foreach ($csvserver->{'csvserver_cspolicy_binding'} as $cspolicy)
    {
      echo "  <tr><td></td><td><a href=info.php?cspolicy=".$cspolicy->{'policyname'}.">".$cspolicy->{'policyname'}."</a></td><td><a href=info.php?lbvserver=".$cspolicy->{'targetlbvserver'}.">".$cspolicy->{'targetlbvserver'}."</a></td></tr>\n";
    }
  }
}
elseif (isset($_GET['cspolicy'])) 
{
  $cspolicy = $_GET['cspolicy'];
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/cspolicy_binding/$cspolicy";
  
  $ctx = stream_context_create($opts);
  $json=file_get_contents($data_url, false, $ctx );
  $data = json_decode($json);

  echo "<title>ContentSwitchPolicy Info</title>\n";
  echo "<tr><th>Policy</th><th>ContentSwitch</th><th>LoadBalance</th><th>URL</th></tr>\n";
  foreach ($data->{'cspolicy_binding'} as $cspolicy)
  {
    echo "  <tr class='boldtr'><td>".$cspolicy->{'policyname'}."</td></tr>\n";
    foreach ($cspolicy->{'cspolicy_csvserver_binding'} as $csvserver)
    {
      echo "  <tr><td></td><td><a href=info.php?csvserver=".$csvserver->{'domain'}.">".$csvserver->{'domain'}."</a></td><td><a href=info.php?lbvserver=".$csvserver->{'action'}.">".$csvserver->{'action'}."</td><td>".$csvserver->{'url'}."</td></tr>\n";
    }
  }
}
elseif (isset($_GET['server'])) 
{
  $server = $_GET['server'];
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/server_servicegroup_binding/$server";
  
  $ctx = stream_context_create($opts);
  $json=file_get_contents($data_url, false, $ctx );
  $data = json_decode($json);

  echo "<title>Server Info</title>\n";
  echo "  <tr><th>Server</th><th>Service Group</th><th>State</th><th>Type</th><th>IP Address</th><th>Port</th></tr>\n";
  echo "  <tr class='boldtr'><td>".$server."</td></tr>\n";
  foreach ($data->{'server_servicegroup_binding'} as $serversgb)
  {
    echo "  <tr><td></td><td><a href=info.php?servicegroup=".$serversgb->{'servicegroupname'}.">".$serversgb->{'servicegroupname'}."</a></td><td>".$serversgb->{'state'}."</td><td>".$serversgb->{'svctype'}."</td><td>".$serversgb->{'serviceipaddress'}."</td><td>".$serversgb->{'port'}."</td></tr>\n";
  }
}
elseif (isset($_GET['servicegroup'])) 
{
  $servicegroup = $_GET['servicegroup'];
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/servicegroup_binding/$servicegroup";
  
  $ctx = stream_context_create($opts);
  $json=file_get_contents($data_url, false, $ctx );
  $data = json_decode($json);

  echo "<title>ServiceGroup Info</title>\n";
  echo "  <tr><th>Service Group</th><th>Server</th><th>State</th><th>IP Address</th><th>Port</th></tr>\n";
  foreach ($data->{'servicegroup_binding'} as $servicegroup)
  {
    echo "  <tr class='boldtr'><td>".$servicegroup->{'servicegroupname'}."</td></tr>\n";
    foreach ($servicegroup->{'servicegroup_servicegroupmember_binding'} as $sgmember)
    {
      echo "  <tr><td></td><td><a href=info.php?server=".$sgmember->{'servername'}.">".$sgmember->{'servername'}."</td><td>".$sgmember->{'svrstate'}."</td><td>".$sgmember->{'ip'}."</td><td>".$sgmember->{'port'}."</td></tr>\n";
    }
  }
}
else
{
  echo "  <tr><td>&nbsp;</td></tr>\n";
  echo "  <tr><td></td><td>&nbsp;</td></tr>\n";
  echo "  <tr><td></td><td>&nbsp;</td></tr>\n";
}

?>
</table>
</div>
</div>
</body>
</html>
