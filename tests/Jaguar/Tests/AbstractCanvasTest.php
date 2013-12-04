<?php

/*
 * This file is part of the Jaguar package.
 *
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jaguar\Tests;

use Jaguar\Tests\JaguarTestCase;
use Jaguar\Tests\Mock\CanvasMock;
use Jaguar\Dimension;
use Jaguar\Color\RGBColor;

abstract class AbstractCanvasTest extends JaguarTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetHandlerThrowInvalidArgumentException()
    {
        $this->getCanvas()->setHandler('invalid gd resource');
    }

    public function testSetHandlerCanConvertPallete()
    {
        $c = $this->getCanvas()->fromFile($this->getPalleteFile());
        $this->assertTrue($c->isTrueColor());
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasException
     */
    public function testAlphaBlendingThrowCanvasException()
    {
        $c = new CanvasMock();
        $c->alphaBlending(true);
    }

    public function testAlphaBlending()
    {
        $c = $this->getCanvas();
        $this->assertSame($c, $c->alphaBlending(true));
        $this->assertSame($c, $c->alphaBlending(false));
    }

    public function testGetCopy()
    {
        $c = $this->getCanvas();
        $copy = $c->getCopy();

        $this->assertInstanceOf(get_class($c), $copy);
        $this->assertNotSame($c, $copy);
        $this->assertNotSame($c->getHandler(), $copy->getHandler());
        $this->assertTrue($c->getDimension()->equals($copy->getDimension()));
    }

    /**
     * @expectedException \Jaguar\Exception\InvalidDimensionException
     */
    public function testCreateInvalidDimensionException()
    {
        $this->getCanvas()->create(new Dimension(0, 0));
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasCreationException
     */
    public function testCreateThrowCanvasCreationException()
    {
        $this->getCanvas()->create(new Dimension(
                500000000000
                , 5000000000000
        ));
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasCreationException
     */
    public function testFromStringThrowCanvasException()
    {
        $this->getCanvas()->fromString('Invalid Canvas String');
    }

    public function testFromString()
    {
        $phpRules = base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
                . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
                . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
                . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg=='
        );

        $c = $this->getCanvas()->fromString($phpRules);

        $this->assertTrue($c->isHandlerSet());
        $this->assertGreaterThan(0, $c->getWidth());
        $this->assertGreaterThan(0, $c->getHeight());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromFileThrowInvalidArgumentException()
    {
        $this->getCanvas()->fromFile('non readable file');
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasCreationException
     */
    public function testFromFileThrowCanvasCreationException()
    {
        $this->getCanvas()->fromFile($this->getInvalidCanvasFile());
    }

    public function testFromFile()
    {
        $c = $this->getCanvas()->fromFile($this->getCanvasFile());
        $this->assertTrue($c->isHandlerSet());
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasException
     */
    public function testFillThrowCanvasException()
    {
        $c = new CanvasMock();
        $c->fill(new RGBColor());
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasException
     */
    public function testPasteThrowCanvasException()
    {
        $c = new CanvasMock();
        $c2 = new CanvasMock();

        $c->paste($c2);
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasEmptyException
     */
    public function testPasteThrowCanvasEmptyExceptionWhenSrcHandlerEmpty()
    {
        $this->getCanvas()->paste(new Mock\EmptyCanvasMock());
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasOutputException
     */
    public function testSaveThrowResourceOutputException()
    {
        $this->getCanvas()->save('"////\\\\"');
    }

    /**
     * @expectedException \Jaguar\Exception\CanvasEmptyException
     */
    public function testSaveAndCanvasEmptyException()
    {
        $c = new Mock\EmptyCanvasMock();
        $c->save('will no be saved');
    }

    public function testSave()
    {
        $path = sys_get_temp_dir() . '/tesSave.canvas';

        if (file_exists($path)) {
            unlink($path);
        }

        $this->getCanvas()->save($path);

        $this->assertFileExists($path);

        unlink($path);
    }

    public function testToString()
    {
        $this->assertInternalType('string', (string) $this->getCanvas());
    }

    /**
     * Get canvas
     * @return \Jaguar\CanvasInterface
     */
    abstract protected function getCanvas();

    /**
     * Get pallete file to test if pallete can be converted to truecolor
     * @return string file's path
     */
    abstract protected function getPalleteFile();

    /**
     * Get normal canvas file to test <tt>fromFile</tt> method
     * @return string file's path
     */
    abstract protected function getCanvasFile();

    /**
     * Get invalid(damaged) file to test that fromFile can fail on this kind
     * of files
     *
     * @return string file's path
     */
    abstract protected function getInvalidCanvasFile();
}