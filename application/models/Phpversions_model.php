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
    const env = 'env';
    const isActive = 'is_active';

    const envBoth = 'both';
    const envApache = 'apache';
    const envNginx = 'nginx';

    public function __construct() {
        parent::__construct();
    }

    public function getPhpVersions() {

        $this->db->select("*")
            ->from(self::table)
            //->order_by(self::version, 'ASC')
            ->where(self::isActive, '1');

        return $this->db->get()->result();
    }

    public function getPhpVersionsByEnv($env = null) {

        if (!is_null($env) && $env == self::envApache) {
            $env = self::envApache;
        } else if (!is_null($env) && $env == self::envNginx) {
            $env = self::envNginx;
        } else {
            $env = self::envBoth;
        }

        $this->db->select("*")
            ->from(self::table)
            ->where(self::env, $env)
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
