<?php
// AppBundle/Controller/Traits/FormErrorHandlerTrait.php
namespace AppBundle\Controller\Traits;

use Symfony\Component\Form\Form;

trait FormErrorHandlerTrait
{
    /*
     * Method returns only one error at a time for now;
     * if user got more then one error after front-end validation, he's definitely messing with html
     */
    public function stringifyFormError(Form $form)
    {
        $errors = $form->getErrors(TRUE, TRUE);

        return ( !empty($errors[0]) ) ? $errors[0]->getMessage() : NULL;
    }
}