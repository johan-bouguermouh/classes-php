<?php
class User
{
    //déclaration des attribus
    public $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    //construct 
    private function securite_bdd($array) //Function pour eviter les injections sql
    {
        $conn = mysqli_connect('localhost','root','','classes');
        foreach ($array as $key => $value){
        // On regarde si le type de string est un nombre entier (int)
        if(ctype_digit($value))
        {
            $value = intval($value);
        }
        // Pour tous les autres types
        else
        {
            $value = mysqli_real_escape_string($conn,$value);
            $value = addcslashes($value, '%_');
        }
        }
        
        return $array; //On retourne les resultats sous forme de tableau
    }
    public function __construct($array){

        
        foreach ($this->securite_bdd($array) as $key => $value)
        {
            $init_key = explode('_',$key);
            if($init_key[0]=='login'){
                $this->login = $value;
            }
            elseif($init_key[0]=='email'){
                $this->email = $value; 
            }
            elseif($init_key[0]=='firstname'){
                $this->firstname = $value;
            }
            elseif( $init_key[0] == 'lastname'){
                $this->lastname = $value;
            }
            elseif($init_key[0]=='id'){
                $this->id = $value;
            }
        }
    }
     //method register
     public function register($array){
        $login_data = $this->login;
        $conn = mysqli_connect('localhost','root','','classes');
        $select_request = mysqli_query($conn,"SELECT * FROM `utilisateurs` WHERE `login`= '$login_data'");
        $select_result = mysqli_fetch_assoc($select_request);

        foreach ($array as $key => $value){
            if($value == null){
                $message_register = 'veuillez remplir tout les champs';
                return $message_register;
            }
        }
        if(!empty($select_result)){
             return "Le login que vous avez choisi n'est pas disponnible";
        } 
        elseif($array['password_inscription']!=$array['passwordv_inscription']){
            $message_register = 'Assurez-vous que les deux mot de passes soient indentiques';
            return $message_register;
        }
        else{
            setcookie("password_cookie", $array['password_inscription'],time()+300);
            $password_data = password_hash($array['password_inscription'],PASSWORD_DEFAULT);
            $login_data = $this->login;
            $email_data = $this->email;
            $firstname_data = $this->firstname;
            $lastname_data = $this->lastname;
            $register_request = mysqli_query($conn,"INSERT INTO `utilisateurs`(`login`, `password`, `email`, `firstname`, `lastname`) VALUES ('$login_data','$password_data','$email_data','$firstname_data','$lastname_data')");
            setcookie("login_cookie", $login_data,time()+300);
            return $array;
        }
        
    }

    public function connect($array)
    {
        $login_data = $this->login;
            function verif_data($login_data){
                $conn = mysqli_connect('localhost','root','','classes');
                $select_request = mysqli_query($conn,"SELECT * FROM `utilisateurs` WHERE `login`= '$login_data'");
                $result = mysqli_fetch_all($select_request,MYSQLI_ASSOC);
                return $result;   
            }
        if(isset($array['password_connect'])){
            if(verif_data($this->login)==null){
                $message_connect = 'Ce Login est incorrect';
                return $message_connect;
            }
            elseif(empty($array['password_connect'])){
                $message_connect = 'veuillez rentre votre mot de passe';
                return $message_connect;
            }
            elseif(password_verify($array['password_connect'],verif_data($this->login)['0']['password'])==false){
                $message_connect = 'Mot de pass incorrect';
                return $message_connect;
            }
            else{
                $this->id=verif_data($this->login)['0']['id'];
                $this->email=verif_data($this->login)['0']['email'];
                $this->firstname=verif_data($this->login)['0']['firstname'];
                $this->lastname=verif_data($this->login)['0']['lastname'];
                $message_connect = 'Félicitation '.$this->firstname.',</br> vous êtes à présent connecté !';
                return $message_connect;
            }
        }
        return verif_data($this->login);
    }

    public function disconnect(){
        session_destroy();
        header('location: index.php');
    }

    public function isConnected(){
        $login_data = $this->login;
        $conn = mysqli_connect('localhost','root','','classes');
        $select_request = mysqli_query($conn,"SELECT * FROM `utilisateurs` WHERE `login`= '$login_data'");
        $result = mysqli_fetch_assoc($select_request);
        if(is_array($result)){
            return true;
        }
        else return false;
    }

    public function delete(){
        $login_data = $this->login;
        $conn = mysqli_connect('localhost','root','','classes');
        $select_request = mysqli_query($conn,"DELETE * FROM `utilisateurs` WHERE `login`= '$login_data'");
    }

    public function update($array){
        $id_data=$this->id;
        foreach ($array as $key => $value)
        {
            $init_key = explode('_',$key);
            if($init_key[0]=='login'){
                if($value!=null){
                $this->login = $value;
                }
            }
            elseif($init_key[0]=='email'){
                if($value!=null){
                $this->email = $value;
                }
            }
            elseif($init_key[0]=='firstname'){
                if($value!=null){
                $this->firstname = $value;
                }
            }
            elseif( $init_key[0] == 'lastname'){
                if($value!=null){
                $this->lastname = $value;
                }
            }
        }
        $login_data = $this->login;
        $email_data = $this->email;
        $firstname_data = $this->firstname;
        $lastname_data = $this->lastname;

        $conn = mysqli_connect('localhost','root','','classes');
        $select_request = mysqli_query($conn,"UPDATE `utilisateurs` SET `login`='$login_data',`email`='$email_data',`firstname`='$firstname_data',`lastname`='$lastname_data' WHERE $id_data");
        
    }

    public function getAllInfos(){
        $login_data = $this->login;
        $array_result = verif_data($login_data);
        return $array_result;
    }

    public function getLogin(){
        if(isset($this->login)){
            $result_login = $this->login;
            return $result_login
        }
    }

    public function getEmail(){
        if(isset($this->email)){
            $result_email = $this->email;
            return $result_email
        }
    }

    public function getfirstname(){
        if(isset($this->firstname)){
            $result_firstname = $this->firstname;
            return $result_firstname
        }
    }

    public function getlastname(){
        if(isset($this->lastname)){
            $result_lastname = $this->lastname;
            return $result_lastname
        }
    }

}
?>