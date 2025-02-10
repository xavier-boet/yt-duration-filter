<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duration', ChoiceType::class, [
                'label' => 'Filter by duration:',
                'choices' => [
                    'All videos' => '',
                    '0 to 5 minutes' => '0-5',
                    '5 to 10 minutes' => '5-10',
                    '0 to 10 minutes' => '0-10',
                    '10 to 20 minutes' => '10-20',
                    '20 to 30 minutes' => '20-30',
                    '30 to 40 minutes' => '30-40',
                    '40 to 50 minutes' => '40-50',
                    '50 to 60 minutes' => '50-60',
                    'More than 60 minutes' => '60+',
                ],
                'required' => false,
                'attr' => ['onchange' => 'this.form.submit()'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
