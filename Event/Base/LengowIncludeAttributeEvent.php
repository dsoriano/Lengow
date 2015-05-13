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
use Lengow\Model\LengowIncludeAttribute;

/**
* Class LengowIncludeAttributeEvent
* @package Lengow\Event\Base
* @author TheliaStudio
*/
class LengowIncludeAttributeEvent extends ActionEvent
{
    protected $id;
    protected $attributeId;
    protected $lengowIncludeAttribute;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getAttributeId()
    {
        return $this->attributeId;
    }

    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;

        return $this;
    }

    public function getLengowIncludeAttribute()
    {
        return $this->lengowIncludeAttribute;
    }

    public function setLengowIncludeAttribute(LengowIncludeAttribute $lengowIncludeAttribute)
    {
        $this->lengowIncludeAttribute = $lengowIncludeAttribute;

        return $this;
    }
}
