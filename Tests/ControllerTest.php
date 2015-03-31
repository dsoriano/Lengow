<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Lengow\Tests;

use Lengow\Controller\ExportController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Thelia\Tests\Controller\ControllerTestBase;

/**
 * Class ControllerTest
 * @package Lengow\Tests
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class ControllerTest extends ControllerTestBase
{
    /**
     * @var ExportController
     */
    protected $controller;

    protected $path;
    /**
     * Use this method to build the container with the services that you need.
     */
    protected function buildContainer(ContainerBuilder $container)
    {

    }
    protected function getController()
    {
        return new ExportController();
    }

    public function getPath()
    {
        return THELIA_CACHE_DIR . "tests";
    }

    public function setUp()
    {
        parent::setUp();

        $this->path = $path = $this->getPath();

        if (!((is_dir($path) || mkdir($path)) && is_writable($path))
            ||
            (file_exists($fooPath = $path . DS . "foo") && chmod($fooPath, 755) &&
                (
                    is_file($path) && !unlink($fooPath) ||
                    is_dir($fooPath) && !rmdir($fooPath)
                )
            )
        ) {
            $this->markTestSkipped("I need writing rights on path-to-thelia/cache to do this test !");
        }

    }

    public function testBuildPathSimpleMkdir()
    {
        $fullPath = $this->path . DS . "foo" . DS . "bar";
        $ret = $this->controller->buildPath($fullPath);

        $this->assertEquals($fullPath, $ret);
        $this->assertTrue(is_dir(dirname($fullPath)));
    }

    /**
     * @expectedException \Thelia\Exception\FileException
     */
    public function testBuildInNonWritable()
    {
        $fullPath = $this->path . DS . "foo" . DS . "bar";
        $ret = $this->controller->buildPath($fullPath);

        chmod(dirname($ret), 555);

        $this->controller->buildPath($fullPath);
    }
}
