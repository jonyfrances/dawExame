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
        $query  = "SELECT *, courses.image AS course_image ,courses.name AS courses_name, teachers.name AS teacher_name, teachers.image AS teacher_image, coursecategories.name AS categ_name FROM coursecategories JOIN courses ON coursecategories.id = courses.cat_id JOIN teachers ON teachers.id = courses.teacher_id ORDER BY courses.id";
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
}
