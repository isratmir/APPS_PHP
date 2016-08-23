<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FrontEndController extends Controller
{
		/**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:FrontEnd:index.html.twig', array(

        ));
    }

		/**
		 * @Route("/ajax/website", name="get_website")
		 */
    public function getWebsiteAction()
    {
	      $website = $this->getDoctrine()->getRepository('AppBundle:Website')
			      ->findRandomWebsite();

	      return new JsonResponse($website);
    }
}
