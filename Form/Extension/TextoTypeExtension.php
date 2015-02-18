<?php

namespace Sistema\MWSFORMBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of TextoTypeExtension
 *
 * @author tito
 */
class TextoTypeExtension extends AbstractTypeExtension
{
    /**
     * Pass the image URL to the view
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {

                $view->vars['data_mask'] = isset($options['data_mask']) ? $options['data_mask'] : "";

    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('data_mask'));
    }

   /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'text';
    }
}
