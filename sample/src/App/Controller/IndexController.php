<?php

namespace Sample\App\Controller;

use Symfony\Component\HttpFoundation\Response;

class IndexController {

    /** @var \Twig\Environment */
    protected $twig;

    public function __construct(\Twig\Environment $twig) {
        $this->twig = $twig;
    }

    public function indexAction() {
        return new Response($this->twig->render('@App/index.html.twig'));
    }

}