<!-- Data table plugin CSS-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href=<?php echo base_url() . "/assets/css/pace/orange/pace-theme-minimal.css"?> type="text/css" />

<script src=<?php echo base_url() . "/assets/js/pace.min.js"?>></script>

<div class="container">
    <div class="row mb-4">
        <h2 class="text-center"><?= gethostname() ?> : Environments list</h2>
        <?php if ($this->session->flashdata('error')) { ?>

            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                <strong><?php echo $this->session->flashdata('error'); ?></strong>
            </div>

        <?php } ?>
        <?php if ($this->session->flashdata('success')) { ?>

            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                <strong><?php echo $this->session->flashdata('success'); ?></strong>
            </div>

        <?php } ?>
    </div>
    <div class="row">

        <div class="col-md-12">

            <table id="table_data" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Webserver</th>
                    <th>Php</th>
                    <th>MySQL / MariaDB</th>
                    <th>phpMyAdmin</th>
                    <th>STFP</th>
                    <th>Creator</th>
                    <th>Creation date</th>
                    <!--<th>Folder</th>-->
                    <th>Php version</th>
                    <th>Php port</th>
                    <th>Php SSL port</th>
                    <th>MySQL / MariaDB version</th>
                    <th>MySQL / MariaDB root user</th>
                    <th>MySQL / MariaDB root password</th>
                    <th>MySQL / MariaDB  port</th>
                    <th>phpMyAdmin port</th>
                    <th>STFP user</th>
                    <th>STFP pass</th>
                    <th>STFP port</th>
                    <th>Action</th>
                </tr>
                </thead>

            </table>

        </div>
    </div>

    <a class="btn btn-success" href="<?= base_url() . 'add-environment' ?>" role="button">Add environment</a>
    <a class="btn btn-warning" href="<?= base_url() . 'display-import-environment' ?>" role="button">Import environment</a>
    <button class="btn btn-info" type="button" onclick="getStatus()" >Refresh status &nbsp<span class="glyphicon glyphicon-refresh"></span></button>
    <!--Todo : Think about it (many buttons -> container, volumes, builds, etc ?! in a menu ?!)-->
    <!--<button class="btn btn-danger" type="button" onclick="cleanAllDockerEnv()" >CLEAN ALL DOCKER ENV (HOST) &nbsp<span class="glyphicon glyphicon-refresh"></span></button>-->

</div>

<script language="JavaScript" src="https://code.jquery.com/jquery-1.12.4.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script language="JavaScript" src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js" type="text/javascript"></script>

