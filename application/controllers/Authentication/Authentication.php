<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {

	public function index()
	{
		$this->load->view('login/login');
	}

	public function login()
	{

		if (!isset($_POST['login']) || empty($_POST['login'])) {
			$this->redirectError('Please enter an email address');
		}

		if (!isset($_POST['password']) || empty($_POST['password'])) {
			$this->redirectError('Please enter a password');
		}

		$login = $_POST['login'];
		$password = $_POST['password'];
		$remember = TRUE; // remember the user
		$loginResult = $this->ion_auth->login($login, $password, $remember);

		if ($loginResult) {
			$this->redirectSuccess();
		} else {
			$this->redirectError('Wrong credentials');
		}

	}

	public function logout()
	{

		$this->ion_auth->logout();

		redirect(base_url());
		exit();

	}

	private function redirectError($error = "")
	{
		$this->session->set_flashdata('error', $error);

		redirect('authentication');
		exit();
	}

	private function redirectSuccess()
	{

		// Default : base url
		$redirect = base_url();

		// Manage redirection after login success
		// Controller (+ method)
		if (null !== $this->session->userdata('attempt_reach_controller')) {

			$redirect = $this->session->userdata('attempt_reach_controller');

			// Unset redirect controller userdata
			$this->session->unset_userdata('attempt_reach_controller');

			// Params GET
			if (null !== $this->session->userdata('attempt_reach_params_get')) {
				$tmpGetParams = json_decode($this->session->userdata('attempt_reach_params_get'));
				$tmpGetParams = (array) $tmpGetParams;

				if (count($tmpGetParams) > 0) {
					$redirect .= '?' . http_build_query($tmpGetParams);
				}

				// Unset redirect GET params userdata
				$this->session->unset_userdata('attempt_reach_params_get');
			}

			// Params POST
			if (null !== $this->session->userdata('attempt_reach_params_post')) {
				$tmpPostParams = json_decode($this->session->userdata('attempt_reach_params_post'));

				$_POST = (array) $tmpPostParams;
				// Unset redirect POST params userdata
				$this->session->unset_userdata('attempt_reach_params_post');
			}

		}

		redirect($redirect);
		exit();

	}

}