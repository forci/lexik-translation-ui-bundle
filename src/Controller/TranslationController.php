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

use Lexik\Bundle\TranslationBundle\Storage\StorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TranslationController extends Controller {

    public function indexAction() {
        $handler = $this->get('lexik_translation.form.handler.trans_unit');
        $localeManager = $this->get('lexik_translation.locale.manager');

        // get form for csrf token
        $form = $this->createForm('Lexik\Bundle\TranslationBundle\Form\Type\TransUnitType', $handler->createFormData(), $handler->getFormOptions());

        return $this->render('@ForciLexikTranslationUI/layout.html.twig', [
            'locales' => $localeManager->getLocales(),
            'defaultLocale' => $this->getParameter('kernel.default_locale'),
            'domains' => array_unique(array_merge(['messages'], $handler->getFormOptions()['domains'])),
            'inputType' => $this->getParameter('lexik_translation.grid_input_type'),
            'form' => $form->createView(),
        ]);
    }

    public function createAction(Request $request) {
        $handler = $this->get('lexik_translation.form.handler.trans_unit');

        $form = $this->createForm('Lexik\Bundle\TranslationBundle\Form\Type\TransUnitType', $handler->createFormData(), $handler->getFormOptions());

        try {
            if (!$handler->process($form, $request)) {
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
            'message' => $this->get('translator')->trans('translations.successfully_added', [], 'LexikTranslationBundle')
        ]);
    }

    public function overviewDataAction() {
        /** @var StorageInterface $storage */
        $storage = $this->get('lexik_translation.translation_storage');

        $stats = $this->get('lexik_translation.overview.stats_aggregator')->getStats();

        return $this->json([
            'layout' => $this->container->getParameter('lexik_translation.base_layout'),
            'locales' => $this->getManagedLocales(),
            'domains' => $storage->getTransUnitDomains(),
            'latestTrans' => $storage->getLatestUpdatedAt(),
            'stats' => $stats,
        ]);
    }

    /**
     * Remove cache files for managed locales.
     */
    public function invalidateCacheAction(Request $request) {
        $this->get('lexik_translation.translator')->removeLocalesCacheFiles($this->getManagedLocales());

        $message = $this->get('translator')->trans('translations.cache_removed', [], 'LexikTranslationBundle');

        if ($request->isXmlHttpRequest()) {
            return $this->json(['message' => $message]);
        }

        return $this->redirectToRoute('forci_lexik_translation_ui_index');
    }

    /**
     * Returns managed locales.
     *
     * @return array
     */
    protected function getManagedLocales() {
        return $this->get('lexik_translation.locale.manager')->getLocales();
    }
}
