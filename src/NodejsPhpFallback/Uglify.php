<?php

namespace NodejsPhpFallback;

use GK\JavascriptPacker as JavascriptMinifier;
use CssMin as CssMininifer;

class Uglify extends Wrapper
{
    protected $css = false;
    protected $concat = array();

    protected $programs = array(
        'js'  => 'uglify',
        'css' => 'clean',
    );

    public function __construct($file)
    {
        $files = (array) $file;
        $this->concat = array_slice($files, 1);
        $this->setModeFromPath($files[0]);
        parent::__construct($files[0]);
    }

    public function setModeFromPath($path)
    {
        $this->css = strtolower(substr($path, -4)) === '.css';

        return $this;
    }

    public function add($file)
    {
        $this->concat[] = $file;
    }

    public function getSource()
    {
        $source = trim(parent::getSource());
        foreach ($this->concat as $file) {
            if ($this->getMode() === 'js') {
                $source = rtrim($source, '; ') . ';';
            }
            $source .= trim(file_get_contents($file));
        }

        return $source;
    }

    public function jsMode()
    {
        $this->css = false;

        return $this;
    }

    public function cssMode()
    {
        $this->css = true;

        return $this;
    }

    public function getMode()
    {
        return $this->css ? 'css' : 'js';
    }

    public function getMinifiedCss()
    {
        return $this->cssMode()->getResult();
    }

    public function getMinifiedJs()
    {
        return $this->jsMode()->getResult();
    }

    public function write($path)
    {
        $this->setModeFromPath($path);

        return parent::write($path);
    }

    public function compile()
    {
        $language = $this->getMode();
        $program = $this->programs[$language];
        $name = $this->path ? basename($this->path) : null;

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $this->getSource());

        return $this->execModuleScript(
            $program . '-' . $language,
            'bin/' . $program . $language,
            escapeshellarg($path)
        );
    }

    public function fallback()
    {
        if ($this->getMode() === 'js') {
            $packer = new JavascriptMinifier($this->getSource());

            return $packer->pack();
        }

        return CssMininifer::minify($this->getSource());
    }
}
