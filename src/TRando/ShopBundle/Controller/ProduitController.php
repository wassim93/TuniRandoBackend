<?php

namespace TRando\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TRando\MainBundle\Entity\Image;
use TRando\MainBundle\Entity\Produit;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProduitController extends FOSRestController
{
    /**
     * @Rest\Post("/AddProduct")
     */
    public function AddProductAction(Request $request)
    {

        // get all data
        $data = json_decode($request->getContent(),true);
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $title = $data['title'];
        $description = $data['description'];
        $date = $data['date'];
        $prix = $data['prix'];
        $images = $data['images'];
        $contact = $data['contact'];
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $type = $data['type'];
        // create instance of event
        $Produit = new Produit();
        $Produit->setTitre($title);
        $Produit->setDescription($description);
        $Produit->setDate($date);
        $Produit->setPrix($prix);
        $Produit->setType($type);
        $Produit->setContact($contact);
        $Produit->setUser($token->getUser());
        $em->persist($Produit);
        $em->flush();

        foreach ($images as $item){
            $Images = new Image();
            $imageKey = $this->generateRandomString();
            $this->image_upload($imageKey.'.png',$item["image"]);
            $Images->setImage($imageKey.'.png');
            $Images->setProduit($Produit);

            $em->persist($Images);
            $em->flush();
        }
        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "Produit ajouté");
        return  new Response($serializer->serialize($arr,'json'));


    }
    /**
     * @Rest\Post("/updateProduct")
     */
    public function updateProductAction(Request $request)
    {

        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Produit::class)->find($request->get('id'));

        $title = $request->get('title');
        $description = $request->get('description');
        $contact = $request->get('contact');
        $date = $request->get('date');
        $prix = $request->get('prix');
        $image = $request->get('images');
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get("token"));

        $type = $request->get('type');
        // create instance of event
        $event->setTitre($title);
        $event->setDescription($description);
        $event->setContact($contact);
        $event->setDate($date);
        $event->setPrix($prix);
        $event->setType($type);
        $event->setUser($token->getUser());

        $ProdImage = new Image();

        if(!empty($image)){
            foreach ($image as $item){
                $imageKey = $this->generateRandomString();
                $this->image_upload($imageKey.'.png',$item["image"]);
                $ProdImage->setImage($imageKey.'.png');
                $ProdImage->setProduit($event);

                $em->persist($ProdImage);
                $em->flush();
            }

        }

        $em->persist($event);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "Produit modifié");

        return  new Response($serializer->serialize($arr,'json'));


    }


    /**
     * @Rest\Post("/checkproductuser")
     */
    public function checkproductuserAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $prod = $em->getRepository('TRandoMainBundle:Produit')->findBy(array('user'=>$token->getUser(),'id'=>$data['idprod']));
        $serializer = $this->get('jms_serializer');
        if ($prod){
            $arr = array('response' => "true");
            return  new Response($serializer->serialize($arr,'json'));
        }else{
            $arr = array('response' => "false");
            return  new Response($serializer->serialize($arr,'json'));
        }
    }




    /**
     * @Rest\Get("/GetAllProduct")
     */
    public function GetAllProductAction()
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();



        $products = $em->getRepository('TRandoMainBundle:Produit')->createQueryBuilder('u')
            ->select('u')->orderBy('u.id','DESC');


        $serializer = $this->get('jms_serializer');

        return  new Response($serializer->serialize($products->getQuery()->getResult(),'json'));

    }


    /**
     * @Rest\Get("/GetSuggestionProduct/{type}")
     */
    public function GetSuggestionProductAction($type)
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $prod = $em->getRepository(Produit::class)->findBy(array("type"=>$type));

        $serializer = $this->get('jms_serializer');

        return  new Response($serializer->serialize($prod,'json'));

    }


    /**
     * @Rest\Get("/deleteProduct/{id}")
     */
    public function deleteProductAction($id)
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository(Produit::class)->find($id);

        $em->remove($event);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "true");
        return  new Response($serializer->serialize($arr,'json'));

    }




    function generateRandomString($length = 5)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
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

}
