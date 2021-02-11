<?php


namespace App\Form;

use App\Entity\Brand;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year_from', TextType::class, [
                'label' => 'Год выпуска от',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('year_to', TextType::class, [
                'label' => 'Год выпуска до',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('price_from', TextType::class, [
                'label' => 'Цена от',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('price_to', TextType::class, [
                'label' => 'Цена до',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('brand', EntityType::class, [
                'label' => 'Марка',
                'required' => false,
                'class' => Brand::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('country', EntityType::class, [
                'label' => 'Страна',
                'required' => false,
                'class' => Country::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Применить',
                'attr' => [
                    'class' => 'btn btn-success mt-3'
                ]
            ])
        ;
    }
}