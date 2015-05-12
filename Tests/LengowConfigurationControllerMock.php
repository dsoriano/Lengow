<?php
/**
 * Created by PhpStorm.
 * User: ducher
 * Date: 12/05/15
 * Time: 14:40
 */

namespace Lengow\Tests;

use Lengow\Controller\LengowConfigurationController;
use Symfony\Component\DependencyInjection\Container;
use Thelia\Model\Map\ProductTableMap;

/**
 * LengowConfigurationController extended for testing LengowConfigurationController::doSearchProducts(); without
 * querying the database. Data depends a lot on the project where the module is used (random E-Commerce site based on
 * Thelia with its own data, Thelia's faker.php and Thelia's import.php). This mocked controller proposes its own datas
 * in order to not depend on Thelia's site data.
 * @package Lengow\Tests
 */
final class LengowConfigurationControllerMock extends LengowConfigurationController
{
    public function __construct()
    {
        parent::__construct(new Container());
    }

    /** @var array Data for tests */
    protected static $DATA = [
        [
            ProductTableMap::ID => 1,
            ProductTableMap::REF => 'Zlatan',
            ProductTableMap::BRAND_ID => 1,
            'brand_NAME' => 'IKEA',
            'category_ID' => 1,
            'rubric_TITLE' => 'ballon',
        ],
        [
            ProductTableMap::ID => 2,
            ProductTableMap::BRAND_ID => 2,
            'category_ID' => 2,
            'brand_NAME' => 'Sed',
            'rubric_TITLE' => 'scie',
            ProductTableMap::REF => 'Bow',
        ],
        [
            ProductTableMap::ID => 3,
            ProductTableMap::REF => 'Armür',
            ProductTableMap::BRAND_ID => 1,
            'brand_NAME' => 'IKEA',
            'category_ID' => 3,
            'rubric_TITLE' => 'meuble',
        ],
        [
            ProductTableMap::ID => 4,
            ProductTableMap::REF => 'Jeanne',
            ProductTableMap::BRAND_ID => 3,
            'brand_NAME' => 'Confo',
            'category_ID' => 3,
            'rubric_TITLE' => 'meuble',
        ],
        [
            ProductTableMap::ID => 5,
            ProductTableMap::REF => 'Jeannette',
            ProductTableMap::BRAND_ID => 3,
            'brand_NAME' => 'Confo',
            'category_ID' => 3,
            'rubric_TITLE' => 'meuble',
        ],
        [
            ProductTableMap::ID => 6,
            ProductTableMap::REF => 'Jeannette by IKEA',
            ProductTableMap::BRAND_ID => 1,
            'brand_NAME' => 'IKEA',
            'category_ID' => 4,
            'rubric_TITLE' => 'chaise',
        ],
        [
            ProductTableMap::ID => 7,
            ProductTableMap::REF => 'dith',
            ProductTableMap::BRAND_ID => 4,
            'brand_NAME' => 'Comcom',
            'category_ID' => 0,
            'rubric_TITLE' => '',
        ],
        [
            ProductTableMap::ID => 8,
            ProductTableMap::REF => 'Piaf',
            ProductTableMap::BRAND_ID => 0,
            'brand_NAME' => '',
            'category_ID' => 4,
            'rubric_TITLE' => 'chaise',
        ],
        [
            ProductTableMap::ID => 9,
            ProductTableMap::REF => 'Édith Piaf',
            ProductTableMap::BRAND_ID => 0,
            'brand_NAME' => '',
            'category_ID' => 0,
            'rubric_TITLE' => '',
        ],
    ];

    /**
     * public for the tests
     * @param $search
     * @return array
     */
    public function doSearchProducts($search, $maxres = 0)
    {
        return parent::doSearchProducts($search, $maxres);
    }

    /**
     * Mocked query method
     * @param $search
     */
    protected function doProductQuerySearch($search, $maxres)
    {
        $search_regex = sprintf("/%s/", preg_quote($search, '/'));
        $maxres = abs(intval($maxres));
        $res = array();

        for ($i = 0; $i < $maxres; $i++) {
            $product = static::$DATAS[$i];
            if (preg_match($search_regex, $product[ProductTableMap::REF])) {
                $res[] = $product;
            }
        }

        return $res;
    }
}
