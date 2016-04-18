<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                array('label' => 'name', 'translation_domain' => 'messages'))
            ->add('visible', CheckboxType::class, array(
                'label' => 'visible', 'translation_domain' => 'messages',
                'required' => false,
            ))
            ->add('url', TextType::class, array(
                'label' => 'url', 'translation_domain' => 'messages',
                'required' => false))
            ->add('routeName', TextType::class, array(
                'label' => 'router_name', 'translation_domain' => 'messages'))
            ->add('pageParentId', EntityType::class, array(
                'label' => 'parent_page', 'translation_domain' => 'messages',
                'class' => 'AppBundle:Page',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.idPage', 'ASC');
                },
                'multiple' => false,
                'required' => true
            ))
            ->add('actions', EntityType::class, array(
                'label' => 'actions', 'translation_domain' => 'messages',
                'class' => 'AppBundle:Action',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
                'multiple' => true,
                'required' => false
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Page'
        ));
    }
}
