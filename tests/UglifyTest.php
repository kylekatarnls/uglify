<?php

use NodejsPhpFallback\Uglify;

class UglifyTest extends PHPUnit_Framework_TestCase
{
    protected function getTempJs()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.min.js';
    }

    protected function getTempCss()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.min.css';
    }

    protected function writeJs($content)
    {
        return file_put_contents($this->getTempJs(), $content);
    }

    protected function writeCss($content)
    {
        return file_put_contents($this->getTempCss(), $content);
    }

    protected function getConsole()
    {
        $file = $this->getTempJs();
        $output = trim(str_replace("\r", '', shell_exec('node ' . escapeshellarg($file))));
        unlink($file);

        return $output;
    }

    protected function getExpectedJs()
    {
        return trim(str_replace("\r", '', file_get_contents(__DIR__ . '/test.log')));
    }

    protected function getExpectedCss()
    {
        return trim(str_replace("\r", '', file_get_contents(__DIR__ . '/test.min.css')));
    }

    public function testMode()
    {
        $uglify = new Uglify(__DIR__ . '/test.css');
        $this->assertSame('css', $uglify->getMode());
        $uglify = new Uglify(__DIR__ . '/test.js');
        $this->assertSame('js', $uglify->getMode());
        $uglify->cssMode();
        $this->assertSame('css', $uglify->getMode());
        $uglify->jsMode();
        $this->assertSame('js', $uglify->getMode());
    }

    public function testCompile()
    {
        $expected = $this->getExpectedJs();
        $uglify = new Uglify(array(
            __DIR__ . '/test.js',
            __DIR__ . '/test2.js',
        ));
        $this->writeJs($uglify);
        $log = $this->getConsole();

        $this->assertSame($expected, $log, 'Uglify should render with node.');
    }

    public function testWrite()
    {
        $expected = $this->getExpectedJs();
        $uglify = new Uglify(__DIR__ . '/test.js');
        $uglify->add(__DIR__ . '/test2.js');
        $this->writeJs($uglify->getMinifiedJs());
        $log = $this->getConsole();

        $this->assertSame($expected, $log, 'Uglify should render with node.');
    }

    public function testCompileCss()
    {
        $expected = $this->getExpectedCss();
        $uglify = new Uglify(__DIR__ . '/test.css');
        $uglify->add(__DIR__ . '/test2.css');
        $css = $uglify->getMinifiedCss();

        $this->assertSame($expected, $css, 'Uglify should render with node.');
    }

    public function testFallback()
    {
        $uglify = new Uglify(array(__DIR__ . '/test.js'));
        $uglify->add(__DIR__ . '/test2.js');
        $expected = $this->getExpectedJs();
        $this->writeJs($uglify->fallback());
        $log = $this->getConsole();

        $this->assertSame($expected, $log, 'Uglify should render without node.');
    }

    public function testFallbackWrite()
    {
        $uglify = new Uglify(array(__DIR__ . '/test.css'));
        $uglify->add(__DIR__ . '/test2.css');
        $expected = $this->getExpectedCss();
        $file = $this->getTempCss();
        $uglify->write($file);
        $css = file_get_contents($file);
        unlink($file);

        $this->assertSame($expected, $css, 'Uglify should render without node.');
        $this->assertSame($expected, $uglify->fallback(), 'Uglify should render without node.');
    }
}
