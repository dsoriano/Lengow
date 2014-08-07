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

namespace Lengow\Export;
use Thelia\Core\FileFormat\Formatting\Formatter\CSVFormatter;

/**
 * Class LengowFormatter
 * @package Lengow\Export
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class LengowFormatter extends CSVFormatter
{
    public $lineReturn = "\r\n";

    public function getName()
    {
        return "Lengow";
    }

    public function getHandledType()
    {
        return LengowType::LENGOW_EXPORT;
    }
}
