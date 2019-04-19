<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		redirect('environments');
	}

}