<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Extractor;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

final class FormErrorExtractor
{
    public static function extractErrors(FormInterface $form): array
    {
        $errors = [];

        /** @var FormError $error */
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = self::extractErrors($child);
            }
        }

        return $errors;
    }
}