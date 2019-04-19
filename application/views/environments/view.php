<!-- Data table plugin CSS-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css" />


<div class="container">
	<div class="row mb-4">
		<h2 class="text-center"><?= gethostname() ?> : Environments list</h2>
		<br/>
	</div>

	<div class="row">

		<div class="col-md-12">

			<table id="table_data" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
				<thead>
				<tr>
					<th>Status</th>
					<th>Name</th>
					<!--<th>Folder</th>-->
					<th>Php Version</th>
					<th>Php port</th>
					<th>MySQL Version</th>
					<th>MySQL port</th>
					<th>PMA</th>
					<th>PMA port</th>
					<th>Creator</th>
					<th>Creation date</th>
					<th>STFP user</th>
					<th>STFP pass</th>
					<th>Action</th>
				</tr>
				</thead>

			</table>

		</div>
	</div>

	<a class="btn btn-primary" href="<?= base_url() . 'add-environment' ?>" role="button">Add environment</a>

</div>

<script language="JavaScript" src="https://code.jquery.com/jquery-1.12.4.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js" type="text/javascript"></script>


<script>


		function getStatus () {

			var statusArray = document.getElementsByClassName("status");
			for (i = 0; i < statusArray.length; i++) {
				var folder = statusArray[i].value;
				var id = "dot_" + folder;
				checkStatusAjax(folder, id);
			}

			setInterval(function(){ getStatus() }, <?= REFRESH_ENV_STATUS_INTERVAL ?>);
		}

		function checkStatusAjax (folder, id) {

			var form_data = {
				folder : folder
			};

			$.ajax({
				url: "<?php echo base_url('environments/environments/checkStatus'); ?>",
				type: 'GET',
				data: form_data,
				dataType: 'json',
				async : true,
				success:function(response){
					if (response) {
						document.getElementById(id).style.backgroundColor = "green";
					} else {
						document.getElementById(id).style.backgroundColor = "red";
					}
				},
				error:function (xhr, ajaxOptions, thrownError){

					document.getElementById(id).style.backgroundColor = "red";
					console.log("ERROR : ");

					if (xhr) {
						console.log("ERROR (xhr) : " + xhr);
					}

					if (ajaxOptions) {
						console.log("ERROR (ajaxOptions) : " + ajaxOptions);
					}

					if (thrownError) {
						console.log("ERROR (thrownError) : " + thrownError);
					}

				},
			});
		}

		$('#table_data').DataTable({
			"initComplete": function(settings, json) {
				getStatus();
			},
			"processing": true,
			"info": true,
			"stateSave": true,
			data: <?= $jsonEnvironments ?>,
			"columns": [
				{
					"data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",
					"data": function (data) {
						//console.log(data);
						return '<span class="dot" id="dot_' + data.folder + '"></span><input type="hidden" class="status" value="' + data.folder + '" />'
						//return '<span class="dot"></span><input class="status" id="delete_id" name="delete_id" value="" />'
					}
				},
				{ "data": "<?= Environments_model::name ?>" },
				/*{ "data": "<?= Environments_model::folder ?>" },*/
				{ "data": "<?= Environments_model::phpVersionId ?>" },
				{
					"data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",
					"data": function (data) {
						return '<a href="http://<?= $_SERVER['SERVER_NAME'] ?>:' + data.php_port + '" target="_blank">' + data.php_port + '</a>'
					}
				},
				/*{ "data": "<?= Environments_model::phpPort ?>" },*/
				{ "data": "<?= Environments_model::mysqlVersionId ?>" },
				{ "data": "<?= Environments_model::mysqlPort ?>" },
				{ "data": "<?= Environments_model::hasPma ?>" },
				{
					"data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",
					"data": function (data) {
						if (data.has_pma == 1) {
							return '<a href="http://<?= $_SERVER['SERVER_NAME'] ?>:' + data.pma_port + '" target="_blank">' + data.pma_port + '</a>'
						} else {
							return data.pma_port
						}
					}
				},
				{ "data": "<?= Environments_model::creator ?>" },
				{ "data": "<?= Environments_model::createdDate ?>" },
				{ "data": "<?= Environments_model::sftpUser ?>" },
				{ "data": "<?= Environments_model::sftpPassword ?>" },
/*				{
					"data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",
					"data": function (data) {
						console.log(data);
						return '<span class="status"> Php </span> &nbsp; ' +
							'<button class="btn btn-info" type="button"> Stop </button> &nbsp; '	 +
							'<button class="btn btn-danger" type="button"> Delete </button> &nbsp; '						}
				},*/
				{
					"data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",
					"data": function (data) {
						return '<button onclick="startEnv(\'' + data.folder + '\')" class="btn btn-success" type="button"> Start </button> &nbsp; '	 +
							'<button onclick="stopEnv(\'' + data.folder + '\')" class="btn btn-info" type="button"> Stop </button> &nbsp; ' +
							'<button onclick="deleteEnv(\'' + data.folder + '\')" class="btn btn-danger" type="button"> Delete </button> &nbsp; '
/*						return '<button class="btn btn-success" type="button"> View </button> &nbsp; ' +
							'<button class="btn btn-info" type="button"> Stop </button> &nbsp; '	 +
							'<button class="btn btn-danger" type="button"> Delete </button> &nbsp; '*/						}
				}
			]
		});

		function startEnv (folder) {

			var form_data = {
				folder : folder
			};

			$.ajax({
				url: "<?php echo base_url('environments/environments/startEnv'); ?>",
				type: 'GET',
				data: form_data,
				dataType: 'json',
				async : true,
				success:function(response){
					if (response) {
						alert('Success');
						getStatus();
					} else {
						alert('Error');
					}
				},
				error:function (xhr, ajaxOptions, thrownError){

					alert('Error');

					console.log("ERROR : ");

					if (xhr) {
						console.log("ERROR (xhr) : " + xhr);
					}

					if (ajaxOptions) {
						console.log("ERROR (ajaxOptions) : " + ajaxOptions);
					}

					if (thrownError) {
						console.log("ERROR (thrownError) : " + thrownError);
					}

				},
			});
		}

		function stopEnv (folder) {

			var form_data = {
				folder : folder
			};

			$.ajax({
				url: "<?php echo base_url('environments/environments/stopEnv'); ?>",
				type: 'GET',
				data: form_data,
				dataType: 'json',
				async : true,
				success:function(response){
					alert('Success');
					if (response) {
						getStatus();
					} else {
						alert('Error');
					}
				},
				error:function (xhr, ajaxOptions, thrownError){

					alert('Error');

					console.log("ERROR : ");

					if (xhr) {
						console.log("ERROR (xhr) : " + xhr);
					}

					if (ajaxOptions) {
						console.log("ERROR (ajaxOptions) : " + ajaxOptions);
					}

					if (thrownError) {
						console.log("ERROR (thrownError) : " + thrownError);
					}

				},
			});
		}

		function deleteEnv (folder) {

			var form_data = {
				folder : folder
			};

			$.ajax({
				url: "<?php echo base_url('environments/environments/deleteEnv'); ?>",
				type: 'GET',
				data: form_data,
				dataType: 'json',
				async : true,
				success:function(response){
					if(!alert('Success')){window.location.reload();}
				},
				error:function (xhr, ajaxOptions, thrownError){

					alert('Error');

					console.log("ERROR : ");

					if (xhr) {
						console.log("ERROR (xhr) : " + xhr);
					}

					if (ajaxOptions) {
						console.log("ERROR (ajaxOptions) : " + ajaxOptions);
					}

					if (thrownError) {
						console.log("ERROR (thrownError) : " + thrownError);
					}

				},
			});

		}



	// Server Side Call using Url
	//var table = $('#table_data').DataTable({
	//    //"responsive": true,
	//    "processing": true,
	//    "serverSide": true,
	//    "info": true,
	//    "stateSave": true,
	//    "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "All"]],
	//    "ajax": {
	//        "url": "/DatatableAdvance/AjaxGetJsonData",
	//        "type": "GET"
	//    },
	//    "columns": [
	//        {
	//            "className": 'details-control',
	//            "orderable": false,
	//            "data": null,
	//            "orderable": true,
	//            "defaultContent": ''
	//        },
	//        {
	//            "data": "Inquiry", "orderable": false,
	//            "data": function (data) {
	//                return '<input type="hidden" id="hiddenTextInquiry" name="hiddenTextInquiry" value="' + data.InquiryId + '">' + data.InquiryId
	//            }
	//        },
	//        { "data": "ReferencesDetails", "orderable": false },
	//        { "data": "ReferencesNumber", "orderable": true },
	//        { "data": "Remark", "orderable": true },
	//        { "data": "TelephoneNumber", "orderable": true },
	//        { "data": "Date", "orderable": true },
	//        {
	//            "data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",
	//            "data": function (data) {
	//                return '<button class="btn btn-danger" type="button">' + data.InquiryId + 'Delete</button>'
	//            }
	//        }
	//    ],
	//    "order": [[0, 'asc']]
	//});

</script>
<style>
	.dot {
		height: 10px;
		width: 10px;
		border-radius: 50%;
		background-color: #bbb;
		display: inline-block;
	}
</style>