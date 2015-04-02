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
use Lengow\Model\LengowExcludeBrand;

/**
* Class LengowExcludeBrandEvent
* @package Lengow\Event\Base
* @author TheliaStudio
*/
class LengowExcludeBrandEvent extends ActionEvent
{
    protected $id;
    protected $brandId;
    protected $lengowExcludeBrand;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getBrandId()
    {
        return $this->brandId;
    }

    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;

        return $this;
    }

    public function getLengowExcludeBrand()
    {
        return $this->lengowExcludeBrand;
    }

    public function setLengowExcludeBrand(LengowExcludeBrand $lengowExcludeBrand)
    {
        $this->lengowExcludeBrand = $lengowExcludeBrand;

        return $this;
    }
}
