<?php $this->load->view('dashboard/project-part');?>

<div class="container-fluid">
      <div class="sidebar">
        <div class="well">
        	<a class="btn success" data-controls-modal="new-project" data-backdrop="static" >+ New Project</a>
        	<aside class="search">
        	</aside>
        </div>
      </div>
      <div class="content">
		<h2>Projects</h2>
		<?php if($projects): ?>
		<div class="project-container">
			<?php foreach($projects as $p) :?>
				<div class="item">
					<div class="skip">
						<ul>
						<?php foreach ($p->nodes as $node) :?>
							<li><a href="<?php echo site_url('mockup/view/'.$node->nid);?>"><img src="<?php echo $node->thumbnail_path;?>" width="200" height="200"/></a></li>
						<?php endforeach;?>
						</ul>
					</div>
					<h4><a rel="popover" data-content="<?php echo $this->functions->time_ago($p->created);?>" data-original-title="Details" href="<?php echo site_url('project/view/'.$p->pid);?>"><?php echo $p->name;?></a></h4>
				</div>
			<?php endforeach;?>
		</div>
		<?php endif;?>
	</div>
</div>
