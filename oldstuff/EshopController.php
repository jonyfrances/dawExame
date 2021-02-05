<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Controller\Eshop_modelController;

class EshopController extends AbstractController
{
    
	private $session;
	private $eshop_model;
	private $validator;
    
    


	public function __construct(SessionInterface $session, Eshop_modelController $eshop_model, ValidatorInterface $validator)
    {
		$this->session = $session;
		$this->eshop_model = $eshop_model;
 		$this->validator = $validator;
    }

    /**
     * @Route("/eshop/products/{id?}", name="products")
     */
    public function index(){

        $data['cartTotal'] = $this->session->get('cartTotal');
        if(!$data['cartTotal']){
            $this->session->set('cartTotal', 0);
        }

        $data['cartFullAmount'] = $this->session->get('cartFullAmount');
        if(!$data['cartFullAmount']){
            $this->session->set('cartFullAmount', 0);
        }

        $data['products'] = $this->eshop_model->get_all_products();
        $data['cartFullAmount'] = 0;
        for ($i=1; $i <= count($data['products']); $i++) { 
            $data['productAmount'][$i] = $this->session->get('productAmount'[$i]);  
            $data['cartFullAmount'] += $data['productAmount'][$i];   
        }

        $data['productQty'][$i] = $this->session->get('productQty'[$i]);   
        if(!$data['cartFullAmount']){
            $this->session->set('cartFullAmount', 0);
        }

        if($this->session->get('loggedin') == 'true'){
            $data['username'] = $this->session->get('username');
            $data['userid'] = $this->session->get('userid');
            $data['loggedin'] = $this->session->get('loggedin');
            
        }
        else{
            $data['userid'] = 0;
            $data['username'] = '';
            $data['loggedin'] = 'false';
        }
        $data['products'] = $this->eshop_model->get_all_products();
        return $this->render('eshop/index.html.twig', $data);
    }




    /**
    * @Route("/eshop/checkout", name="checkout")
    */
    public function checkout(){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        
        $data['cartTotal'] = $this->session->get('cartTotal');
        $data['products'] = $this->eshop_model->get_all_products();
        $data['cartFullAmount'] = $this->session->get('cartFullAmount');


        for ($i=1; $i <= count($data['products']); $i++) { 
            $data['productAmount'][$i] = $this->session->get('productAmount'[$i]);  
            $data['productQty'][$i] = $this->session->get('productQty'[$i]) * $data['products'][$i-1]['price'];   
            $data['cartFullAmount'] += $data['productAmount'][$i];
        }
        
        if($data['loggedin'] == 'true' && $data['cartFullAmount'] > 0){

            $data['orderId'] = $this->eshop_model->create_new_order($data['userid'], $data['cartTotal']);

            for ($i=1; $i <= count($data['products']) ; $i++) { 
                if ($data['productAmount'][$i] > 0) {
                    $this->eshop_model->create_new_order_item($data['orderId'], $i, $data['productAmount'][$i]);
                }
            }

            $this->session->set('message',"checkout successful");
            $this->session->set('cartTotal', 0);
            $this->session->set('cartFullAmount', 0);
            for ($i=1; $i <= count($data['products']); $i++) { 
                $this->session->set('productAmount'[$i], 0);  
            }
        }
        else {
            $this->session->set('message', "Login first or cart is empty");
        }
        return $this->message();
    }

    /**
    * @Route("/eshop/history", name="history")
    */
    public function history(){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        $data['history'] = $this->session->get('history');        
        $data['cartTotal'] = $this->session->get('cartTotal');
        $data['cartFullAmount'] = $this->session->get('cartFullAmount');

        if($data['loggedin'] == 'true'){
            $data['history'] = $this->eshop_model->get_order_history($data['userid']);
        }
        
        return $this->render('eshop/history.html.twig', $data);
    }
    
    /**
     * @Route("/eshop/history_items/{id?}", name="history_items")
     */
    public function history_items($id){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        $data['history_items'] = $this->session->get('history_items');        
        $data['cartTotal'] = $this->session->get('cartTotal');
        $data['cartFullAmount'] = $this->session->get('cartFullAmount');
        $data['orderid'] = $id;

        if($data['loggedin'] == 'true'){
            $data['history_items'] = $this->eshop_model->get_order_items($data['orderid']);
        }
        
        return $this->render('eshop/view_order.html.twig', $data);
    }

