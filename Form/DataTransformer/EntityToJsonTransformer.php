<?php

namespace Sistema\MWSFORMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity to JSON Many To Many
 */
class EntityToJsonTransformer implements DataTransformerInterface {

    /**
     * Class para conectarse
     */
    private $class;

    /**
     * ObjectManager
     */
    private $om;
    private $id;

    /*     * *
     * Constructor
     */

    public function __construct($dataConnect, $id) {
        $this->class = $dataConnect['class'];
        $this->om = $dataConnect['om'];
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($entities) {
        if (!$entities) {
            return null;
        };
        $jsonResponse = array();
        $métodos_clase = get_class_methods('miclase');
        $jsonResponse = $entities->map(function ($entity) {
                    return array(
                        $this->id => call_user_func(array($entity, 'get' . $this->id)),
                        'text' => $entity->__toString()
                    );
                })->toArray();

        return json_encode($jsonResponse);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($json) {
        $om = $this->om;
        $class = $this->class;
        $entitiesResponse = new ArrayCollection();
        if (!$json) {
            return $entitiesResponse;
        }
        $jEntities = json_decode($json, true);
        foreach ($jEntities as $j) {
            $entity = $om
                    ->getRepository($class)
                    ->findOneBy(array($this->id => $j[key($j)]));
       
            if (!$entitiesResponse->contains($entity)) {
                $entitiesResponse->add($entity);
            }
        }

        return $entitiesResponse;
    }

}
