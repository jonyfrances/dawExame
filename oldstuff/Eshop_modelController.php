<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Driver\Connection;

class Eshop_modelController extends AbstractController
{
	private $connection;	
	public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get_all_products(){
        $query = "SELECT * FROM products";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();

    }


    public function get_product($id){
        $query = "SELECT * FROM products WHERE products.id = '$id'";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create_new_order($userid, $total){
        $query = "INSERT INTO orders(`user_id`, `created_at`, `status`, `total`) VALUES ('$userid', NOW(), 0, '$total')";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $id = $this->connection->lastInsertId();
        return $id;
    }

    public function get_order_history($userid){
        $query = "SELECT * FROM orders WHERE orders.user_id = '$userid'";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function get_order_items($orderid){
        $query = "SELECT *, order_items.quantity AS item_quantity, order_items.order_id AS order_id FROM products JOIN order_items ON products.id = order_items.product_id WHERE order_items.order_id = '$orderid'";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create_new_order_item($orderid, $productid, $quantity){
        $query = "INSERT INTO order_items(`order_id`, `product_id`, `quantity`) VALUES ('$orderid', '$productid', '$quantity')";
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

    public function register($email,$username,$password){

        $codifpass = substr(md5($password), 0, 32);

        $query = "INSERT INTO users(`name`,`email`,`password_digest`,`created_at`,`updated_at`) VALUES ('$username', '$email', '$codifpass', NOW(), NOW())";

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
