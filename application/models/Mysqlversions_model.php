<?php

/**
 * Created by PhpStorm.
 * User: rrazzi
 * Date: 15/04/2019
 * Time: 13:25
 */
class Mysqlversions_model extends CI_Model {

    const table = 'mysql_versions';
    const pk = 'id';
    const version = 'version';
    const tag = 'tag';
    const isActive = 'is_active';

    public function __construct() {
        parent::__construct();
    }

    public function getMysqlVersions() {

        $this->db->select("*")
            ->from(self::table)
            ->order_by(self::version, 'DESC')
            ->where(self::isActive, '1');

        return $this->db->get()->result();
    }

    public function getTagById($id) {

        $this->db->select(self::tag)
            ->from(self::table)
            ->where(self::pk, $id);
            //->where(self::isActive, '1');

        return $this->db->get()->row();
    }

}