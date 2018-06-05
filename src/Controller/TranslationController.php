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

namespace Forci\Bundle\LexikTranslationUIBundle\Controller;

use Forci\Bundle\LexikTranslationUIBundle\Authorization\TranslationAuthorizationCheckerInterface;
use Lexik\Bundle\TranslationBundle\Form\Handler\TransUnitFormHandler;
use Lexik\Bundle\TranslationBundle\Form\Type\TransUnitType;
use Lexik\Bundle\TranslationBundle\Manager\LocaleManager;
use Lexik\Bundle\TranslationBundle\Storage\StorageInterface;
use Lexik\Bundle\TranslationBundle\Translation\Translator;
use Lexik\Bundle\TranslationBundle\Util\Overview\StatsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationController extends AbstractController {

    /** @var TransUnitFormHandler */
    protected $formHandler;

    /** @var LocaleManager */
    protected $localeManager;

    /** @var StorageInterface */
    protected $storage;

    /** @var StatsAggregator */
    protected $statsAggregator;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var Translator */
    protected $lexikTranslator;

    /** @var TranslationAuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var string */
    protected $defaultLocale;

    /** @var string */
    protected $inputType;

    public function __construct(
        TransUnitFormHandler $formHandler, LocaleManager $localeManager, StorageInterface $storage,
        StatsAggregator $statsAggregator, TranslatorInterface $translator, Translator $lexikTranslator,
        TranslationAuthorizationCheckerInterface $authorizationChecker,
        string $defaultLocale, string $inputType
    ) {
        $this->formHandler = $formHandler;
        $this->localeManager = $localeManager;
        $this->storage = $storage;
        $this->statsAggregator = $statsAggregator;
        $this->translator = $translator;
        $this->lexikTranslator = $lexikTranslator;
        $this->authorizationChecker = $authorizationChecker;
        $this->defaultLocale = $defaultLocale;
        $this->inputType = $inputType;
    }

    public function indexAction() {
        // get form for csrf token
        $form = $this->createForm(TransUnitType::class, $this->formHandler->createFormData(), $this->formHandler->getFormOptions());

        $locales = $this->localeManager->getLocales();

        $editableLocales = [];

        foreach ($locales as $locale) {
            if ($this->authorizationChecker->canEditLocale($locale)) {
                $editableLocales[] = $locale;
            }
        }

        return $this->render('@ForciLexikTranslationUI/layout.html.twig', [
            'locales' => $locales,
            'editableLocales' => $editableLocales,
            'defaultLocale' => $this->defaultLocale,
            'domains' => array_unique(array_merge(['messages'], $this->formHandler->getFormOptions()['domains'])),
            'inputType' => $this->inputType,
            'form' => $form->createView(),
            'canCreate' => $this->authorizationChecker->canCreate()
        ]);
    }

    public function createAction(Request $request) {
        if (!$this->authorizationChecker->canCreate()) {
            return $this->json([
                'errors' => ['You are not allowed to perform this action']
            ], Response::HTTP_UNAUTHORIZED);
        }

        $form = $this->createForm(TransUnitType::class, $this->formHandler->createFormData(), $this->formHandler->getFormOptions());

        try {
            if (!$this->formHandler->process($form, $request)) {
                return $this->json([
                    'errors' => $form->getErrors(true, false)
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $e) {
            return $this->json([
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'success' => true,
            'message' => $this->translator->trans('translations.successfully_added', [], 'LexikTranslationBundle')
        ]);
    }

    public function overviewDataAction() {
        $stats = $this->statsAggregator->getStats();

        return $this->json([
            'locales' => $this->localeManager->getLocales(),
            'domains' => $this->storage->getTransUnitDomains(),
            'latestTrans' => $this->storage->getLatestUpdatedAt(),
            'stats' => $stats,
        ]);
    }

    /**
     * Remove cache files for managed locales.
     */
    public function invalidateCacheAction(Request $request) {
        $this->lexikTranslator->removeLocalesCacheFiles($this->localeManager->getLocales());

        $message = $this->translator->trans('translations.cache_removed', [], 'LexikTranslationBundle');

        if ($request->isXmlHttpRequest()) {
            return $this->json(['message' => $message]);
        }

        return $this->redirectToRoute('forci_lexik_translation_ui_index');
    }
}
