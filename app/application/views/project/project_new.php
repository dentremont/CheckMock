<h2>New Project</h2>
<?php echo form_open();?>
	<?php echo validation_errors(); ?>
	<label>Project Name</label>
	<?php echo form_input('project_name', '');?>
	<?php echo form_submit('create', 'Create Project');?>
<?php echo form_close();?>