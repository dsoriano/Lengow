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

namespace Lengow;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;

class Lengow extends BaseModule
{
    const MESSAGE_DOMAIN = "lengow";

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        $database->insertSql(null, [__DIR__ . "/Config/insert.sql"]);
    }
}
