<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Street;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName')
            ->add('firstName')
            ->add('middleName')
            ->add('documentFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_label' => true,
                'download_uri' => true,
                'asset_helper' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '100M'
                    ])
                ],
            ])
            ->add('apartment')
            ->add('buildingNumber')
            ->add('info', TextareaType::class, [
                'required' => false,
            ])
            ->add('street', Select2EntityType::class, [
                'label' => false,
                'class' => Street::class,
                'remote_route' => 'find_streets_list',
                'placeholder' => 'Select a street',
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000,
                'mapped' => true,
                'required' => true,
                'minimum_input_length' => 1,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
