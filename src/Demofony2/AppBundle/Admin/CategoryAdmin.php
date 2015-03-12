<?php
namespace Demofony2\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryAdmin extends Admin
{
    protected $translationDomain = 'admin';

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'name', // field name
    );

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('name',null, array('label' => 'name'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', array('label' => 'name'))
            ->add('description', 'textarea', array('required' => false, 'label' => 'description'))
            ->add('image', 'demofony2_admin_image', array('required' => false, 'label' => 'image'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $mapper)
    {
        $mapper
            ->addIdentifier('name', null, array('label' => 'name') )
            ->add('image', null, array('label' => 'image', 'template' => ':Admin\ListFieldTemplate:image.html.twig'))
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array(),
                    ),
                    'label' => 'actions',
                )
            );
    }

    /**
     * Configure route collection
     *
     * @param RouteCollection $collection collection
     *
     * @return mixed
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'translation_domain' => 'admin',
            )
        );
    }
}
