<?php

namespace Sistema\MWSFORMBundle\Form\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\FormFilterBundle\Event\ApplyFilterEvent;

use Sistema\RRHHBundle\Entity\Empleado;

class FilterSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'lexik_form_filter.apply.orm.filter_text_like'  => array('filterTextLike'),
            //'lexik_form_filter.apply.orm.text'                 => array('filterTextLike'),
            'lexik_form_filter.apply.dbal.filter_text_like' => array('filterTextLike'),
            'lexik_form_filter.apply.orm.select2'           => array('filterTextEntity'),
            'lexik_form_filter.apply.dbal.select2'          => array('filterTextEntity'),
            //'lexik_form_filter.apply.dbal.text'                => array('filterTextLike'),
        );
    }

    /**
     * Apply a filter for a filter_locale type.
     *
     * This method should work whih both ORM and DBAL query builder.
     */
   public function filterTextLike(ApplyFilterEvent $event)
    {
        $qb = $event->getQueryBuilder();
        $expr = $event->getFilterQuery()->getExpressionBuilder();
        $values = $event->getValues();
        if ('' !== $values['value'] && null !== $values['value']) {
            if (isset($values['condition_pattern'])) {
                $qb->andWhere($expr->stringLike($event->getField(), "%" . $values['value'] . "%", $values['condition_pattern']));
            } else {
                $qb->andWhere($expr->stringLike($event->getField(), "%" . $values['value'] .  "%"));
            }
        }
    }
    /**
     * Esta funcion solo sirve para select2 de cliente.
     * Se necesita tener un por ej: ->join('a.cliente', 'cli') indicar 'cli'
     * Y tambien ->select('a, cli') indicando nuevamente 'cli'
     */
    public function filterTextEntity(ApplyFilterEvent $event)
    {
        $qb = $event->getQueryBuilder();
        $values = $event->getValues();
        
        
        if (!empty($values['value'])) {
            
            if ($values['value'] instanceof Empleado){
                $qb
                    ->where('e.id = :id')
                    ->setParameter('id', $values['value']->getId())
                    ->getQuery()
                    ->getResult()
                ;
                
            }else{
                $qb
                    ->where('cli.id = :id')
                    ->setParameter('id', $values['value']->getId())
                    ->getQuery()
                    ->getResult()
                ;
            }
        }
    }
}