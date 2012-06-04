<div id="new-project" class="modal fade">
	<div class="modal-header">
		<a href="#" class="close">x</a>
		<h3>New Project</h3>
	</div>
	<div class="modal-body">
		<form action="/project/create" method="post">
			<input type="text" name="project_name" value="" placeholder="Project Name"/>
			<input type="date" name="due_date" value="" placeholder="Due Date"/>
	</div>
	<div class="modal-footer">
		<input class="btn primary" type="submit" name="create" value="Create" />
		</form>
	</div>
</div>