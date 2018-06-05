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
use Lexik\Bundle\TranslationBundle\Manager\LocaleManager;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitInterface;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitManager;
use Lexik\Bundle\TranslationBundle\Document\TransUnit as TransUnitDocument;
use Lexik\Bundle\TranslationBundle\Util\DataGrid\DataGridRequestHandler;
use Lexik\Bundle\TranslationBundle\Util\DataGrid\DataGridFormatter;
use Lexik\Bundle\TranslationBundle\Storage\StorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function __construct(
        DataGridRequestHandler $requestHandler, DataGridFormatter $gridFormatter, StorageInterface $storage,
        TransUnitManager $transUnitManager, LocaleManager $localeManager,
        TranslationAuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->requestHandler = $requestHandler;
        $this->gridFormatter = $gridFormatter;
        $this->storage = $storage;
        $this->transUnitManager = $transUnitManager;
        $this->localeManager = $localeManager;
        $this->authorizationChecker = $authorizationChecker;
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
     * @param $token
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
//        $transUnit = $this->requestHandler->updateFromRequest($id, $request);

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

//        return $transUnit;

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
//        if (!$this->isGranted('ROLE_TRANSLATOR_DELETE')) {
//            return $this->json([
//                'message' => 'You are not allowed to perform this action'
//            ], Response::HTTP_UNAUTHORIZED);
//        }

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
//        if (!$this->isGranted('ROLE_TRANSLATOR_DELETE')) {
//            return $this->json([
//                'message' => 'You are not allowed to perform this action'
//            ], Response::HTTP_UNAUTHORIZED);
//        }

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
}
