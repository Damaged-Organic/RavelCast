<?php
// src/AppBundle/Controller/AjaxController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class AjaxController extends Controller
{
    /** @DI\Inject("entity_manager.package_counter") */
    private $_packageCounter;

    /**
     * @Method({"POST"})
     * @Route(
     *      "/packagesNumber",
     *      name="packages_number",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|ua|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/packagesNumber",
     *      name="packages_number_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function packagesNumberAction()
    {
        $packagesNumber = str_pad($this->_packageCounter->getSentPackages(), 9, '0', STR_PAD_LEFT);

        $response = [
            'data' => ['count' => $packagesNumber],
            'code' => 200
        ];

        return new JsonResponse($response['data'], $response['code']);
    }
}