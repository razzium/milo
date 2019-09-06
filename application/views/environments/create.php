
<link rel="stylesheet" href=<?php echo base_url() . "/assets/css/pace/orange/pace-theme-minimal.css"?> type="text/css" />

<script src=<?php echo base_url() . "/assets/js/pace.min.js"?>></script>
<?php
if (isset($environment) && !empty($environment)) {
    var_dump($environment);
}
?>
<div class="container">
	<div class="row">
		<form method="post" accept-charset="utf-8" action="<?= base_url(); ?>create-environment" role="form" class="col-md-9 go-right">
			<h2>Add environment</h2>
			<div class="form-group">
				<label >Options</label>
				<div class="checkbox">
					<label><input id="webserverTrigger" name="webserverTrigger" type="checkbox" value="true" <?= isset($environment) && isset($environment->webserver) && !empty($environment->webserver) ? 'checked' : '' ?>>Webserver</label>
					<label><input id="phpTrigger" name="phpTrigger" type="checkbox" value="true" <?= isset($environment) && isset($environment->php_version_id) && !empty($environment->php_version_id) ? 'checked' : '' ?>>Php</label>
					<label><input id="mysqlTrigger" name="mysqlTrigger" type="checkbox" value="true" <?= isset($environment) && isset($environment->mysql_version_id) && !empty($environment->mysql_version_id) ? 'checked' : '' ?>>MySQL / MariaDB</label>
					<label><input id="sftp" name="sftp" type="checkbox" value="true" <?= isset($environment) && isset($environment->has_sftp) && !empty($environment->has_sftp) ? 'checked' : '' ?>>SFTP</label>
					<label><input id="pma" name="pma" type="checkbox" value="true" <?= isset($environment) && isset($environment->has_pma) && !empty($environment->has_pma) ? 'checked' : '' ?>>phpMyAdmin</label>
<!--					<label><input id="redis" name="redis" type="checkbox" value="true">Redis</label>-->
				</div>
			</div>
			<div class="form-group">
				<label for="name">Name</label>
				<input id="name" name="name" type="text" value="<?= isset($environment) && isset($environment->name) && !empty($environment->name) ? $environment->name : '' ?>" class="form-control" required>
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
					<!--<option value="custom">Custom (with Dockerfile)</option>-->
					<?php
					if (isset($phpVersions) && !empty($phpVersions)) {
						foreach ($phpVersions as $phpVersion) {
                            if (isset($environment) && isset($environment->php_version_id) && !empty($environment->php_version_id) && $environment->php_version_id == $phpVersion->id) {
                                ?>
                                <option value=<?= $phpVersion->id ?> selected="selected"><?= $phpVersion->version ?></option>
                                <?php
                            } else {
                                ?>
                                <option value=<?= $phpVersion->id ?>><?= $phpVersion->version ?></option>
                                <?php
                            }
							?>
							<?php
						}
					}
					?>
				</select>
			</div>
			<div id="phpDockerfileDiv" class="form-group">
				<label for="comment">Dockerfile Php</label>
				<textarea class="form-control" rows="5" id="phpDockerfile" name="phpDockerfile" readonly>
                    <?php
                    if (isset($environment) && isset($environment->php_dockerfile) && !empty($environment->php_dockerfile)) {
                        echo $environment->php_dockerfile;
                    }
                    ?>
                </textarea>
			</div>
			<div id="mysqlDiv" class="form-group">
				<label for="mysqlVersion">MySQL / MariaDB version</label>
				<select class="form-control" id="mysqlVersion" name="mysqlVersion" required>
					<option>--</option>
					<!--<option value="custom">Custom (with Dockerfile)</option>-->
					<?php
                    if (isset($mysqlVersions) && !empty($mysqlVersions)) {
                        foreach ($mysqlVersions as $mysqlVersion) {
                            if (isset($environment) && isset($environment->mysql_version_id) && !empty($environment->mysql_version_id) && $environment->mysql_version_id == $mysqlVersion->id) {
                                ?>
                                <option value=<?= $mysqlVersion->id ?> selected="selected"><?= $mysqlVersion->version ?></option>
                                <?php
                            } else {
                                ?>
                                <option value=<?= $mysqlVersion->id ?>><?= $mysqlVersion->version ?></option>
                                <?php
                            }
                            ?>
                            <?php
                        }
                    }
					?>
				</select>
			</div>
			<div id="mysqlDockerfileDiv" class="form-group">
				<label for="comment">Dockerfile MySQL / MariaDB</label>
				<textarea class="form-control" rows="5" id="mysqlDockerfile" name="mysqlDockerfile" readonly>
                     <?php
                     if (isset($environment) && isset($environment->mysql_dockerfile) && !empty($environment->mysql_dockerfile)) {
                         echo $environment->mysql_dockerfile;
                     }
                     ?>
                </textarea>
			</div>
			<button type="submit" onclick="launchLoader()" id="form-submit" class="btn btn-primary btn-lg pull-right "><?= isset($environment) && !empty($environment) ? 'Edit' : 'Add' ?></button>
		</form>
	</div>
