<?php

namespace TRando\PostsBundle\Controller;


use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use TRando\MainBundle\Entity\likes;
use TRando\MainBundle\Entity\Posts;

class PostsController extends FOSRestController
{
    /**
     *
     * @Rest\Post("/addPost")
     */
    public function AddPostAction(Request $request)
    {
 $posts = new Posts();
 $em = $this->getDoctrine()->getManager();
 $content = $request->get('content');
 $date = new \DateTime();
 $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
 $user = $token->getUser() ;
 $imagekey = $this->generateRandomString() ;
 if($request->get('image')=="null"){
     $posts->setContent($content);
     $posts->setDate($date);
     $posts->setImagePath("null");
     $posts->setUser($user);
     $em->persist($posts);
     $em->flush();
     return new Response( $this->get('jms_serializer')->serialize(array("response"=>"posted"),'json'));
 }else{
     $this->image_upload($imagekey.'.png',$request->get('image'));

     $posts->setContent($content);
     $posts->setDate($date);
     $posts->setImagePath($imagekey.'.png');
     $posts->setUser($user);
     $em->persist($posts);
     $em->flush();
     return new Response( $this->get('jms_serializer')->serialize(array("response"=>"posted"),'json'));

 }




    }
    /**
     *
     * @Rest\Post("/UpdatePost")
     */
    public function UpdatePostAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('TRandoMainBundle:Posts')->find($request->get('postid'));
        $content = $request->get('content');
        $date = new \DateTime();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $user = $token->getUser() ;
        $imagekey = $this->generateRandomString() ;
        if($request->get('image')=="null"){
            $posts->setContent($content);
            $posts->setDate($date);
            $posts->setImagePath($posts->getImagePath());
            $posts->setUser($user);
            $em->persist($posts);
            $em->flush();
            return new Response( $this->get('jms_serializer')->serialize(array("response"=>"posted"),'json'));
        }else{
            $this->image_upload($imagekey.'.png',$request->get('image'));

            $posts->setContent($content);
            $posts->setDate($date);
            $posts->setImagePath($imagekey.'.png');
            $posts->setUser($user);
            $em->persist($posts);
            $em->flush();
            return new Response( $this->get('jms_serializer')->serialize(array("response"=>"posted"),'json'));

        }




    }

    /**
     *
     *
     * @Rest\Get("/getAllPosts")
     */


    public function getAllPostAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $posts = $em->getRepository('TRandoMainBundle:Posts')->createQueryBuilder('u')
        ->select('u')->orderBy('u.date','DESC');

        return $posts->getQuery()->getResult();
    }

    /**
     *
     *
     * @Rest\Put("/hitlike")
     */

    public function hitlikeAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
     $user = $token->getUser();
     $post = $em->getRepository('TRandoMainBundle:Posts')->find($request->get('postid'));
     $likes = $em->getRepository('TRandoMainBundle:likes')->findOneBy(array('user'=>$user ,'posts'=>$post));
     if($likes){
         $post->setLikes($post->getLikes() - 1);
         $em->remove($likes);
         $em->persist($post);
         $em->flush();
         return new Response($this->get('jms_serializer')->serialize(array("response" => "disliked"), 'json'));
     }else {

         $like = new likes();
         $like->setUser($user);
         $like->setPosts($post);
         $post->setLikes($post->getLikes() + 1);
         $em->persist($post);
         $em->persist($like);
         $em->flush();
         return new Response($this->get('jms_serializer')->serialize(array("response" => "liked","user"=>$user->getUsername()), 'json'));
     }
   }
    /**
     * @Rest\Get("/GetPostById/{idpost}")
     */
    public function GetPostByIdAction($idpost){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('TRandoMainBundle:Posts')->find($idpost);

        return  $post ;
    }
    /**
     * @Rest\Get("/Removepost/{idpost}")
     */
    public function DeletePostAction($idpost){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('TRandoMainBundle:Posts')->find($idpost);
        $em->remove($post);
        $em->flush();
        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "true");
        return  new Response($serializer->serialize($arr,'json'));
    }
    /**
     * @Rest\Post("/checkPostuser")
     */
    public function checkpostuserAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $event = $em->getRepository('TRandoMainBundle:Posts')->findBy(array('user'=>$token->getUser(),'id'=>$data['idpost']));
        $serializer = $this->get('jms_serializer');
        if ($event){
            $arr = array('response' => "true");
            return  new Response($serializer->serialize($arr,'json'));
        }else{
            $arr = array('response' => "false");
            return  new Response($serializer->serialize($arr,'json'));
    }
    }
    /**
     * @Rest\Post("/checkliked")
     */
    public function checklikedAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $post = $em->getRepository('TRandoMainBundle:Posts')->find($data['idpost']);
        $like = $em->getRepository('TRandoMainBundle:likes')->findBy(array('user'=>$token->getUser(),'posts'=>$post));
        $serializer = $this->get('jms_serializer');
        ;
        if ($like){
            $arr = array('response' => "true");
            return  new Response($serializer->serialize($arr,'json'));
        }else{
            $arr = array('response' => "false");
            return  new Response($serializer->serialize($arr,'json'));
        }
    }
    /***costum function **/
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
    function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}
