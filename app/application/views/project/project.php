<div class="container-fluid">
      <div class="sidebar">
        <div class="well">
        	<aside class="search">
        		<h5>Search</h5>
        	</aside>
        	<aside class="p-control">
        		<form method="post" action="">
        			<?php if($project->archived) : ?>
        			<input class="btn" type="submit" name="archive" value="Unarchive">
        			<?php else: ?>
        			<input class="btn" type="submit" name="archive" value="Archive">
        			<?php endif; ?>
        		</form>
        	</aside>
        </div>
      </div>
      <div class="content">
		<h2><?php echo $project->name;?></h2>
		<div class="add-mockup" style="width:400px">
			<?php echo form_open_multipart('mockup/upload');?>
				<?php echo form_hidden('project', $project->pid);?>
				<label>Mockup Name</label>
				<?php echo form_input('mockup_name', $this->input->post('mockup_name'));?>
				<label>Image</label>
				<?php echo form_upload('image', '');?>
				<label>Background Color</label>
				<?php echo form_input('bg_color', '#FFFFFF');?>
				<label>Height from top</label>
				<?php echo form_input('margin', '0px');?>
				<?php echo form_submit('add', 'Add');?>
			<?php echo form_close();?>
		</div>
		
		<?php if($mockups): ?>
			<table>
				<thead>
					<tr><td>Preview</td><td>Name</td><td>Created</td></tr>
				</thead>
				<?php foreach($mockups as $m): ?>
					<tr>
						<td><img src="<?php echo $m->thumbnail_path;?>"/></td>
						<td><a href="<?php echo site_url('mockup/view/'.$m->nid);?>"><?php echo $m->name;?></a></td>
						<td><?php echo $this->functions->time_ago($m->created);?></td>
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif; ?>
	</div>
</div>

