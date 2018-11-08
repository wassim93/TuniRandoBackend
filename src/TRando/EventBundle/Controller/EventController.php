<?php

namespace TRando\EventBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Validator\Constraints\DateTime;
use TRando\MainBundle\Entity\Event;
use TRando\MainBundle\Entity\EventImage;
use TRando\MainBundle\Entity\participantList;
use TRando\MainBundle\Entity\subscribe;

class EventController extends FOSRestController
{
    /**
     * @Rest\Post("/AddEvent")
     */
    public function AddEventAction(Request $request)
    {

        // get all data
        $data = json_decode($request->getContent(),true);
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $title = $data['title'];
        $description = $data['description'];
        $contact = $data['contact'];
        $date = $data['date'];
        $prix = $data['prix'];
        $image = $data['image'];
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $pointDepart = $data['pointDepart'];
        $pointArrive = $data['pointArrive'];
        $type = $data['type'];
        $niveau = $data['niveau'];
        $nbrplace = $data['nbrPlace'];
         // create instance of event
        $Event = new Event();
        $Event->setTitle($title);
        $Event->setDescription($description);
        $Event->setContact($contact);
        $Event->setDate($date);
        $Event->setPrix($prix);
        $Event->setPointDepart($pointDepart);
        $Event->setPointArrive($pointArrive);
        $Event->setType($type);
        $Event->setNiveau($niveau);
        $Event->setNbrPlaces($nbrplace);
        $Event->setUser($token->getUser());

        $em->persist($Event);
        $em->flush();

        foreach ($image as $item){
            $EventImage = new EventImage();
            $imageKey = $this->generateRandomString();
            $this->image_upload($imageKey.'.png',$item["image"]);
            $EventImage->setImage($imageKey.'.png');
            $EventImage->setEvent($Event);

            $em->persist($EventImage);
            $em->flush();
        }
        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "Evenement ajouté");
        return  new Response($serializer->serialize($arr,'json'));


    }


    /**
     * @Rest\Get("/GetAllEvent")
     */
    public function GetAllEventAction()
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('TRandoMainBundle:Event')->createQueryBuilder('u')
            ->select('u')->orderBy('u.id','DESC');


        $serializer = $this->get('jms_serializer');

        return  new Response($serializer->serialize($events->getQuery()->getResult(),'json'));

    }


    /**
     * @Rest\Get("/GetEventById/{id}")
     */
    public function GetEventByIdAction($id)
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository(Event::class)->findOneById($id);

        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize($event,'json'));


    }



    /**
     * @Rest\Post("/checkeventuser")
     */
    public function checkeventuserAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $event = $em->getRepository('TRandoMainBundle:Event')->findBy(array('user'=>$token->getUser(),'id'=>$data['idevent']));
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
     * @Rest\Get("/GetEventParticipant/{idevent}")
     */
    public function GetEventParticipantAction($idevent)
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository(Event::class)->findOneById($idevent);

        $participantlist = $em->getRepository('TRandoMainBundle:participantList')->findBy(array('event'=>$event));
        $arr = array();
        foreach ($participantlist as $item){
           array_push($arr,$item->getParticipant());
        }


        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize($arr,'json'));


    }

    /**
     * @Rest\Get("/GetTodayEvent")
     */
    public function GetTodayEventAction()
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $today = date("Y-m-d",strtotime('now'));

        $events = $em->getRepository(Event::class)->findBy(array("date"=>$today));

        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize($events,'json'));


    }


    /**
     * @Rest\Get("/GetSportifEvent")
     */
    public function GetSportifEventAction()
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository(Event::class)->findBy(array("type"=>"Sportifs"));

        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize($events,'json'));


    }


    /**
     * @Rest\Get("/GetFamilleEvent")
     */
    public function GetFamilleEventAction()
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository(Event::class)->findBy(array("type"=>"Famille"));

        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize($events,'json'));


    }



    /**
     * @Rest\Get("/GetEventSubscribed/{token}")
     */
    public function GetEventSubscribedAction($token)
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $arrayevent = array();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($token);
        $subs = $em->getRepository("TRandoMainBundle:subscribe")->findBy(array('user'=>$token->getUser()));

       foreach ($subs as $value){
           $event = $em->getRepository("TRandoMainBundle:Event")->findOneBy(array('user'=>$value->getSubscribeTo()));
           array_push($arrayevent,$event);
       }

        $serializer = $this->get('jms_serializer');
        return  new Response($serializer->serialize($arrayevent,'json'));


    }

    /**
     * @Rest\Get("/deleteEvent/{id}")
     */
    public function deleteEventAction($id)
    {
        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository(Event::class)->find($id);

        $em->remove($event);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "true");
        return  new Response($serializer->serialize($arr,'json'));

    }


    /**
     * @Rest\Post("/updateEvent")
     */
    public function updateEventAction(Request $request)
    {

        // get doctrine manager
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($request->get('id'));

        $title = $request->get('title');
        $description = $request->get('description');
        $contact = $request->get('contact');
        $date = $request->get('date');
        $prix = $request->get('prix');
        $image = $request->get('image');
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($request->get("token"));
        $pointDepart = $request->get('pointDepart');
        $pointArrive = $request->get('pointArrive');
        $type = $request->get('type');
        $niveau = $request->get('niveau');
        $nbrplace = $request->get('nbrPlace');
        // create instance of event
        $event->setTitle($title);
        $event->setDescription($description);
        $event->setContact($contact);
        $event->setDate($date);
        $event->setPrix($prix);
        $event->setPointDepart($pointDepart);
        $event->setPointArrive($pointArrive);
        $event->setType($type);
        $event->setNiveau($niveau);
        $event->setNbrPlaces($nbrplace);
        $event->setUser($token->getUser());
        
        $EventImage = new EventImage();

        if(!empty($image)){
            foreach ($image as $item){
                $imageKey = $this->generateRandomString();
                $this->image_upload($imageKey.'.png',$item["image"]);
                $EventImage->setImage($imageKey.'.png');
                $EventImage->setEvent($event);

                $em->persist($EventImage);
                $em->flush();
            }

        }

            $em->persist($event);
            $em->flush();




        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "Evenement modifié");

        return  new Response($serializer->serialize($arr,'json'));


    }
    /**
     * @Rest\Post("/checkeventuser")
     */
    public function heckeventuserAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $event = $em->getRepository('TRandoMainBundle:Event')->findBy(array('user'=>$token->getUser(),'id'=>$data['idevent']));
        $serializer = $this->get('jms_serializer');
        ;
        if ($event){
            $arr = array('response' => "true");
            return  new Response($serializer->serialize($arr,'json'));
        }else{
            $arr = array('response' => "false");
            return  new Response($serializer->serialize($arr,'json'));
        }
    }
    /**
     * @Rest\Post("/participate")
     */
    public function particpateAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $participantlist = new participantList();
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $event = $em->getRepository('TRandoMainBundle:Event')->find($data['idevent']);
        $participantlist->setEvent($event);
        $participantlist->setParticipant($token->getUser());
        $em->persist($participantlist);
        $event->setNbrPlaces($event->getNbrPlaces() - 1);
        $em->persist($event);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $arr = array('response' => "participer");
        return  new Response($serializer->serialize($arr,'json'));
    }
    /**
     * @Rest\Post("/checkparticipate")
     */
    public function checkparticipateAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
         $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $event = $em->getRepository('TRandoMainBundle:Event')->find($data['idevent']);
        $participantlist = $em->getRepository('TRandoMainBundle:participantList')->findBy(array('event'=>$event,'participant'=>$token->getUser()));
        $serializer = $this->get('jms_serializer');
     ;
        if ($participantlist){

            $arr = array('response' => "true");
            return  new Response($serializer->serialize($arr,'json'));
        }else{
            $arr = array('response' => "false");
            return  new Response($serializer->serialize($arr,'json'));
        }
    }
    /**
     * @Rest\Post("/deletepart")
     */
    public function deletepartAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $em = $this->getDoctrine()->getManager();
        $token= $this->get('fos_oauth_server.access_token_manager.default')->findTokenByToken($data["token"]);
        $event = $em->getRepository('TRandoMainBundle:Event')->find($data['idevent']);
        $participantlist = $em->getRepository('TRandoMainBundle:participantList')->findOneBy(array('event'=>$event,'participant'=>$token->getUser()));

        $serializer = $this->get('jms_serializer');
 if ($participantlist){
     $event->setNbrPlaces($event->getNbrPlaces() + 1);
     $em->persist($event);
     $em->remove($participantlist);
     $em->flush();
            $arr = array('response' => "true");
            return  new Response($serializer->serialize($arr,'json'));
        }else{
            $arr = array('response' => "false");
            return  new Response($serializer->serialize($arr,'json'));
        }
    }
    /**
     * @Rest\Get("/geteventofthisweek")
     */
    public function getEventThisWeekAction(){
        $start_week = date("Y-m-d",strtotime('now'));
        $end_week = date("Y-m-d",strtotime('sunday this week'));
        $em = $this->getDoctrine()->getEntityManager();
        $events = $em->getRepository('TRandoMainBundle:Event')->createQueryBuilder('u')
            ->select('u')->where('u.date >= :start')
            ->andWhere('u.date <= :end')
            ->setParameter(':start',$start_week)
            ->setParameter(':end',$end_week)
            ->getQuery()
            ->getResult();
      return $events;

        }
    /**************** costum function **************************/
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
