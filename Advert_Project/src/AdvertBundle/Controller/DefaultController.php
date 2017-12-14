<?php

namespace AdvertBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('base.html.twig');
    }

    /**
     * Finds adverts by user and displays
     *
     * @Route("/user")
     * @Security("has_role('ROLE_USER')")
     */
    public function showByUserAction(){

        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository('AdvertBundle:Advert')->findBy([
            'user' => $this->getUser()
        ]);

        return $this->render('advert/advert_content.html.twig', array(
            'adverts' => $adverts,
        ));
    }

}
