<?php
namespace Demofony2\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Demofony2\AppBundle\Enum\UserRolesEnum;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'lastLogin', // field name
    );

    protected $translationDomain = 'admin';

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('username', null, array('label' => 'username'))
            ->add('email', null, array('label' => 'email'))
            ->add('enabled', null, array('label' => 'enabled'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array('label' => 'name'))
            ->add('username', null, array('label' => 'username'))
            ->add('email', null, array('label' => 'email'))
            ->add(
                'plainPassword',
                'repeated',
                array(
                    'required' => false,
                    'type' => 'password',
                    'invalid_message' => 'passwords_not_equals',
                    'options' => array('label' => 'user.form.password'),
                    'first_options' => array('label' => 'Nova contrasenya'),
                    'second_options' => array('label' => 'Reescriu la nova contraseña'),
                )
            )
            ->add(
                'roles',
                'choice',
                array('label' => 'roles', 'choices' => UserRolesEnum::toArray(), 'multiple' => true, 'expanded' => true)
            )
            ->add('enabled', 'checkbox', array('label' => 'enabled', 'required' => false))
            ->add('image', 'file', array('label' => 'image', 'required' => false));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $mapper)
    {
        $mapper
            ->addIdentifier('username', null, array('label' => 'username'))
            ->add('email', null, array('label' => 'email'))
            ->add('createdAt', null, array('label' => 'createdAt'))
            ->add('lastLogin', null, array('label' => 'lastLogin'))
            ->add('roles', null, array('label' => 'roles'))
            ->add('enabled', 'boolean', array('label' => 'enabled', 'editable' => true))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                ),
                'label' => 'actions',
            ))
        ;
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

    public function preUpdate($object)
    {
        $this->updateUser($object);
    }

    public function prePersist($object)
    {
        $this->updateUser($object);
    }

    protected function updateUser($object)
    {
        $this->getConfigurationPool()->getContainer()->get(
            'fos_user.user_manager'
        )->updateUser($object);
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
