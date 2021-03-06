<?php

namespace Sistema\MWSFORMBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Document class
 **/
class DocumentType extends FileType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setOptional(array('file_path'));
        $resolver->setDefaults(array(
            'compound' => false,
            'data_class' => 'Symfony\Component\HttpFoundation\File\File',
            'empty_data' => null,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_replace($view->vars, array(
            'type'  => 'file',
            'value' => '',
        ));
        if (array_key_exists('file_path', $options)) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $accessor = PropertyAccess::getPropertyAccessor();
                $imageUrl = $accessor->getValue($parentData, $options['file_path']);
                $value = $accessor->getValue($parentData, 'filePath');
            } else {
                $imageUrl = null;
                $value = null;
            }

            // set an "image_url" variable that will be available when rendering this field
            $view->vars['file_url'] = $imageUrl;
            $view->vars['value'] = $value;
        }
    }
    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'document_file';
    }

}
