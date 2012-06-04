<h2>Add Mockup</h2>
<?php echo form_open_multipart();?>
	<?php echo validation_errors(); ?>
	<label>Project</label>
	<?php echo form_dropdown('project', $projects);?>
	<label>Mockup Name</label>
	<?php echo form_input('mockup_name', $this->input->post('mockup_name'));?>
	<label>Image</label>
	<?php echo form_upload('image', '');?>
	<label>Background Color</label>
	<?php echo form_input('bg_color', '#FFFFFF');?>
	<label>Height from top</label>
	<?php echo form_input('margin', '0px');?>
	
	<?php echo form_submit('add', 'Add Mockup');?>
<?php echo form_close();?>