    /**
    * @Route("/eshop/cart", name="cart")
    */
    public function cart(){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        
        $data['cartTotal'] = $this->session->get('cartTotal');
        $data['products'] = $this->eshop_model->get_all_products();

        $data['cartFullAmount'] = 0;
        for ($i=1; $i <= count($data['products']); $i++) { 
            $data['productAmount'][$i] = $this->session->get('productAmount'[$i]);  
            $data['productQty'][$i] = $this->session->get('productQty'[$i]) * $data['products'][$i-1]['price'];   
            $data['cartFullAmount'] += $data['productAmount'][$i];   
        }




        return $this->render('eshop/orders.html.twig', $data);
    }


    /**
    * @Route("/eshop/addProduct/{id?}", name="addProduct")
    */
    public function addProduct($id){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        $data['prod'] = $this->eshop_model->get_product($id);




        $data['productAmount'][$id] = $this->session->get('productAmount'[$id]) + 1;

        $data['cartTotal'] = $this->session->get('cartTotal') + $data['prod']['price'];


        $this->session->set('productAmount'[$id], $data['productAmount'][$id]);
        $this->session->set('cartTotal', $data['cartTotal']);


        return $this->redirectToRoute('products');
    }

    /**
    * @Route("/eshop/addProductCart/{id?}", name="addProductCart")
    */
    public function addProductCart($id){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        $data['prod'] = $this->eshop_model->get_product($id);




        $data['productAmount'][$id] = $this->session->get('productAmount'[$id]) + 1;

        $data['cartTotal'] = $this->session->get('cartTotal') + $data['prod']['price'];


        $this->session->set('productAmount'[$id], $data['productAmount'][$id]);
        $this->session->set('cartTotal', $data['cartTotal']);


        return $this->redirectToRoute('cart');
    }

    /**
    * @Route("/eshop/removeProductCart/{id?}", name="removeProductCart")
    */
    public function removeProductCart($id){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        $data['prod'] = $this->eshop_model->get_product($id);




        $data['productAmount'][$id] = $this->session->get('productAmount'[$id]) - 1;

        $data['cartTotal'] = $this->session->get('cartTotal') - $data['prod']['price'];


        $this->session->set('productAmount'[$id], $data['productAmount'][$id]);
        $this->session->set('cartTotal', $data['cartTotal']);


        return $this->redirectToRoute('cart');
    }

    /**
    * @Route("/eshop/register", name="register")
    */

    public function register()
    {
        $data['errors'] = 0;
        $data['newemail'] = '';
        $data['newname'] = '';
        return $this->render('eshop/register_template.html.twig', $data);
    }

    /**
    * @Route("/eshop/register_action", name="register_action")
    */
    public function register_action(Request $request, ValidatorInterface $validator)
    {
            
           // method is "POST": this is the "register action" controller part                 
           $token = $request->request->get("token");

           if (!$this->isCsrfTokenValid('register_form', $token)) {
               return new Response("Operation not allowed", Response::HTTP_OK,
                   ['content-type' => 'text/plain']);
           }
      
      
           $username=$request->request->get('newname');
           $password=$request->request->get('newpassword');
           $email=$request->request->get('newemail');
           $passconf=$request->request->get('newconfpassword');
      
      
            $user = $this->eshop_model->get_user($email);
           if ($user == false)
               $value = '';
           else
               $value = $user['email'];

            $user2 = $this->eshop_model->get_username($username);
            if ($user2 == false)
                $value2 = '';
            else
                $value2 = $user2['name'];
       
           $input = ['password' => $password, 'passconf' => $passconf, 'username' => $username, 'email' => $email];

           $constraints = new Assert\Collection([
			    'email' => [new Assert\NotIdenticalTo(['value' => $value, 'message' => "This user is already in the database"]), // !== !=
							new Assert\NotBlank(['message' => "Email field must not be blank."]),
							new Assert\Email(['message' => "The email {{ value }} is not a valid email."])],							
                'username' => [new Assert\NotBlank(['message' => "Name field must not be blank."]),
                                new Assert\NotIdenticalTo(['value' => $value2, 'message' => "This username is already in the database"])],
			    'password' => [new Assert\NotBlank(['message' => "Password field must not be blank."]),
								new Assert\EqualTo(['value' => $passconf, 'message' => "Passwords do not match"])],
			    'passconf' => [new Assert\NotBlank(['message' => "Password Confirmation field must not be blank."])],
		    ]);
          
           $data = $this->requestValidation($input, $constraints);
              
            if ( $data['errors'] > 0) {
                $data['newname'] = $username;
                $data['newemail'] = $email;
                return $this->render('eshop/register_template.html.twig', $data);
            }
      
            $this->eshop_model->register($email,$username,$password);

            $this->session->set('message',"Registration successful. Welcome $username!");

            return $this->message();
      
    }

