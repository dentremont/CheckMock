<img src="<?php echo $mockup->full_path;?>" id="mockup"/>
<?php echo form_open();?>
	<?php if($mockup->status == false):?>
		<?php echo form_submit('approve', 'Approve');?>
	<?php else:?>
		<?php echo form_submit('unapprove', 'Unapprove');?>
	<?php endif; ?>
<?php echo form_close();?>
<h2>Comments</h2>
<?php if($comments) :?>
	<?php //print_r($comments);?>
	<?php foreach($comments as $c) :?>
		<div class="comment">
			<span><?php echo $c->username;?></span>
			<p><?php echo $c->comment;?></p>
		</div>
	<?php endforeach;?>
<?php else: ?>
	<p>No comments at this time. :(</p>
<?php endif;?>
<?php echo form_open();?>
	<div class="validation"><?php echo validation_errors(); ?></div>
	<label>Comment</label>
	<?php echo form_textarea('comment_text');?>
	<?php echo form_hidden('user', $this->session->userdata('user_id'));?>
	<?php echo form_hidden('nid', $mockup->nid);?>
	<input class="btn primary" type="submit" name="comment" value="Add Comment"/>
<?php echo form_close();?>