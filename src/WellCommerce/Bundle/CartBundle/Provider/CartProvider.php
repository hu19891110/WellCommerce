<?php
/*
 * WellCommerce Open-Source E-Commerce Platform
 * 
 * This file is part of the WellCommerce package.
 *
 * (c) Adam Piotrowski <adam@wellcommerce.org>
 * 
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace WellCommerce\Bundle\CartBundle\Provider;

use WellCommerce\Bundle\CartBundle\Entity\CartInterface;
use WellCommerce\Bundle\CoreBundle\Provider\AbstractProvider;

/**
 * Class CartProvider
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class CartProvider extends AbstractProvider implements CartProviderInterface
{
    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * {@inheritdoc}
     */
    public function getCurrentCart()
    {
        return $this->cart;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentCart(CartInterface $cart)
    {
        $this->cart = $cart;
    }
}
