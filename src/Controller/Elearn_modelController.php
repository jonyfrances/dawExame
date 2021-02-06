<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Driver\Connection;

class Elearn_modelController extends AbstractController
{
	private $connection;	
	public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get_all_courses()
    {
        $query  = "SELECT courses.id, courses.name, courses.description, courses.price, courses.image, courses.sales, coursecategories.name AS categ_name, teachers.name AS teacher_name, teachers.image AS teacherimage FROM coursecategories JOIN courses ON coursecategories.id = courses.cat_id JOIN teachers ON teachers.id = courses.teacher_id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function get_all_enrolls($id)
    {
        $query  = "SELECT enrolls.id, enrolls.user_id, enrolls.course_id, enrolls.enroll_date, courses.name AS course_name, courses.description AS course_description, teachers.name AS teacher_name FROM enrolls JOIN courses ON courses.id = enrolls.course_id JOIN teachers ON teachers.id = courses.teacher_id WHERE enrolls.user_id = '$id'";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function register($email,$username,$password){

        $codifpass = substr(md5($password), 0, 32);

        $query = "INSERT INTO users(`name`,`email`,`password_digest`,`created_at`,`updated_at`) VALUES ('$username', '$email', '$codifpass', NOW(), NOW())";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return true;
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

    public function get_course($id){

        $query = "SELECT * FROM courses WHERE courses.id = '$id'";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function enroll_action($userId, $courseId){

        $query = "INSERT INTO enrolls(`user_id`,`course_id`,`enroll_date`) VALUES ('$userId', '$courseId', NOW())";

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
}
