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
use Forci\Bundle\LexikTranslationUIBundle\Description\DescriptionCollection;
use Lexik\Bundle\TranslationBundle\Document\TransUnit as TransUnitDocument;
use Lexik\Bundle\TranslationBundle\Form\Handler\TransUnitFormHandler;
use Lexik\Bundle\TranslationBundle\Form\Type\TransUnitType;
use Lexik\Bundle\TranslationBundle\Manager\LocaleManager;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitInterface;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitManager;
use Lexik\Bundle\TranslationBundle\Storage\StorageInterface;
use Lexik\Bundle\TranslationBundle\Translation\Translator;
use Lexik\Bundle\TranslationBundle\Util\DataGrid\DataGridFormatter;
use Lexik\Bundle\TranslationBundle\Util\DataGrid\DataGridRequestHandler;
use Lexik\Bundle\TranslationBundle\Util\Overview\StatsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

class RestController extends AbstractController {

    /** @var DataGridRequestHandler */
    protected $requestHandler;

    /** @var DataGridFormatter */
    protected $gridFormatter;

    /** @var StorageInterface */
    protected $storage;

    /** @var TransUnitManager */
    protected $transUnitManager;

    /** @var LocaleManager */
    protected $localeManager;

    /** @var TranslationAuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var DescriptionCollection */
    protected $descriptions;

    /** @var StatsAggregator */
    protected $statsAggregator;

    /** @var TransUnitFormHandler */
    protected $formHandler;

    /** @var Router */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var Translator */
    protected $lexikTranslator;

    /** @var string */
    protected $defaultLocale;

    /** @var string */
    protected $inputType;

    /** @var int */
    protected $perPage;

