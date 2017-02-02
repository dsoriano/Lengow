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
use Thelia\Core\HttpFoundation\Response;
use Lengow\Controller\Base\ExportControllerTrait;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Model\LangQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ExportController
 * @package Lengow\Controller
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class ExportController extends BaseFrontController
{
    use ExportControllerTrait;

    public function lengowExport($locale)
    {
        $requestedLang = LangQuery::create()->findOneByLocale($locale);

        if ($requestedLang === null || $requestedLang->getVisible() === 0) {
            throw new NotFoundHttpException('Url not found');
        }

        $this->lang = $requestedLang;

        ini_set("memory_limit",-1);
        return $this->buildExportDatas();
    }
}
