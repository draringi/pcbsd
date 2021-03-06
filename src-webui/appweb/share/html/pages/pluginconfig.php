<?php
defined('DS') OR die('No direct access allowed.');

  // Get the default network interface for this jail
  $defaultnic = exec("netstat -f inet -nrW | grep '^default' | awk '{ print $6 }'");

  // Get the default IP4 base range
  $defaultip4base = exec("netstat -f inet -nrW | grep '^default' | awk '{ print $2 }' | cut -d '.' -f 1-3");

  // Get the iocage pool
  $curpool = get_iocage_pool();

  // Check if the zpool changed
  if ( ! empty($_POST['iocpool']) and $curpool != $_POST['iocpool'] )
  {
    $curpool = $_POST['iocpool'];
    run_cmd("iocage activate " . $curpool);
  }

  $output = run_cmd("iocage get ip4_autostart default");
  $ip4start = $output[0];
  $output = run_cmd("iocage get ip4_autoend default");
  $ip4end = $output[0];
  $output = run_cmd("iocage get ip4_autosubnet default");
  $ip4subnet = $output[0];

  // Save the ip4 ranges / settings
  $setranges=true;
  if ( empty($_POST['ip4start']) or empty($_POST['ip4end']) or empty($_POST['ip4subnet']) ) {
    $setranges=false;
  } else {
    $ip4start = $_POST['ip4start'];
    $ip4end = $_POST['ip4end'];
    $ip4subnet = $_POST['ip4subnet'];
    if ( is_numeric($ip4start) and is_numeric($ip4end) and is_numeric($ip4subnet) ) {
      // validate
      if ( $ip4start > 254 or $ip4start < 1 ) {
        $setranges=false;
        $errormsg="ERROR: The ip4 start range must be between 1-254!";
      }
      if ( $ip4end > 254 or $ip4end < 1 ) {
        $setranges=false;
        $errormsg="ERROR: The ip4 end range must be between 1-254!";
      }
      if ( $ip4end <= $ip4start ) {
        $setranges=false;
        $errormsg="ERROR: The ip4 end range must be less than start range!";
      }
      if ( $ip4subnet < 16 or $ip4subnet > 30 ) {
        $setranges=false;
        $errormsg="ERROR: The ip4 subnet should be between 16-30";
      }
    } else {
      $setranges=false;
      $errormsg="ERROR: The ranges must be numbers!";
    }
  }

  if ( $ip4start == "none" )
    $ip4start="";
  
  if ( $ip4end == "none" )
    $ip4end="";

  if ( $ip4subnet == "none" )
    $ip4subnet="";

  if ( $setranges ) {
    run_cmd("iocage set ip4_autostart=$ip4start default");
    run_cmd("iocage set ip4_autoend=$ip4end default");
    run_cmd("iocage set ip4_autosubnet=$ip4subnet default");
  }

  if ( $setranges and ! empty($_GET['firstrun']) )
  {
    require("pages/plugins.php");
  } else {

    if ( $firstrun )
      echo "<h1>Welcome to AppCafe Plugins!</h1><br>";
    else
      echo "<h1>Plugin Configuration</h1><br>";

    echo "<p>Each AppCafe managed plugin requires an IP address on your network. Please specify a range of usable IPs which can be assigned to plugins.</p>";

    if ( ! empty($errormsg) ) {
      echo "<br><p style=\"color:red;\">$errormsg</p><br>";
    }
?>
<table class="jaillist" style="width:100%">
<tr>
   <th></th>
   <th></th>
</tr>

<form method="post" action="?p=pluginconfig&firstrun=<?php if ( $firstrun ) { echo "1"; } ?>">
<tr>
  <td style="text-align: center; vertical-align: middle;">
  Available IPv4 Range
  </td>
  <td style="text-align: left; vertical-align: middle;">
    <?php echo $defaultip4base; ?>. 
    <input name="ip4start" type="text" size=3 maxlength=3 value="<?php echo "$ip4start"; ?>" /> - 
    <input name="ip4end" type="text" size=3 maxlength=3 value="<?php echo "$ip4end"; ?>" /> / 
    <input name="ip4subnet" type="text" size=2 maxlength=2 value="<?php echo "$ip4subnet"; ?>" />
  </td>
</tr>
<tr>
  <td style="text-align: center; vertical-align: middle;">Plugin zpool:</td>
  <td style="text-align: left; vertical-align: middle;">
  <select name="iocpool">
  <?php
    $pools = get_zpools();
    foreach ($pools as $pool )
    {
       echo "<option value=\"" . $pool . "\"";
       if ($pool == $curpool )
         echo " selected>";
       else
         echo ">";
       echo $pool . "</option>\n";
    }
  ?>
  </select>
  </td>
</tr>
<tr>
  <td colspan="2"><input name="submit" type="submit" value="Save" class="btn-style" /></td>
</tr>

</form>

</table>

<?php
  }
?>
