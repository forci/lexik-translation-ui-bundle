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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestController extends Controller {

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction(Request $request) {
        list($transUnits, $count) = $this->get('lexik_translation.data_grid.request_handler')->getPage($request);

        return $this->get('lexik_translation.data_grid.formatter')->createListResponse($transUnits, $count);
    }

    /**
     * @param Request $request
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listByProfileAction(Request $request, $token) {
        list($transUnits, $count) = $this->get('lexik_translation.data_grid.request_handler')->getPageByToken($request, $token);

        return $this->get('lexik_translation.data_grid.formatter')->createListResponse($transUnits, $count);
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
        $transUnit = $this->get('lexik_translation.data_grid.request_handler')->updateFromRequest($id, $request);

        return $this->get('lexik_translation.data_grid.formatter')->createSingleResponse($transUnit);
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id) {
        if (!$this->isGranted('ROLE_TRANSLATOR_DELETE')) {
            return $this->json([
                'message' => 'You are not allowed to perform this action'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $transUnit = $this->get('lexik_translation.translation_storage')->getTransUnitById($id);

        if (!$transUnit) {
            throw $this->createNotFoundException(sprintf('No TransUnit found for id "%s".', $id));
        }

        $deleted = $this->get('lexik_translation.trans_unit.manager')->delete($transUnit);

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
        if (!$this->isGranted('ROLE_TRANSLATOR_DELETE')) {
            return $this->json([
                'message' => 'You are not allowed to perform this action'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $transUnit = $this->get('lexik_translation.translation_storage')->getTransUnitById($id);

        if (!$transUnit) {
            throw $this->createNotFoundException(sprintf('No TransUnit found for id "%s".', $id));
        }

        $deleted = $this->get('lexik_translation.trans_unit.manager')->deleteTranslation($transUnit, $locale);

        return new JsonResponse(['deleted' => $deleted], $deleted ? 200 : 400);
    }
}
