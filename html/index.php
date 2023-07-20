<?php
session_start();
$_SESSION['home'] = $_SERVER['REQUEST_URI'];
if (isset($_SESSION['netscaler']))
{
  $defaultNetscaler = strtoupper($_SESSION['netscaler']);
}
else 
{
  $defaultNetscaler = "0";
  $_SESSION['netscaler'] = $defaultNetscaler;
}
?>
<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css"/>
<script src="sorttable.js"></script>
</head>
<body>
<?php
if (isset($_POST['netscaler']))
{
  $postNetscaler = $_POST['netscaler'];
}
else
{
  $postNetscaler = "";
}

if ($postNetscaler == "")
{ 
  if ($defaultNetscaler == "") { $defaultNetscaler = "0"; }
} 
else 
{ 
  $defaultNetscaler = $postNetscaler;
  $_SESSION['netscaler'] = $postNetscaler;
}

if (isset($_GET['type']))
{
  $defaultType = $_GET['type'];
}
elseif (isset($_POST['type']))
{
  $defaultType = $_POST['type'];
}
else
{
  $defaultType = "";
}
if ($defaultType == "") { $defaultType = "L"; }

include('config.php');
?>
<br>
<div class="lbsrc">
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post">
<?php
  foreach ($netscaler as $key => $value)
  {
    echo '<input type="radio" name="netscaler" id="';
    echo $key;
    echo '" value="';
    echo $key;
    echo '" ';
    if ($defaultNetscaler == $key) { echo "checked"; } else { echo ""; }
    echo '>';
    echo $netscaler[$key]['label'];
  }
?>
&nbsp;
&nbsp;
<input type="submit" name="sub" value="Submit" class="button" />
</form>
</div>

<div class="sidenav">
  <a href="?type=L">LoadBalance</a>
  <a href="?type=C">ContentSwitch</a>
  <a href="?type=P">CSPolicy</a>
  <a href="?type=S">Server</a>
  <a href="?type=G">ServiceGroup</a>
  <a href="?type=K">Certificates</a>
</div>

