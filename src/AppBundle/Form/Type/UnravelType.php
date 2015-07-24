<?php
// AppBundle/Form/Type/UnravelType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UnravelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('passPhrase', 'password', [
                'mapped' => FALSE,
                'label'  => "unravel.pass_phrase.label",
                'attr' => [
                    'placeholder' => "unravel.pass_phrase.placeholder"
                ]
            ])
            ->add('saltGamma', 'text', [
                'mapped'   => FALSE,
                'label'    => "unravel.salt_gamma.label",
                'attr' => [
                    'placeholder' => "unravel.salt_gamma.placeholder"
                ]
            ])
            ->add('hashGamma')
            ->add('blankField', 'text')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\StashedDataPackage',
            'translation_domain' => 'forms',
            'validation_groups'  => ['unravel']
        ]);
    }

    public function getName()
    {
        return 'stashedDataPackage';
    }
}