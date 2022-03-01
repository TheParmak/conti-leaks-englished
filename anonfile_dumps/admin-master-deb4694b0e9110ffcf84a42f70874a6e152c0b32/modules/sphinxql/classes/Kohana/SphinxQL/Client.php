<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This file is part of SphinxQL for Kohana.
 *
 * Copyright (c) 2010, Deoxxa Development
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package kohana-sphinxql
 */

/**
 * Class for communicating with a sphinx server
 *
 * @package kohana-sphinxql
 * @author MasterCJ <mastercj@mastercj.net>
 * @version 0.1
 * @license http://mastercj.net/license.txt
 */
class Kohana_SphinxQL_Client {
    /**
     * @var string The address and port of the server this client is to connect to
     */
    protected $_server = false;
    /**
     * @var resource A reference to the mysql link that this client will be using
     */
    protected $_handle = false;
    /**
     * @var false|string A flag to denote whether or not this client has tried to connect and failed
     */
    protected $_failed = false;
    /**
     * @var resource A reference to the mysql result returned by a query that this client has performed
     */
    protected $_result = false;

    /**
     * Constructor
     *
     * @param string The address and port of a sphinx server
     */
    public function __construct($server) {
        if (!is_string($server)) { return false; }
        $this->_server = $server;
    }

    /**
     * Used to attempt connection to the sphinx server, keeps a record of whether it failed to connect or not
     *
     * @return boolean Status of the connection attempt
     */
    protected function connect() {
        if ($this->_handle) { return true; }
        if ($this->_failed) { return false; }
        if ($this->_server === false) { return false; }
        try {
            $server_param = explode(':',$this->_server);
                    $this->_handle = new MySQLi($server_param[0], null, null, null, $server_param[1], null);
        } catch (Exception $e) {
            $this->_failed = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Perform a query
     *
     * @param string The query to perform
     * @return SphinxQL_Client This client object
     */
    public function query($query) {
        $this->_result = false;
        if (is_string($query) && $this->connect()) {
            @$this->_result = mysqli_query($this->_handle,$query);
        }
        return $this;
    }

    public function total_info() {
        if ( false === $this->_handle ) {
            if ( $this->_failed ) {
                $err = $this->_failed;
            } elseif ( ! $this->connect() ) {
                $err = 'Could not connect to sphinx server';
            } else {
                $err = mysqli_error($this->_handle);
            }
            return array(
                'error' => $err,
            );
        }
        $result = mysqli_query($this->_handle, "SHOW META");
        if($result) {
            $res = array();
            while($tmp = mysqli_fetch_assoc($result)){
                $res[$tmp['Variable_name']] = $tmp['Value'];
            }
            return $res;
        } else {
            return false;
        }
    }

    /**
     * Fetch one row of the result set
     *
     * @return array|false The row or an error
     */
    public function fetch_row() {
        if ($this->_result === false) { return false; }
        if ($arr = mysqli_fetch_assoc($this->_result)) { return $arr; }
        return false;
    }

    /**
     * Fetch the whole result set
     *
     * @return array|false The results or an error
     */
    public function fetch_all() {
        if ($this->_result === false) { return false; }
        $ret = array();
        while ($arr = mysqli_fetch_assoc($this->_result)) { $ret[] = $arr; }
        return $ret;
    }
}
