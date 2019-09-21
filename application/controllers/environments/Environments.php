<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Environments extends MI_Controller {

    function __construct()
    {
        parent::__construct();
    }


    public function index()
    {

        // Load models
        $this->load->model('Environments_model');
        // Instantiate json environments
        $data['jsonEnvironments'] = "";
        if (isset($this->ion_auth->user()->row()->id) && !empty($this->ion_auth->user()->row()->id)) {
            $userGroups = $this->ion_auth->get_users_groups($this->ion_auth->user()->row()->id)->result();
            if (isset($userGroups[0]) && !empty($userGroups[0]) && isset($userGroups[0]->id)) {

                // ADMIN
                if ($userGroups[0]->id == 1) {
                    $environments = $this->Environments_model->getAllEnvironments();
                } else {
                    $environments = $this->Environments_model->getEnvironmentsByCreator($this->ion_auth->user()->row()->id);
                }

                foreach ($environments as $environment) {

                    if (isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId})) {

                        $environment->has_php = "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>";

                    } else {

                        $environment->has_php = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::phpVersionId} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::phpPort} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::phpSSLPort} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::xDebugRemoteHost} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";

                    }

                    if (isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId})) {
                        $environment->has_mysql = "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>";
                    } else {
                        $environment->has_mysql = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::mysqlVersionId} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::mysqlUser} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::mysqlPassword} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::mysqlPort} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                    }

                    if ($environment->{Environments_model::hasPma}) {
                        $environment->{Environments_model::hasPma} = "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>";
                    } else {
                        $environment->{Environments_model::hasPma} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::pmaPort} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                    }

                    if ($environment->{Environments_model::hasSftp}) {
                        $environment->{Environments_model::hasSftp} = "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>";
                    } else {
                        $environment->{Environments_model::hasSftp} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::sftpUser} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::sftpPassword} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                        $environment->{Environments_model::sftpPort} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
                    }


                }

                if (isset($environments) && !empty($environments)) {
                    $data['jsonEnvironments']  = json_encode($environments);
                }

                $this->load->view('elements/header');
                $this->load->view('environments/view', $data);

            } else {
                // Todo error
            }

        } else {
            // Todo error
        }

    }

    /*
     * Form submission methods START
     */
    // Form environment : add only for the moment
    public function formEnvironment()
    {

        // Todo
        // - add : define params : ports, id, dockerfile, passwords (+ check & error message -> ex : ports)
        // - edit (warning : volumes erased and add copy source if first delete then reconstruct => check commented "formEnvironment" method)

        if (
            //(!isset($_POST['webserverTrigger']) || empty($_POST['webserverTrigger'])) && // Todo : when webserver not embedded in php
            (!isset($_POST['phpTrigger']) || empty($_POST['phpTrigger'])) &&
            (!isset($_POST['mysqlTrigger']) || empty($_POST['mysqlTrigger'])) &&
            (!isset($_POST['sftp']) || empty($_POST['sftp']))
        ) {
            // Todo : proper error (display error flash ?!)
            exit ('no options choosen');
        }


        // Todo more params setable
        // Todo Errors
        // Todo Mails (params env.php)
        // Todo Facto
        // Todo : Warning session problem (db empty & exemple : no logout)
        // Todo : Warning ion_auth_users_groups delete cascade (+check all)


        // Load models
        $this->load->model('Environments_model');
        $this->load->model('Mysqlversions_model');
        $this->load->model('Phpversions_model');

        // Load helpers
        $this->load->helpers('Security_helper');

        // Load libs
        $this->load->library('zip');

        // User id
        $userId = $this->ion_auth->user()->row()->id;
        if (!isset($userId) || empty($userId)) {
            // Todo : proper error (display error flash ?!)
            exit ('no user id');
        }

        // 1. Instantiate environment
        $environment = new stdClass();

        // 2. Set userId
        $environment->{Environments_model::userId} = $userId;

        // 3. Set folder uniqId
        $phpUniqueId = uniqid();
        $environment->{Environments_model::folder} = $phpUniqueId;
        //Custom id management $environment->{Environments_model::folder} = (isset($_POST['customId']) && !empty($_POST['customId'])) ? strtolower(str_replace(' ', '_', trim($_POST['customId']))) : uniqid();

        // 4. Get $_POST params
        // Set name
        $environment->{Environments_model::name} = (isset($_POST['name']) && !empty($_POST['name'])) ? trim($_POST['name']) : $phpUniqueId;
        // Set webserver
        // Todo : $_POST['webserverTrigger']
        $environment->{Environments_model::webserver} = (isset($_POST['webserver']) && !empty($_POST['webserver'])) ? mb_strtolower($_POST['webserver']) : null ;

        // Set php
        $environment->{Environments_model::phpVersionId} = (isset($_POST['phpTrigger']) && !empty($_POST['phpTrigger']) && isset($_POST['phpVersion']) && !empty($_POST['phpVersion']) && $_POST['phpVersion'] != "--" && $_POST['phpVersion'] != "custom") ? $_POST['phpVersion'] : null;
        if (isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId})) {
            $environment->{Environments_model::phpPort} = (isset($_POST['phpPort']) && !empty($_POST['phpPort'])) ? /*$this->TODOchekAvailablePort($_POST['phpPort'])*/ $_POST['phpPort'] : $this->getAvailablePort();// Todo
            $environment->{Environments_model::phpSSLPort} = (isset($_POST['phpSSLPort']) && !empty($_POST['phpSSLPort'])) ? /*$this->TODOchekAvailablePort($_POST['phpSSLPort'])*/ $_POST['phpSSLPort'] : $this->getAvailablePort();// Todo
            // Todo : $environment->{Environments_model::phpDockerfile} = (isset($_POST['phpDockerfile']) && !empty($_POST['phpDockerfile'])) ? $_POST['phpDockerfile'] : null;
        }

        // Set mysql
        $environment->{Environments_model::mysqlVersionId} = (isset($_POST['mysqlTrigger']) && !empty($_POST['mysqlTrigger']) && isset($_POST['mysqlVersion']) && !empty($_POST['mysqlVersion'])  && $_POST['mysqlVersion'] != "--" && $_POST['mysqlVersion'] != "custom") ? $_POST['mysqlVersion'] : null;
        if (isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId})) {
            $environment->{Environments_model::mysqlUser} = (isset($_POST['mysqlUser']) && !empty($_POST['mysqlUser'])) ? $_POST['mysqlUser'] : 'root';// Todo
            $environment->{Environments_model::mysqlPassword} = (isset($_POST['mysqlPassword']) && !empty($_POST['mysqlPassword'])) ? $_POST['mysqlPassword'] : randomPassword();// Todo
            $environment->{Environments_model::mysqlPort} = (isset($_POST['mysqlPort']) && !empty($_POST['mysqlPort'])) ? /*$this->TODOchekAvailablePort($_POST['mysqlPort'])*/ $_POST['mysqlPort'] : $this->getAvailablePort();// Todo
            // Todo : $environment->{Environments_model::mysqlDockerfile} = (isset($_POST['mysqlDockerfile']) && !empty($_POST['mysqlDockerfile'])) ? $_POST['mysqlDockerfile'] : null;
        }

        // Set phpmyadmin
        $environment->{Environments_model::hasPma} = (isset($_POST['mysqlTrigger']) && !empty($_POST['mysqlTrigger']) && isset($_POST['mysqlVersion']) && !empty($_POST['mysqlVersion']) && isset($_POST['pma']) && !empty($_POST['pma'])) ? true : false;
        if (isset($environment->{Environments_model::hasPma}) && !empty($environment->{Environments_model::hasPma})) {
            $environment->{Environments_model::pmaPort} = (isset($_POST['pmaPort']) && !empty($_POST['pmaPort'])) ? /*$this->TODOchekAvailablePort($_POST['pmaPort'])*/ $_POST['pmaPort'] : $this->getAvailablePort();// Todo
        }

        // Set sftp
        $environment->{Environments_model::hasSftp} = (isset($_POST['sftp']) && !empty($_POST['sftp'])) ? true : false;
        if (isset($environment->{Environments_model::hasSftp}) && !empty($environment->{Environments_model::hasSftp})) {
            //$environment->{Environments_model::sftpUser} = (isset($_POST['sftpUser']) && !empty($_POST['sftpUser'])) ? $_POST['sftpUser'] : $environment->{Environments_model::folder};// Todo
            $environment->{Environments_model::sftpUser} = strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));
            $environment->{Environments_model::sftpPassword} = (isset($_POST['sftpPassword']) && !empty($_POST['sftpPassword'])) ? $_POST['sftpPassword'] : randomPassword();// Todo
            $environment->{Environments_model::sftpPort} = (isset($_POST['sftpPort']) && !empty($_POST['sftpPort'])) ? /*$this->TODOchekAvailablePort($_POST['sftpPort'])*/ $_POST['sftpPort'] : $this->getAvailablePort();// Todo
        }

        // Set xDebug
        if ($_POST['xDebugTrigger']) {
            $environment->{Environments_model::xDebugRemoteHost} = (isset($_POST['xDebugRemoteHost']) && !empty($_POST['xDebugRemoteHost'])) ? $_POST['xDebugRemoteHost'] : '0.0.0.0';
        }

        // 5. Generate docker compose
        $isProjectDockerFolderCreated = $this->generateProjectDockerFolder($environment);

        // 6. Add environment
        if ($isProjectDockerFolderCreated) {
            $environmentId = $this->Environments_model->insertEnvironment($environment);
            if (isset($environmentId) && $environmentId != -1) {

                // 7. Start docker compose

                $folderName = strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));
                $dockerComposePath = INNER_ENVS_FOLDER . "/" . $folderName . "/";
                $this->startEnvironment($dockerComposePath);

                // Todo send admin mail ?!
                redirect('environments');

            } else {
                // Todo : proper error (display error flash ?!)
                exit('Error insert env add || update !');
            }
        } else {
            // Todo : proper error (display error flash ?!)
            exit('Error docker compose file !');
        }


    }

