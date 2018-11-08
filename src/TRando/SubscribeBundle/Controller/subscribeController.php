<?php

namespace TRando\SubscribeBundle\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TRando\MainBundle\Entity\subscribe;

class subscribeController extends FOSRestController
{
    /**
     *
     * @Rest\Post("/subscribe")
     */
    public function SubscribeAction(Request $request)
    {
         $em = $this->getDoctrine()->getManager();
         $subscribe = new  subscribe();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $subscribeto = $em->getRepository('TRandoMainBundle:User')->find($request->get('iduser'));
         $subscriber =    $token->getUser();
         $check = $em->getRepository('TRandoMainBundle:subscribe')->findBy(array('user'=>$subscriber,'subscribeTo'=>$subscribeto));
  if($check){
      return new Response($this->get('jms_serializer')->serialize(array("response" => "vous etes deja abonnée a ".$subscribeto->getUsername()), 'json'));

  }else {
      $subscribe->setSubscribeTo($subscribeto);
      $subscribe->setUser($subscriber);
      $em->persist($subscribe);
      $em->flush();
      return new Response($this->get('jms_serializer')->serialize(array("response" => "subscribed"), 'json'));
  }
    }
    /**
     *
     * @Rest\Post("/checksubscribe")
     */
    public function checlsubscribeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subscribe = new  subscribe();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $subscribeto = $em->getRepository('TRandoMainBundle:User')->find($request->get('iduser'));
        $subscriber =    $token->getUser();
        $check = $em->getRepository('TRandoMainBundle:subscribe')->findBy(array('user'=>$subscriber,'subscribeTo'=>$subscribeto));
        if($check){
            return new Response($this->get('jms_serializer')->serialize(array("response" => "true"), 'json'));

        }else {

            return new Response($this->get('jms_serializer')->serialize(array("response" => "false"), 'json'));
        }
    }
    /**
     *
     * @Rest\Post("/deletesubscribe")
     */
    public function deletesubscribeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subscribe = new  subscribe();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get('token'));
        $subscribeto = $em->getRepository('TRandoMainBundle:User')->find($request->get('iduser'));
        $subscriber =    $token->getUser();
        $check = $em->getRepository('TRandoMainBundle:subscribe')->findOneBy(array('user'=>$subscriber,'subscribeTo'=>$subscribeto));
        if($check){
            $em->remove($check);
            $em->flush();
            return new Response($this->get('jms_serializer')->serialize(array("response" => "deleted"), 'json'));

        }else {

            return new Response($this->get('jms_serializer')->serialize(array("response" => "error"), 'json'));
        }
    }

}
