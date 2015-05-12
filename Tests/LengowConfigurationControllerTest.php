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

class LengowConfigurationControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var LengowConfigurationControllerMock */
    protected $controller = null;

    public function setUp()
    {
        $this->controller = new LengowConfigurationControllerMock();
    }

    public function testFilters()
    {
        // Nominal case with Zlatan
        $this->assertEquals(
            [
                'brands' => [
                    'IKEA' => [
                        [ 'id' => 1, 'ref' => 'Zlatan', ],
                    ],
                ],
                'categories' => [
                    'ballon' => [
                        [ 'id' => 1, 'ref' => 'Zlatan', ],
                    ],
                ],
            ],
            $this->controller->doSearchProducts('Zlatan')
        );

        // Multiple responses with Jeanne
        $this->assertEquals(
            [
                'brands' => [
                    'IKEA' => [
                        [ 'id' => 6, 'ref' => 'Jeannette by IKEA', ],
                    ],
                    'Confo' => [
                        [ 'id' => 4, 'ref' => 'Jeanne', ],
                        [ 'id' => 5, 'ref' => 'Jeannette', ],
                    ],
                ],
                'categories' => [
                    'meuble' => [
                        [ 'id' => 4, 'ref' => 'Jeanne', ],
                        [ 'id' => 5, 'ref' => 'Jeannette', ],
                    ],
                    'chaise' => [
                        [ 'id' => 6, 'ref' => 'Jeannette by IKEA', ],
                    ],
                ],
            ],
            $this->controller->doSearchProducts('Jeanne')
        );

        // Empty search : all products
        $this->assertEquals(
            [
                'brands' => [
                    'IKEA' => [
                        [ 'id' => 1, 'ref' => 'Zlatan', ],
                        [ 'id' => 3, 'ref' => 'Armür', ],
                        [ 'id' => 6, 'ref' => 'Jeannette by IKEA', ],
                    ],
                    'Sed' => [
                        [ 'id' => 2, 'ref' => 'Bow', ],
                    ],
                    'Confo' => [
                        [ 'id' => 4, 'ref' => 'Jeanne', ],
                        [ 'id' => 5, 'ref' => 'Jeannette', ],
                    ],
                    'Comcom' => [
                        [ 'id' => 7, 'ref' => 'dith', ],
                    ],
                    [ 'id' => 8, 'ref' => 'Piaf', ],
                    [ 'id' => 9, 'ref' => 'Édith Piaf', ],
                ],
                'categories' => [
                    'ballon' => [
                        [ 'id' => 1, 'ref' => 'Zlatan', ],
                    ],
                    'scie' => [
                        [ 'id' => 2, 'ref' => 'Bow', ],
                    ],
                    'meuble' => [
                        [ 'id' => 3, 'ref' => 'Armür', ],
                        [ 'id' => 4, 'ref' => 'Jeanne', ],
                        [ 'id' => 5, 'ref' => 'Jeannette', ],
                    ],
                    'chaise' => [
                        [ 'id' => 6, 'ref' => 'Jeannette by IKEA', ],
                        [ 'id' => 8, 'ref' => 'Piaf', ],
                    ],
                    [ 'id' => 7, 'ref' => 'dith', ],
                    [ 'id' => 9, 'ref' => 'Édith Piaf', ],
                ],
            ],
            $this->controller->doSearchProducts('')
        );

        // No results
        $this->assertEquals(
            [
                'brands' => [],
                'categories' => [],
            ],
            $this->controller->doSearchProducts('fgvyjvkkjyfvuykjfj')
        );

        // No brands
        $this->assertEquals(
            [
                'brands' => [
                    [ 'id' => 8, 'ref' => 'Piaf', ],
                    [ 'id' => 9, 'ref' => 'Édith Piaf', ],
                ],
                'categories' => [
                    'chaise' => [
                        [ 'id' => 8, 'ref' => 'Piaf', ],
                    ],
                    [ 'id' => 9, 'ref' => 'Édith Piaf', ],
                ],
            ],
            $this->controller->doSearchProducts('Piaf')
        );

        // No categories
        $this->assertEquals(
            [
                'brands' => [
                    'Comcom' => [
                        [ 'id' => 7, 'ref' => 'dith', ],
                    ],
                    [ 'id' => 9, 'ref' => 'Édith Piaf', ],
                ],
                'categories' => [
                    [ 'id' => 7, 'ref' => 'dith', ],
                    [ 'id' => 9, 'ref' => 'Édith Piaf', ],
                ],
            ],
            $this->controller->doSearchProducts('dith')
        );

        // With a limit
        $this->assertEquals(
            [
                'brands' => [
                    'IKEA' => [
                        [ 'id' => 1, 'ref' => 'Zlatan', ],
                        [ 'id' => 3, 'ref' => 'Armür', ],
                    ],
                    'Sed' => [
                        [ 'id' => 2, 'ref' => 'Bow', ],
                    ],
                    'Confo' => [
                        [ 'id' => 4, 'ref' => 'Jeanne', ],
                    ],
                ],
                'categories' => [
                    'ballon' => [
                        [ 'id' => 1, 'ref' => 'Zlatan', ],
                    ],
                    'scie' => [
                        [ 'id' => 2, 'ref' => 'Bow', ],
                    ],
                    'meuble' => [
                        [ 'id' => 3, 'ref' => 'Armür', ],
                        [ 'id' => 4, 'ref' => 'Jeanne', ],
                    ],
                ],
            ],
            $this->controller->doSearchProducts('', 4)
        );
    }
}
