<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Domain;
use App\Entity\Product;
use App\Entity\Appellation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotNull;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez compléter le nom du produit',
                    ]),
            ]])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez compléter la description du produit',
                    ]),
            ]])
            ->add('unitPrice', MoneyType::class, [
                'label' => 'Prix à l\'unité',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez compléter le prix du produit',
                    ]),
            ]])
            
            ->add('unitStock', NumberType::class, [
                'label' => 'Quantité en stock',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez compléter la quantité du produit en stock',
                    ]),
            ]])
            
            ->add('photo', TextType::class, [
                'label' => 'Nom du fichier de la photo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez compléter le nom du fichier de la photo du produit',
                    ]),
            ]])
            
            ->add('year', NumberType::class, [
                'label' => 'Année',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez compléter l\'année du produit',
                    ]),
            ]])
            
            ->add('appellation', EntityType::class, [
                'class' => Appellation::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez choisir une appellation',
                    ]),
            ]])
            
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez choisir un type',
                    ]),
            ]])

            ->add('domain', EntityType::class, [
                'class' => Domain::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez choisir un domaine',
                    ]),
            ]])
            
            ->add('ajouter', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