    private function requestValidation($input, $constraints)
   {
      
           $violations = $this->validator->validate($input, $constraints);
      
               $errorMessages = [];
          
           if (count($violations) > 0) {

               $accessor = PropertyAccess::createPropertyAccessor();

               foreach ($violations as $violation) {

                   $accessor->setValue($errorMessages,
                       $violation->getPropertyPath(),
                       $violation->getMessage());
               }
          
           }   
               $data['errors'] = count($violations);
               $data['errorMessages'] = $errorMessages;
                
           return $data;
   }
    
    /**
    * @Route("/eshop/message", name="message")
    */
    public function message(){
        if($this->session->get('message'))
            $data['message'] = $this->session->get('message');
        else
            $data['message'] = '';

        return $this->render('eshop/message_template.html.twig', $data);
    }

    /**
    * @Route("/eshop/logout", name="logout")
    */

    public function logout(Request $request){

        $this->session->set('userid', 0);
        $this->session->set('username', '');
        $this->session->set('email', '');
        $this->session->set('loggedin', 'false');
        $this->session->set('errors', '');
        $this->session->set('message', 'See you back soon!');
        
        return $this->redirectToRoute('message');

    }
    
     /**
    * @Route("/eshop/login", name="login")
    */
    public function login(){
        if ($this->session->get('errors') > 0)
        {
           $data['errors'] = $this->session->get('errors');
           $data['email'] = $this->session->get('email') ;
           $data['errorMessages'] = $this->session->get('errorMessages');
        }
        else
        {
            $data['errors'] = 0;
            $data['email'] = '';
        }
      
        return $this->render('eshop/login_template.html.twig', $data);
    }

    /**
    * @Route("/eshop/login_action", name="login_action")
    */
    public function login_action(Request $request, ValidatorInterface $validator){

        $token = $request->request->get("token");

        if (!$this->isCsrfTokenValid('login_form', $token)) {
            return new Response("Operation not allowed", Response::HTTP_OK,
                ['content-type' => 'text/plain']);
        }
   
   
        $email=$request->request->get('email');
        $password=$request->get('password');

   
   
            $user = $this->eshop_model->login($email, $password);
        if ($user == false)
            $value = '';
        else
            $value = $password;
       
        $input = ['password' => $password,  'email' => $email];

        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(['message' => "Email must not be blank"]), new Assert\Email(['message' => "The email {{ value }} is not a valid email."])],
            'password' => [new Assert\notBlank(['message' => "Password must not be blank"]),
                            new Assert\EqualTo(['value' => $value, 'message' => "Wrong email or password"])],             
        ]);


       
        $data = $this->requestValidation($input, $constraints);
           
        if ( $data['errors'] > 0) {
           
            $this->session->set('email', $email);
            $this->session->set('errors', $data['errors']);
            $this->session->set('errorMessages', $data['errorMessages']);
            return $this->redirectToRoute('login');
        }
   
        $this->session->set('userid', $user['id']);
        $this->session->set('email', $user['email']);
        $this->session->set('username', $user['name']);
        $this->session->set('loggedin', 'true');

        
        $this->session->set('message', "Welcome back $user[name]!");
        

        $reply = new Response();





        return $this->redirectToRoute('message');


    }


}
