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

class AuthorizationServiceCompiler implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        $serviceId = $container->getParameter('forci_lexik_translation_ui.authorization_service_id');

        if (null === $serviceId) {
            return;
        }

        if (!$container->hasDefinition($serviceId)) {
            throw new \RuntimeException(sprintf(
                'You have configured "%s" as authorization service ID, but it was not found in the Container.',
                $serviceId
            ));
        }

        $definition = $container->getDefinition($serviceId);

        if (!is_a($definition->getClass(), TranslationAuthorizationCheckerInterface::class)) {
            throw new \RuntimeException(sprintf(
                'Translatipon Authorization Checker must implement %s',
                TranslationAuthorizationCheckerInterface::class
            ));
        }

        $container->setAlias('forci_lexik_translation.translation_authorization_checker', $serviceId);
    }
}
