<?php
# Copyright 2007 Amazon Technologies, Inc.  Licensed under the Apache License, Version 2.0 (the "License"); # you may not use this file except in compliance with the License. You may obtain a copy of the License at:
#
# http://aws.amazon.com/apache2.0
#
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and limitations under the License.

/** 
 * submit.php displays the POST data it receives from sample-attachments.php 
 */
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">	
	<head>
		<link href="styles.css" rel="stylesheet" type="text/css" />		
	</head>
	<body>
		
		<p><a href="readme.html">View readme.html</a> | <a href="sample.php">View sample.php</a> | <a href="sample-attachments.php">View sample-attachments.php</a></p>
		
		<h1>Asynchronous Uploads to Amazon S3</h1>
		<p><em>submit.php</em></p>		
		<p>The "mainForm" form has been submitted.  Here are the values of the form, followed by a print_r of the POST data.</p>		
		<h2>Form submitted</h2>
		
		<fieldset class="formContainer">
			<legend>Subject: <?php echo $_POST['subject']; ?></legend>
			<p>
			<strong>Message:</strong><br />
			<?php echo $_POST['message']; ?>
			</p>
			<p>Attachments:</p>
			<p> 
				<?php
					foreach ($_POST['attachments'] as $attachment) {
						echo '<a href="' . $attachment . '">' . $attachment . '</a><br />';
					}
				?>
			</p>
		</fieldset>
		
		<hr />
				
		<p>POST Data:<br />
		<pre><?php print_r($_POST); ?></pre>
		</p>
		
	</body>
</html>