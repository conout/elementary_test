<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Car;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Заголовок',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Текст объявления',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('year', TextType::class, [
                'label' => 'Год выпуска',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('price', TextType::class, [
                'label' => 'Цена',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('brand', EntityType::class, [
                'label' => 'Марка',
                'class' => Brand::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('country', EntityType::class, [
                'label' => 'Страна',
                'class' => Country::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => [
                    'class' => 'btn btn-success mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
