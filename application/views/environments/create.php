<!-- Data table plugin CSS-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css" />


<div class="container">
	<div class="row">
		<form method="post" accept-charset="utf-8" action="<?= base_url(); ?>create-environment" role="form" class="col-md-9 go-right">
			<h2>Add environment</h2>
			<div class="form-group">
				<label >Options</label>
				<div class="checkbox">
					<label><input id="webserverTrigger" name="webserverTrigger" type="checkbox" value="true">Webserver</label>
					<label><input id="phpTrigger" name="phpTrigger" type="checkbox" value="true">Php</label>
					<label><input id="mysqlTrigger" name="mysqlTrigger" type="checkbox" value="true">MySQL / MariaDB</label>
					<label><input id="sftp" name="sftp" type="checkbox" value="true">SFTP</label>
					<label><input id="pma" name="pma" type="checkbox" value="true">phpMyAdmin</label>
<!--					<label><input id="redis" name="redis" type="checkbox" value="true">Redis</label>-->
				</div>
			</div>
			<div class="form-group">
				<label for="name">Name</label>
				<input id="name" name="name" type="text" class="form-control" required>
			</div>
			<div id="webserverDiv" class="form-group">
				<label for="webserver">Webserver</label>
				<select class="form-control" id="webserver" name="webserver" required>
					<option value="apache">Apache</option>
					<!--<option value="nginx">NGINX</option>-->Todo
				</select>
			</div>
			<div id="phpDiv" class="form-group">
				<label for="phpVersion">Php version</label>
				<select class="form-control" id="phpVersion" name="phpVersion" required>
					<option>--</option>
					<option value="custom">Custom (with Dockerfile)</option>
					<?php
					if (isset($phpVersions) && !empty($phpVersions)) {
						foreach ($phpVersions as $phpVersion) {
							?>
							<option value=<?= $phpVersion->id ?>><?= $phpVersion->version ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>
			<div id="phpDockerfileDiv" class="form-group">
				<label for="comment">Dockerfile Php</label>
				<textarea class="form-control" rows="5" id="phpDockerfile" name="phpDockerfile"></textarea>
			</div>
			<div id="mysqlDiv" class="form-group">
				<label for="mysqlVersion">MySQL / MariaDB version</label>
				<select class="form-control" id="mysqlVersion" name="mysqlVersion" required>
					<option>--</option>
					<option value="custom">Custom (with Dockerfile)</option>
					<?php
					if (isset($mysqlVersions) && !empty($mysqlVersions)) {
						foreach ($mysqlVersions as $mysqlVersion) {
							?>
							<option value=<?= $mysqlVersion->id ?>><?= $mysqlVersion->version ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>
			<div id="mysqlDockerfileDiv" class="form-group">
				<label for="comment">Dockerfile MySQL / MariaDB</label>
				<textarea class="form-control" rows="5" id="mysqlDockerfile" name="mysqlDockerfile"></textarea>
			</div>
			<button type="submit" id="form-submit" class="btn btn-primary btn-lg pull-right ">Add</button>
		</form>
	</div>
</div>

<script>
	$(function() {

		$( "#phpDockerfileDiv" ).hide();
		$( "#mysqlDockerfileDiv" ).hide();
		$( "#webserverDiv" ).hide();
		$( "#phpDiv" ).hide();
		$( "#mysqlDiv" ).hide();

		$('#webserverTrigger').on('change', function() {

			if ($('#webserverTrigger').is(':checked')) {
				$( "#webserverDiv" ).show();
			} else {
				$( "#webserverDiv" ).hide();
			}

		});

		$('#phpTrigger').on('change', function() {

			if ($('#phpTrigger').is(':checked')) {
				$( "#phpDiv" ).show();

				if ($('#phpVersion').val().includes("custom") ) {
					$( "#phpDockerfileDiv" ).show();
				} else {
					$( "#phpDockerfileDiv" ).hide();
				}

			} else {
				$( "#phpDiv" ).hide();
				$( "#phpDockerfileDiv" ).hide();
			}

		});

		$('#mysqlTrigger').on('change', function() {

			if ($('#mysqlTrigger').is(':checked')) {
				$( "#mysqlDiv" ).show();

				if ($('#mysqlVersion').val().includes("custom") ) {
					$( "#mysqlDockerfileDiv" ).show();
				} else {
					$( "#mysqlDockerfileDiv" ).hide();
				}

			} else {
				$( "#mysqlDiv" ).hide();
				$( "#mysqlDockerfileDiv" ).hide();
			}

		});

		$('#phpVersion').on('change', function() {

			if ( this.value.includes("custom") ) {
				$( "#phpDockerfileDiv" ).show();
			} else {
				$( "#phpDockerfileDiv" ).hide();
			}

		});

		$('#mysqlVersion').on('change', function() {

			if ( this.value.includes("custom") ) {
				$( "#mysqlDockerfileDiv" ).show();
			} else {
				$( "#mysqlDockerfileDiv" ).hide();
			}

		});
	});
</script>
