<?php

namespace RedCircle\ConsoleProcessManagerBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProcessAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by'    => 'updatedAt'
    );

    /**
     * Removing bath actions
     *
     * @return array
     */
    public function getBatchActions()
    {
        return [];
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('commandName')
            ->add('command')
            ->add('executionCounter')
            ->add('avgExecutionTime')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('commandName')
            ->add('command')
            ->add('executionCounter')
            ->add('avgExecutionTime', null, array(
                'template' => 'ConsoleProcessManagerBundle:CRUD:execution_time_field.html.twig'
            ))
            ->add('createdAt', null, array('format' => 'Y-m-d H:i:s'))
            ->add('updatedAt', null, array('format' => 'Y-m-d H:i:s'))
//            ->add('_action', null, array(
//                'actions' => array(
//                    'show' => array(),
//                    'edit' => array(),
//                    'delete' => array(),
//                )
//            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('commandName')
            ->add('command')
            ->add('executionCounter')
            ->add('avgExecutionTime')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('commandName')
            ->add('command')
            ->add('executionCounter')
            ->add('avgExecutionTime')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

}
