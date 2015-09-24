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

namespace WellCommerce\Bundle\CartBundle\Manager\Front;

use WellCommerce\Bundle\CartBundle\Entity\CartInterface;
use WellCommerce\Bundle\CartBundle\Entity\CartProductInterface;
use WellCommerce\Bundle\CartBundle\Exception\AddCartItemException;
use WellCommerce\Bundle\CartBundle\Repository\CartRepositoryInterface;
use WellCommerce\Bundle\ClientBundle\Entity\ClientInterface;
use WellCommerce\Bundle\CoreBundle\Manager\Front\AbstractFrontManager;
use WellCommerce\Bundle\MultiStoreBundle\Entity\ShopInterface;
use WellCommerce\Bundle\ProductBundle\Entity\ProductAttributeInterface;
use WellCommerce\Bundle\ProductBundle\Entity\ProductInterface;

/**
 * Class CartManager
 *
 * @author Adam Piotrowski <adam@wellcommerce.org>
 */
class CartManager extends AbstractFrontManager implements CartManagerInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $repository;

    /**
     * @var CartProductManagerInterface
     */
    protected $cartProductManager;

    /**
     * @param CartProductManagerInterface $cartProductManager
     */
    public function setCartProductManager(CartProductManagerInterface $cartProductManager)
    {
        $this->cartProductManager = $cartProductManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addProductToCart(ProductInterface $product, ProductAttributeInterface $attribute = null, $quantity = 1)
    {
        try {
            $cart        = $this->getCurrentCart();
            $cartProduct = $this->cartProductManager->findProductInCart($cart, $product, $attribute);

            if (null === $cartProduct) {
                $cartProduct = $this->cartProductManager->initCartProduct($cart, $product, $attribute, $quantity);
                $cart->addProduct($cartProduct);
            } else {
                $cartProduct->increaseQuantity($quantity);
            }

            $cart->setShippingMethodCost(null);
            $cart->setPaymentMethod(null);
            $this->updateResource($cart);

        } catch (\Exception $e) {
            throw new AddCartItemException($product, $attribute, $quantity, $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCartProduct(CartProductInterface $cartProduct)
    {
        $cart = $this->getCurrentCart();
        $this->cartProductManager->removeResource($cartProduct);
        $cart->setShippingMethodCost(null);
        $cart->setPaymentMethod(null);
        $this->updateResource($cart);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function changeCartProductQuantity(CartProductInterface $cartProduct, $qty)
    {
        $cart = $this->getCurrentCart();
        $this->cartProductManager->changeCartProductQuantity($cartProduct, $qty);

        $cart->setShippingMethodCost(null);
        $cart->setPaymentMethod(null);
        $this->updateResource($cart);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeCart()
    {
        $requestHelper = $this->getRequestHelper();
        $sessionId     = $requestHelper->getSessionId();
        $client        = $requestHelper->getClient();
        $currency      = $requestHelper->getCurrentCurrency();
        $shop          = $this->getShopContext()->getCurrentScope();
        $cart          = $this->getCart($shop, $client, $sessionId, $currency);

        $cartProvider = $this->getCartProvider();
        $cartProvider->setCurrentCart($cart);

        return $cart;
    }

    /**
     * Returns an existent cart or creates a new one if needed
     *
     * @param ShopInterface        $shop
     * @param ClientInterface|null $client
     * @param string               $sessionId
     * @param string               $currency
     *
     * @return CartInterface
     */
    protected function getCart(ShopInterface $shop, ClientInterface $client = null, $sessionId, $currency)
    {
        $cart = $this->repository->findCart($client, $sessionId, $shop);

        if (null === $cart) {
            $cart = $this->createCart($shop, $client, $sessionId);
        } else {
            $this->updateCart($cart, $client, $currency);
        }

        return $cart;
    }

    /**
     * Updates client and/or currency if changed
     *
     * @param CartInterface        $cart
     * @param ClientInterface|null $client
     * @param string               $currency
     */
    protected function updateCart(CartInterface $cart, ClientInterface $client = null, $currency)
    {
        $needsUpdate = false;

        if (null !== $client && null === $cart->getClient()) {
            $cart->setClient($client);
            $needsUpdate = true;
        }

        if ($currency !== $cart->getCurrency()) {
            $cart->setCurrency($currency);
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $this->updateResource($cart);
        }
    }

    /**
     * Creates cart using factory
     *
     * @param ShopInterface        $shop
     * @param ClientInterface|null $client
     * @param string               $sessionId
     * @param string               $currency
     *
     * @return CartInterface
     */
    protected function createCart(ShopInterface $shop, ClientInterface $client = null, $sessionId, $currency)
    {
        $cart = $this->initResource();
        $cart->setShop($shop);
        $cart->setSessionId($sessionId);
        $cart->setCurrency($currency);

        if (null !== $client) {
            $cart->setClient($client);
        }

        $this->createResource($cart);

        return $cart;
    }
}
