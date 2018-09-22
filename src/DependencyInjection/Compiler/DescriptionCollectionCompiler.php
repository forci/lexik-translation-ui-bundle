<?php

/*
 * This file is part of the ForciLexikTranslationUIBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Tatyana Mincheva <tatjana@forci.com>
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\LexikTranslationUIBundle\DependencyInjection\Compiler;

use Forci\Bundle\LexikTranslationUIBundle\Authorization\TranslationAuthorizationCheckerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DescriptionCollectionCompiler implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        $definition = $container->getDefinition('Forci\Bundle\LexikTranslationUIBundle\Description\DescriptionCollection');

        $files = $container->getParameter('forci_lexik_translation_ui.description_files');

        /** @var string $file */
        foreach ($files as $file) {
            $definition->addMethodCall('addFile', [
                $file
            ]);
        }
    }
}
