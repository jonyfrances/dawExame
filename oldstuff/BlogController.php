<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Controller\Blog_modelController;

class BlogController extends AbstractController
{

    private $blog_model;
    private $session;
    private $validator;
    
    public function __construct(Blog_modelController $blog_model, SessionInterface $session, ValidatorInterface $validator)
    {
        $this->blog_model = $blog_model;
        $this->session = $session;
        $this->validator = $validator;
    }

    /**
    * @Route("/blog", name="blog")
    */

    public function index(Request $request)
    {

        if($request->cookies->get('siteAuth')){
            
            $remember_digest=$request->cookies->get('siteAuth');
            
            $checker = $this->blog_model->check_remember_digest($remember_digest);

            $this->session->set('loggedin', 'true');
            $this->session->set('username', $checker['name']);
            $this->session->set('userid', $checker['id']);
            $this->session->set('email', $checker['email']);

        }

        if($this->session->get('loggedin') == 'true'){
            $data['loggedin'] = $this->session->get('loggedin');
            $data['username'] = $this->session->get('username');
            $data['userid'] = $this->session->get('userid');
            $data['blogid'] = $request->attributes->get('blogid');

        }
        else{
            $data['loggedin'] = 'false';
            $data['username'] = '';
            $data['userid'] = 0;
        }

        $data['users'] = $this->blog_model->get_posts();
        
        return $this->render('blog/index_template.html.twig', $data);
    }


    /**
    * @Route("/blog/logout", name="logout")
    */

    public function logout(Request $request){

        $this->session->set('userid', 0);
        $this->session->set('username', '');
        $this->session->set('email', '');
        $this->session->set('loggedin', 'false');
        $this->session->set('errors', '');
        $this->session->set('message', 'See you back soon!');
        
        $reply = new Response();

        if($request->cookies->get('siteAuth')){
            $reply->headers->setCookie(Cookie::create('siteAuth', '' , time() - 170000000 ));
            $reply->send();
        }
        

        return $this->redirectToRoute('message');


    }


    /**
    * @Route("/blog/register", name="register")
    */

    public function register()
    {
        $data['errors'] = 0;
        $data['newemail'] = '';
        $data['newname'] = '';
        return $this->render('blog/register_template.html.twig', $data);
    }

    /**
    * @Route("/blog/register_action", name="register_action")
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
      
      
            $user = $this->blog_model->get_user($email);
           if ($user == false)
               $value = '';
           else
               $value = $user['email'];

            $user2 = $this->blog_model->get_username($username);
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
                return $this->render('blog/register_template.html.twig', $data);
            }
      
            $this->blog_model->register($email,$username,$password);

            $this->session->set('message',"Registration successful. Welcome $username!");

            return $this->message();
      
    }

    
    /**
    * @Route("/blog/post/{blogid?}", name="post")
    */

    public function post($blogid = FALSE){

        $userid = $this->session->get('userid');


        if ($this->session->get('loggedin') == 'true')
        {
            if($blogid){
                $post = $this->blog_model->get_post($userid,$blogid);
                if($post == false){
                    $this->session->set('message', 'This post is not yours or it does not exist');
                    return $this->redirectToRoute('message');
                }
                $data['blogid'] = $blogid;
                $data['oldcontent'] = $post['content'];
                $this->session->set('blogid', $blogid);
            }
            else{
                $data['blogid'] = '';
				$data['oldcontent'] = '';
            }
            $data['username'] = $this->session->get('username');
            $data['loggedin'] = $this->session->get('loggedin');
        }
        else
        {
            $data['loggedin'] = 'false';
			$this->session->set('message', 'ERROR: Login first.');
			return $this->redirectToRoute('message');
        }
        
        return $this->render('blog/blog_template.html.twig', $data);

    }


    /**
    * @Route("/blog/post_action/{blogid?}", name="post_action")
    */
    public function post_action($blogid = FALSE, Request $request){

        $token = $request->request->get("token");


		if($request->isMethod('POST') && $request->attributes->get('blogid') != '' && $request->attributes->get('blogid') == $this->session->get('blogid')) {

			if (!$this->isCsrfTokenValid('blog_form', $token)) {
				return new Response("Operation not allowed", Response::HTTP_OK,
						['content-type' => 'text/plain']);
			}

			$content = $request->request->get('content');
			$userid = $this->session->get('userid');
			$blogid = $request->attributes->get('blogid');
			
			$this->blog_model->update_blog($blogid, $content);
            
            $this->session->set('message', 'Post updated successfully!');
            // dd($content);
			return $this->redirectToRoute('message');
		}
		else if($request->isMethod('POST') && $request->attributes->get('blogid') == '') {

			if (!$this->isCsrfTokenValid('blog_form', $token)) {
				return new Response("Operation not allowed", Response::HTTP_OK,
						['content-type' => 'text/plain']);
			}

			$content = $request->request->get('content');
			$userid = $this->session->get('userid');
			
            $this->blog_model->new_blog($userid, $content);
            
            $this->session->set('message', 'New post submitted successfully!');
			return $this->redirectToRoute('message');
		}
		else {
			$this->session->set('message', 'Something went wrong, please try again.');

			return $this->redirectToRoute('message');
		}

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
    * @Route("/blog/message", name="message")
    */
    public function message(){
        if($this->session->get('message'))
            $data['message'] = $this->session->get('message');
        else
            $data['message'] = '';

        return $this->render('blog/message_template.html.twig', $data);
    }

    /**
    * @Route("/blog/login", name="login")
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
      
        return $this->render('blog/login_template.html.twig', $data);
    }

    /**
    * @Route("/blog/login_action", name="login_action")
    */
    public function login_action(Request $request, ValidatorInterface $validator){

        $token = $request->request->get("token");

        if (!$this->isCsrfTokenValid('login_form', $token)) {
            return new Response("Operation not allowed", Response::HTTP_OK,
                ['content-type' => 'text/plain']);
        }
   
   
        $email=$request->request->get('email');
        $password=$request->get('password');

   
   
            $user = $this->blog_model->login($email, $password);
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

        if($request->request->get('autologin')){
            $cookie_name = 'siteAuth';
            $cookie_time = (60 * 24 * 30);
            $remember_digest = substr(md5(time()),0,32);
            
            $reply->headers->setCookie(Cookie::create($cookie_name,$remember_digest, time() + $cookie_time));
            $reply->send();

            $this->blog_model->set_remember_digest($email, $remember_digest);
        }




        return $this->redirectToRoute('message');


    }
}

?>
