<?php

namespace TRando\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use TRando\MainBundle\Entity\User;


class UserController extends FOSRestController
{
    /********************************* rest api routes and services *****************************************/

    /**
     * @Rest\Post("/ahh")
     */
    public function AuthAction()
    {
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/register")
     *
     */
    public function registerAction(Request $request)
    {


        /** @var \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        if($userManager->findUserByUsernameOrEmail($request->get('username')) || $userManager->findUserByUsernameOrEmail($request->get('email'))){
            $data = array("status" => "User exist");
            return  new Response($this->serialize($data));

        }
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm(array('csrf_protection' => false));
        $form->setData($user);
        $this->processForm($request, $form);


       if($user){
           $this->sendActivationTokenWithEmail($this->generateRandomString(),$user);

           $userManager->updateUser($user);
           $dispatcher->dispatch(
               FOSUserEvents::REGISTRATION_SUCCESS, $event

           );
           $data = array("status" => "User created");
           return  new Response($this->serialize($data));

       }else{
           $data = array("status" => "error");
           return  new Response($this->serialize($data));

       }

    }
    /**
     *
     * @Rest\Post("/auth")
     */
    public  function AuthenticationAction(Request $request){
        $data = json_decode($request->getContent(), true);
        $user_manager = $this->get('fos_user.user_manager');
        $factory = $this->get('security.encoder_factory');

        $user = $user_manager->findUserByUsernameOrEmail($data['username']);

          if ($user) {
               $encoder = $factory->getEncoder($user);
               $bool = ($encoder->isPasswordValid(
                   $user->getPassword(),
                   $data['password'],
                   $user->getSalt()
               )) ? "true" : "false";

   if($bool === "true") {
       if(!$user->isEnabled()){
           return  new Response($this->serialize(array('response'=>'Account disabled !')), Response::HTTP_CREATED);
       }
       $this->get('fos_user.security.login_manager')->logInUser('oauth_authorize', $user, new Response("OK"));


       
   return  new Response($this->serialize(array('response'=>'User Authenticated')), Response::HTTP_CREATED);



   }

           }
           return   new Response($this->serialize(array('response'=>'error')), Response::HTTP_BAD_REQUEST);

    }
    /**
     * @Rest\Put("/ActivateAccount");
     */
    public function ActivateAccountAction(Request $request){
        $userManager = $this->get('fos_user.user_manager');
      $user= $userManager->findUserByUsernameOrEmail($request->get('email')) ;
      if($user){
          if($user->getActivationToken()== $request->get('token')){
              $user->setActivationToken(" ");
              $user->setEnabled(true);
              $userManager->updateUser($user);
              return new Response($this->serialize(array("response"=>"Activated")));
          }else{
              return new Response($this->serialize(array("response"=>"InvalidToken")));
          }
      }
        return new Response($this->serialize(array("response"=>"Error")));

    }



    /**
     * @Rest\Get("/users")
     */
    public function getAllAction()
    {
        $em =$this->getDoctrine()->getManager();
        $users = $em->getRepository('TRandoMainBundle:User')->findAll();
        return new Response($this->serialize($users),Response::HTTP_CREATED);
    }
    /**
     * @Rest\Post("getUserByToken")
     */
    public  function getUserByTokenAction(Request $request){

        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('TRandoMainBundle:User')->findAll();
        foreach ($users as $value){
            if($value->getUsername()==$token->getUser()->getUsername()){
                return $value;
            }
        }


    }

    /**
     * @Rest\Post("getUserIdByToken")
     */
    public  function getUserIdByTokenAction(Request $request){

        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('TRandoMainBundle:User')->findAll();
        foreach ($users as $value){
            if($value->getUsername()==$token->getUser()->getUsername()){
                return $value->getId();
            }
        }


    }
    /**
     * @Rest\Put("/UpdateUser")
     */
public function UpdateUserProfileImageAction(Request $request){

    $userManager = $this->get('fos_user.user_manager');
    $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
    $imageKey = $this->generateRandomString();
    $this->image_upload($imageKey.'.png',$request->get('image'));
    $user=$token->getUser();
    $user->setProfilePicUrl($imageKey.'.png');
    $userManager->updateUser($user);
    return "great !!";


}
    /**
 * @Rest\Put("/UpdateUserBackgroundImage")
 */
    public function UpdateUserBackgroundImageAction(Request $request){

        $userManager = $this->get('fos_user.user_manager');
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $imageKey = $this->generateRandomString();
        $this->image_upload($imageKey.'.png',$request->get('image'));
        $user=$token->getUser();
        $user->setBackgroundPicUrl($imageKey.'.png');
        $userManager->updateUser($user);
        return "great !!";


    }
    /**
     * @Rest\Put("/UpdateUserProfileDetails")
     */
    public function UpdateUserProfileDetailseAction(Request $request){

        $userManager = $this->get('fos_user.user_manager');
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $imageKey = $this->generateRandomString();
        $this->image_upload($imageKey.'.png',$request->get('image'));
        $user=$token->getUser();
        $user->setBackgroundPicUrl($imageKey.'.png');
        $userManager->updateUser($user);
        return "great !!";


    }
    /**
     * @Rest\Put("/CompleteProfile")
     */
    public function CompleteProfileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->get('fos_user.user_manager');
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $user= $em->getRepository('TRandoMainBundle:User')->findOneBy(array('id'=> $token->getUser()));
        $user->setPhoneNumber($request->get('phoneNumber'));
        $user->setAddress($request->get('adress'));
        $user->setFisrtName($request->get('firstname'));
        $user->setLastName($request->get('lastname'));
        $userManager->updateUser($user);

        return new Response($this->serialize(array("response"=>"updated")),Response::HTTP_CREATED);


    }
    /**
     * @Rest\GET("/GetUserById/{id}")
     */
    public function getUserByIdAction($id){
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id'=>$id));
        return new Response($this->serialize($user),Response::HTTP_CREATED);
    }
    /************************************************************* Costum Functions *********************************************/
    public function image_upload($filename, $uploadedfile)
    {
        $save_file_path = __DIR__ . '/../../../../web/bundles/images/'.$filename;

        $image_file = base64_decode($uploadedfile);

        //DELETES EXISTING
        if (file_exists($save_file_path))
            unlink($save_file_path);

        //CREATE NEW FILE
        file_put_contents($save_file_path, $image_file);

        //DOUBLE CHECK FILE IF EXIST
        return ((file_exists($save_file_path)) ? true : false );
    }
    /**
     * @param  Request $request
     * @param  FormInterface $form
     */
    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException();
        }

        $form->submit($data);
    }

    /**
     * Data serializing via JMS serializer.
     *
     * @param mixed $data
     *
     * @return string JSON string
     */
    private function serialize($data)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $this->get('jms_serializer')
            ->serialize($data, 'json', $context);
    }

    /**
     * Set base HTTP headers.
     *
     * @param Response $response
     *
     * @return Response
     */
    private function setBaseHeaders(Response $response)
    {
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
    function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

   public function sendActivationTokenWithEmail($rand,User $user){
        $em = $this->getDoctrine()->getManager();
       $message = \Swift_Message::newInstance()
           ->setSubject('Welcome to T-Rando ')->setFrom('erandopi14@gmail.com')
           ->setTo($user->getEmail())
           ->setBody(
               'your activation code ... '. $rand
           );
       $this->get('mailer')->send($message);
       $user->setEnabled(0);
       $user->setActivationToken($rand);
       $em->persist($user);
   }



}
