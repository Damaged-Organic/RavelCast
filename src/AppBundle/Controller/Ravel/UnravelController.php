<?php
// AppBundle/Controller/Ravel/UnravelController.php
namespace AppBundle\Controller\Ravel;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Validator\Constraints as Assert;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Traits\FormErrorHandlerTrait,
    AppBundle\Form\Type\UnravelType,
    AppBundle\Validator\Constraints as CustomAssert;

class UnravelController extends Controller
{
    use FormErrorHandlerTrait;

    const PROD_ENV   = 'prod';
    const PHASE_NAME = 'unravel';

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("service.utility.salter") */
    private $_salter;

    /** @DI\Inject("service.storage.flash_storage") */
    private $_flashStorage;

    /** @DI\Inject("service.phase_filter.unravel_filter") */
    private $_unravelFilter;

    /** @DI\Inject("entity_manager.stashed_data_package") */
    private $_stashedDataPackageManager;

    /** @DI\Inject("service.honey_pot") */
    private $_honeyPot;

    /**
     * @Method({"POST"})
     * @Route(
     *      "/validateGamma",
     *      name="validate_gamma",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en|ru", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/validateGamma",
     *      name="validate_gamma_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     *
     * Phase 1: Client sends Gamma hash to lookup database for his cipherData
     *
     * Response: In any case returns bcrypt salt, for obfuscation purposes. It could be 'real', from exact DB row,
     * or randomly generated, in case if Gamma hash is not found in DB.
     *
     * @return string JSON
     */
    public function validateGammaAction(Request $request)
    {
        //TODO: This can be wrapped in PreFilter Event
        //Verify Honey Pot to tell whether it's a spam
        if( !$this->_honeyPot->verifyHoneyPot($request->request->get("honeyPot")) )
            return new JsonResponse([
                'message' => $this->_translator->trans('error.spam')
            ], 500);

        //Set phase identifier to check that second request is done after first one
        $this->_flashStorage->rememberPhase(self::PHASE_NAME);

        //Verify stashedDataPackage form
        $stashedDataPackage = $this->_stashedDataPackageManager->create();

        $unravelForm = $this->createForm(new UnravelType, $stashedDataPackage);

        $unravelForm->handleRequest($request);

        if( !($unravelForm->isValid()) ) {
            return new JsonResponse([
                'message' => $this->stringifyFormError($unravelForm)
            ], 500);
        } else {
            //PRE PHASE FILTER: decrypt and verify Hash Gamma
            $stashedDataPackage = $this->_unravelFilter->prePhase($stashedDataPackage);

            //Remember entity ID for next validation
            if( !$stashedDataPackage->getError() )
            {
                $stashedDataPackage = $this->_stashedDataPackageManager
                    ->findOneByHashGamma($stashedDataPackage->getHashGamma());

                $this->_flashStorage->rememberStashedDataPackageId($stashedDataPackage->getId());
            }

            //Make response
            $response = ( !$stashedDataPackage->getError() )
                ? [
                    'data' => ['saltBeta' => $stashedDataPackage->getSaltBeta()],
                    'code' => 200
                ]
                : [
                    'data' => ['saltBeta' => $this->_salter->generateBcryptSalt()],
                    'code' => 200
                ];

            return new JsonResponse($response['data'], $response['code']);
        }
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/validateBeta",
     *      name="validate_beta",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|en|ru", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/validateBeta",
     *      name="validate_beta_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     *
     * Phase 2: Client sends Beta hash to validate whether DB row that matches Gamma hash also matches his Beta hash.
     * Client's Beta hash is then used to decrypt cipherData from matching DB row.
     *
     * Response: Object that contains primarily decrypted cipherData and final salt Alpha, or error message, if
     * validation or decryption fails.
     *
     * @return string JSON
     */
    public function validateBetaAction(Request $request)
    {
        //Verify phase identifier to check that first request is already made
        if( !$this->_flashStorage->recallPhase(self::PHASE_NAME) ) {
            return new JsonResponse([
                'message' => $this->_translator->trans('error.broken_sequence')
            ], 500);
        }

        $clientHashBeta = $request->request->get('hashBeta');

        //TODO: Constraint validation probably should be transferred to some kind of service
        $constraints = [
            'notBlank' => new Assert\NotBlank,
            'isHash'   => new CustomAssert\IsHashConstraint
        ];

        $errorList = $this->get('validator')
            ->validate($clientHashBeta, $constraints);

        if( count($errorList) !== 0 ) {
            return new JsonResponse([
                'message' => $errorList[0]->getMessage()
            ], 500);
        } else {
            //Find and decrypt corresponding stashedDataPackage entity
            $this->_stashedDataPackageManager
                ->setClientHashBeta($clientHashBeta);

            $stashedDataPackageId = $this->_flashStorage->recallStashedDataPackageId();

            $stashedDataPackage = $this->_stashedDataPackageManager
                ->findOneById($stashedDataPackageId);

            $error = ( $stashedDataPackage->getError() && ( $this->container->get('kernel')->getEnvironment() === self::PROD_ENV ) )
                ? $this->_translator->trans('error.unravel')
                : $stashedDataPackage->getError();

            //POST PHASE FILTER: Remove Cipher File and found stashedDataPackage entity, if no errors have occurred
            if( !$error ) {
                $this->_unravelFilter->postPhase();

                $this->_stashedDataPackageManager->remove($stashedDataPackage);
            }

            //Make response
            $response = ( !$error )
                ? [
                    'data' => [
                        'saltAlpha'  => $stashedDataPackage->getSaltAlpha(),
                        'cipherData' => $stashedDataPackage->getData()
                    ],
                    'code' => 200
                ]
                : [
                    'data' => ['message' => $error],
                    'code' => 500
                ];

            return new JsonResponse($response['data'], $response['code']);
        }
    }
}