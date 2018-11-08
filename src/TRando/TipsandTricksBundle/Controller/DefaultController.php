<?php

namespace TRando\TipsandTricksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TRando\MainBundle\Entity\TipsandTricks;

class DefaultController extends Controller
{
    /**
     * @Rest\Post("/addArticleTips")
     */
    public function addArticleTipsAction(Request $request)
    {
        // get all data
        $data = json_decode($request->getContent(),true);
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $title = $data['title'];
        $description = $data['description'];
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $imageKey = $this->generateRandomString();

        if($request->get('imagepath')){
            $this->image_upload($imageKey.'.png',$request->get('imagepath'));
            $tipsandtricks = new TipsandTricks();
            $tipsandtricks->setTitle($title);
            $tipsandtricks->setContent($description);
            $tipsandtricks->setImageUrl($imageKey.'.png');
            $tipsandtricks->setUser($token->getUser());
            $em->persist($tipsandtricks);
            $em->flush();
        }else{
            $tipsandtricks = new TipsandTricks();
            $tipsandtricks->setTitle($title);
            $tipsandtricks->setContent($description);
            $tipsandtricks->setImageUrl("null");
            $tipsandtricks->setUser($token->getUser());
            $em->persist($tipsandtricks);
            $em->flush();
        }

        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize(array("response"=>"Article Added"),'json'));

    }
    /**
     * @Rest\Post("/UpdateArticleTips")
     */
    public function UpdateTipsAction(Request $request)
    {
        // get all data
        $data = json_decode($request->getContent(),true);
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $title = $data['title'];
        $description = $data['description'];
        $imageKey = $this->generateRandomString();

        if($request->get('imagepath') != "null"){
            $this->image_upload($imageKey.'.png',$request->get('imagepath'));
            $tipsandtricks = $em->getRepository('TRandoMainBundle:TipsandTricks')->find($request->get('idtips'));
            $tipsandtricks->setTitle($title);
            $tipsandtricks->setContent($description);
            $tipsandtricks->setImageUrl($imageKey.'.png');

            $em->persist($tipsandtricks);
            $em->flush();
        }else{
            $tipsandtricks = $em->getRepository('TRandoMainBundle:TipsandTricks')->find($request->get('idtips'));
            $tipsandtricks->setTitle($title);
            $tipsandtricks->setContent($description);
            $tipsandtricks->setImageUrl("null");
            $em->persist($tipsandtricks);
            $em->flush();
        }

        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize(array("response"=>"Article Added"),'json'));

    }

    /**
     * @Rest\Get("/TipsTricksGetAll")
     */
    public function TipsTricksGetAllAction()
    {
        // get all data

        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('TRandoMainBundle:TipsandTricks')->findAll();
        $serializer = $this->get('jms_serializer');
        return new Response($serializer->serialize($articles,'json'));



    }
    /**
     * @Rest\Get("/GetTipsTricksById/{id}")
     */
    public function GetTipsTricksByIdAction($id)
    {
        // get all data

        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('TRandoMainBundle:TipsandTricks')->find($id);
        $serializer = $this->get('jms_serializer');
        return new Response($serializer->serialize($articles,'json'));



    }
    /**
     * @Rest\Post("/Checktips")
     */
    public function ChecktipsAction(Request $request)
    {
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $serializer = $this->get('jms_serializer');

        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('TRandoMainBundle:TipsandTricks')->findBy(array('user'=>$token->getUser(),'id'=>$request->get('idarticle')));
   if($articles){
       return new Response($serializer->serialize(array("response"=>"true"),'json'));
   }else{
       return new Response($serializer->serialize(array("response"=>"false"),'json'));
   }




    }


    /**
     * @Rest\Get("/Removetips/{id}")
     */
    public function RemovetipsAction($id)
    {
        // get all data

        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('TRandoMainBundle:TipsandTricks')->find($id);
        $em->remove($articles);
        $em->flush();
        $serializer = $this->get('jms_serializer');
        return new Response($serializer->serialize(array("response"=>"true"),'json'));



    }
    /***********************costum methods *******************************/
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
    function generateRandomString($length = 6)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length);
    }
}
