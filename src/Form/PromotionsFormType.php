<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Promotions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;

class PromotionsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discount', options:[
                'label' => 'Montant de la promotion',
                'constraints' => [
                    new Positive([
                        'message' => 'La promotion ne peux pas être négative'
                    ]),
                    new LessThan([
                        'value' => 100,
                        'message' => 'La promotion doit être inférieure à 100%',
                    ]),
                ]
            ])
            ->add('start_date', null, [
                'label' => 'Date de début de la promotion',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new Date([
                        'message' => 'La date de début n\'est pas valide',
                    ]),
                    new NotBlank([
                        'message' => 'La date de début est requise',
                    ])
                ],
            ])
            ->add('end_date', null, [
                'label' => 'Date de fin de la promotion',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new Date([
                        'message' => 'La date de fin n\'est pas valide',
                    ]),
                    new NotBlank([
                        'message' => 'La date de fin est requise',
                    ]),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[start_date].data',
                        'message' => 'La date de fin doit être supérieure à la date de début',
                    ])
                ],
            ]);

        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotions::class,
        ]);
    }
}
