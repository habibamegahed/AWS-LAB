<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Test High Availability</title>
    <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" title="default" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        #content {
            margin-top: 50px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        h1, h2 {
            color: #333;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .instance-info {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php
# Function to get the private IPv4 address of the EC2 instance
function getEC2PrivateIPv4() {
    $tokenCommand = 'curl -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 21600"';
    $token = shell_exec($tokenCommand);

    $ipv4Command = 'curl -H "X-aws-ec2-metadata-token: ' . trim($token) . '" http://169.254.169.254/latest/meta-data/local-ipv4';
    return shell_exec($ipv4Command);
}

# Stress test handling
$stressOrKill = $_GET["stress"] ?? "";
if ($stressOrKill === "start") {
    echo "<h1>Demand on your application is growing</h1>";
    exec("stress --cpu 4 --io 1 --vm 1 --vm-bytes 128M --timeout 600s > /dev/null 2>/dev/null &");
} elseif ($stressOrKill === "stop") {
    exec("kill -9 \$(pidof stress)");
    echo "<h1>Cooling down</h1>";
}
?>

<div id="content">
    <img src="images/logo-med.png" alt="Logo" style="max-width: 150px;">
    <h2>Simulate high demand on your application!</h2>
    <div>
        <form action="index.php">
            <input type="hidden" name="stress" value="start" />
            <input type="submit" value="Simulate High Demand" />
        </form>
        <form action="index.php">
            <input type="hidden" name="stress" value="stop" />
            <input type="submit" value="Cool Down" />
        </form>
    </div>
</div>

<div class="instance-info">
    EC2 Instance Private IPv4: <?php echo getEC2PrivateIPv4(); ?>
</div>

</body>
</html>
