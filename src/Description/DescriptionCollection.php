<?php

namespace Forci\Bundle\LexikTranslationUIBundle\Description;

use Symfony\Component\Yaml\Yaml;

class DescriptionCollection {

    /** @var string[] */
    protected $files = [];

    /** @var array */
    protected $descriptions = [];

    protected $parsed = false;

    public function addFile(string $path): void {
        $this->files[] = $path;
    }

    public function toArray(): array {
        if (!$this->parsed) {
            $this->parse();
        }

        return $this->descriptions;
    }

    protected function parse(): void {
        if ($this->parsed) {
            return;
        }

        foreach ($this->files as $file) {
            $this->descriptions = array_merge($this->descriptions, Yaml::parseFile($file));
        }
    }

}