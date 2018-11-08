<?php

namespace TRando\PostsBundle\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TRando\MainBundle\Entity\comment;

class CommentController extends FOSRestController
{
    /**
     *
     * @Rest\Post("/AddComment")
     */
    public function AddCommentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = new comment();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'))
        ;
        $user = $token->getUser();
        $post = $em->getRepository('TRandoMainBundle:Posts')->find($request->get('postid'));
        $comment->setUser($user);
        $comment->setContent($request->get('message'));
        $comment->setPost($post);
        $comment->setDate( new \DateTime());
        $post->setComments($post->getComments() + 1);
        $em->persist($comment);
        $em->persist($post);
        $em->flush();
        return new Response($this->get('jms_serializer')->serialize(array("response" => "commented"), 'json'));
}
    /**
     *
     * @Rest\Post("/UpdateComment")
     */
    public function UpdateCommentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('TRandoMainBundle:comment')->find($request->get('idcomment'));

        $comment->setContent($request->get('message'));
        $comment->setDate( new \DateTime());

        $em->persist($comment);

        $em->flush();
        return new Response($this->get('jms_serializer')->serialize(array("response" => "commented"), 'json'));
    }
/**
 * @Rest\Get("/getcomments/{postid}")
 */
    public function GetCommentsAction(Request $request,$postid)
    {
        $em = $this->getDoctrine()->getManager();
                  $post = $em->getRepository('TRandoMainBundle:Posts')->find($postid);
      $comments = $em->getRepository('TRandoMainBundle:comment')->findBy(array('post'=>$post));
        return $comments;
    }
    /**
     * @Rest\Post("/checkcommentuser")
     */
    public function checkcommentuserAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $event = $em->getRepository('TRandoMainBundle:comment')->findBy(array('user'=>$token->getUser(),'id'=>$data['idcomment']));
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
     * @Rest\Get("/RemoveComment/{idcomment}")
     */
    public function RemoveCommentAction($idcomment){
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('TRandoMainBundle:comment')->find($idcomment);
        $post =  $em->getRepository('TRandoMainBundle:Posts')->findOneBy(array('id'=>$comment->getPost()));
        $post->setComments($post->getComments() - 1 );
        $em->remove($comment);
        $em->persist($post);
        $em->flush();
        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "true");
        return  new Response($serializer->serialize($arr,'json'));
    }
    /**
     * @Rest\Get("/GetCommentById/{idcomment}")
     */
    public function GetCommentByIdAction($idcomment){
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('TRandoMainBundle:comment')->find($idcomment);

        return  $comment ;
    }
}
