<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MI_Controller extends CI_Controller {

	function __construct()
	{

		parent::__construct();

		// Check if user is logged in
		$this->manageLoginSecurity();

	}

	private function manageLoginSecurity()
	{

		if (!$this->ion_auth->logged_in())
		{

			// Get controller user is trying to reach (+ method)
			if (null !== $this->router->fetch_class()) {

				$controllerToReach = $this->router->fetch_class();

				if (null !== $this->router->fetch_method() && "index" !== $this->router->fetch_method()) {
					$controllerToReach .= "/" . $this->router->fetch_method();
				}

				// Set redirect controller userdata
				$this->session->set_userdata('attempt_reach_controller', $controllerToReach);

				if (isset($_GET) && !empty($_GET)) {
					// Set redirect controller GET params userdata
					$this->session->set_userdata('attempt_reach_params_get', json_encode($_GET));
				}

				if (isset($_POST) && !empty($_POST)) {
					// Set redirect controller POST params userdata
					$this->session->set_userdata('attempt_reach_params_post', json_encode($_POST));
				}
			}

			redirect('authentication');

		}

	}

}