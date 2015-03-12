<?php
namespace Demofony2\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Demofony2\AppBundle\Enum\ProposalStateEnum;
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProposalAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id', // field name
    );

    protected $translationDomain = 'admin';

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('title', null, array('label' => 'title'))
            ->add('state', 'doctrine_orm_choice', array('label' => 'state', 'choices' => ProposalStateEnum::toArray()))
//            ->add('finishAt', 'doctrine_orm_datetime_range', array(), null,  array('widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm', 'attr' => array('class' => 'datepicker')))

            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with(
                'general',
                array(
                    'class' => 'col-md-6',
                    'description' => '',
                )
            )
            ->add('title', null, array('label' => 'title'))
            ->add('description', 'ckeditor', array('label' => 'description'))
            ->end()
            ->with(
                'controls',
                array(
                    'class' => 'col-md-6',
                    'description' => ''
                )
            )
                ->add('categories', 'sonata_type_model', array('label' => 'categories', 'multiple' => true, 'by_reference' => false))
                ->add('commentsModerated','checkbox', array('label' => 'commentsModerated','required' => false))
                ->add(
                    'finishAt',
                    'sonata_type_datetime_picker',
                    array('label' => 'finishAt','widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm')
                )
                ->add('state', 'choice', array('label' => 'state', 'choices' => ProposalStateEnum::getTranslations()))
            ->end()
            ->with(
                'proposal_answers',
                array(
                    'class' => 'col-md-12',
                    'description' => '',
                )
            )
            ->add(
                'proposalAnswers',
                'sonata_type_collection',
                array(
                    'label' => 'proposal_answers',
                    'type_options' => array(
                        // Prevents the "Delete" option from being displayed
                        'delete' => true,
                        'delete_options' => array(
                            // You may otherwise choose to put the field but hide it
                            'type' => 'checkbox',
                            // In that case, you need to fill in the options as well
                            'type_options' => array(
                                'mapped' => false,
                                'required' => false,
                            ),
                        ),
                    ),
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                )
            )

            ->end()

            ->with(
                'files',
                array(
                    'class' => 'col-md-12',
                    'description' => '',
                )
            )

            ->add('documents', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'label' => 'documents'
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
            ))
            ->add('images', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'label' => 'images'
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
            ))

            ->end()

            ->with(
                'institutional_answer',
                array(
                    'class' => 'col-md-12',
                    'description' => '',
                )
            )
            ->add('institutionalAnswer', 'sonata_type_admin', array('label' => 'institutional_answer', 'delete' => false, 'btn_add' => false))
            ->end()



        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $mapper)
    {
        $mapper
            ->addIdentifier('title', null, array('label' => 'title'))
            ->add('finishAt', null, array('label' => 'finishAt') )
            ->add('state', null, array('label' => 'state'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                ),
                'label' => 'actions',
            ))
        ;
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

    public function prePersist($object)
    {
        foreach ($object->getDocuments() as $document) {
            $document->setProposal($object);
        }

        foreach ($object->getImages() as $image) {
            $image->setProposal($object);
        }

        foreach ($object->getProposalAnswers() as $pa) {
            $pa->setProposal($object);
        }
    }

    public function preUpdate($object)
    {
        $this->prePersist($object);
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