//    public function formEnvironment()
//    {
//
//
//        exit('stop');
//        // Todo more params setable
//        // Todo Errors
//        // Todo Mails (params env.php)
//        // Todo Facto
//        // Todo : Warning session problem (db empty & exemple : no logout)
//        // Todo : Warning ion_auth_users_groups delete cascade (+check all)
//
//        // Load models
//        $this->load->model('Environments_model');
//        $this->load->model('Mysqlversions_model');
//        $this->load->model('Phpversions_model');
//
//        // Load helpers
//        $this->load->helpers('Security_helper');
//
//        // Load libs
//        $this->load->library('zip');
//
//        // User id
//        $userId = $this->ion_auth->user()->row()->id;
//        // Todo if not -> error (+ all error management)
//
//        // 1. Get $_POST params
//        $environment = new stdClass();
//        $environment->{Environments_model::userId} = $userId;
//        $environment->{Environments_model::name} = (isset($_POST['name']) && !empty($_POST['name'])) ? trim($_POST['name']) : "Error"; // Todo generate name ?!
//        $environment->{Environments_model::webserver} = mb_strtolower((isset($_POST['webserver']) && !empty($_POST['webserver'])) ? $_POST['webserver'] : null);
//        $environment->{Environments_model::folder} = (isset($_POST['customId']) && !empty($_POST['customId'])) ? strtolower(str_replace(' ', '_', trim($_POST['customId']))) : strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));
//
//        // Todo : uniqId Or not
//        //$environment->{Environments_model::folder} = (isset($_POST['customId']) && !empty($_POST['customId'])) ? trim(strtolower(str_replace(' ', '_', $_POST['customId']))) : uniqid();
//        $environment->{Environments_model::phpVersionId} = (isset($_POST['phpVersion']) && !empty($_POST['phpVersion']) && $_POST['phpVersion'] != "--" && $_POST['phpVersion'] != "custom") ? $_POST['phpVersion'] : null;
//        $environment->{Environments_model::phpDockerfile} = (isset($_POST['phpDockerfile']) && !empty($_POST['phpDockerfile'])) ? $_POST['phpDockerfile'] : null;
//        $environment->{Environments_model::mysqlVersionId} = (isset($_POST['mysqlVersion']) && !empty($_POST['mysqlVersion'])  && $_POST['mysqlVersion'] != "--" && $_POST['mysqlVersion'] != "custom") ? $_POST['mysqlVersion'] : null;
//        $environment->{Environments_model::mysqlDockerfile} = (isset($_POST['mysqlDockerfile']) && !empty($_POST['mysqlDockerfile'])) ? $_POST['mysqlDockerfile'] : null;
//        $environment->{Environments_model::hasPma} = (isset($_POST['phpVersion']) && !empty($_POST['phpVersion']) && isset($_POST['pma']) && !empty($_POST['pma'])) ? true : false;
//        $environment->{Environments_model::hasSftp} = (isset($_POST['sftp']) && !empty($_POST['sftp'])) ? true : false;
//
//        // Get ports
//        $environment->{Environments_model::phpPort} = (isset($_POST['phpPort']) && !empty($_POST['phpPort'])) ? /*$this->TODOchekAvailablePort($_POST['phpPort'])*/ $_POST['phpPort'] : $this->getAvailablePort();// Todo
//        $environment->{Environments_model::phpSSLPort} = (isset($_POST['phpSSLPort']) && !empty($_POST['phpSSLPort'])) ? /*$this->TODOchekAvailablePort($_POST['phpSSLPort'])*/ $_POST['phpSSLPort'] : $this->getAvailablePort();// Todo
//        $environment->{Environments_model::mysqlPort} = (isset($_POST['mysqlPort']) && !empty($_POST['mysqlPort'])) ? /*$this->TODOchekAvailablePort($_POST['mysqlPort'])*/ $_POST['mysqlPort'] : $this->getAvailablePort();// Todo
//        $environment->{Environments_model::pmaPort} = (isset($_POST['pmaPort']) && !empty($_POST['pmaPort'])) ? /*$this->TODOchekAvailablePort($_POST['pmaPort'])*/ $_POST['pmaPort'] : $this->getAvailablePort();// Todo
//
//        // MySQL params
//        $environment->{Environments_model::mysqlUser} = (isset($_POST['mysqlUser']) && !empty($_POST['mysqlUser'])) ? $_POST['mysqlUser'] : 'root';// Todo
//        $environment->{Environments_model::mysqlPassword} = (isset($_POST['mysqlPassword']) && !empty($_POST['mysqlPassword'])) ? $_POST['mysqlPassword'] : randomPassword();// Todo
//
//        // Sftp param
//        $environment->{Environments_model::sftpUser} = (isset($_POST['sftpUser']) && !empty($_POST['sftpUser'])) ? $_POST['sftpUser'] : $environment->{Environments_model::folder};// Todo
//        $environment->{Environments_model::sftpPassword} = (isset($_POST['sftpPassword']) && !empty($_POST['sftpPassword'])) ? $_POST['sftpPassword'] : randomPassword();// Todo
//        $environment->{Environments_model::sftpPort} = (isset($_POST['sftpPort']) && !empty($_POST['sftpPort'])) ? /*$this->TODOchekAvailablePort($_POST['sftpPort'])*/ $_POST['sftpPort'] : $this->getAvailablePort();// Todo
//
//        // 2. Manage action type update or add
//        $isEditAction = false;
//        $tmpSourceFolderPath = '';
//        $newFolderSrcPath = '';
//        //if (isset($_POST['envId']) && !empty($_POST['envId'])) {
//        if (isset($_POST['initialFolderName']) && !empty($_POST['initialFolderName'])) {
//
//            $isEditAction = true;
//
//            // TODO : php copy SRC
//            // TODO : check if name/id different than intial
//            // TODO : sql keep DB
//            // TODO : sftp ?!
//
//            // Get initial folder name
//            $initialFolderName = $_POST['initialFolderName'];
//
//            // Get ENVS path
//            $path = getcwd() . '/' . ABSOLUTE_ENVS_FOLDER . '/';
//
//            // Set initial src folder path
//            $initialFolderSrcPath = $path . $initialFolderName . "/src";
//
//            // Set new src folder path
//            $newFolderSrcPath = $path . $environment->{Environments_model::folder} . "/src";
//
//            // Create tmp_src_folder
//            $tmpSourceFolderPath = $path . '/tmp_src_folder_' . $initialFolderName;
//            shell_exec('mkdir ' . $tmpSourceFolderPath);
//
//            // Move files from actual src folder to tmp src folder
//            shell_exec('mv -v ' . $initialFolderSrcPath . '/* ' . $tmpSourceFolderPath);
//            shell_exec('mv -v ' . $initialFolderSrcPath . '/.* ' . $tmpSourceFolderPath);
//
//            //$envId = $_POST['envId'];
//
//            $isEnvDeleted = $this->deleteEnv($initialFolderName);
//            if (!$isEnvDeleted) {
//                exit ('UPDATE : DELETE ERROR');
//            }
//
//        }
//
//        // Generate docker compose
//        $this->generateProjectDockerFolder($environment);
//
//        if ($isEditAction) {
//
//            // Move initial files to new src folder
//            shell_exec('mv -v ' . $tmpSourceFolderPath . '/* ' . $newFolderSrcPath);
//            shell_exec('mv -v ' . $tmpSourceFolderPath . '/.* ' . $newFolderSrcPath);
//
//
//            // Delete tmp folder
//            shell_exec('rm ' . $tmpSourceFolderPath);
//
//        }
//
//        // Add environment
//        $environmentId = $this->Environments_model->insertEnvironment($environment);
//
//        if (isset($environmentId) && $environmentId != -1) {
//
//            // Start docker compose
//            $dockerComposePath = INNER_ENVS_FOLDER . "/" . $environment->{Environments_model::folder} . "/";
//            $this->startEnvironment($dockerComposePath);
//
//            redirect('environments');
//
//        } else {
//            // todo manage error
//            exit('Error insert env add || update !');
//        }
//
//        // Todo generate compose then run it
//        // Send mail admin
//        // redirect('environments');
//
//    }

    function recursive_copy($source, $dest)
    {
        if(is_dir($source))
        {
            if(!is_dir($dest))
            {
                mkdir($dest, 0777, true);
            }

            $dir_items = array_diff(scandir($source), array('..', '.'));

            if(count($dir_items) > 0)
            {
                foreach($dir_items as $v)
                {
                    $this->recursive_copy(rtrim(rtrim($source, '/'), '\\').DIRECTORY_SEPARATOR.$v, rtrim(rtrim($dest, '/'), '\\').DIRECTORY_SEPARATOR.$v);
                }
            }
        }
        elseif(is_file($source))
        {
            copy($source, $dest);
        }
    }

    /*
     * Form submission methods END
     */


    // Todo AJAX
    public function addEnvironment()
    {

        $data['phpVersions'] = $this->getPhpVersions();
        $data['mysqlVersions'] = $this->getMysqlVersions();

        $this->load->view('elements/header');
        $this->load->view('environments/create', $data);

    }

    public function editEnvironment()
    {

        if (isset($_GET["id"]) && !empty($_GET["id"])) {

            // Load models
            $this->load->model('Environments_model');

            // Set folder name
            $folderName = $_GET["id"];

            // Get env by user id and folder name
            $environment = $this->Environments_model->getEnvironmentByFolderAndUserId($folderName, $this->ion_auth->user()->row()->id);
            if (isset($environment) && !empty($environment)) {
                // Set data
                $data['phpVersions'] = $this->getPhpVersions();
                $data['mysqlVersions'] = $this->getMysqlVersions();
                $data['environment'] = $environment;

                $this->load->view('elements/header');
                $this->load->view('environments/create', $data);

            } else {
                $this->session->set_flashdata('error', 'Erreur : no environment');// Todo constant
                redirect('environments');
                exit();
            }

        } else {

            $this->session->set_flashdata('error', 'Erreur : no id');// Todo constant
            redirect('environments');
            exit();

        }

    }

    public function updateXDebugRemoteHostByAjax()
    {

        // Load models
        $this->load->model('Environments_model');
        $this->load->model('Mysqlversions_model');
        $this->load->model('Phpversions_model');

        $response = false;

        if (isset($_POST['folder']) && !empty($_POST['folder'])) {

            $environment = $this->Environments_model->getEnvironmentByFolder($_POST['folder']);
            $environment->{Environments_model::xDebugRemoteHost} = $_POST['newXDebugRemote'];

            // Generate docker compose
            $isProjectDockerFolderUpdated = $this->generateProjectDockerFolder($environment);
            if ($isProjectDockerFolderUpdated) {
                $this->Environments_model->editEnvironment($environment);
            }

            // Start docker compose
            $folderName = strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));
            $dockerComposePath = INNER_ENVS_FOLDER . "/" . $folderName . "/";
            $this->startEnvironment($dockerComposePath);

            $response = true;

        }

        echo json_encode($response);

    }

    public function startEnv()
    {

        // Load models
        $this->load->model('Environments_model');
        $this->load->model('Mysqlversions_model');
        $this->load->model('Phpversions_model');

        $response = false;

        if (isset($_GET['folder']) && !empty($_GET['folder'])) {

            $environment = $this->Environments_model->getEnvironmentByFolder($_GET['folder']);

            // Generate docker compose
            $this->generateProjectDockerFolder($environment);

            // Start docker compose
            $folderName = strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));
            $dockerComposePath = INNER_ENVS_FOLDER . "/" . $folderName . "/";
            $this->startEnvironment($dockerComposePath);

            $response = true;

        }

        echo json_encode($response);

    }

    public function stopEnv()
    {
        $response = false;

        if (isset($_GET['name']) && !empty($_GET['name'])) {

            $folderName = strtolower(str_replace(' ', '_', trim($_GET['name'])));
            $dockerComposePath = INNER_ENVS_FOLDER . "/" . $folderName . "/";
            $this->stopEnvironment($dockerComposePath);

            $response = true;
        }

        echo json_encode($response);
    }

    // Todo : WARNING disabled -> use with care ! (clean/prune all docker host env)
    public function cleanAllDockerEnv()
    {

        // Delete useless volumes
        //shell_exec('sudo docker system prune --volumes -f');
        //shell_exec('docker system prune --volumes -f');
        echo json_encode(true);

    }

    public function deleteEnvAjax()
    {

        // Load models
        $this->load->model('Environments_model');

        $response = false;

        if (isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['folder']) && !empty($_POST['folder'])) {

            $folderName = strtolower(str_replace(' ', '_', trim($_POST['name'])));
            $networkName = $folderName . "_default";
            $volumeName = $folderName . "_mysql_dir-" . $_POST['folder'];
            $dockerComposePath = INNER_ENVS_FOLDER . "/" . $folderName . "/";
            $this->stopEnvironment($dockerComposePath);
            $this->deleteEnvironment($dockerComposePath, $networkName, $volumeName);

            $this->Environments_model->deleteEnvironmentByName($_POST['name']);

            // Delete project folder
            shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; rm -rf ' . $folderName . ';');

            $response = true;

        }

        echo json_encode($response);
    }

