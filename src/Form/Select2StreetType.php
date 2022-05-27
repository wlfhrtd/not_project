<?php

namespace App\Form;

use App\Entity\Street;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class Select2StreetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', Select2EntityType::class, [
                'label' => false,
                'class' => Street::class,
                'remote_route' => 'findstreets',
                'placeholder' => 'Select a street',
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000,
                'mapped' => true,
                'required' => true,

                //'language' => 'ru',

                // 'primary_key' => 'id',
                // 'text_property' => 'name',

                // 'multiple' => true,
                // 'allow_add' => [       // only if multiple=true
                //     'enabled',
                //     'new_tag_text',  // default (NEW)
                //     'new_tag_prefix', // default "__"; real values must not contain these symbols
                //     'tag_separators',
                //  ],

                // 'minimum_input_length' => 2,
                // 'page_limit' => 10,
                // 'scroll' => false, // true will enable infinite scrolling; more: true key needed in response?

                // 'language' => 'en', // i18n language code
                // 'theme' => 'default',
                // 'transformer' => FormTransform::class,
                // 'autostart' => true, // default true; select2 code calling automatically on document ready
                // 'width' => null,
                // 'class_type' => // optional value that will be added to the ajax request as a query string parameter
                // 'render_html' => true, // default:true; this will render your results returned under ['html']

                // 'remote_route',
                // 'remote_params', // optional (slug, id?)
                // 'remote_path', // to specify url directly

                // 'query_parameters', // for dynamic change them using $('#elem').data('query-parameters', { /* new params */ })

                // If you use Embedded Collection Forms and data-prototype to add new elements in your form, you will need the following JavaScript that will listen for adding an element .select2entity:
                //
                // $('body').on('click', '[data-prototype]', function(e) {
                //     $(this).prev().find('.select2entity').last().select2entity();
                // });

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
