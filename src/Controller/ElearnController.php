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
use App\Controller\Elearn_modelController;

class ElearnController extends AbstractController
{
    
	private $session;
	private $elearn_model;
	private $validator;
	
	public function __construct(SessionInterface $session, Elearn_modelController $elearn_model, ValidatorInterface $validator)
    {
		$this->session = $session;
		$this->elearn_model = $elearn_model;
        $this->validator = $validator;
    }
	
		
	/**
     * @Route("/elearn", name="elearn")
     */
    public function index(): Response
    {
        return $this->render('elearn/home.html.twig', [
            'controller_name' => 'ElearnController',
        ]);
    }

    /**
     * @Route("/elearn/courses", name="courses")
     */
    public function courses(): Response
    {
        $data['courses'] = $this->elearn_model->get_all_courses();
        
        if($this->session->get('loggedin') == 'true'){
            $data['username'] = $this->session->get('username');
            $data['userid'] = $this->session->get('userid');
            $data['loggedin'] = $this->session->get('loggedin');
            $data['mycourses'] = $this->elearn_model->get_all_enrolls($data['userid']);
            // dump($data['mycourses']);
            // dd($data['courses'] );
            // path('enroll') ~ '/' ~ course.id
            // $data['cena'][$i] = 'enroll';  
            //             $data['cena_id'][$i] = $data['courses'][$i]['id'];
            //             $data['texto'][$i] = 'enroll';
            for ($i=0; $i < count($data['courses']); $i++) { 
                $data['cena'][$i] = 'enroll';  
                $data['cena_id'][$i] = $data['courses'][$i]['id'];
                $data['texto'][$i] = 'enroll';
            }
            for ($i=0; $i < count($data['courses']); $i++) { 
                for ($j=0; $j < count($data['mycourses']); $j++) {
                    if($data['courses'][$i]['id'] == $data['mycourses'][$j]['course_id']){
                        $data['cena'][$i] = 'courses';
                        $data['texto'][$i] = 'enrolled';
                        $data['cena_id'][$i] = '';
                        break;
                    }
                    else{
                        $data['cena'][$i] = 'enroll';  
                        $data['cena_id'][$i] = $data['courses'][$i]['id'];
                        $data['texto'][$i] = 'enroll';
                    }
                }
            }
        }
        else{
            for ($i=0; $i < count($data['courses']); $i++) { 
                $data['cena'][$i] = 'courses';
                $data['texto'][$i] = 'Must be logged in to enroll';
                $data['cena_id'][$i] = '';
            }
            $data['userid'] = 0;
            $data['username'] = '';
            $data['loggedin'] = 'false';
        }

        return $this->render('elearn/courses.html.twig', $data);
    }

     /**
    * @Route("/elearn/register", name="register")
    */

    public function register()
    {
        if($this->session->get('loggedin') == 'true'){
            return $this->redirectToRoute('courses');
        }
        $data['errors'] = 0;
        $data['email'] = '';
        $data['name'] = '';
        return $this->render('elearn/register.html.twig', $data);
    }

    /**
    * @Route("/elearn/register_action", name="register_action")
    */
    public function register_action(Request $request, ValidatorInterface $validator)
    {
            
           // method is "POST": this is the "register action" controller part                 
           $token = $request->request->get("token");

           if (!$this->isCsrfTokenValid('register_form', $token)) {
               return new Response("Operation not allowed", Response::HTTP_OK,
                   ['content-type' => 'text/plain']);
           }
      
      
           $username=$request->request->get('name');
           $password=$request->request->get('passwd1');
           $email=$request->request->get('email');
           $passconf=$request->request->get('passwd2');
      
      
            $user = $this->elearn_model->get_user($email);
           if ($user == false)
               $value = '';
           else
               $value = $user['email'];

            $user2 = $this->elearn_model->get_username($username);
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
                $data['name'] = $username;
                $data['email'] = $email;
                return $this->render('elearn/register.html.twig', $data);
            }
      
            $this->elearn_model->register($email,$username,$password);

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
    * @Route("/elearn/message", name="message")
    */
    public function message(){
        if($this->session->get('message'))
            $data['message'] = $this->session->get('message');
        else
            $data['message'] = '';

        return $this->render('elearn/message_template.html.twig', $data);
    }

    /**
    * @Route("/elearn/mycourses", name="mycourses")
    */
    public function mycourses(){
        if($this->session->get('loggedin') == 'true'){
            $data['username'] = $this->session->get('username');
            $data['userid'] = $this->session->get('userid');
            $data['loggedin'] = $this->session->get('loggedin');
        }
        else{
            $data['userid'] = NULL;
            $data['username'] = '';
            $data['loggedin'] = 'false';
        }
        if($data['loggedin'] == 'true'){
            $data['mycourses'] = $this->elearn_model->get_all_enrolls($data['userid']);
        }
        else{
            $this->session->set('message', 'you can view your courses if you are logged in!');
            return $this->redirectToRoute('message');
        }
        
        return $this->render('elearn/mycourses.html.twig', $data);
    }

    /**
    * @Route("/elearn/login", name="login")
    */
    public function login(){
        if($this->session->get('loggedin') == 'true'){
            return $this->redirectToRoute('courses');
        }
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
      
        return $this->render('elearn/login.html.twig', $data);
    }

    /**
    * @Route("/elearn/login_action", name="login_action")
    */
    public function login_action(Request $request, ValidatorInterface $validator){

        $token = $request->request->get("token");

        if (!$this->isCsrfTokenValid('login_form', $token)) {
            return new Response("Operation not allowed", Response::HTTP_OK,
                ['content-type' => 'text/plain']);
        }
   
   
        $email=$request->request->get('email');
        $password=$request->get('password');

   
   
            $user = $this->elearn_model->login($email, $password);
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

    /**
    * @Route("/elearn/logout", name="logout")
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
    * @Route("/elearn/enroll/{id?}", name="enroll")
    */
    public function enroll($id){
        $data['username'] = $this->session->get('username');
        $data['userid'] = $this->session->get('userid');
        $data['loggedin'] = $this->session->get('loggedin');
        $data['course'] = $this->elearn_model->get_course($id);
        $data['mycourses'] = $this->elearn_model->get_all_enrolls($data['userid']);

        for ($i=0; $i < count($data['mycourses']); $i++) { 
            if($data['mycourses'][$i]['course_id'] == $id){
                $this->session->set('message', 'you can only enroll once per course');
                return $this->redirectToRoute('message');
            }
        }
        if($data['loggedin'] == 'true'){
            $this->elearn_model->enroll_action($data['userid'], $data['course']['id']);
        }
        else{
            $this->session->set('message', 'you can only enroll when logged in!');
            return $this->redirectToRoute('message');
        }
        return $this->redirectToRoute('courses');
    }

}
