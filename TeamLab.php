<?php
session_start();

class TeamLab
{
    protected $_tlHost = null;
    protected $_tlUsername = null;
    protected $_tlPassword = null;
    protected $_tlToken = null;
    protected $_isLoggedIn = false;
    
    public static function &getInstance() {
        static $instance;
        
        if (!$instance) {
            $instance = new TeamLab();
        }
        
        return $instance;
    }
    public static function logout() {
        unset($_SESSION['tlUser'], $_SESSION['tlPass'], $_SESSION['tlHost']);
    }
    
    public static function initWith($username, $password, $host) {
        $i =& self::getInstance();
        $i->setUser($username);
        $i->setPassword($password);
        $i->setHost($host);
    }
    
    private function _connect($freshConnect = false) {
        $i =& self::getInstance();
        
        if ( ($_SESSION['tlUser']) && (!$freshConnect) ) {
            
            $i->setUser($_SESSION['tlUser']);
            $i->setPassword($_SESSION['tlPass']);
            $i->setHost($_SESSION['tlHost']);
            
        } 
        
        if ( (strlen($i->getUser()) > 0) && (strlen($i->getPassword()) > 0) && (strlen($i->getHost()) > 0)) {
            $i->setToken($i->auth());
        
            if (strlen($i->getToken()) > 0) {
                $i->setLoginStatus(true);
            } else {
                throw new Exception('Could not authorize TeamLab. Check username, password and host.');
                $i->setLoginStatus(false);
            }
            
            if ($i->isLoggedIn()) {
                $_SESSION['TeamLabLoggedIn'] = true;
                return true; 
                
            } else {
                throw new Exception('You are not logged in');
                $_SESSION['TeamLabLoggedIn'] = false;
                return false;
            }
        } else {
            
            throw new Exception('Missing parameters in TeamLab.php. Please set username, password and host');
        }
        
        $_SESSION['TeamLabLoggedIn'] = false;
        return false;
        
    }
    
    public function getToken() { return $this->_tlToken; }
    public function getUser() { return $this->_tlUsername; }
    public function getHost() { return $this->_tlHost; }
    public function getPassword() { return $this->_tlPassword; }
    public function isLoggedIn() { return $this->_isLoggedIn; }
    
    public function setUser($username) { $this->_tlUsername = $username;}
    public function setPassword($password) { $this->_tlPassword = $password; }
    public function setHost($host) { $this->_tlHost = $host; }
    public function setToken($token) { 
        if (strlen($token) > 0) {
            $this->_tlToken = $token; 
            return true;
        } else {
            throw new Exception('Could not authorize. Please edit your login information in demo.php');
        }
    }
    public function setLoginStatus($status) { $this->_isLoggedIn = $status; }
    
    public function auth() {
        $ch = curl_init();
        $url = 'https://'. $this->getHost() .'/api/1.0/authentication.json';
        $options = array();
        $headers = array(
                'Accept: application/json',
                'Accept-Encoding: gzip, deflate',
                'Content-Type: application/json;',
                );
        
        $params['userName'] = $this->getUser();
        $params['password'] = $this->getPassword();
        
        // $data = http_build_query($params, '', '&');  
        $data2 = json_encode($params);
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => $data2,
            CURLOPT_HTTPHEADER => $headers
        );
        curl_setopt_array($ch, ($options + $defaults));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        return $response->response->token;

    }
    
    
    /*
     * See api.teamlab.com for more details 
     */
    public static function post($url, $params = array()) {
        
        $i =& self::getInstance();
        
        return $i->getData($url, $params, 'post');
    }
    
    /*
     * See api.teamlab.com for more details 
     */
    public static function get($url, $params = array()) {
        $i =& self::getInstance();
        
        return $i->getData($url, $params, 'get');
    }
    
    public function getData($url, $params = array(), $method = 'get') {
        $this->_connect();
        
        if ($this->isLoggedIn()) {
            if (substr($url, 0, 1) != '/') {
                $url = '/' . $url;
            }
            $ch = curl_init();
            $headers = array(
                    'Accept: application/json',
                    'Authorization: '. $this->getToken()
                    );
            $options = array();
            switch (strtolower($method))
            {
                case 'get' :

                    $data = http_build_query($params, '', '&');
                    if (strlen($data) > 0) {
                        $url = 'https://'. $this->getHost() . $url .'.json?'. $data;
                    } else {
                        $url = 'https://'. $this->getHost() . $url .'.json';
                    }

                    $defaults = array(
                        CURLOPT_HTTPGET => 1,
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_HTTPHEADER => $headers
                    );
                    curl_setopt_array($ch, ($defaults));
                    $response = curl_exec($ch);
                    curl_close($ch);
                    break;
                    
                case 'post' :
                    $headers = array(
                        'Accept: application/json',
                        'Accept-Encoding: gzip, deflate',
                        'Content-Type: application/json;',
                        'Authorization: '. $this->getToken()
                    );
                    $data = http_build_query($params, '', '&');
                    $data2 = json_encode($params);
                    $url = 'https://'. $this->getHost() . $url .'.json';
                    $defaults = array(
                        CURLOPT_POST => 1,
                        CURLOPT_HEADER => 0,
                        CURLOPT_URL => $url,
                        CURLOPT_FRESH_CONNECT => 1,
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_TIMEOUT => 999,
                        CURLOPT_POSTFIELDS => $data2,
                        CURLOPT_HTTPHEADER => $headers
                    );
                    curl_setopt_array($ch, ($options + $defaults));
                    $response = curl_exec($ch);
                    curl_close($ch);
                    break;
                
                case 'put' :
                    $headers = array(
                        'Accept: application/json',
                        'Accept-Encoding: gzip, deflate',
                        'Content-Type: application/json;',
                        'Authorization: '. $this->getToken()
                    );
                    $data = http_build_query($params, '', '&');
                    $url = 'https://'. $this->getHost() . $url .'.json';
                    $defaults = array(
                        CURLOPT_CUSTOMREQUEST => 'PUT',
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_TIMEOUT => 999
                    );
                    curl_setopt_array($ch, ($options + $defaults));
                    $response = curl_exec($ch);
                    curl_close($ch);
                    break;
                
                case 'delete' :
                    throw new Exception('Not yet implemented');
                    break;
                    
                default :
                    throw new Exception('Nothing happens...');
                    break;
            }
            if($response === false) {
                throw new Exception('cURL error: '. curl_error($ch));
            } else {
                $response = json_decode($response);
                return $response->response;
            }
            
        }
    }
}