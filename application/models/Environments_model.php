<?php

/**
 * Created by PhpStorm.
 * User: rrazzi
 * Date: 15/04/2019
 * Time: 13:25
 */
class Environments_model extends CI_Model {

    const table = 'environments';
    const pk = 'id';
    const userId = 'user_id';
    const name = 'name';
    const folder = 'folder';
    const webserver = 'webserver';
    const phpVersionId = 'php_version_id';
    const phpPort = 'php_port';
    const phpSSLPort = 'php_ssl_port';
    const phpDockerfile = 'php_dockerfile';
    const mysqlVersionId = 'mysql_version_id';
    const mysqlPort = 'mysql_port';
    const mysqlDockerfile = 'mysql_dockerfile';
    const mysqlUser = 'mysql_user';
    const mysqlPassword = 'mysql_password';
    const hasPma = 'has_pma';
    const hasSftp = 'has_sftp';
    const pmaPort = 'pma_port';
    const sftpUser = 'sftp_user';
    const sftpPassword = 'sftp_password';
    const sftpPort = 'sftp_port';
    const dockerCompose = 'docker_compose';
    const xDebugRemoteHost = 'xDebug_remote_host';
    const createdDate = 'created_date';

    const creator = 'creator';

    public function __construct() {
        parent::__construct();
    }

    public function getAllEnvironments() {

        $select = [
            self::table . '.' . self::pk,
            self::table . '.' . self::name,
            self::table . '.' . self::folder,
            self::table . '.' . self::webserver,
            self::table . '.' . self::phpPort,
            self::table . '.' . self::phpSSLPort,
            self::table . '.' . self::phpDockerfile,
            self::table . '.' . self::mysqlPort,
            self::table . '.' . self::mysqlDockerfile,
            self::table . '.' . self::mysqlUser,
            self::table . '.' . self::mysqlPassword,
            self::table . '.' . self::hasPma,
            self::table . '.' . self::pmaPort,
            self::table . '.' . self::hasSftp,
            self::table . '.' . self::sftpUser,
            self::table . '.' . self::sftpPassword,
            self::table . '.' . self::sftpPort,
            self::table . '.' . self::dockerCompose,
            self::table . '.' . self::xDebugRemoteHost,
            self::table . '.' . self::createdDate,
            'ion_auth_users.username AS ' . self::creator,
            'php_versions.version AS ' . self::phpVersionId,
            'mysql_versions.version AS ' . self::mysqlVersionId,
        ];

        $this->db->select($select)
            ->from(self::table)
            ->join('ion_auth_users', self::table . '.' . self::userId . ' = ' . 'ion_auth_users.id')
            ->join('php_versions', self::table . '.' . self::phpVersionId . ' = ' . 'php_versions.id', 'left')
            ->join('mysql_versions', self::table . '.' . self::mysqlVersionId . ' = ' . 'mysql_versions.id', 'left')
            ->order_by(self::createdDate, 'DESC');

        return $this->db->get()->result();
    }

    public function getEnvironmentByFolderAndUserId($folder, $userId) {

        $select = [
            self::table . '.' . self::pk,
            self::table . '.' . self::name,
            self::table . '.' . self::folder,
            self::table . '.' . self::webserver,
            self::table . '.' . self::phpVersionId,
            self::table . '.' . self::phpPort,
            self::table . '.' . self::phpSSLPort,
            self::table . '.' . self::phpDockerfile,
            self::table . '.' . self::mysqlVersionId,
            self::table . '.' . self::mysqlPort,
            self::table . '.' . self::mysqlDockerfile,
            self::table . '.' . self::mysqlUser,
            self::table . '.' . self::mysqlPassword,
            self::table . '.' . self::hasPma,
            self::table . '.' . self::pmaPort,
            self::table . '.' . self::hasSftp,
            self::table . '.' . self::sftpUser,
            self::table . '.' . self::sftpPassword,
            self::table . '.' . self::sftpPort,
            self::table . '.' . self::dockerCompose,
            self::table . '.' . self::xDebugRemoteHost,
            self::table . '.' . self::createdDate
        ];

        $this->db->select($select)
            ->from(self::table)
            ->join('ion_auth_users', self::table . '.' . self::userId . ' = ' . 'ion_auth_users.id')
            ->join('php_versions', self::table . '.' . self::phpVersionId . ' = ' . 'php_versions.id', 'left')
            ->join('mysql_versions', self::table . '.' . self::mysqlVersionId . ' = ' . 'mysql_versions.id', 'left')
            ->where(self::folder, $folder)
            ->where(self::userId, $userId)
            ->order_by(self::createdDate, 'DESC');

        return $this->db->get()->row();
    }

