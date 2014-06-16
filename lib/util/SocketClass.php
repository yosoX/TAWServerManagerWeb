<?php

/**
 * @author      yosoX
 * @category    TAWcraft Sercer Manager
 */

class Socket {

    private $_socket = null,
            $_response = '',
            $_error = null;

    public function __construct($host, $port, $domain = AF_INET, $type = SOCK_STREAM, $protocol = 0) {

        $this->_socket = $this->create($domain, $type, $protocol);

        if ($this->_socket === false) {
            $this->error();
        } else {
            $this->connect($host, $port);
        }
    }

    private function create($domain = AF_INET, $type = SOCK_STREAM, $protocol = 0) {

        return socket_create($domain, $type, $protocol);
    }

    private function connect($host, $port) {

        if (isset($this->_socket) && $this->_socket !== false) {
            $con = socket_connect($this->_socket, $host, $port);

            if ($con === false) {
                $this->error();
            }
        }
    }

    public function write($string) {

        if (substr($string, -1) !== PHP_EOL) {
            $string .= PHP_EOL;
        }

        $write = socket_write($this->_socket, $string);

        if ($write === false) {
            $this->error();
        }
    }

    public function read($bytes = 1024) {

        while ($response = socket_read($this->_socket, $bytes)) {
            if (!$response) {
                break;
            }

            $this->_response .= $response;

            if (strpos($response, PHP_EOL) !== false) {
                break;
            }
        }
        
        return $this->_response;
    }

    private function error() {

        $this->_error = socket_strerror(socket_last_error());
    }

    public function getError() {

        if (isset($this->_error)) {
            return $this->_error;
        }

        return false;
    }

}
