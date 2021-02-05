<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Driver\Connection;

class Blog_modelController extends AbstractController
{
    private $connection;
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function get_posts()
    {
        $query  = "SELECT * , users.id AS usersID, microposts.id AS micropostsID FROM users JOIN microposts ON users.id = microposts.user_id ORDER BY microposts.updated_at DESC";

        $posts = $this->connection->fetchAll($query);
        return $posts;
    }

    public function get_post($userid, $blogid){
        $query = "SELECT * FROM microposts WHERE microposts.user_id = '$userid' AND microposts.id='$blogid'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function get_user($email){
        $query = "SELECT * FROM users WHERE users.email = '$email'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function get_username($username){

        $query = "SELECT * FROM users WHERE users.name = '$username'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetch();

    }


    public function register($email,$username,$password){

        $codifpass = substr(md5($password), 0, 32);

        $query = "INSERT INTO users(`name`,`email`,`password_digest`,`created_at`,`updated_at`) VALUES ('$username', '$email', '$codifpass', NOW(), NOW())";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return true;
    }

    public function new_blog($userid,$content){

        $query = "INSERT INTO microposts(user_id,created_at,updated_at,content) VALUES('$userid', NOW() , NOW() ,'$content')";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return true;
    }

    public function update_blog($blogid,$content){

        $query = "UPDATE microposts SET microposts.content='$content' , microposts.updated_at=NOW() WHERE microposts.id='$blogid'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return true;
    }


    public function login($email,$password){

        $codifpass = substr(md5($password), 0, 32);



        $query = "SELECT * FROM users WHERE users.email='$email' AND users.password_digest='$codifpass'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function set_remember_digest($email,$remember_digest){
        $query = "UPDATE users SET users.remember_digest='$remember_digest' WHERE users.email='$email'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return true;

    }

    public function check_remember_digest($remember_digest){
        $query = "SELECT * FROM users WHERE users.remember_digest='$remember_digest'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetch();

    }

}
?>