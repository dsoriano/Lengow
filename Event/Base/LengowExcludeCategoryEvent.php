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
use Lengow\Model\LengowExcludeCategory;

/**
* Class LengowExcludeCategoryEvent
* @package Lengow\Event\Base
* @author TheliaStudio
*/
class LengowExcludeCategoryEvent extends ActionEvent
{
    protected $id;
    protected $categoryId;
    protected $lengowExcludeCategory;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getLengowExcludeCategory()
    {
        return $this->lengowExcludeCategory;
    }

    public function setLengowExcludeCategory(LengowExcludeCategory $lengowExcludeCategory)
    {
        $this->lengowExcludeCategory = $lengowExcludeCategory;

        return $this;
    }
}