<script>

    function getStatus (withMessage) {

        var statusArray = document.getElementsByClassName("status");
        for (i = 0; i < statusArray.length; i++) {
            var folder = statusArray[i].value;
            var id = "dot_" + folder;
            checkStatusAjax(folder, id);
        }

        if (withMessage == true) {
            alert('Success');
        }
        //setInterval(function(){ getStatus() }, <?= REFRESH_ENV_STATUS_INTERVAL ?>);
    }

    function checkStatusAjax (folder, id) {
        Pace.restart();
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
        data: <?= isset($jsonEnvironments) && !empty($jsonEnvironments) ? $jsonEnvironments : null ?>,
        "columns": [
            {
                "data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",
                "data": function (data) {
                    //console.log(data);
                    return '&nbsp&nbsp&nbsp<span class="dot" id="dot_' + data.folder + '"></span><input type="hidden" class="status" value="' + data.folder + '" />'
                    //return '<span class="dot"></span><input class="status" id="delete_id" name="delete_id" value="" />'
                }
            },
            { "data": "<?= Environments_model::name ?>" },
            { "data": "<?= Environments_model::webserver ?>" },
            { "data": "has_php" },
            { "data": "has_mysql" },
            { "data": "<?= Environments_model::hasPma ?>" },
            { "data": "<?= Environments_model::hasSftp ?>" },
            { "data": "<?= Environments_model::creator ?>" },
            { "data": "<?= Environments_model::createdDate ?>" },
            /*{ "data": "<?= Environments_model::folder ?>" },*/
            { "data": "<?= Environments_model::phpVersionId ?>" },
            {
                "data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",// Todo : UGLY !!!
                "data": function (data) {
                    if (data.has_php == "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>") {
                        return '<a href="http://<?= $_SERVER['SERVER_NAME'] ?>:' + data.php_port + '" target="_blank">' + data.php_port + '</a>'
                    } else {
                        return data.php_port
                    }
                }
            },
            {
                "data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",// Todo : UGLY !!!
                "data": function (data) {
                    if (data.has_php == "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>") {
                        return '<a href="https://<?= $_SERVER['SERVER_NAME'] ?>:' + data.php_ssl_port + '" target="_blank">' + data.php_ssl_port + '</a>'
                    } else {
                        return data.php_port
                    }
                }
            },
            /*{ "data": "<?= Environments_model::phpPort ?>" },*/
            { "data": "<?= Environments_model::mysqlVersionId ?>" },
            { "data": "<?= Environments_model::mysqlUser ?>" },
            { "data": "<?= Environments_model::mysqlPassword ?>" },
            { "data": "<?= Environments_model::mysqlPort ?>" },
            {
                "data": "Inquiry", "bSearchable": false, "bSortable": false, "sWidth": "40px",// Todo : UGLY !!!
                "data": function (data) {
                    if (data.has_pma == "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>") {
                        return '<a href="http://<?= $_SERVER['SERVER_NAME'] ?>:' + data.pma_port + '" target="_blank">' + data.pma_port + '</a>'
                    } else {
                        return data.pma_port
                    }
                }
            },
            { "data": "<?= Environments_model::sftpUser ?>" },
            { "data": "<?= Environments_model::sftpPassword ?>" },
            { "data": "<?= Environments_model::sftpPort ?>" },
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
                        '<button onclick="deleteEnv(\'' + data.folder + '\')" class="btn btn-danger" type="button"> Delete </button> &nbsp; ' +
                        '<button onclick="editEnv(\'' + data.folder + '\')" class="btn btn-primary" type="button"> Edit </button> &nbsp; ' +
                        '<button onclick="exportEnv(\'' + data.folder + '\')" class="btn btn-warning" type="button"> Export </button> &nbsp; '
                    /*						return '<button class="btn btn-success" type="button"> View </button> &nbsp; ' +
                                                '<button class="btn btn-info" type="button"> Stop </button> &nbsp; '	 +
                                                '<button class="btn btn-danger" type="button"> Delete </button> &nbsp; '*/						}
            }
        ]
    });

    // Todo : WARNING -> use with care ! (clean/prune all docker host env)
    function cleanAllDockerEnv () {

        if ( confirm( "Warning DOCKER HOST ENV : remove all stopped containers / all networks not used by at least one container / all dangling images / all build cache / all volumes not used by at least one container ?" ) ) {

            Pace.restart();

            $.ajax({
                url: "<?php echo base_url('environments/environments/cleanAllDockerEnv'); ?>",
                type: 'GET',
                async : true,
                success:function(response){
                    alert('ALL DOCKER HOST ENVIRONMENT WAS CLEANED !');
                },
                error:function (xhr, ajaxOptions, thrownError){

                    alert('Error : during CLEAN ALL DOCKER HOST ENVIRONMENT');
                    getStatus();

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

    }

    function startEnv (folder) {

        Pace.restart();

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
                    getStatus();
                } else {
                    console.log('Error : startEnv !'); // Todo manage error !
                }
            },
            error:function (xhr, ajaxOptions, thrownError){

                getStatus();

                console.log('Error : startEnv ajax error !'); // Todo manage error !

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

        Pace.restart();

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
                if (response) {
                    getStatus();
                } else {
                    console.log('Error : stopEnv !'); // Todo manage error !
                }
            },
            error:function (xhr, ajaxOptions, thrownError){

                getStatus();

                console.log('Error : stopEnv ajax !'); // Todo manage error !

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


    function exportEnv (folder) {
        Pace.restart();
        document.location = "<?php echo base_url('environments/environments/exportEnv?folder='); ?>" + folder;
    }

    function editEnv (folder) {
        console.log("<?= base_url() . 'edit-environment?id=' ?>" + folder);
        window.location.href ="<?= base_url() . 'edit-environment?id=' ?>" + folder;

    }

    function deleteEnv (folder) {

        Pace.restart();

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
                window.location.reload();
            },
            error:function (xhr, ajaxOptions, thrownError){

                alert('Error : delete env');
                getStatus();

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