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

namespace WellCommerce\Bundle\AttributeBundle\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use WellCommerce\Bundle\AttributeBundle\Entity\Attribute;
use WellCommerce\Bundle\CoreBundle\Factory\AbstractFactory;

/**
 * Class AttributeFactory
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class AttributeFactory extends AbstractFactory
{
    /**
     * @return \WellCommerce\Bundle\AttributeBundle\Entity\AttributeInterface
     */
    public function create()
    {
        $attribute = new Attribute();
        $attribute->setValues(new ArrayCollection());
        $attribute->setGroups(new ArrayCollection());

        return $attribute;
    }
}