</div>

<script>
	$(function() {

        var actionType = "<?= isset($environment) && !empty($environment) ? 'edit' : 'add' ?>";

            if (actionType == "edit") {

/*                // WAY 1 : Control Dockerfiles
                var hasWebserver = "<?= isset($environment) && isset($environment->webserver) && !empty($environment->webserver) ? 'true' : 'false' ?>";
                if (hasWebserver) {
                    $( "#webserverDiv" ).show();
                } else {
                    $( "#webserverDiv" ).hide();
                }

                var hasPhp = "<?= isset($environment) && isset($environment->php_version_id) && !empty($environment->php_version_id) ? 'true' : 'false' ?>";
                if (hasPhp) {
                    $( "#phpDiv" ).show();
                    $( "#phpDockerfileDiv" ).show();
                } else {
                    $( "#phpDiv" ).hide();
                    $( "#phpDockerfileDiv" ).hide();
                }

                var hasMySQL = "<?= isset($environment) && isset($environment->mysql_version_id) && !empty($environment->mysql_version_id) ? 'true' : 'false' ?>";
                if (hasMySQL) {
                    $( "#mysqlDiv" ).show();
                    $( "#mysqlDockerfileDiv" ).show();
                } else {
                    $( "#mysqlDiv" ).hide();
                    $( "#mysqlDockerfileDiv" ).hide();
                }*/

                // WAY 2 : No control Dockerfiles (todo warning : UI only, not secure)
                var hasWebserver = "<?= isset($environment) && isset($environment->webserver) && !empty($environment->webserver) ? 'true' : 'false' ?>";
                if (hasWebserver) {
                    $( "#webserverDiv" ).show();
                } else {
                    $( "#webserverDiv" ).hide();
                }

                var hasPhp = "<?= isset($environment) && isset($environment->php_version_id) && !empty($environment->php_version_id) ? 'true' : 'false' ?>";
                if (hasPhp) {
                    $( "#phpDiv" ).show();
                } else {
                    $( "#phpDiv" ).hide();
                }

                var hasMySQL = "<?= isset($environment) && isset($environment->mysql_version_id) && !empty($environment->mysql_version_id) ? 'true' : 'false' ?>";
                if (hasMySQL) {
                    $( "#mysqlDiv" ).show();
                } else {
                    $( "#mysqlDiv" ).hide();
                }


                $( "#phpDockerfileDiv" ).hide();
                $( "#mysqlDockerfileDiv" ).hide();

            }
            else {

                $( "#webserverDiv" ).hide();
                $( "#phpDiv" ).hide();
                $( "#mysqlDiv" ).hide();
                $( "#phpDockerfileDiv" ).hide();
                $( "#mysqlDockerfileDiv" ).hide();

            }

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
<script>
	function launchLoader () {
		Pace.restart();
	}
</script>
