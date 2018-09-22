<?php

namespace Sample\App\Translation;

use Forci\Bundle\LexikTranslationUIBundle\Authorization\TranslationAuthorizationCheckerInterface;
use Lexik\Bundle\TranslationBundle\Model\TransUnit;

class AuthorizationChecker implements TranslationAuthorizationCheckerInterface {

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