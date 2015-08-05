<?php
// AppBundle/Controller/Ravel/RavelController.php
namespace AppBundle\Controller\Ravel;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Traits\FormErrorHandlerTrait,
    AppBundle\Form\Type\RavelType;

class RavelController extends Controller
{
    use FormErrorHandlerTrait;

    const PROD_ENV   = 'prod';
    const PHASE_NAME = 'ravel';

    /** @DI\Inject("session") */
    private $_session;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("service.storage.flash_storage") */
    private $_flashStorage;

    /** @DI\Inject("service.phase_filter.ravel_filter") */
    private $_ravelFilter;

    /** @DI\Inject("entity_manager.temp_data_package") */
    private $_tempDataPackageManager;

    /** @DI\Inject("entity_manager.stashed_data_package") */
    private $_stashedDataPackageManager;

    /** @DI\Inject("service.honey_pot") */
    private $_honeyPot;

    /**
     * @Method({"POST"})
     * @Route(
     *      "/{_locale}/requestSalts",
     *      name="request_salts",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|ua|en", "domain" = "%domain%"}
     * )
     *
     * Phase 1: Client makes request to get Alpha, Beta and Gamma salts to hash them with passPhrase.
     *
     * Response: Object containing Alpha, Beta and Gamma bcrypt salts.
     *
     * @return string JSON
     */
    public function requestSaltsAction(Request $request)
    {
        //TODO: This can be wrapped in PreFilter Event
        //Verify Honey Pot to tell whether it's a spam
        if( !$this->_honeyPot->verifyHoneyPot($request->request->get("honeyPot")) )
            return new JsonResponse([
                'message' => $this->_translator->trans('error.spam')
            ], 500);

        //Set phase identifier to check that second request is done after first one
        $this->_flashStorage->rememberPhase(self::PHASE_NAME);

        //PRE PHASE FILTER: Generate required salts
        $salts = $this->_ravelFilter->prePhase(
            $this->_tempDataPackageManager->findAllSalts(),
            $this->_stashedDataPackageManager->findAllSalts()
        );

        //Create and save tempDataPackage via manager
        $tempDataPackage = $this->_tempDataPackageManager->create();

        $tempDataPackage
            ->setSessionId($this->_session->getId())
            ->setSaltAlpha($salts['alpha'])
            ->setSaltBeta($salts['beta'])
            ->setTimeOfDying();

        $tempDataPackage = $this->_tempDataPackageManager->persist($tempDataPackage);

        $error = ( $tempDataPackage->getError() && ( $this->container->get('kernel')->getEnvironment() === self::PROD_ENV ) )
            ? $this->_translator->trans('error.ravel')
            : $tempDataPackage->getError();

        //Make response
        $response = ( !$error )
            ? ['data' => ['salts' => $salts], 'code' => 200]
            : ['data' => ['message' => $error], 'code' => 500];

        return new JsonResponse($response['data'], $response['code']);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/{_locale}/stashData",
     *      name="stash_data",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|ua|en", "domain" = "%domain%"}
     * )
     *
     * Phase 2: Client sends primarily encrypted cipherData with hashes and TTL, to store them in DB.
     *
     * Response: Object containing success or error message.
     *
     * @return string JSON
     */
    public function stashDataAction(Request $request)
    {
        //Verify phase identifier to check that first request is already made
        if( !$this->_flashStorage->recallPhase(self::PHASE_NAME) )
            return new JsonResponse([
                'message' => $this->_translator->trans('error.broken_sequence')
            ], 500);

        //Verify stashedDataPackage form
        $stashedDataPackage = $this->_stashedDataPackageManager->create();

        $ravelForm = $this->createForm(new RavelType, $stashedDataPackage);

        $ravelForm->handleRequest($request);

        if( !($ravelForm->isValid()) ) {
            return new JsonResponse([
                'message' => $this->stringifyFormError($ravelForm)
            ], 500);
        } else {
            //Build and save stashedDataPackage via manager with values of user's tempDataPackage
            $tempDataPackage = $this->_tempDataPackageManager
                ->findCurrentUserSalts($this->_session->getId());

            $stashedDataPackage
                ->setSaltAlpha($tempDataPackage->getSaltAlpha())
                ->setSaltBeta($tempDataPackage->getSaltBeta());

            $stashedDataPackage = $this->_stashedDataPackageManager->persist($stashedDataPackage);

            $this->_tempDataPackageManager->remove($tempDataPackage);

            $error = ( $stashedDataPackage->getError() && ($this->container->get('kernel')->getEnvironment() === self::PROD_ENV) )
                ? $this->_translator->trans('error.ravel')
                : $stashedDataPackage->getError();

            //POST PHASE FILTER: Increment counter of sent packages if no errors have occurred
            if( !$error )
                $this->_ravelFilter->postPhase($stashedDataPackage);

            //Make response
            $response = ( !$error )
                ? ['data' => ['message' => 'OK'], 'code' => 200]
                : ['data' => ['message' => $error], 'code' => 500];

            return new JsonResponse($response['data'], $response['code']);
        }
    }
}