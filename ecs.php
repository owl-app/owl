<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->import('vendor/sylius-labs/coding-standard/ecs.php');

    $config->parallel();
    $config->paths(['src/Owl']);
    $config->skip([
        InlineDocCommentDeclarationSniff::class . '.MissingVariable',
        InlineDocCommentDeclarationSniff::class . '.NoAssignment',
        VisibilityRequiredFixer::class => ['*Spec.php'],
        '**/var/*'
    ]);
    $config->ruleWithConfiguration(PhpdocSeparationFixer::class, ['groups' => [['Given', 'When', 'Then']]]);
};
