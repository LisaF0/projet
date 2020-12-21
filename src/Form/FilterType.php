<?php 
namespace App\Form;

use App\Entity\Appellation;
use App\Entity\Domain;
use App\Entity\Type;
use App\Entity\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('appellations', EntityType::class, [
            'label' => 'Appellations',
            'class' => Appellation::class,
            'expanded' => true,
            'multiple' => true,
            'choice_label' => 'name'
        ])
            ->add('domains', EntityType::class, [
                'label' => 'Domaines',
                'class' => Domain::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name'
            ])
            ->add('types', EntityType::class, [
                'label' => 'Types',
                'class' => Type::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
                
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
        ]);
    }
}
?>