    public function getEnvironmentByFolder($folder) {

        $select = [
            self::table . '.' . self::pk,
            self::table . '.' . self::name,
            self::table . '.' . self::folder,
            self::table . '.' . self::webserver,
            self::table . '.' . self::phpVersionId,
            self::table . '.' . self::phpPort,
            self::table . '.' . self::phpSSLPort,
            self::table . '.' . self::phpDockerfile,
            self::table . '.' . self::mysqlVersionId,
            self::table . '.' . self::mysqlPort,
            self::table . '.' . self::mysqlDockerfile,
            self::table . '.' . self::mysqlUser,
            self::table . '.' . self::mysqlPassword,
            self::table . '.' . self::hasPma,
            self::table . '.' . self::pmaPort,
            self::table . '.' . self::hasSftp,
            self::table . '.' . self::sftpUser,
            self::table . '.' . self::sftpPassword,
            self::table . '.' . self::sftpPort,
            self::table . '.' . self::dockerCompose,
            self::table . '.' . self::xDebugRemoteHost,
            self::table . '.' . self::createdDate
        ];

        $this->db->select($select)
            ->from(self::table)
            ->join('ion_auth_users', self::table . '.' . self::userId . ' = ' . 'ion_auth_users.id')
            ->join('php_versions', self::table . '.' . self::phpVersionId . ' = ' . 'php_versions.id', 'left')
            ->join('mysql_versions', self::table . '.' . self::mysqlVersionId . ' = ' . 'mysql_versions.id', 'left')
            ->where(self::folder, $folder)
            ->order_by(self::createdDate, 'DESC');

        return $this->db->get()->row();
    }

    public function getEnvironmentsByCreator($creatorId) {

        $select = [
            self::table . '.' . self::pk,
            self::table . '.' . self::name,
            self::table . '.' . self::folder,
            self::table . '.' . self::webserver,
            self::table . '.' . self::phpPort,
            self::table . '.' . self::phpDockerfile,
            self::table . '.' . self::mysqlPort,
            self::table . '.' . self::mysqlDockerfile,
            self::table . '.' . self::mysqlUser,
            self::table . '.' . self::mysqlPassword,
            self::table . '.' . self::hasPma,
            self::table . '.' . self::pmaPort,
            self::table . '.' . self::hasSftp,
            self::table . '.' . self::sftpUser,
            self::table . '.' . self::sftpPassword,
            self::table . '.' . self::sftpPort,
            self::table . '.' . self::dockerCompose,
            self::table . '.' . self::xDebugRemoteHost,
            self::table . '.' . self::createdDate,
            'ion_auth_users.username AS ' . self::creator,
            'php_versions.version AS ' . self::phpVersionId,
            'mysql_versions.version AS ' . self::mysqlVersionId,
        ];

        $this->db->select($select)
            ->from(self::table)
            ->join('ion_auth_users', self::table . '.' . self::userId . ' = ' . 'ion_auth_users.id')
            ->join('php_versions', self::table . '.' . self::phpVersionId . ' = ' . 'php_versions.id', 'left')
            ->join('mysql_versions', self::table . '.' . self::mysqlVersionId . ' = ' . 'mysql_versions.id', 'left')
            ->where(self::userId, $creatorId)
            ->order_by(self::createdDate, 'DESC');

        return $this->db->get()->result();
    }

    public function getPhpPorts() {

        $select = [
            self::table . '.' . self::phpPort
        ];

        $this->db->select($select)
            ->from(self::table);

        return $this->db->get()->result();
    }

    public function getMysqlPorts() {

        $select = [
            self::table . '.' . self::mysqlPort
        ];

        $this->db->select($select)
            ->from(self::table);

        return $this->db->get()->result();
    }

    public function getPmaPorts() {

        $select = [
            self::table . '.' . self::pmaPort
        ];

        $this->db->select($select)
            ->from(self::table);

        return $this->db->get()->result();
    }

    public function insertEnvironment($environment) {

        $this->db->insert(self::table, $environment);
        $insert_id = $this->db->insert_id();

        if (isset($insert_id) && !empty($insert_id)) {
            return  $insert_id;
        } else {
            return -1;
        }
    }

    public function editEnvironment($environment) {

        $this->db->where(self::pk, $environment->{self::pk});
        $this->db->update(self::table, $environment);

        if($this->db->affected_rows() >=0){
            return true;
        } else {
            return false;
        }

    }

    public function deleteEnvironmentByFolder($folder) {

        $this->db->delete(self::table, array(self::folder => $folder));
    }

    public function deleteEnvironmentByName($name) {

        $this->db->delete(self::table, array(self::name => $name));
    }

}