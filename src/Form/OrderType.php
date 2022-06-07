<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\Customer;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', Select2EntityType::class, [
                'label' => false,
                'class' => Customer::class,
                'remote_route' => 'find_customers_list',
                'placeholder' => 'Select a customer',
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000,
                'mapped' => true,
                'required' => true,
                'minimum_input_length' => 1,
            ])
            ->add('status', TextType::class, [
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('spreadsheetFilename')
            ->add('total', TextType::class, [
                'mapped' => true,
                'required' => true,
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('info', TextareaType::class, [
                'required' => false,
            ])
            ->add(
                $builder->create('cart', FormType::class, [
                    'data_class' => Cart::class,
                    'mapped' => true,
                    'required' => true,
                    ])
                    ->add('items', CollectionType::class, [
                        'entry_type' => OrderCartItemType::class,
                        'entry_options' => [
                            'label' => false,
                        ],
                        'label' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
