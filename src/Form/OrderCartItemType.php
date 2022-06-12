<?php

namespace App\Form;

use App\Entity\CartItem;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class OrderCartItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', Select2EntityType::class, [
                'class' => Product::class,
                'remote_route' => 'find_products_list',
                'placeholder' => 'Select a product',
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000,
                'mapped' => true,
                'required' => true,
                'width' => 400,
                'label' => 'Product',
                'minimum_input_length' => 1,
                'primary_key' => 'id',
                'text_property' => 'name',
            ])
            ->add('quantity', NumberType::class, [
                'mapped' => true,
                'required' => true,
            ])
            ->add('in_stock', NumberType::class, [
                'mapped' => false,
            ])
            ->add('price', TextType::class, [
                'mapped' => false,
            ])
            ->add('item_total_price', TextType::class, [
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CartItem::class,
        ]);
    }
}
