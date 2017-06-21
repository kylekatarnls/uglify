<?php

namespace NodejsPhpFallback;

use CssMin as CssMininifer;
use GK\JavascriptPacker as JavascriptMinifier;

class Uglify extends Wrapper
{
    protected $css = false;
    protected $concat = array();

    protected $programs = array(
        'js'  => array('uglify-js/bin/', 'uglifyjs'),
        'css' => array('clean-css/', 'index.js'),
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
        $source = parent::getSource();
        foreach ($this->concat as $file) {
            if ($this->getMode() === 'js') {
                $source .= "\n;";
            }
            $source .= "\n" . file_get_contents($file);
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
        list($programDirectory, $programFile) = $this->programs[$language];
        $path = $this->getPath();
        $name = $path ? basename($path) : null;

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $this->getSource());

        return $this->execModuleScript(
            $programDirectory,
            $programFile,
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
