<?php
// AppBundle/Controller/StateController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class StateController extends Controller
{
    /** @DI\Inject("entity_manager.package_counter") */
    private $_packageCounter;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="index",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|ua|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/",
     *      name="index_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     *
     * Initial and only system state so far
     */
    public function indexAction()
    {
        $packagesNumber = str_pad($this->_packageCounter->getSentPackages(), 9, '0', STR_PAD_LEFT);

        return $this->render('AppBundle:State:index.html.twig', [
            'packagesNumber' => $packagesNumber
        ]);
    }
}