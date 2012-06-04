<!DOCTYPE html>
<html>
<head>
	<title>CheckMock</title>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css">
	<link rel="stylesheet" href="/css/custom.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	<script src="http://twitter.github.com/bootstrap/1.4.0/bootstrap-modal.js"></script>
	<script src="http://twitter.github.com/bootstrap/1.4.0/bootstrap-dropdown.js"></script>
	<script src="http://twitter.github.com/bootstrap/1.4.0/bootstrap-tabs.js"></script>
	<script src="http://twitter.github.com/bootstrap/1.4.0/bootstrap-alerts.js"></script>
	<script src="http://twitter.github.com/bootstrap/1.4.0/bootstrap-twipsy.js"></script>
	<script src="http://twitter.github.com/bootstrap/1.4.0/bootstrap-popover.js"></script>
	<script type="text/javascript" src="/js/skim.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".alert-message").alert();
	        $(".skip").skim();
	        $("a[rel=popover]").popover({placement:'below'});
         
		});
		
	</script>
	<style>
		body {
			padding-top: 60px;
		}
		.modal {
			display: none;
		}

	</style>
</head>
<body>
<div class="topbar">
	<div class="topbar-inner">
		<div class="container-fluid">
			<a class="brand" href="/"><?php echo $account->title;?></a>
			<ul class="nav">
				<li class="active"><a href="/">Dashboard</a></li>
				<li><a href="#about">Feedback</a></li>
			</ul>
			<form class="pull-left" action="">
				<input type="text" placeholder="Search" />
			</form>
			<ul class="nav secondary-nav pull-right">
				<li class="dropdown" data-dropdown="dropdown" >
				<a href="#" class="dropdown-toggle"><?php print $user['username'];?></a>
				<ul class="dropdown-menu">
					<li><a href="/user/settings">My Settings</a></li>
					<li><a href="/user/settings">Manage Users</a></li>
					<li class="divider"></li>
					<li><a href="/account/subscription">Account Settings</a></li>
				</ul>
				</li>
				<li><a href="/auth/logout">Logout</a></li>
			</ul>
		</div>
	</div>
</div>
<?php if($this->session->flashdata('message')):?>
<div class="alert-message success">
  <a class="close" href="#">X</a>
  <p><?php echo $this->session->flashdata('message');?></p>
</div>
<?php endif;?>