<div class="main">
<!-- Add all page content inside this div if you want the side nav to push page content to the right (not used if you only want the sidenav to sit on top of the page -->
<div class="lblist">
<table class="sortable">
<?php

if ($defaultType == "L")
{
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/lbvserver?attrs=$netscalerAttrs[$defaultType]";
}
if ($defaultType == "C")
{
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/csvserver?attrs=$netscalerAttrs[$defaultType]";
}
if ($defaultType == "S")
{
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/server?attrs=$netscalerAttrs[$defaultType]";
}
if ($defaultType == "G")
{
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/servicegroup?attrs=$netscalerAttrs[$defaultType]";
}
if ($defaultType == "K")
{
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/sslcertkey";
}
if ($defaultType == "P")
{
  $data_url = $netscaler[$defaultNetscaler]['URL']."/config/cspolicy";
}

$opts = array('http'=>array('header'=>"X-NITRO-USER: $username\r\nX-NITRO-PASS: $password\r\n"), 'ssl' => array('verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=>true));
$ctx = stream_context_create($opts);
$json=file_get_contents($data_url, false, $ctx );
$data = json_decode($json);

if ($defaultType == "L") 
{
  echo "<title>LoadBalance List</title>\n";
  echo "  <tr><th>Virtual Server</th><th>State</th><th>Type</th><th>Method</th><th>Persistence</th><th>Timeout</th><th>IP Address</th><th>Port</th></tr>\n";

  foreach ($data->{'lbvserver'} as $lbvserver)
  {
    $primaryip=ip2long($lbvserver->{'ipv46'});
    echo "  <tr><td><a href=info.php?lbvserver=".$lbvserver->{'name'}.">".$lbvserver->{'name'}."</a></td><td>".$lbvserver->{'effectivestate'}."</td><td>".$lbvserver->{'servicetype'}."</td><td>".$lbvserver->{'lbmethod'}."</td><td>".$lbvserver->{'persistencetype'}."</td><td>".$lbvserver->{'timeout'}."</td><td sorttable_customkey='".$primaryip."'>".$lbvserver->{'ipv46'}."</td><td>".$lbvserver->{'port'}."</td></tr>\n";
  }
}
if ($defaultType == "C") 
{  
  echo "<title>ContentSwitch List</title>\n";
  echo "  <tr><th>Content Switch</th><th>State</th><th>Type</th><th>IP Address</th><th>Port</th><th>Virtual Server</th></tr>\n";
  foreach ($data->{'csvserver'} as $csvserver)
  {
    $primaryip=ip2long($csvserver->{'ipv46'});
    echo "  <tr><td><a href=info.php?csvserver=".$csvserver->{'name'}.">".$csvserver->{'name'}."</a></td><td>".$csvserver->{'curstate'}."</td><td>".$csvserver->{'servicetype'}."</td><td sorttable_customkey='".$primaryip."'>".$csvserver->{'ipv46'}."</td><td>".$csvserver->{'port'}."</td><td><a href=info.php?lbvserver=".$csvserver->{'lbvserver'}.">".$csvserver->{'lbvserver'}."</a></td></tr>\n";
  }
}
if ($defaultType == "S") 
{  
  echo "<title>Server List</title>\n";
  echo "  <tr><th class='sorttable_alpha'>Server</th><th>Domain</th><th>IP Address</th><th>State</th></tr>\n";
  foreach ($data->{'server'} as $server)
  {
    $primaryip=ip2long($server->{'ipaddress'});
    echo "  <tr><td><a href=info.php?server=".$server->{'name'}.">".$server->{'name'}."</a></td><td>";
    if (isset($server->{'domain'})) { echo $server->{'domain'}; }
    echo "</td><td sorttable_customkey='".$primaryip."'>".$server->{'ipaddress'}."</td><td>".$server->{'state'}."</td></tr>\n";
  }
}
if ($defaultType == "G") 
{  
  echo "<title>ServiceGroup List</title>\n";
  echo "  <tr><th>Service Group</th><th>State</th><th>Type</th><th>cip</th><th>cipheader</th></tr>\n";
  foreach ($data->{'servicegroup'} as $servicegroup)
  {
    echo "  <tr><td><a href=info.php?servicegroup=".$servicegroup->{'servicegroupname'}.">".$servicegroup->{'servicegroupname'}."</a></td><td>".$servicegroup->{'servicegroupeffectivestate'}."</td><td>".$servicegroup->{'servicetype'}."</td><td>".$servicegroup->{'cip'}."</td><td>".$servicegroup->{'cipheader'}."</td></tr>\n";
  }
}
if ($defaultType == "K")
{  
  echo "<title>Certificate List</title>\n";
  echo "  <tr><th>Certificate</th><th>Linked</th><th>Not Before</th><th>Not After</th><th>days</th></tr>\n";
  foreach ($data->{'sslcertkey'} as $sslcertkey)
  {
    echo "  <tr title='".trim($sslcertkey->{'subject'})."'><td>".$sslcertkey->{'certkey'}."</td><td>".$sslcertkey->{'linkcertkeyname'}."</td><td>".$sslcertkey->{'clientcertnotbefore'}."</td><td>".$sslcertkey->{'clientcertnotafter'}."</td><td>".$sslcertkey->{'daystoexpiration'}."</td></tr>\n";
  }
}
if ($defaultType == "P")
{  
  echo "<title>CSPolicy List</title>\n";
  echo "  <tr><th>Policy</th><th>Type</th><th>Hits</th></tr>\n";
  foreach ($data->{'cspolicy'} as $cspolicy)
  {
    echo "  <tr title='".trim($cspolicy->{'rule'})."'><td><a href=info.php?cspolicy=".$cspolicy->{'policyname'}.">".$cspolicy->{'policyname'}."</td><td>".$cspolicy->{'cspolicytype'}."</td><td>".$cspolicy->{'hits'}."</td></tr>\n";
  }
}

?>
</table>
</div>
</div>
<script> window.onload = function() { (document.getElementsByTagName('th')[0]).click(); }; </script>
</body>
</html>
