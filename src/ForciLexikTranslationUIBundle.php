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

namespace Forci\Bundle\LexikTranslationUIBundle;

use Forci\Bundle\LexikTranslationUIBundle\DependencyInjection\Compiler\AuthorizationServiceCompiler;
use Forci\Bundle\LexikTranslationUIBundle\DependencyInjection\Compiler\DescriptionCollectionCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ForciLexikTranslationUIBundle extends Bundle {

    public function build(ContainerBuilder $container) {
        parent::build($container);

        $container->addCompilerPass(new AuthorizationServiceCompiler());
        $container->addCompilerPass(new DescriptionCollectionCompiler());
    }
}
