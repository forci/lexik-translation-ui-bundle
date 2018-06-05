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

namespace Forci\Bundle\LexikTranslationUIBundle\Authorization;

use Lexik\Bundle\TranslationBundle\Model\TransUnit;

interface TranslationAuthorizationCheckerInterface {

    public function canCreate(): bool;

    public function canEditLocale(string $locale): bool;

    public function canDelete(TransUnit $unit): bool;

    public function canDeleteTranslation(TransUnit $unit, string $locale): bool;
}
