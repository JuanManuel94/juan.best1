<?php
  class LoginModel extends Mysql{
    private $intId,$strUsername,$strPassword,$strEmail,$strToken;
    public function __construct(){
      parent::__construct();
    }
    public function validation(string $username, string $password){
			$this->strUsername = $username;
			$this->strPassword = $password;
			$sql = "SELECT id,state FROM users WHERE username = '$this->strUsername' AND password = '$this->strPassword' AND state != 0";
			$request = $this->select($sql);
			return $request;
		}
		public function login_session(int $id){
			$this->intId = $id;
			$sql = "SELECT u.id,u.names,u.surnames,u.documentid,u.document,u.mobile,u.email,u.profileid,p.profile,u.username,u.password,u.image,u.state
			FROM users u
			JOIN profiles p ON u.profileid = p.id
			WHERE u.id = $this->intId";
			$request = $this->select($sql);
			$_SESSION['userData'] = $request;
			return $request;
    }
    public function validation_email(string $email){
    	$this->strEmail = $email;
      $sql = "SELECT id,names,surnames FROM users WHERE email = '$this->strEmail' AND state = 1";
    	$request = $this->select($sql);
    	return $request;
    }
    public function update_token(int $id, string $token){
      $this->intId = $id;
      $this->strToken = $token;
      $answer = "";
      $sql = "UPDATE users SET token = ? WHERE id = $this->intId";
      $data = array($this->strToken);
      $update = $this->update($sql,$data);
      if($update){
        $answer = 'success';
      }else{
        $answer = 'error';
      }
      return $answer;
    }
    public function user_information(string $email, string $token){
      $this->strEmail = $email;
      $this->strToken = $token;
    	$sql = "SELECT id,names,surnames FROM users WHERE email = '$this->strEmail' AND token = '$this->strToken' AND state = 1 ";
    	$request = $this->select($sql);
    	return $request;
    }
    public function update_password(int $id, string $password){
      $this->intId = $id;
      $this->strPassword = $password;
      $answer = "";
      $sql = "UPDATE users SET password = ?, token = ? WHERE id = $this->intId";
      $arrData = array($this->strPassword,"");
      $update = $this->update($sql,$arrData);
      if($update){
        $answer = 'success';
      }else{
        $answer = 'error';
      }
      return $answer;
    }
  }
