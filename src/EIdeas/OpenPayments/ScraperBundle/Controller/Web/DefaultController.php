<?php

namespace EIdeas\OpenPayments\ScraperBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('EIdeasOpScraperBundle:Default:index.html.twig');
    }
}
