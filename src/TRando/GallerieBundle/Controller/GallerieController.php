<?php

namespace TRando\GallerieBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use TRando\MainBundle\Entity\GallerieImg;
use FOS\RestBundle\Controller\Annotations as Rest;


class GallerieController extends FOSRestController
{
    /**
     * @Rest\Get("/GetPhotos")
     */
    public function GetPhotosAction()
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $photos = $em->getRepository(GallerieImg::class)->findAll();


        $serializer = $this->get('jms_serializer');

        return  new Response($serializer->serialize($photos,'json'));    }
}
