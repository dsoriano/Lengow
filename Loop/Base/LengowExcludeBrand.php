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

namespace Lengow\Loop\Base;

use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;
use Lengow\Model\LengowExcludeBrandQuery;

/**
 * Class LengowExcludeBrand
 * @package Lengow\Loop\Base
 * @author TheliaStudio
 */
class LengowExcludeBrand extends BaseLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \Lengow\Model\LengowExcludeBrand $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);

            $row
                ->set("ID", $entry->getId())
                ->set("BRAND_ID", $entry->getBrandId())
            ;

            $this->addMoreResults($row, $entry);

            $loopResult->addRow($row);
        }

        return $loopResult;
    }

    /**
     * Definition of loop arguments
     *
     * example :
     *
     * public function getArgDefinitions()
     * {
     *  return new ArgumentCollection(
     *
     *       Argument::createIntListTypeArgument('id'),
     *           new Argument(
     *           'ref',
     *           new TypeCollection(
     *               new Type\AlphaNumStringListType()
     *           )
     *       ),
     *       Argument::createIntListTypeArgument('category'),
     *       Argument::createBooleanTypeArgument('new'),
     *       ...
     *   );
     * }
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument("id"),
            Argument::createIntListTypeArgument("brand_id"),
            Argument::createEnumListTypeArgument(
                "order",
                [
                    "id",
                    "id-reverse",
                    "brand_id",
                    "brand_id-reverse",
                ],
                "id"
            )
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = new LengowExcludeBrandQuery();

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null !== $brand_id = $this->getBrandId()) {
            $query->filterByBrandId($brand_id);
        }

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case "id":
                    $query->orderById();
                    break;
                case "id-reverse":
                    $query->orderById(Criteria::DESC);
                    break;
                case "brand_id":
                    $query->orderByBrandId();
                    break;
                case "brand_id-reverse":
                    $query->orderByBrandId(Criteria::DESC);
                    break;
            }
        }

        return $query;
    }

    protected function addMoreResults(LoopResultRow $row, $entryObject)
    {
    }
}
