<?php
// src/AppBundle/Controller/FormController.php
namespace AppBundle\Controller;

use DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Traits\FormErrorHandlerTrait,
    AppBundle\Form\Type\RavelType,
    AppBundle\Form\Type\UnravelType,
    AppBundle\Form\Type\FeedbackType,
    AppBundle\Model\Feedback;

class FormController extends Controller
{
    use FormErrorHandlerTrait;

    /** @DI\Inject("entity_manager.stashed_data_package") */
    private $_stashedDataPackageManager;

    /** @DI\Inject("helper.mailer_shortcut") */
    private $_mailerShortcut;

    /**
     * Rendered controller
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ravelAction()
    {
        $stashedDataPackage = $this->_stashedDataPackageManager->create();

        $ravelForm = $this->createForm(new RavelType, $stashedDataPackage);

        return $this->render('AppBundle:Form:ravel.html.twig',[
            'ravelForm' => $ravelForm->createView()
        ]);
    }

    /**
     * Rendered controller
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unravelAction()
    {
        $stashedDataPackage = $this->_stashedDataPackageManager->create();

        $unravelForm = $this->createForm(new UnravelType, $stashedDataPackage);

        return $this->render('AppBundle:Form:unravel.html.twig', [
            'unravelForm' => $unravelForm->createView()
        ]);
    }

    /**
     * Rendered controller
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function feedbackAction()
    {
        $feedbackForm = $this->createForm(new FeedbackType, new Feedback, [
            'action' => $this->generateUrl('feedback_send')
        ]);

        return $this->render('AppBundle:Form:feedback.html.twig', [
            'feedbackForm' => $feedbackForm->createView()
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/feedbackSend",
     *      name="feedback_send",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%|ua|en", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/feedbackSend",
     *      name="feedback_send_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function feedbackSendAction(Request $request)
    {
        $feedbackForm = $this->createForm(new FeedbackType, ($feedback = new Feedback));

        $feedbackForm->handleRequest($request);

        if( !($feedbackForm->isValid()) ) {
            $response = [
                'data' => ['message' => $this->stringifyFormError($feedbackForm)],
                'code' => 500
            ];
        } else {
            $subject = $this->get('translator')->trans("feedback.subject", [
                "%datetime%" => (new DateTime)->format('H:i:s d-m-Y')
            ], 'emails');

            $body = $this->renderView('AppBundle:Email:feedback.html.twig', [
                'feedback'  => $feedback
            ]);

            if( $this->_mailerShortcut->sendMail("webmaster@cheers-development.in.ua", "webmaster@cheers-development.in.ua", $subject, $body) ) {
                $response = [
                    'data' => ['message' => $this->get('translator')->trans("feedback.success", [], 'responses')],
                    'code' => 200
                ];
            } else {
                $response = [
                    'data' => ['message' => $this->get('translator')->trans("feedback.fail", [], 'responses')],
                    'code' => 500
                ];
            }
        }

        return new JsonResponse($response['data'], $response['code']);
    }
}