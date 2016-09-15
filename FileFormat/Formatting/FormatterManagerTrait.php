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

namespace Lengow\FileFormat\Formatting;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait FormatterManagerTrait
 * @package Lengow\FileFormat\Formatter
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
trait FormatterManagerTrait
{
    /**
     * @param  ContainerInterface                                  $container
     * @return \Lengow\FileFormat\Formatting\FormatterManager
     */
    public function getFormatterManager(ContainerInterface $container)
    {
        return $container->get("thelia.manager.formatter_manager");
    }
}
