<?php

/**
 * Created by PhpStorm.
 * User: rrazzi
 * Date: 15/04/2019
 * Time: 13:25
 */
class Phpversions_model extends CI_Model {

    const table = 'php_versions';
    const pk = 'id';
    const version = 'version';
    const tag = 'tag';

    public function __construct() {
        parent::__construct();
    }

    public function getPhpVersions() {

        $this->db->select("*")
            ->from(self::table)
            ->order_by(self::version, 'DESC');

        return $this->db->get()->result();
    }

    public function getTagById($id) {

        $this->db->select(self::tag)
            ->from(self::table)
            ->where(self::pk, $id);

        return $this->db->get()->row();
    }

}