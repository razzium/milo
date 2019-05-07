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
					}

					if (isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId})) {
						$environment->has_mysql = "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>";
					} else {
						$environment->has_mysql = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
					}

					if ($environment->{Environments_model::hasPma}) {
						$environment->{Environments_model::hasPma} = "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>";
					} else {
						$environment->{Environments_model::hasPma} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
					}

					if ($environment->{Environments_model::hasSftp}) {
						$environment->{Environments_model::hasSftp} = "<span style=\"color:green\" class=\"glyphicon glyphicon-ok\"></span>";
					} else {
						$environment->{Environments_model::hasSftp} = "<span style=\"color:red\" class=\"glyphicon glyphicon-remove\"></span>";
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

	// Todo AJAX
	public function addEnvironment()
	{

		$data['phpVersions'] = $this->getPhpVersions();
		$data['mysqlVersions'] = $this->getMysqlVersions();

		$this->load->view('elements/header');
		$this->load->view('environments/create', $data);

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
			$this->generateEnvDockerCompose($environment);

			// Todo check if exists
			#echo shell_exec('cd envs; mkdir ' . $environment->{Environments_model::folder} . '; cd ' . $environment->{Environments_model::folder}. '; mkdir src; cd src; sh ../../../.docker/scripts_shell/docker_compose_create_index_php.sh;');
			shell_exec('cd envs; mkdir ' . $environment->{Environments_model::folder} . '; cd ' . $environment->{Environments_model::folder}. '; mkdir src; cd src; sh ../../../.docker/scripts_shell/docker_compose_create_index_php.sh;');

			// Create php folder (dockerfile)
			#echo shell_exec('cd envs; cd ' . $environment->{Environments_model::folder} . '; mkdir image; chmod -R 777 image; cd image; mkdir php; chmod -R 777 php;');
			shell_exec('cd envs; cd ' . $environment->{Environments_model::folder} . '; mkdir image; chmod -R 777 image; cd image; mkdir php; chmod -R 777 php;');

			// Start docker compose
			$this->startEnvironment($environment);

			$dockerComposePath = ENVS_FOLDER . "/" . $_GET['folder'] . "/";
			#echo shell_exec('pwd');
			shell_exec('pwd');
			#echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/launch_docker-compose.sh;');
			shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/launch_docker-compose.sh;');

			$response = true;

		}

		echo json_encode($response);
	}

	public function stopEnv()
	{
		$response = false;

		if (isset($_GET['folder']) && !empty($_GET['folder'])) {

			$dockerComposePath = ENVS_FOLDER . "/" . $_GET['folder'] . "/";
			#echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/stop_docker-compose.sh;');
			shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/stop_docker-compose.sh;');

			$response = true;
		}

		echo json_encode($response);
	}

	public function deleteEnv()
	{

		// Load models
		$this->load->model('Environments_model');

		$response = false;

		if (isset($_GET['folder']) && !empty($_GET['folder'])) {

			$dockerComposePath = ENVS_FOLDER . "/" . $_GET['folder'] . "/";
			# echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/stop_docker-compose.sh;');
			shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/stop_docker-compose.sh;');

			$dockerComposePath = ENVS_FOLDER . "/" . $_GET['folder'] . "/";
			# echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/delete_docker-compose.sh;');
			shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/delete_docker-compose.sh;');

			// Delete SFTP account + folder
			# echo shell_exec('cd .docker; sh scripts_shell/docker_compose_delete_sftp_user.sh ' . $_GET['folder'] . ';');
			shell_exec('cd .docker; sh scripts_shell/docker_compose_delete_sftp_user.sh ' . $_GET['folder'] . ';');
			#echo shell_exec('cd .docker; sh scripts_shell/docker_compose_delete_sftp_folder.sh ' . $_GET['folder'] . ';');
			shell_exec('cd .docker; sh scripts_shell/docker_compose_delete_sftp_folder.sh ' . $_GET['folder'] . ';');

			#echo shell_exec('cd envs; rm -rf ' . $_GET['folder'] . ';');
			shell_exec('cd envs; rm -rf ' . $_GET['folder'] . ';');

			$this->Environments_model->deleteEnvironmentByFolder($_GET['folder']);

            $deleteVolumes = shell_exec('cd .docker; sh scripts_shell/docker_delete_volumes.sh ;');
            if (is_null($deleteVolumes)) {
                $deleteVolumes = shell_exec('cd .docker; sh scripts_shell/docker_delete_volumes_bin.sh ;');
            }
            if (is_null($deleteVolumes)) {
                $deleteVolumes = shell_exec('cd .docker; sh scripts_shell/docker_delete_volumes_local_bin.sh ;');
            }

			$response = true;

		}

		echo json_encode($response);
	}

	public function checkStatus()
	{

		$generalStatus = false;
		if (isset($_GET['folder']) && !empty($_GET['folder'])) {

			$folderName = $_GET['folder'];

			// Check MySQL
			$mySqlContainer = $folderName . "_mysql-" . $folderName . "_1";
			$mySqlContainerStatus = shell_exec('cd .docker; sh scripts_shell/docker_check_status.sh ' . $mySqlContainer. ';');
			if (is_null($mySqlContainerStatus)) {
				$mySqlContainerStatus = shell_exec('cd .docker; sh scripts_shell/docker_check_status_bin.sh ' . $mySqlContainer. ';');
			}

			if (is_null($mySqlContainerStatus)) {
				$mySqlContainerStatus = shell_exec('cd .docker; sh scripts_shell/docker_check_status_local_bin.sh ' . $mySqlContainer. ';');
			}
			if (isset($mySqlContainerStatus) && !empty($mySqlContainerStatus) && strpos($mySqlContainerStatus, 'true') !== false) {
				$generalStatus = true;
			} else {
				$generalStatus = false;
			}

			// Check PHP
			if ($generalStatus) {
				$phpContainer = $folderName . "_php-" . $folderName . "_1";
				$phpContainerStatus = shell_exec('cd .docker; sh scripts_shell/docker_check_status.sh ' . $phpContainer. ';');

				if (!isset($phpContainerStatus) || is_null($phpContainerStatus)) {
					$phpContainerStatus = shell_exec('cd .docker; sh scripts_shell/docker_check_status_bin.sh ' . $phpContainer. ';');
				}

				if (!isset($phpContainerStatus) || is_null($phpContainerStatus)) {
					$phpContainerStatus = shell_exec('cd .docker; sh scripts_shell/docker_check_status_local_bin.sh ' . $phpContainer. ';');
				}
				if (isset($phpContainerStatus) && !empty($phpContainerStatus) && strpos($phpContainerStatus, 'true') !== false) {
					$generalStatus = true;
				} else {
					$generalStatus = false;
				}
			}


		} else {
			// todo error
		}

		echo json_encode($generalStatus);
	}

	public function displayImportEnvironment()
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
			$environment->{Environments_model::mysqlPassword} = randomPassword();;

			// Sftp params
			$environment->{Environments_model::sftpUser} = $projectUniqId;
			$environment->{Environments_model::sftpPassword} = randomPassword();;
			$environment->{Environments_model::sftpPort} = $this->getAvailablePort();


			if (isset($environment->{Environments_model::folder}) && !empty($environment->{Environments_model::folder})) {

				// Create folder
				// Todo check if exists
				# echo shell_exec('cd envs; mkdir ' . $environment->{Environments_model::folder} . '; cd ' . $environment->{Environments_model::folder}. '; mkdir src; cd src; sh ../../../.docker/scripts_shell/docker_compose_create_index_php.sh;');
				shell_exec('cd envs; mkdir ' . $environment->{Environments_model::folder} . '; cd ' . $environment->{Environments_model::folder}. '; mkdir src; cd src; sh ../../../.docker/scripts_shell/docker_compose_create_index_php.sh;');

				// Create php folder (dockerfile)
				# echo shell_exec('cd envs; cd ' . $environment->{Environments_model::folder} . '; mkdir image; chmod -R 777 image; cd image; mkdir php; chmod -R 777 php;');
				shell_exec('cd envs; cd ' . $environment->{Environments_model::folder} . '; mkdir image; chmod -R 777 image; cd image; mkdir php; chmod -R 777 php;');

				// Generate docker compose
				$this->generateEnvDockerCompose($environment);

				// Add environment
				$environmentId = $this->Environments_model->insertEnvironment($environment);

				if (isset($environmentId) && $environmentId != -1) {

					// Start docker compose
					$this->startEnvironment($environment);

					redirect('environments');

				} else {
					// todo manage error
					exit('Error insert env !');
				}

			} else {
				// Todo error
			}
		} else {
			// Todo error
		}
	}

	public function createEnvironment()
	{

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

		// Instantiate project uniqid (folder name)
		$projectUniqId = uniqid();

		// Root user
		$mySqlRootUser = 'root';
		// Generate random my password
		$mySqlPassword = randomPassword();

		// Generate random sftp password
		$sftpPassword = randomPassword();

		// Get available port
		$sftpPort = $this->getAvailablePort();

		// User id
		$userId = $this->ion_auth->user()->row()->id;

		// Get $_POST params
		$webserver = (isset($_POST['webserver']) && !empty($_POST['webserver'])) ? $_POST['webserver'] : null;
		$name = (isset($_POST['name']) && !empty($_POST['name'])) ? $_POST['name'] : "Error";
		$phpVersionId = (isset($_POST['phpVersion']) && !empty($_POST['phpVersion']) && $_POST['phpVersion'] != "--" && $_POST['phpVersion'] != "custom") ? $_POST['phpVersion'] : null;
		$phpDockerfile = (isset($_POST['phpDockerfile']) && !empty($_POST['phpDockerfile'])) ? $_POST['phpDockerfile'] : null;
		$mysqlVersionId = (isset($_POST['mysqlVersion']) && !empty($_POST['mysqlVersion'])  && $_POST['mysqlVersion'] != "--" && $_POST['mysqlVersion'] != "custom") ? $_POST['mysqlVersion'] : null;
		$mysqlDockerfile = (isset($_POST['mysqlDockerfile']) && !empty($_POST['mysqlDockerfile'])) ? $_POST['mysqlDockerfile'] : null;
		$hasPma = (isset($_POST['phpVersion']) && !empty($_POST['phpVersion']) && isset($_POST['pma']) && !empty($_POST['pma'])) ? true : false;
		$hasSftp = (isset($_POST['sftp']) && !empty($_POST['sftp'])) ? true : false;

		// Add phpinfo()
		# echo shell_exec('cd envs; mkdir ' . $projectUniqId . '; chmod -R 777 ' . $projectUniqId . ';cd ' . $projectUniqId. '; mkdir src; chmod -R 777 src; cd src; sh ../../../.docker/scripts_shell/docker_compose_create_index_php.sh; chmod 777 index.php;');
		shell_exec('cd envs; mkdir ' . $projectUniqId . '; chmod -R 777 ' . $projectUniqId . ';cd ' . $projectUniqId. '; mkdir src; chmod -R 777 src; cd src; sh ../../../.docker/scripts_shell/docker_compose_create_index_php.sh; chmod 777 index.php;');

		// Create php folder (dockerfile)
		# echo shell_exec('cd envs; cd ' . $projectUniqId . '; mkdir image; chmod -R 777 image; cd image; mkdir php; chmod -R 777 php;');
		shell_exec('cd envs; cd ' . $projectUniqId . '; mkdir image; chmod -R 777 image; cd image; mkdir php; chmod -R 777 php;');

		$environment = new stdClass();
		$environment->{Environments_model::userId} = $userId;
		$environment->{Environments_model::name} = $name;
		$environment->{Environments_model::webserver} = mb_strtolower($webserver);
		$environment->{Environments_model::folder} = $projectUniqId;
		$environment->{Environments_model::phpVersionId} = $phpVersionId;
		$environment->{Environments_model::phpDockerfile} = $phpDockerfile;
		$environment->{Environments_model::phpVersionId} = $phpVersionId;
		$environment->{Environments_model::phpDockerfile} = $phpDockerfile;
		$environment->{Environments_model::mysqlVersionId} = $mysqlVersionId;
		$environment->{Environments_model::mysqlDockerfile} = $mysqlDockerfile;
		$environment->{Environments_model::hasPma} = $hasPma;
		$environment->{Environments_model::hasSftp} = $hasSftp;

		// Get ports
		$environment->{Environments_model::phpPort} = $this->getAvailablePort();
		$environment->{Environments_model::phpSSLPort} = $this->getAvailablePort();
		$environment->{Environments_model::mysqlPort} = $this->getAvailablePort();
		$environment->{Environments_model::pmaPort} = $this->getAvailablePort();

		// MySQL params
		$environment->{Environments_model::mysqlUser} = $mySqlRootUser;
		$environment->{Environments_model::mysqlPassword} = $mySqlPassword;

		// Sftp params
		$environment->{Environments_model::sftpUser} = $projectUniqId;
		$environment->{Environments_model::sftpPassword} = $sftpPassword;
		$environment->{Environments_model::sftpPort} = $sftpPort;

		// Generate docker compose
		$this->generateEnvDockerCompose($environment);

		// Add environment
		$environmentId = $this->Environments_model->insertEnvironment($environment);

		if (isset($environmentId) && $environmentId != -1) {

			// Start docker compose
			$this->startEnvironment($environment);

			redirect('environments');

		} else {
			// todo manage error
			exit('Error insert env !');
		}

		// Todo generate compose then run it
		// Send mail admin
		// redirect('environments');

	}

	private function generateEnvDockerCompose($environment)
	{

		$this->load->library('parser');

		// Instantiate docker compose
		$dockerCompose = "";

		// Add compose header
		$filePath = "templates/docker/compose/docker-compose-services-header.yml";
		$data = array();
		$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

		// Add SFTP
		$filePath = "templates/docker/compose/docker-compose-sftp.yml";
		// To check
/*		if ($data[$environment->{Environments_model::hasSftp}]) {
			$filePath = "templates/docker/compose/docker-compose-sftp.yml";
		} else {
			$filePath = "templates/docker/compose/docker-compose-sftp-disabled.yml";
		}*/

		$data = array();
		$data['user'] = $environment->{Environments_model::folder};
		$data['pass'] = $environment->{Environments_model::sftpPassword};
		$data['port'] = $environment->{Environments_model::sftpPort};
		$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

		// Services
		// MySQL
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

		// php todo Apache / Nginx
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
				$filePath = "templates/docker/compose/docker-compose-php-build.yml";
				$data['localPath'] = $localPath;
				$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

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

		// PMA
		if (isset($environment->{Environments_model::hasPma}) && !empty($environment->{Environments_model::hasPma})) {
			$filePath = "templates/docker/compose/docker-compose-pma.yml";
			$data = array('project' => $environment->{Environments_model::folder}, 'port' => $environment->{Environments_model::pmaPort});
			$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);
		}

		// Volumes
		// MySQL
		if ((isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId})) || (isset($environment->{Environments_model::mysqlDockerfile}) && !empty($environment->{Environments_model::mysqlDockerfile}))) {
			$filePath = "templates/docker/compose/docker-compose-mysql-volume.yml";
			$data = array('project' => $environment->{Environments_model::folder}, 'port' => $environment->{Environments_model::pmaPort});
			$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);
		}

		$environment->{Environments_model::dockerCompose} = $dockerCompose;

		if (!isset($environment->{Environments_model::phpPort}) || empty($environment->{Environments_model::phpPort}) || $environment->{Environments_model::phpPort} == -1) {
			// Todo error
		}

		if (!isset($environment->{Environments_model::mysqlPort}) || empty($environment->{Environments_model::mysqlPort}) || $environment->{Environments_model::mysqlPort} == -1) {
			// Todo error
		}

		if (!isset($environment->{Environments_model::pmaPort}) || empty($environment->{Environments_model::pmaPort}) || $environment->{Environments_model::pmaPort} == -1) {
			// Todo error
		}

	}

	private function startEnvironment($environment)
	{

		if (isset($environment) && !empty($environment)) {

			$dockerComposePath = ENVS_FOLDER . "/" . $environment->{Environments_model::folder} . "/";
			file_put_contents($dockerComposePath . "docker-compose.yml", $environment->{Environments_model::dockerCompose});

			// Todo php dockerfile
			$dockerfilePhpPath = ENVS_FOLDER . "/" . $environment->{Environments_model::folder} . "/image/php/";
			file_put_contents($dockerfilePhpPath . "Dockerfile", $environment->{Environments_model::phpDockerfile});

			# echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/launch_docker-compose.sh;');
			shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/launch_docker-compose.sh;');

		} else {
			// Todo error
		}
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

		$portsStr = shell_exec('cd .docker; sh scripts_shell/docker_check_ports.sh ;');
		if (is_null($portsStr)) {
			$portsStr = shell_exec('cd .docker; sh scripts_shell/docker_check_ports_bin.sh ;');
		}
		if (is_null($portsStr)) {
			$portsStr = shell_exec('cd .docker; sh scripts_shell/docker_check_ports_local_bin.sh ;');
		}

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