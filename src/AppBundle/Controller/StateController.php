<?php
// AppBundle/Controller/StateController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class StateController extends Controller
{
    /** @DI\Inject("entity_manager.package_counter") */
    private $_packageCounter;

    /** @DI\Inject("service.browsers_versions") */
    private $_browsersVersions;

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
        // IE8- access denied exception
        if( $this->_browsersVersions->isLesserIE($_SERVER['HTTP_USER_AGENT']) ){
            throw new HttpException(400, $this->get('translator')->trans('error_state.title.ie8'));
        }

        $packagesNumber = str_pad($this->_packageCounter->getSentPackages(), 8, '0', STR_PAD_LEFT);

        return $this->render('AppBundle:State:index.html.twig', [
            'packagesNumber' => $packagesNumber
        ]);
    }
}