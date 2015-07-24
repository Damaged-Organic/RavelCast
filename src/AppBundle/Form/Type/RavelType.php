<?php
// AppBundle/Form/Type/RavelType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Entity\StashedDataPackage;

class RavelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $timeToLiveMeasureChoices = StashedDataPackage::getTimeToLiveMeasures();

        $builder
            ->add('passPhrase', 'password', [
                'mapped' => FALSE,
                'label'  => "ravel.pass_phrase.label",
                'attr' => [
                    'placeholder' => "ravel.pass_phrase.placeholder"
                ]
            ])
            ->add('timeToLiveNumber', 'text', [
                'data'  => '42',
                'label' => "ravel.time_to_live_number.label",
                'attr' => [
                    'placeholder' => "ravel.time_to_live_number.placeholder"
                ]
            ])
            ->add('timeToLiveMeasure', 'choice', [
                'choices'         => array_combine(
                    $timeToLiveMeasureChoices, ["ravel.choice.minutes", "ravel.choice.hours", "ravel.choice.days"]
                ),
                'expanded'        => TRUE,
                'multiple'        => FALSE,
                'data'            => $timeToLiveMeasureChoices[0],
                'invalid_message' => "ravel.choice.invalid_message"
            ])
            ->add('data', 'textarea', [
                'label' => "ravel.data.label",
                'attr' => [
                    'placeholder' => "ravel.data.placeholder"
                ]
            ])
            ->add('hashBeta')
            ->add('hashGamma')
            ->add('blankField', 'text')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\StashedDataPackage',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'stashedDataPackage';
    }
}