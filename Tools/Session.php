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

namespace Lengow\Tools;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Thelia\Core\HttpFoundation\Session\Session as BaseSession;

/**
 * Class Session
 * @package Lengow\Tools
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class Session extends BaseSession
{
    public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
    {
        $storage = new MockArraySessionStorage();

        parent::__construct($storage, $attributes, $flashes);
    }

    /**
     * @var \Thelia\Model\Cart
     */
    protected $cart;

    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    public function getCart()
    {
        return $this->cart;
    }
}
