<?php
// AppBundle/Controller/HoneyPot/HoneyPot.php
namespace AppBundle\Controller\HoneyPot;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\JsonResponse;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;

class HoneyPot extends Controller
{
    /** @DI\Inject("service.honey_pot") */
    private $_honeyPot;

    /**
     * @Route("/requestHoneyPot", name="request_honey_pot")
     */
    public function requestHoneyPotAction()
    {
        $honeyPot = $this->_honeyPot->createHoneyPot();

        return new JsonResponse([
            'honeyPot' => $honeyPot
        ], 200);
    }
}