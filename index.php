<?php
include "scripts/mysql.php";
$CONFIG = include "scripts/config.php";
?>
<html>
<head>
<link href="css/main.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet"
	href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
	integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu"
	crossorigin="anonymous">
<link rel="stylesheet"
	href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css"
	integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ"
	crossorigin="anonymous">
<link
	href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css"
	rel="stylesheet" type="text/css" />
<!-- wideley used favicon -->
<link rel="icon" href="/fluidicon.png" sizes="32x32" type="image/png">

<!-- for apple mobile devices -->
<link rel="apple-touch-icon-precomposed" href="/fluidicon.png"
	type="image/png" sizes="152x152">
<link rel="apple-touch-icon-precomposed" href="/fluidicon.png"
	type="image/png" sizes="120x120">

<!-- google tv favicon -->
<link rel="icon" href="/fluidicon.png" sizes="96x96" type="image/png">
<title>i like to eat rocks - private file hosting</title>
</head>
<body
	style="background-image: url('https://wallpaperaccess.com/full/3009498.jpg');">
	<div class="container main_container"
		style="background-color: rgba(255, 255, 255, 0.95); border: 1px; border-color: black; margin-top: 5%;">
		<h1 style="margin-bottom: 0px;">i.like2eat.rocks</h1>
		private file hosting<br /> 
			<?php
# filestore hard disk space calc
$si_prefix = array(
    'B',
    'KB',
    'MB',
    'GB',
    'TB',
    'EB',
    'ZB',
    'YB'
);
$base = 1024;

$bytes = disk_free_space($CONFIG['filestore_dir']);
$class = min((int) log($bytes, $base), count($si_prefix) - 1);
echo "Filestore hard disk space: ";
echo sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class] . ' / ';

$bytes = disk_total_space($CONFIG['filestore_dir']);
$class = min((int) log($bytes, $base), count($si_prefix) - 1);
echo sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class] . '';
?> 
			<hr>
		<br>
		<table id="yes" class="table table-striped table-bordered"
			cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Size</th>
					<th>Date Uploaded (<?php echo date("T"); ?>)</th>
					<th>Uploaded By</th>
					<th>Type</th>
				</tr>
			</thead>

			<tbody>
		        	<?php

        function human_filesize($bytes, $decimals = 2)
        {
            $sz = ' KMGTP';
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor] . "B";
        }
        ?>
		        	<?php

        foreach (get_files() as $file) {
            $filename = $file['filename'];
            $name = explode(".", $filename);
            $name = current($name);
            $uploader = get_username($file['uploader']);
            ?>
		            <tr>
					<td><a target="_blank"
						href="<?php echo $CONFIG['webfront_url'] . "/" . $filename;?>"><?php echo($name);?></a></td>
					<td><?php echo human_filesize(filesize($CONFIG['filestore_dir'] . "/" . $filename));?></td>
					<td><?php echo date ("Y-m-d H:i", filemtime($CONFIG['filestore_dir'] . "/" . $filename))?></td>
					<td><?php echo $uploader;?></td>
					<td><?php echo pathinfo($CONFIG['filestore_dir'] . "/" . $filename, PATHINFO_EXTENSION);?></td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<hr style="margin-bottom: 8px; margin-top: 24px;" />
		<p style="font-size: 10px; margin-bottom: 8px;">
			<span style="color: #7289da">meatball#0001</span> - Notice anything
			here that shouldn't be? <a href="mailto:abuse@like2eat.rocks">abuse@like2eat.rocks</a>
			- <a href="https://github.com/possiblemeatball/sharex-custom-uploader">view our source code</a>
	
	</div>
	<script src="https://code.jquery.com/jquery-1.11.3.min.js"
		type="text/javascript"></script>
	<script
		src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
		integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd"
		crossorigin="anonymous"></script>
	<script
		src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"
		type="text/javascript"></script>
	<script
		src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"
		type="text/javascript"></script>
	<script>
			$('#yes').DataTable( {
			    "order": [[2, "desc"]]
			} );
		</script>
</body>
</html>