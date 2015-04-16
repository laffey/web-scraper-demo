<?php
namespace EIdeas\OpenPayments\ScraperBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class OpenPayments extends Controller {

    /**
     * Return list of last 20 payment records, date descending
     *
     * @Route("/", name="open_payments_index")
     */
    public function indexAction()
    {
        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'A Okay!',
            'message' => 'I hope you have a lovely day'
        ));
        return $response;
    }

}