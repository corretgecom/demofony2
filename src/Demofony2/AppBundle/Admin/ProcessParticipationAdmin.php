<?php
namespace Demofony2\AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Demofony2\AppBundle\Enum\ProcessParticipationStateEnum;
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessParticipationAdmin extends Admin
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
            ->add('state', 'doctrine_orm_callback', array(
                'title' => 'state',
                'callback'   => array($this, 'getStateFilter'),
                'field_type' => 'choice',
                'field_options' => array(
                    'choices' => ProcessParticipationStateEnum::getTranslations()
                )
            ))
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
                    'description' => '',

                )
            )
            ->add('categories', 'sonata_type_model', array('label'=> 'categories', 'multiple' => true, 'by_reference' => false))

            ->add('commentsModerated', 'checkbox', array('label' =>'commentsModerated' , 'required' => false))
            ->add(
                'presentationAt',
                'sonata_type_datetime_picker',
                array('label'=> 'presentationAt', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm')
            )
            ->add(
                'finishAt',
                'sonata_type_datetime_picker',
                array('label' => 'finishAt', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm')
            )
            ->add(
                'debateAt',
                'sonata_type_datetime_picker',
                array('label' => 'debateAt', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm')
            )
//            ->add('state', 'choice', array('choices' => ProcessParticipationStateEnum::getTranslations()))


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
                'label' => 'documents',
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
            ))
            ->add('images', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'label' => 'images',
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
                )
            )
            ->add('institutionalAnswer', 'sonata_type_admin', array('label' => 'institutional_answer', 'btn_add' => false, 'btn_delete' => false, 'required' => false))
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
            ->add('presentationAt', null, array('label' => 'presentationAt'))
            ->add('debateAt', null, array('label' => 'debateAt'))
            ->add('finishAt', null, array('label' =>'finishAt'))
            ->add('state', null, array('label' => 'state', 'template' => ':Admin\ListFieldTemplate:state.html.twig'))
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

    public function getStateFilter($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return;
        }

        if (ProcessParticipationStateEnum::DRAFT === $value['value']) {

            $queryBuilder->andWhere(sprintf(':now < %s.presentationAt', $alias));
            $queryBuilder->setParameter('now', new \DateTime('now'));
        }

        if (ProcessParticipationStateEnum::PRESENTATION === $value['value']) {
            $queryBuilder->andWhere(sprintf(':now > %s.presentationAt', $alias));
            $queryBuilder->andWhere(sprintf(':now < %s.debateAt', $alias));
            $queryBuilder->setParameter('now', new \DateTime('now'));
        }

        if (ProcessParticipationStateEnum::DEBATE === $value['value']) {
            $queryBuilder->andWhere(sprintf(':now > %s.presentationAt', $alias));
            $queryBuilder->andWhere(sprintf(':now > %s.debateAt', $alias));
            $queryBuilder->andWhere(sprintf(':now < %s.finishAt', $alias));
            $queryBuilder->setParameter('now', new \DateTime('now'));
        }

        if (ProcessParticipationStateEnum::CLOSED === $value['value']) {
            $queryBuilder->andWhere(sprintf(':now > %s.presentationAt', $alias));
            $queryBuilder->andWhere(sprintf(':now > %s.debateAt', $alias));
            $queryBuilder->andWhere(sprintf(':now > %s.finishAt', $alias));
            $queryBuilder->setParameter('now', new \DateTime('now'));
        }

        return true;
    }

    public function prePersist($object)
    {
        foreach ($object->getDocuments() as $document) {
            $document->setProcessParticipation($object);
        }

        foreach ($object->getImages() as $image) {
            $image->setProcessParticipation($object);
        }

        foreach ($object->getProposalAnswers() as $pa) {
            $pa->setProcessParticipation($object);
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