/*    public function deleteEnv($folderName)
    {

        // Load models
        $this->load->model('Environments_model');

        $response = false;

        if (isset($folderName) && !empty($folderName)) {

            $dockerComposePath = INNER_ENVS_FOLDER . "/" . $folderName . "/";
            $this->stopEnvironment($dockerComposePath);

            $networkName = $folderName . "_default";
            $volumeName = $folderName . "_mysql_dir-" . $_POST['folder'];

            $this->deleteEnvironment($dockerComposePath);

            $this->Environments_model->deleteEnvironmentByFolder($folderName);

            // Delete project folder
            shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; rm -rf ' . $folderName . ';');

            $response = true;

        }

        // $this->cleanAllDockerEnv();// Todo careful

        return $response;
    }*/

    // Todo : check by project attributes !!! (ex : if no sql, do not check sql !)
    public function checkStatus()
    {

        // Load models
        $this->load->model('Environments_model');

        // Instantiate general status true (todo : env by env ?!)
        $generalStatus = true;

        // Get project by folder
        if (isset($_GET['folder']) && !empty($_GET['folder'])) {

            // Set env id
            $envId = $_GET['folder'];

            // Get env
            $environment = $this->Environments_model->getEnvironmentByFolder($_GET['folder']);

            // Set env name
            $envName = strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));

            // Check if env has MySQL
            if ($generalStatus && isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId})) {

                // Check MySQL
                $mySqlContainer = $envName . "_mysql-" . $envId . "_1";
                //$cmd = 'sudo docker inspect -f "{{.State.Running}}" ' . $mySqlContainer. ';';
                $cmd = 'docker inspect -f "{{.State.Running}}" ' . $mySqlContainer. ';';
                $mySqlContainerStatus = shell_exec($cmd);

                if (isset($mySqlContainerStatus) && !empty($mySqlContainerStatus) && strpos($mySqlContainerStatus, 'true') !== false) {
                    $generalStatus = true;
                } else {
                    $generalStatus = false;
                }

            }

            // Check if env has PHP (todo one day : webserver)
            if ($generalStatus && isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId})) {

                // Check php
                $phpContainer = $envName . "_php-" . $envId . "_1";
                //$cmd = 'sudo docker inspect -f "{{.State.Running}}" ' . $phpContainer. ';';
                $cmd = 'docker inspect -f "{{.State.Running}}" ' . $phpContainer. ';';
                $phpContainerStatus = shell_exec($cmd);

                if (isset($phpContainerStatus) && !empty($phpContainerStatus) && strpos($phpContainerStatus, 'true') !== false) {
                    $generalStatus = true;
                } else {
                    $generalStatus = false;
                }
            }

            // Check if env has STFP
            if ($generalStatus && isset($environment->{Environments_model::hasSftp}) && !empty($environment->{Environments_model::hasSftp})) {

                // Check PMA
                $sftpContainer = $envName . "_sftp-" . $envId . "_1";
                //$cmd = 'sudo docker inspect -f "{{.State.Running}}" ' . $sftpContainer. ';';
                $cmd = 'docker inspect -f "{{.State.Running}}" ' . $sftpContainer. ';';
                $sftpContainerStatus = shell_exec($cmd);

                if (isset($sftpContainerStatus) && !empty($sftpContainerStatus) && strpos($sftpContainerStatus, 'true') !== false) {
                    $generalStatus = true;
                } else {
                    $generalStatus = false;
                }
            }

            // Check if env has PMA
            if ($generalStatus && isset($environment->{Environments_model::hasPma}) && !empty($environment->{Environments_model::hasPma})) {

                // Check PMA
                $pmaContainer = $envName . "_phpmyadmin-" . $envId . "_1";
                //$cmd = 'sudo docker inspect -f "{{.State.Running}}" ' . $pmaContainer. ';';
                $cmd = 'docker inspect -f "{{.State.Running}}" ' . $pmaContainer. ';';
                $pmaContainerStatus = shell_exec($cmd);

                if (isset($pmaContainerStatus) && !empty($pmaContainerStatus) && strpos($pmaContainerStatus, 'true') !== false) {
                    $generalStatus = true;
                } else {
                    $generalStatus = false;
                }
            }

        }
        else {
            $generalStatus = false;
        }


        echo json_encode($generalStatus);

    }

    public function formImportEnvironment()
    {
        $this->load->view('elements/header');
        $this->load->view('environments/import', null);
    }

    public function exportEnv()
    {

        // Load models
        $this->load->model('Environments_model');

        if (isset($_GET["folder"]) && !empty($_GET["folder"])) {

            $folder = $_GET["folder"];
            $env = $this->Environments_model->getEnvironmentByFolder($folder);
            if (isset($env) && !empty($env)) {
                $envJson = json_encode($env);

                $file = $folder.'.json';
                file_put_contents($file, $envJson);

            } else {
                $file = 'Error folder !';
                $envJson = json_encode($env);
                file_put_contents($file, $envJson);

            }

            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/force-download');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                @unlink($file);
            }

        }

    }

    public function importEnvironment()
    {

        // Load models
        $this->load->model('Environments_model');
        $this->load->model('Mysqlversions_model');
        $this->load->model('Phpversions_model');

        // Load helpers
        $this->load->helpers('Security_helper');

        if (isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['envJson']) && !empty($_POST['envJson'])) {

            $environment = json_decode($_POST['envJson']);

            // Instantiate project uniqid (folder name)
            $projectUniqId = uniqid();

            unset($environment->{Environments_model::pk});
            unset($environment->{Environments_model::creator});
            unset($environment->{Environments_model::createdDate});
            // User id
            $userId = $this->ion_auth->user()->row()->id;
            $environment->{Environments_model::userId} = $userId;
            $environment->{Environments_model::name} = $_POST['name'];
            $environment->{Environments_model::folder} = $projectUniqId;

            // Get ports
            $environment->{Environments_model::phpPort} = $this->getAvailablePort();
            $environment->{Environments_model::phpSSLPort} = $this->getAvailablePort();
            $environment->{Environments_model::mysqlPort} = $this->getAvailablePort();
            $environment->{Environments_model::pmaPort} = $this->getAvailablePort();

            // MySQL params
            // Root user
            $mySqlRootUser = 'root';
            $environment->{Environments_model::mysqlUser} = $mySqlRootUser;
            $environment->{Environments_model::mysqlPassword} = randomPassword();

            // Sftp params
            $environment->{Environments_model::sftpUser} = $projectUniqId;
            $environment->{Environments_model::sftpPassword} = randomPassword();
            $environment->{Environments_model::sftpPort} = $this->getAvailablePort();


            if (isset($environment->{Environments_model::folder}) && !empty($environment->{Environments_model::folder})) {

                // Generate docker compose
                $this->generateProjectDockerFolder($environment);

                // Add environment
                $environmentId = $this->Environments_model->insertEnvironment($environment);

                if (isset($environmentId) && $environmentId != -1) {

                    // Start docker compose
                    $folderName = strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));
                    $dockerComposePath = INNER_ENVS_FOLDER . "/" . $folderName . "/";
                    $this->startEnvironment($dockerComposePath);

                    redirect('environments');

                } else {
                    // todo manage error
                    exit('Error insert env (import) !');
                }

            } else {
                // Todo error
            }
        } else {
            // Todo error
        }
    }

    /**
     * @param $environment
     */
    private function generateProjectDockerFolder($environment)
    {

        // Load Parser lib
        $this->load->library('parser');

        // Instantiate docker compose
        $dockerCompose = "";

        // Add compose header
        $filePath = "templates/docker/compose/docker-compose-services-header.yml";
        $data = array();
        $dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

        // Add SFTP (if needed)
        // Todo : different check & disable ?!
        /*		if ($data[$environment->{Environments_model::hasSftp}]) {
                    $filePath = "templates/docker/compose/docker-compose-sftp.yml";
                } else {
                    $flePath = "templates/docker/compose/docker-compose-sftp-disabled.yml";
                }*/
        if (isset($environment->{Environments_model::hasSftp}) && !empty($environment->{Environments_model::hasSftp})) {

            if (isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId}) && $environment->{Environments_model::phpVersionId} != "--") {
                $filePath = "templates/docker/compose/docker-compose-sftp.yml";
            } else {
                $filePath = "templates/docker/compose/docker-compose-sftp-no-source-no-logs.yml";
            }

            $data = array();
            $data['project'] = $environment->{Environments_model::folder};
            $data['user'] = $environment->{Environments_model::sftpUser};
            $data['pass'] = $environment->{Environments_model::sftpPassword};
            $data['port'] = $environment->{Environments_model::sftpPort};
            $dockerCompose .= $this->parser->parse($filePath, $data, TRUE);
        }

        // Services
        // Add MySQL (if needed)
        if (isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId}) && $environment->{Environments_model::mysqlVersionId} != "--") {

            if ($environment->{Environments_model::mysqlVersionId} != "custom") {

                $data = array('project' => $environment->{Environments_model::folder}, 'port' => $environment->{Environments_model::mysqlPort}, 'user' => $environment->{Environments_model::mysqlUser}, 'pass' => $environment->{Environments_model::mysqlPassword});

                $mysqlTag = $this->Mysqlversions_model->getTagById($environment->{Environments_model::mysqlVersionId});
                if (isset($mysqlTag->tag) && !empty($mysqlTag->tag)) {
                    $data['version'] = $mysqlTag->tag;
                } else {
                    // Todo error
                }

                $filePath = "templates/docker/compose/docker-compose-mysql-image.yml";
                $dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

            } else {
                // Todo builds
            }

        } else {
            // Todo if dockerfile builds
        }

        // Add PHP (if needed)
        if (isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId}) && $environment->{Environments_model::phpVersionId} != "--") {
            if ($environment->{Environments_model::phpVersionId} != "custom") {

                $data = array('project' => $environment->{Environments_model::folder}, 'port' => $environment->{Environments_model::phpPort}, 'port-ssl' => $environment->{Environments_model::phpSSLPort});

                $phpTag = $this->Phpversions_model->getTagById($environment->{Environments_model::phpVersionId});
                if (isset($phpTag->tag) && !empty($phpTag->tag)) {
                    $data['version'] = $phpTag->tag;
                } else {
                    // Todo error
                }

                // Todo : local env path
                $localPath = "../src";

                // Todo : Logs php, mysql

                #$filePath = "templates/docker/compose/docker-compose-php-image.yml";

                // Check if env has MySQL (to link php or not)
                if (isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId}) && $environment->{Environments_model::mysqlVersionId} != "--") {
                    $filePath = "templates/docker/compose/docker-compose-php-build.yml";
                } else {
                    $filePath = "templates/docker/compose/docker-compose-php-build-without-db.yml";
                }
                $data['localPath'] = $localPath;
                $dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

                // Todo check if xDebug
                $data['xDebug_remote_host'] = $environment->{Environments_model::xDebugRemoteHost};

                // Create image/php dockerfile
                $filePath = "templates/docker/dockerfile/php/dockerfile-php.php";
                $dockerfile = $this->parser->parse($filePath, $data, TRUE);
                $environment->{Environments_model::phpDockerfile} = $dockerfile;


            } else {
                // Todo builds
            }
        }


        /*		if (isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId}) && $environment->{Environments_model::phpVersionId} != "--") {
                    if ($environment->{Environments_model::phpVersionId} != "custom") {

                        $data = array('project' => $environment->{Environments_model::folder}, 'port' => $environment->{Environments_model::phpPort});

                        $phpTag = $this->Phpversions_model->getTagById($environment->{Environments_model::phpVersionId});
                        if (isset($phpTag->tag) && !empty($phpTag->tag)) {
                            $data['version'] = $phpTag->tag;
                        } else {
                            // Todo error
                        }

                        // Todo : local env path
                        $localPath = "../src";

                        // Create php dockerfile
                        // Logs php, mysql

                        #$filePath = "templates/docker/compose/docker-compose-php-image.yml";
                        $filePath = "templates/docker/compose/docker-compose-php-build.yml";
                        $data['localPath'] = $localPath;
                        $dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

                    } else {
                        // Todo builds
                    }
                } else {
                    // Todo if dockerfile builds
                }*/

        // Add PMA (if needed)
        if (isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId}) && $environment->{Environments_model::mysqlVersionId} != "--" && isset($environment->{Environments_model::hasPma}) && !empty($environment->{Environments_model::hasPma})) {
            $filePath = "templates/docker/compose/docker-compose-pma.yml";
            $data = array('project' => $environment->{Environments_model::folder}, 'port' => $environment->{Environments_model::pmaPort});
            $dockerCompose .= $this->parser->parse($filePath, $data, TRUE);
        }

        // Volumes
        // Add MySQL volumes (if needed)
        if ((isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId})) || (isset($environment->{Environments_model::mysqlDockerfile}) && !empty($environment->{Environments_model::mysqlDockerfile}))) {
            $filePath = "templates/docker/compose/docker-compose-mysql-volume.yml";
            $data = array('project' => $environment->{Environments_model::folder}, 'port' => $environment->{Environments_model::pmaPort});
            $dockerCompose .= $this->parser->parse($filePath, $data, TRUE);
        }

        // Set final docker compose
        $environment->{Environments_model::dockerCompose} = $dockerCompose;

        if (isset($environment->{Environments_model::dockerCompose}) && !empty($environment->{Environments_model::dockerCompose})) {

            // Folder name
            $folderName = strtolower(str_replace(' ', '_', trim($environment->{Environments_model::name})));

            // Create folder
            shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; mkdir ' . $folderName . '; chmod 777 -R ' . $folderName . '; cd ' . $folderName . '; ');
            // OR add mkdir apache ?! shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; mkdir ' . $folderName . '; chmod 777 -R ' . $folderName . '; cd ' . $folderName . '; mkdir src; mkdir apache;');

            // Set php image (if needed)
            if (isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId}) && $environment->{Environments_model::phpVersionId} != "--") {

                $filename = ABSOLUTE_ENVS_FOLDER . "/" . $folderName . "/src/index.php";
                if (!file_exists($filename)){

                    // Create project folder
                    shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; cd ' . $folderName . '; mkdir src; cd src; echo "<?php echo phpinfo(); ?>" >> index.php ; chmod 777 index.php');

                }

                // Create php folder (dockerfile)
                $dockerFolderPath = ABSOLUTE_ENVS_FOLDER . "/" . $folderName . "/docker";
                if (!file_exists($dockerFolderPath)){

                    // Create docker project folder
                    shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; cd ' . $folderName . '; mkdir docker; chmod -R 777 docker;');

                }

                shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; cd ' . $folderName. '; cd docker; mkdir image; chmod -R 777 image; cd image; mkdir php; chmod -R 777 php;');


                // Todo php dockerfile
                $dockerfilePhpPath = ABSOLUTE_ENVS_FOLDER . "/" . $folderName . "/docker/image/php/";
                file_put_contents($dockerfilePhpPath . "Dockerfile", $environment->{Environments_model::phpDockerfile});

            }

            $dockerComposePath = ABSOLUTE_ENVS_FOLDER . "/" . $folderName . "/";
            file_put_contents($dockerComposePath . "docker-compose.yml", $environment->{Environments_model::dockerCompose});

            // CHMOD -R 777 folder
            shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; chmod 777 -R ' . $folderName . ';');
            shell_exec('cd ' . ABSOLUTE_ENVS_FOLDER . '; chmod -R 777 ' . $folderName . ';');

            return true;

        } else {
            // Todo error
            return false;
        }

    }

    private function startEnvironment($dockerComposePath)
    {
        //shell_exec('sudo docker exec docker-dood-milo bash -c \'cd ' . $dockerComposePath . ';docker-compose up -d --build\'');
        shell_exec('docker exec docker-dood-milo bash -c \'cd ' . $dockerComposePath . ';docker-compose up -d --build\'');
    }

    private function stopEnvironment($dockerComposePath)
    {
        //shell_exec('sudo docker exec docker-dood-milo bash -c \'cd ' . $dockerComposePath . ';docker-compose stop\'');
        shell_exec('docker exec docker-dood-milo bash -c \'cd ' . $dockerComposePath . ';docker-compose stop\'');
    }

    private function deleteEnvironment($dockerComposePath, $networkName, $volumeName)
    {

        shell_exec('docker exec docker-dood-milo bash -c \'cd ' . $dockerComposePath . ';docker-compose rm -f\'');
        shell_exec('docker exec docker-dood-milo bash -c \'docker network rm ' . $networkName . '\'');
        shell_exec('docker exec docker-dood-milo bash -c \'docker volume rm ' . $volumeName . '\'');

        //shell_exec('sudo docker exec docker-dood-milo bash -c \'cd ' . $dockerComposePath . ';docker-compose rm -f\'');
        //shell_exec('sudo docker exec docker-dood-milo bash -c \'docker network rm ' . $networkName . '\'');
        //shell_exec('sudo docker exec docker-dood-milo bash -c \'docker volume rm ' . $volumeName . '\'');
    }

    private function startEnvironmentById($envId)
    {
        // Get id
        // STart env
    }

    public function getAvailablePort()
    {

        $busyPorts = array();
        $busyPorts = $this->getDockerMachinePort($busyPorts);
        $busyPorts = $this->getPhpPort($busyPorts);
        $busyPorts = $this->getMysqlPort($busyPorts);
        $busyPorts = $this->getPmaPort($busyPorts);

        if (isset($busyPorts) && !empty($busyPorts)) {

            if (count($busyPorts) > 15000) {
                $port =  -1;
            } else {
                $portsArray = array();
                foreach ($busyPorts as $port) {
                    $portsArray[] = $port;
                }
                while( in_array( ($port = rand(10000,25000)), $portsArray ) );
            }

        } else {
            $port = rand(10000,25000);
        }

        return $port;

    }

    private function getDockerMachinePort($busyPorts)
    {


        //$portsStr = shell_exec('sudo docker ps --format "{{.Ports}}";');
        $portsStr = shell_exec('docker ps --format "{{.Ports}}";');

        $portsArray = explode("tcp", $portsStr);

        $from = ":";
        $to = "->";

        foreach ($portsArray as $portArray) {
            $subStr = substr($portArray, strpos($portArray,$from)+strlen($from),strlen($portArray));
            $finalSubStr = substr($subStr,0,strpos($subStr,$to));

            if (isset($finalSubStr) && !empty($finalSubStr)) {
                $busyPorts[] = $finalSubStr;
            }
        }

        return $busyPorts;
    }

    /*
     * php START Todo : controller php
     */

    private function getPhpVersions()
    {

        // Load models
        $this->load->model('Phpversions_model');

        return $this->Phpversions_model->getPhpVersions();

    }

    private function getPhpPort($busyPorts)
    {

        // Load models
        $this->load->model('Environments_model');

        $ports = $this->Environments_model->getPhpPorts();
        if (isset($ports) && !empty($ports)) {
            foreach ($ports as $port) {
                if (isset($port->{Environments_model::phpPort}) && !empty($port->{Environments_model::phpPort})) {
                    $busyPorts[] = $port->{Environments_model::phpPort};
                }

                if (isset($port->{Environments_model::phpSSLPort}) && !empty($port->{Environments_model::phpSSLPort})) {
                    $busyPorts[] = $port->{Environments_model::phpSSLPort};
                }
            }
        }

        return $busyPorts;

    }

    /*
     * php END
     */

    /*
     * MySQL START Todo : controller MySQL
     */

    public function getMysqlVersions()
    {

        // Load models
        $this->load->model('Mysqlversions_model');

        return $this->Mysqlversions_model->getMysqlVersions();

    }

    private function getMysqlPort($busyPorts)
    {

        // Load models
        $this->load->model('Environments_model');

        $ports = $this->Environments_model->getMysqlPorts();
        if (isset($ports) && !empty($ports)) {
            foreach ($ports as $port) {
                if (isset($port->{Environments_model::mysqlPort}) && !empty($port->{Environments_model::mysqlPort})) {
                    $busyPorts[] = $port->{Environments_model::mysqlPort};
                }
            }
        }

        return $busyPorts;

    }

    /*
     * MySQL END
     */

    /*
     * PMA START Todo : controller PMA
     */

    private function getPmaPort($busyPorts)
    {

        // Load models
        $this->load->model('Environments_model');

        $ports = $this->Environments_model->getPmaPorts();
        if (isset($ports) && !empty($ports)) {
            foreach ($ports as $port) {
                if (isset($port->{Environments_model::pmaPort}) && !empty($port->{Environments_model::pmaPort})) {
                    $busyPorts[] = $port->{Environments_model::pmaPort};
                }
            }
        }

        return $busyPorts;

    }

    /*
     * PMA END
     */

}