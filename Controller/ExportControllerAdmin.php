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

namespace Lengow\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Lengow\Controller\Base\BaseExportController;
use Lengow\Controller\Base\ExportControllerTrait;

/**
 * Class ExportController
 * @package Lengow\Controller
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class ExportControllerAdmin extends BaseExportController
{

    use ExportControllerTrait;

    public function lengowManualExport()
    {
        ini_set("memory_limit",-1);
        $this->lang = $this->getSession()->getLang();
        $this->buildExportDatas();
        $response = new BinaryFileResponse($this->getLengowFileInfo());
        $response->headers->set('Content-Type', 'text/csv');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'lengow.csv'
        );
        return $response;
    }


}
