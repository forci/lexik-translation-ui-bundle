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

class FreeAuthorizationChecker implements TranslationAuthorizationCheckerInterface {

    public function canCreate(): bool {
        return true;
    }

    public function canEditLocale(string $locale): bool {
        return true;
    }

    public function canDelete(TransUnit $unit): bool {
        return true;
    }

    public function canDeleteTranslation(TransUnit $unit, string $locale): bool {
        return true;
    }
}
