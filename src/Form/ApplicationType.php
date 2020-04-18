<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * 
     * Permet d'avoir la configuration de base d'un coup !
     * 
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     * 
     * on met la fonction en protected pour les classe qui héritent de ApplicationType puissent utiliser cette fonction
     */
    protected function getConfiguration($label, $placeholder, $options = []) {
        return array_merge_recursive([ //le récursif permet de ne pas recouvrir les attr de la fonction si elle en a déjà
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }
}