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

        /** @var string $file */
        foreach ($this->files as $file) {
            if (!file_exists($file)) {
                throw new \RuntimeException(sprintf(
                    'Descriptions file %s does not exist',
                    $file
                ));
            }

            $descriptions = Yaml::parseFile($file);

            if (!is_array($descriptions)) {

                throw new \RuntimeException(sprintf(
                    'Descriptions file %s should contain an array if key:value pairs',
                    $file
                ));
            }

            $this->descriptions = array_merge($this->descriptions, $descriptions);
        }
    }

}