<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\Resource\Theme\Data;

/**
 * Theme data collection
 */
class Collection extends \Magento\Theme\Model\Resource\Theme\Collection implements
    \Magento\Framework\View\Design\Theme\Label\ListInterface,
    \Magento\Framework\View\Design\Theme\ListInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Magento\Theme\Model\Theme\Data', 'Magento\Theme\Model\Resource\Theme');
    }
}
