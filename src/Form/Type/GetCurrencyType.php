<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class GetCurrencyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget'      => 'single_text',
                'format'      => 'yyyy-MM-dd',
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('code', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('baseCode', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
            ]);
    }
}