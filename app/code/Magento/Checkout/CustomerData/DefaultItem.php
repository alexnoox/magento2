<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\CustomerData;

/**
 * Default item
 */
class DefaultItem extends AbstractItem
{
    /**
     * @var \Magento\Catalog\Model\Product\Image\View
     */
    protected $productImageView;

    /**
     * @var \Magento\Msrp\Helper\Data
     */
    protected $msrpHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Catalog\Helper\Product\ConfigurationPool
     */
    protected $configurationPool;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @param \Magento\Catalog\Model\Product\Image\View $productImageView
     * @param \Magento\Msrp\Helper\Data $msrpHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Image\View $productImageView,
        \Magento\Msrp\Helper\Data $msrpHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Checkout\Helper\Data $checkoutHelper
    ) {
        $this->configurationPool = $configurationPool;
        $this->productImageView = $productImageView;
        $this->msrpHelper = $msrpHelper;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetItemData()
    {
        $this->productImageView->init(
            $this->getProductForThumbnail(),
            'mini_cart_product_thumbnail',
            'Magento_Catalog'
        );
        return [
            'options' => $this->getOptionList(),
            'qty' => $this->item->getQty() * 1,
            'item_id' => $this->item->getId(),
            'configure_url' => $this->getConfigureUrl(),
            'is_visible_in_site_visibility' => $this->item->getProduct()->isVisibleInSiteVisibility(),
            'product_name' => $this->item->getProduct()->getName(),
            'product_url' => $this->getProductUrl(),
            'product_has_url' => $this->hasProductUrl(),
            'product_price' => $this->checkoutHelper->formatPrice($this->item->getCalculationPrice()),
            'product_image' => [
                'src' => $this->productImageView->getUrl(),
                'alt' => $this->productImageView->getLabel(),
                'width' => $this->productImageView->getWidth(),
                'height' => $this->productImageView->getHeight(),
            ],
            'canApplyMsrp' => $this->msrpHelper->isShowBeforeOrderConfirm($this->item->getProduct())
                && $this->msrpHelper->isMinimalPriceLessMsrp($this->item->getProduct()),
        ];
    }

    /**
     * Get list of all options for product
     *
     * @return array
     */
    protected function getOptionList()
    {
        return $this->configurationPool->getByProductType($this->item->getProductType())->getOptions($this->item);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProductForThumbnail()
    {
        return $this->getProduct();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        return $this->item->getProduct();
    }

    /**
     * Get item configure url
     *
     * @return string
     */
    protected function getConfigureUrl()
    {
        return $this->urlBuilder->getUrl(
            'checkout/cart/configure',
            ['id' => $this->item->getId(), 'product_id' => $this->item->getProduct()->getId()]
        );
    }

    /**
     * Check Product has URL
     *
     * @return bool
     */
    protected function hasProductUrl()
    {
        if ($this->item->getRedirectUrl()) {
            return true;
        }

        $product = $this->item->getProduct();
        $option = $this->item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        if ($product->isVisibleInSiteVisibility()) {
            return true;
        } else {
            if ($product->hasUrlDataObject()) {
                $data = $product->getUrlDataObject();
                if (in_array($data->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    protected function getProductUrl()
    {
        if ($this->item->getRedirectUrl()) {
            return $this->item->getRedirectUrl();
        }

        $product = $this->item->getProduct();
        $option = $this->item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $product->getUrlModel()->getUrl($product);
    }
}
