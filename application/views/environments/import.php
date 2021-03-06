
<link rel="stylesheet" href=<?php echo base_url() . "/assets/css/pace/orange/pace-theme-minimal.css"?> type="text/css" />

<script src=<?php echo base_url() . "/assets/js/pace.min.js"?>></script>

<div class="container">
	<div class="row">
		<form method="post" accept-charset="utf-8" action="<?= base_url(); ?>import-environment" role="form" class="col-md-9 go-right">
			<h2>Import environment</h2>
			<div class="form-group">
				<label for="name">Name</label>
				<input id="name" name="name" type="text" class="form-control" required>
			</div>
			<divclass="form-group">
				<label for="comment">JSON</label>
				<textarea class="form-control" rows="5" id="envJson" name="envJson"></textarea>
			</div>
			<button type="submit" onclick="launchLoader()" id="form-submit" class="btn btn-primary btn-lg pull-right ">Add</button>
		</form>
	</div>
</div>

<script>
	function launchLoader () {
		Pace.restart();
	}
</script>