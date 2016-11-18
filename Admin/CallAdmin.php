<?php

namespace RedCircle\ConsoleProcessManagerBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CallAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by'    => 'createdAt'
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
            ->add('process.commandName')
            ->add('createdAt')
            ->add('finishedAt')
            ->add('executionTime')
            ->add('status', null, array(), 'choice', array(
                'choices' => array(
                    0 => 'In progress',
                    1 => 'Finished',
                    2 => 'Failed'
                ),
            ))
            ->add('output')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('process', 'many_to_one', [
                'route' => [
                    'name' => 'show',
                ],
                'associated_property' => 'command', // property name of entity Country
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => array(
                    'fieldName' => 'command' // property name of entity Country
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'process'), // property state of entity City
                )
            ])
//            ->add('process.commandName', null, ['route' => ['name' => 'show']])
            ->add('status', null, array(
                'template' => 'ConsoleProcessManagerBundle:CRUD:call_status_field.html.twig'
            ))
            ->add('ExecutionTimeProportion', null, array(
                'template' => 'ConsoleProcessManagerBundle:CRUD:call_execution_time_proportion.html.twig'
            ))
            ->add('createdAt', null, array('format' => 'Y-m-d H:i:s'))
            ->add('finishedAt', null, array('format' => 'Y-m-d H:i:s'))
            ->add('executionTime', null, array(
                'template' => 'ConsoleProcessManagerBundle:CRUD:execution_time_field.html.twig'
            ))
            ->add('process.avgExecutionTime', null, array(
                'template' => 'ConsoleProcessManagerBundle:CRUD:execution_time_field.html.twig'
            ))
            ->add('output', 'html', array(
                'truncate' => array(
                    'preserve' => true
                )
            ))
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
//                    'edit' => array(),
//                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('consolePid')
            ->add('process.commandName')
            ->add('process', 'many_to_one', [
                'route' => [
                    'name' => 'show',
                ]
            ])
            ->add('createdAt', null, array('format' => 'Y-m-d H:i:s'))
            ->add('finishedAt', null, array('format' => 'Y-m-d H:i:s'))
            ->add('executionTimeToString', null, ['label' => 'Execution Time'])
            ->add('process.avgExecutionTimeToString', null, ['label' => 'Average Process Execution Time'])
            ->add('status', 'choice', array(
                'choices' => array(
                    0 => 'In progress',
                    1 => 'Finished',
                    2 => 'Failed'
                ),
            ))
            ->add('output')
        ;
    }
}