    public function __construct(
        DataGridRequestHandler $requestHandler, DataGridFormatter $gridFormatter, StorageInterface $storage,
        TransUnitManager $transUnitManager, LocaleManager $localeManager,
        TranslationAuthorizationCheckerInterface $authorizationChecker,
        DescriptionCollection $descriptions, StatsAggregator $statsAggregator,
        TransUnitFormHandler $formHandler, Router $router,
        TranslatorInterface $translator, Translator $lexikTranslator,
        string $defaultLocale, string $inputType, int $perPage
    ) {
        $this->requestHandler = $requestHandler;
        $this->gridFormatter = $gridFormatter;
        $this->storage = $storage;
        $this->transUnitManager = $transUnitManager;
        $this->localeManager = $localeManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->descriptions = $descriptions;
        $this->statsAggregator = $statsAggregator;
        $this->formHandler = $formHandler;
        $this->router = $router;
        $this->translator = $translator;
        $this->lexikTranslator = $lexikTranslator;
        $this->defaultLocale = $defaultLocale;
        $this->inputType = $inputType;
        $this->perPage = $perPage;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction(Request $request) {
        list($transUnits, $count) = $this->requestHandler->getPage($request);

        return $this->gridFormatter->createListResponse($transUnits, $count);
    }

    /**
     * @param Request $request
     * @param         $token
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listByProfileAction(Request $request, $token) {
        list($transUnits, $count) = $this->requestHandler->getPageByToken($request, $token);

        return $this->gridFormatter->createListResponse($transUnits, $count);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateAction(Request $request, $id) {
        /** @var TransUnitInterface $transUnit */
        $transUnit = $this->storage->getTransUnitById($id);

        if (!$transUnit) {
            throw new NotFoundHttpException(sprintf('No TransUnit found for "%s"', $id));
        }

        $translationsContent = [];
        foreach ($this->localeManager->getLocales() as $locale) {
            if ($this->authorizationChecker->canEditLocale($locale)) {
                $translationsContent[$locale] = $request->request->get($locale);
            }
        }

        $this->transUnitManager->updateTranslationsContent($transUnit, $translationsContent);

        if ($transUnit instanceof TransUnitDocument) {
            $transUnit->convertMongoTimestamp();
        }

        $this->storage->flush();

        return $this->gridFormatter->createSingleResponse($transUnit);
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id) {
        /** @var TransUnitInterface $transUnit */
        $transUnit = $this->storage->getTransUnitById($id);

        if (!$transUnit) {
            throw $this->createNotFoundException(sprintf('No TransUnit found for id "%s".', $id));
        }

        if (!$this->authorizationChecker->canDelete($transUnit)) {
            return $this->json([
                'message' => 'You are not allowed to perform this action'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $deleted = $this->transUnitManager->delete($transUnit);

        return new JsonResponse(['deleted' => $deleted], $deleted ? 200 : 400);
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteTranslationAction($id, $locale) {
        /** @var TransUnitInterface $transUnit */
        $transUnit = $this->storage->getTransUnitById($id);

        if (!$transUnit) {
            throw $this->createNotFoundException(sprintf('No TransUnit found for id "%s".', $id));
        }

        if (!$this->authorizationChecker->canDeleteTranslation($transUnit, $locale)) {
            return $this->json([
                'message' => 'You are not allowed to perform this action'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $deleted = $this->transUnitManager->deleteTranslation($transUnit, $locale);

        return new JsonResponse(['deleted' => $deleted], $deleted ? 200 : 400);
    }

    public function descriptionsAction() {
        return $this->json($this->descriptions->toArray());
    }

    public function configAction() {
        // get form for csrf token
        $form = $this->createForm(TransUnitType::class, $this->formHandler->createFormData(), $this->formHandler->getFormOptions());

        $locales = $this->localeManager->getLocales();

        $editableLocales = [];

        foreach ($locales as $locale) {
            if ($this->authorizationChecker->canEditLocale($locale)) {
                $editableLocales[] = $locale;
            }
        }

        return $this->json([
            'defaultLocale' => $this->defaultLocale,
            'locales' => $locales,
            'editableLocales' => $editableLocales,
            'domains' => array_unique(array_merge(['messages'], $this->formHandler->getFormOptions()['domains'])),
            'inputType' => $this->inputType,
            'transUnitToken' => $form->createView()->children['_token']->vars['value'],
            'recordsPerPage' => $this->perPage,
            'canCreate' => $this->authorizationChecker->canCreate(),
            'labels' => [
                'hideCol' => $this->translator->trans('translations.show_hide_columns', [], 'LexikTranslationBundle'),
                'invalidateCache' => $this->translator->trans('translations.invalidate_cache', [], 'LexikTranslationBundle'),
                'allTranslations' => $this->translator->trans('translations.all_translations', [], 'LexikTranslationBundle'),
                'profiler' => $this->translator->trans('translations.profiler', [], 'LexikTranslationBundle'),
                'latestProfiles' => $this->translator->trans('translations.latest_profiles', [], 'LexikTranslationBundle'),
                'profile' => $this->translator->trans('translations.profile', [], 'LexikTranslationBundle'),
                'domain' => $this->translator->trans('translations.domain', [], 'LexikTranslationBundle'),
                'overviewDomain' => $this->translator->trans('overview.domain', [], 'LexikTranslationBundle'),
                'key' => $this->translator->trans('translations.key', [], 'LexikTranslationBundle'),
                'save' => $this->translator->trans('translations.save', [], 'LexikTranslationBundle'),
                'saveAdd' => $this->translator->trans('translations.save_add', [], 'LexikTranslationBundle'),
                'pageTitle' => $this->translator->trans('translations.page_title', [], 'LexikTranslationBundle'),
                'addTranslation' => $this->translator->trans('translations.add_translation', [], 'LexikTranslationBundle'),
                'newTranslation' => $this->translator->trans('translations.new_translation', [], 'LexikTranslationBundle'),
                'pageTitleOverview' => $this->translator->trans('overview.page_title', [], 'LexikTranslationBundle'),
                'backToList' => $this->translator->trans('translations.back_to_list', [], 'LexikTranslationBundle'),
                'showGrid' => $this->translator->trans('overview.show_grid', [], 'LexikTranslationBundle'),
                'latestTranslation' => $this->translator->trans('overview.msg_latest_translation', [], 'LexikTranslationBundle'),
                'overviewNoStats' => $this->translator->trans('overview.no_stats', [], 'LexikTranslationBundle'),
                'toggleAllColumns' => $this->translator->trans('translations.toggle_all_columns', [], 'LexikTranslationBundle'),
                'error' => $this->translator->trans('translations.error', [], 'ForciLexikTranslationUIBundle'),
                'createSuccess' => $this->translator->trans('translations.successfully_created', [], 'ForciLexikTranslationUIBundle'),
                'updateSuccess' => $this->translator->trans('translations.successfully_updated', [], 'ForciLexikTranslationUIBundle'),
                'doubleClickToEdit' => $this->translator->trans('translations.double_click_to_edit', [], 'ForciLexikTranslationUIBundle'),
                'allDone' => $this->translator->trans('translations.all_done', [], 'ForciLexikTranslationUIBundle'),
            ]
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
