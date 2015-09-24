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

namespace WellCommerce\Bundle\ProductBundle\Entity;

/**
 * Interface ProductAttributeAwareInterface
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
interface ProductAttributeAwareInterface
{
    /**
     * @param ProductAttributeInterface $productAttribute
     */
    public function setProductAttribute(ProductAttributeInterface $productAttribute);

    /**
     * @return ProductAttributeInterface
     */
    public function getProductAttribute();
}
