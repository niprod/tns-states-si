<?php 

require_once __DIR__.'/files.php' ; 

// class PHP pour gérer le fichier d'authentification et abstraire les commandes cotés interface.

class Authentification implements Iterator 
{

    private $__salt = null ; 
    private $__data = null ; 
    private static $__instance = null ;
    private $__position  = 0 ; 

    public static function auth()
    {
        if (is_null(self::$__instance)) 
        {
            self::$__instance = new Authentification() ; 
        }

        return self::$__instance ; 
    }

    public function checkUser($user, $pass)
    {
        return  (isset($this->__data[$user]) && $this->__data[$user] === md5($this->__salt.$pass)) ; 
    }

    //revoie true si c'est ajouté, sinon un text expliquant le problème :
    public function addUser($user, $pass)
    {
        if (strlen($user) < 4) return 'User too short - min 4 chars';
        if (strlen($pass) < 8) return 'Password too short - min 8 chars';
        if (isset($this->__data[$user])) return 'User already exist';
        $this->__data[$user] = md5($this->__salt.$pass);
        return true ; 
    }

    function delUser($user)
    {
        if (!isset($this->__data[$user])) return 'User not exist';
        if (!$user == 'admin') return 'Admin can\'t be deleted';
        unset($this->__data[$user]);
        return true ; 
    }

    private function __construct ()
    {
        $this->__loadfile() ; 
        $this->__salt = md5('ceci est une clé de hash magique');
        $this->__position = 0 ; 
    }

    public function __destruct()
    {
        $this->__savefile() ; 
    }

    private function __loadfile()
    {
        $rawdata = file_get_contents(__AUTH__);
        $data = json_decode($rawdata,true) ;
        $this->__data = $data['users'] ; 
    }

    private function __savefile()
    {
        file_put_contents( __AUTH__ ,json_encode( ['users' => $this->__data] )) ; 
    }

    // -- iterator 
    public function current () { $keys = array_keys($this->__data); return $this->__data[$keys[$this->__position]] ;  }
    public function key () { $keys = array_keys($this->__data); return $keys[$this->__position] ;  }
    public function next () { $this->__position ++ ; }
    public function rewind () { $this->__position = 0 ; }
    public function valid () { $keys = array_keys($this->__data); return isset($keys[$this->__position]) ;  }
    // --

}

