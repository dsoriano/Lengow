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

namespace Lengow\Event\Base;

use Thelia\Core\Event\ActionEvent;
use Lengow\Model\LengowExcludeProduct;

/**
* Class LengowExcludeProductEvent
* @package Lengow\Event\Base
* @author TheliaStudio
*/
class LengowExcludeProductEvent extends ActionEvent
{
    protected $id;
    protected $productId;
    protected $lengowExcludeProduct;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    public function getLengowExcludeProduct()
    {
        return $this->lengowExcludeProduct;
    }

    public function setLengowExcludeProduct(LengowExcludeProduct $lengowExcludeProduct)
    {
        $this->lengowExcludeProduct = $lengowExcludeProduct;

        return $this;
    }
}
