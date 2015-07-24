<?php
// src/AppBundle/Form/Type/FeedbackType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", 'text', [
                'label' => "feedback.name.label",
                'attr' => [
                    'placeholder' => "feedback.name.placeholder"
                ]
            ])
            ->add("email", 'email', [
                'required' => FALSE,
                'label'    => "feedback.email.label",
                'attr' => [
                    'placeholder' => "feedback.email.placeholder"
                ]
            ])
            ->add("message", 'textarea', [
                'label' => "feedback.message.label",
                'attr' => [
                    'placeholder' => "feedback.message.placeholder"
                ]
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Model\Feedback',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return "feedbackType";
    }
}