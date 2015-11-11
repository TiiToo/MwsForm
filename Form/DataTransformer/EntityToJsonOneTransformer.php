<?php

namespace Sistema\MWSFORMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Entity to JSON Many To Many
 */
class EntityToJsonOneTransformer implements DataTransformerInterface {

    /**
     * Class para conectarse
     */
    private $class;

    /**
     * ObjectManager
     */
    private $om;
    private $id;
    private $opciones;
    private $campo;

    /*     * *
     * Constructor
     */

    public function __construct($dataConnect, $id, $opciones) {
        $this->class = $dataConnect['class'];
        $this->om = $dataConnect['om'];
        $this->id = $id;
        $this->opciones = $opciones;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($entities) {
        $this->campo = "__toString";
        if (isset($this->opciones["configs"]["mostrarCampo"])) {
            $this->campo = 'get' . $this->opciones["configs"]["mostrarCampo"];
        }
        if (!$entities) {
            return null;
        };
        $jsonResponse = array();
        if (is_array($entities)) {
            if (array_key_exists(0, $entities)) {
                $jsonResponse = $entities->map(function ($entity) {
                            return array(
                                $this->id => call_user_func(array($entity, 'get' . $this->id)),
                                'text' => $entity->__toString()
                            );
                        })->toArray();
            } else {
                $cliente = array(
                    $this->id => call_user_func(array($entities, 'get' . $this->id)),
                    'text' => $entities->__toString()
                );
                $jsonResponse = $cliente;
            }
        } else {
            $om = $this->om;
            $class = $this->class;
            $entity = $om
                    ->getRepository($class)
                    ->findOneBy(array($this->id => $entities))
            ;
   
            $cliente = array(
                $this->id => call_user_func(array($entity, 'get' . $this->id)),
                'text' => call_user_func(array($entity, $this->campo)),
            );
            $jsonResponse = $cliente;
        }

        return json_encode($jsonResponse);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($json) {
        $om = $this->om;
        $class = $this->class;
        $entityResponse = null;
        if (!$json) {
            return $entityResponse;
        }
        $jEntities = json_decode($json, true);
        if (array_key_exists(0, $jEntities)) {
            foreach ($jEntities as $j) {
                $entity = $om
                        ->getRepository($class)
                        ->findOneBy(array($this->id => $j[key($j)]));
                ;
                if ($entity) {
                    $entityResponse = $entity;
                }
            }
        } else {
            $entity = $om
                    ->getRepository($class)
                    ->findOneBy(array($this->id => $jEntities[key($jEntities)]));
            ;
            if ($entity) {
                $entityResponse = $entity;
            }
        }
        return $entityResponse;
    }

}
