<?php


 class users{

   private $db;

   
   public function  __construct($connect){
      $this->db = $connect;
   }



   //chek if email exist in data base
  public function emailExists($email) {
       $sql = "SELECT id_user FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            return true;
        }
        return false;
    }




    // insert user if email not exist
    public function registre($name, $email, $hashed_password , $phone, $whatsapp, $role){

         $sql = "INSERT INTO users(name, email, password, phone, whatsapp, role)VALUES(?, ?, ? , ? ,? ,?)";
         $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
             $name ,
             $email,
             $hashed_password,
             $phone,
             $whatsapp,
             $role
         ]);

       if($result){
            return $this->db->lastInsertId();

       }

       return false;
    }




    //method log in 
    public function login($email , $password){
         $sql = " SELECT id_user , name , password , role FROM users WHERE email = ?";

          $stmt = $this->db->prepare($sql);
          $stmt->execute([$email]);
          $user = $stmt->fetch();
 
          
          // check if user exists 
          if($user){
             
           //chek if password is correct
              if(password_verify($password , $user['password'])){
                 
              // if password is correct return data of this user for use it in session
                  return $user ;
              }
          }
          return false;
       }




    // get all users to use in managment users i dashbord admin
            public function getAllUsers() {
                $stmt = $this->db->prepare("SELECT * FROM users ORDER BY id_user DESC");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    }
























?>