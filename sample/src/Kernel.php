<?php

namespace Sample;

use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends \Symfony\Component\HttpKernel\Kernel {

    public function __construct(string $environment, bool $debug) {
        parent::__construct(trim($environment), $debug);
    }

    public function registerBundles() {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\DebugBundle\DebugBundle(),
            new \Sample\App\App(),
            new \Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),
            new \Forci\Bundle\LexikTranslationUIBundle\ForciLexikTranslationUIBundle(),
            new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle()
        );

        $bundles[] = new \Symfony\Bundle\WebServerBundle\WebServerBundle();

        return $bundles;
    }

    public function getRootDir() {
        return __DIR__;
    }

    public function getVarDir() {
        return dirname(__DIR__) . '/var';
    }

    public function getCacheDir() {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir() {
        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load(__DIR__ . '/../app/config/config.yml');
    }
}