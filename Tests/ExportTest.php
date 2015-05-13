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

use Lengow\Export\LengowExport;
use Symfony\Component\DependencyInjection\Container;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CategoryQuery;
use Thelia\Tools\URL;

/**
 * Class ExportTest
 * @package Lengow\Tests
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class ExportTest extends \PHPUnit_Framework_TestCase
{
    /** @var LengowExport $handler */
    protected $handler;

    public function setUp()
    {
        $container = new Container();

        new URL($container);
        new Translator($container);
        $this->handler = new LengowExport($container);
    }

    public function testTreeBuilding()
    {
        /**
         * Benchmarked 10 times faster as the testing code :D
         */
        $tree = $this->handler->generateTree("en_US");

        foreach ($tree as $id => $generatedBreadcrumb) {
            $category = CategoryQuery::create()->findPk($id);

            $breadcrumb = [$category->setLocale("en_US")->getTitle()] ;

            while (null !== $category = CategoryQuery::create()->findPk($category->getParent())) {
                array_unshift($breadcrumb, $category->setLocale("en_US")->getTitle());
            }

            $this->assertEquals(
                implode(" > ", $breadcrumb),
                $generatedBreadcrumb
            );
        }
    }
}
