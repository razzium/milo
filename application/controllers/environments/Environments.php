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
		$response = false;

		if (isset($_GET['folder']) && !empty($_GET['folder'])) {

			$dockerComposePath = ENVS_FOLDER . "/" . $_GET['folder'] . "/";
			echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/launch_docker-compose.sh;');

			$response = true;

		}

		echo json_encode($response);
	}

	public function stopEnv()
	{
		$response = false;

		if (isset($_GET['folder']) && !empty($_GET['folder'])) {

			$dockerComposePath = ENVS_FOLDER . "/" . $_GET['folder'] . "/";
			echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/stop_docker-compose.sh;');

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
			shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/stop_docker-compose.sh;');


			$dockerComposePath = ENVS_FOLDER . "/" . $_GET['folder'] . "/";
			shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/delete_docker-compose.sh;');

			$this->Environments_model->deleteEnvironmentByFolder($_GET['folder']);

			// Delete SFTP account + folder
			shell_exec('cd .docker; sh scripts_shell/docker_compose_delete_sftp_user.sh ' . $_GET['folder'] . ';');
			shell_exec('cd .docker; sh scripts_shell/docker_compose_delete_sftp_folder.sh ' . $_GET['folder'] . ';');

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
			if (isset($mySqlContainerStatus) && !empty($mySqlContainerStatus) && strpos($mySqlContainerStatus, 'true') !== false) {
				$generalStatus = true;
			}

			// Check PHP
			if ($generalStatus) {
				$phpContainer = $folderName . "_php-" . $folderName . "_1";
				$phpContainerStatus = shell_exec('cd .docker; sh scripts_shell/docker_check_status.sh ' . $phpContainer. ';');
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

		// Instantiate project uniqid
		$projectUniqId = uniqid();
		// Generate random sftp password
		$sftpPassword = randomPassword();

		// Create SFTP account + folder
		echo shell_exec('cd .docker; sh scripts_shell/docker_compose_create_sftp_1.sh ' . $projectUniqId);
		echo shell_exec('cd .docker; sh scripts_shell/docker_compose_create_sftp_2.sh ' . $projectUniqId);
		echo shell_exec('cd .docker; sh scripts_shell/docker_compose_create_sftp_3.sh ' . $projectUniqId);
		echo shell_exec('cd .docker; sh scripts_shell/docker_compose_create_sftp_4.sh ' . $projectUniqId . " " . $sftpPassword);
		echo shell_exec('cd .docker; sh scripts_shell/docker_compose_create_sftp_5.sh ' . $projectUniqId);

		// Add phpinfo()
		echo shell_exec('cd envs; cd ' . $projectUniqId. '; cd src; sh ../../../.docker/scripts_shell/docker_compose_create_sftp_6.sh;');

		$environment = new stdClass();
		$environment->{Environments_model::userId} = $this->ion_auth->user()->row()->id;
		$environment->{Environments_model::name} = (isset($_POST['name']) && !empty($_POST['name'])) ? $_POST['name'] : "Error";
		$environment->{Environments_model::folder} = $projectUniqId;
		$environment->{Environments_model::phpVersionId} = (isset($_POST['phpVersion']) && !empty($_POST['phpVersion']) && $_POST['phpVersion'] != "--" && $_POST['phpVersion'] != "custom") ? $_POST['phpVersion'] : null;
		$environment->{Environments_model::phpDockerfile} = (isset($_POST['phpDockerfile']) && !empty($_POST['phpDockerfile'])) ? $_POST['phpDockerfile'] : null;
		$environment->{Environments_model::mysqlVersionId} = (isset($_POST['mysqlVersion']) && !empty($_POST['mysqlVersion'])  && $_POST['mysqlVersion'] != "--" && $_POST['mysqlVersion'] != "custom") ? $_POST['mysqlVersion'] : null;
		$environment->{Environments_model::mysqlDockerfile} = (isset($_POST['mysqlDockerfile']) && !empty($_POST['mysqlDockerfile'])) ? $_POST['mysqlDockerfile'] : null;
		$environment->{Environments_model::hasPma} = (isset($_POST['phpMyAdmin']) && !empty($_POST['phpMyAdmin'])) ? true : false;

		$environment->{Environments_model::phpPort} = $this->getAvailablePort();
		$environment->{Environments_model::mysqlPort} = $this->getAvailablePort();
		$environment->{Environments_model::pmaPort} = $this->getAvailablePort();

		$environment->{Environments_model::sftpUser} = $projectUniqId;
		$environment->{Environments_model::sftpPassword} = $sftpPassword;

		$this->load->library('parser');

		// Instanntiate dpcker compose
		$dockerCompose = "";

		// Add compose header
		$filePath = "templates/docker/compose/docker-compose-services-header.yml";
		$data = array();
		$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

		// Services
		// MySQL
		if (isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId}) && $environment->{Environments_model::mysqlVersionId} != "--") {

			if ($environment->{Environments_model::mysqlVersionId} != "custom") {

				$data = array('project' => $projectUniqId, 'port' => $environment->{Environments_model::mysqlPort});

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

		// Php
		if (isset($environment->{Environments_model::phpVersionId}) && !empty($environment->{Environments_model::phpVersionId}) && $environment->{Environments_model::phpVersionId} != "--") {
			if ($environment->{Environments_model::phpVersionId} != "custom") {

				$data = array('project' => $projectUniqId, 'port' => $environment->{Environments_model::phpPort});

				$phpTag = $this->Phpversions_model->getTagById($environment->{Environments_model::phpVersionId});
				if (isset($phpTag->tag) && !empty($phpTag->tag)) {
					$data['version'] = $phpTag->tag;
				} else {
					// Todo error
				}

				// Todo : local env path
				$localPath = "../src";

				$filePath = "templates/docker/compose/docker-compose-php-image.yml";
				$data['localPath'] = $localPath;
				$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);

			} else {
				// Todo builds
			}
		} else {
			// Todo if dockerfile builds
		}

		// PMA
		if (isset($environment->{Environments_model::hasPma}) && !empty($environment->{Environments_model::hasPma})) {
			$filePath = "templates/docker/compose/docker-compose-pma.yml";
			$data = array('project' => $projectUniqId, 'port' => $environment->{Environments_model::pmaPort});
			$dockerCompose .= $this->parser->parse($filePath, $data, TRUE);
		}

		// Volumes
		// MySQL
		if ((isset($environment->{Environments_model::mysqlVersionId}) && !empty($environment->{Environments_model::mysqlVersionId})) || (isset($environment->{Environments_model::mysqlDockerfile}) && !empty($environment->{Environments_model::mysqlDockerfile}))) {
			$filePath = "templates/docker/compose/docker-compose-mysql-volume.yml";
			$data = array('project' => $projectUniqId, 'port' => $environment->{Environments_model::pmaPort});
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

		// Add environment
		$envId = $this->Environments_model->insertEnvironment($environment);

		if (isset($envId) && $envId != -1) {
			$this->startEnvironment($environment);
			redirect('environments');
		} else {
			// todo manage error
			exit('Error insert env !');
		}
		// Todo
/*		*/

		// Todo generate compose then run it
		// Send mail admin
		// redirect('environments');

	}

	private function startEnvironment($environment)
	{
		if (isset($environment) && !empty($environment)) {

			$dockerComposePath = ENVS_FOLDER . "/" . $environment->{Environments_model::folder} . "/";
			file_put_contents($dockerComposePath . "docker-compose.yml", $environment->{Environments_model::dockerCompose});

			echo shell_exec('cd ' . $dockerComposePath . '; sh ../../.docker/scripts_shell/launch_docker-compose.sh;');

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
	 * Php START Todo : controller php
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
			}
		}

		return $busyPorts;

	}

	/*
	 * Php END
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