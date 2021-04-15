<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\PromoBannersLite\Api\Data;

/**
 * Interface BannersInterface
 * @package Udeytech\PromoBannersLite\Api\Data
 */
interface BannersInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     *
     */
    const UTBANNERSLITE_AFTER_NAME_BANNER_DESCRIPTION = 'utbannerslite_after_name_banner_description';
    /**
     *
     */
    const UTBANNERSLITE_BANNER_CATEGORIES = 'utbannerslite_banner_categories';
    /**
     *
     */
    const UTBANNER_ID = 'utbanner_id';
    /**
     *
     */
    const UTBANNERSLITE_AFTER_NAME_BANNER_IMG = 'utbannerslite_after_name_banner_img';
    /**
     *
     */
    const UTBANNERSLITE_BANNER_PRODUCTS = 'utbannerslite_banner_products';
    /**
     *
     */
    const UTBANNERSLITE_TOP_BANNER_ALT = 'utbannerslite_top_banner_alt';
    /**
     *
     */
    const UTBANNERSLITE_AFTER_NAME_BANNER_LINK = 'utbannerslite_after_name_banner_link';
    /**
     *
     */
    const UTBANNERSLITE_LABEL_IMG = 'utbannerslite_label_img';
    /**
     *
     */
    const UTBANNERSLITE_LABEL_ENABLE = 'utbannerslite_label_enable';
    /**
     *
     */
    const UTBANNERSLITE_TOP_BANNER_IMG = 'utbannerslite_top_banner_img';
    /**
     *
     */
    const UTBANNERSLITE_BANNER_TYPE = 'utbannerslite_banner_type';
    /**
     *
     */
    const BANNERS_ID = 'banners_id';
    /**
     *
     */
    const UTBANNERSLITE_AFTER_NAME_BANNER_ALT = 'utbannerslite_after_name_banner_alt';
    /**
     *
     */
    const UTBANNERSLITE_AFTER_NAME_BANNER_HOVER_TEXT = 'utbannerslite_after_name_banner_hover_text';
    /**
     *
     */
    const UTBANNERSLITE_AFTER_NAME_BANNER_GIFT_IMAGES_ENABLE = 'utbannerslite_after_name_banner_gift_images_enable';
    /**
     *
     */
    const UTBANNERSLITE_AFTER_NAME_BANNER_ENABLE = 'utbannerslite_after_name_banner_enable';
    /**
     *
     */
    const UTBANNERSLITE_TOP_BANNER_LINK = 'utbannerslite_top_banner_link';
    /**
     *
     */
    const UTBANNERSLITE_TOP_BANNER_HOVER_TEXT = 'utbannerslite_top_banner_hover_text';
    /**
     *
     */
    const RULE_ID = 'rule_id';
    /**
     *
     */
    const UTBANNERSLITE_LABEL_ALT = 'utbannerslite_label_alt';
    /**
     *
     */
    const UTBANNERSLITE_TOP_BANNER_ENABLE = 'utbannerslite_top_banner_enable';
    /**
     *
     */
    const UTBANNERSLITE_TOP_BANNER_GIFT_IMAGES_ENABLE = 'utbannerslite_top_banner_gift_images_enable';
    /**
     *
     */
    const UTBANNERSLITE_TOP_BANNER_DESCRIPTION = 'utbannerslite_top_banner_description';

    /**
     * Get banners_id
     * @return string|null
     */
    public function getBannersId();

    /**
     * Set banners_id
     * @param string $bannersId
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setBannersId($bannersId);

    /**
     * Get utbanner_id
     * @return string|null
     */
    public function getUtbannerId();

    /**
     * Set utbanner_id
     * @param string $utbannerId
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannerId($utbannerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Udeytech\PromoBannersLite\Api\Data\BannersExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Udeytech\PromoBannersLite\Api\Data\BannersExtensionInterface $extensionAttributes
    );

    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $ruleId
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setRuleId($ruleId);

    /**
     * Get utbannerslite_banner_type
     * @return string|null
     */
    public function getUtbannersliteBannerType();

    /**
     * Set utbannerslite_banner_type
     * @param string $utbannersliteBannerType
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteBannerType($utbannersliteBannerType);

    /**
     * Get utbannerslite_top_banner_enable
     * @return string|null
     */
    public function getUtbannersliteTopBannerEnable();

    /**
     * Set utbannerslite_top_banner_enable
     * @param string $utbannersliteTopBannerEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerEnable($utbannersliteTopBannerEnable);

    /**
     * Get utbannerslite_label_enable
     * @return string|null
     */
    public function getUtbannersliteLabelEnable();

    /**
     * Set utbannerslite_label_enable
     * @param string $utbannersliteLabelEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteLabelEnable($utbannersliteLabelEnable);

    /**
     * Get utbannerslite_after_name_banner_enable
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerEnable();

    /**
     * Set utbannerslite_after_name_banner_enable
     * @param string $utbannersliteAfterNameBannerEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerEnable($utbannersliteAfterNameBannerEnable);

    /**
     * Get utbannerslite_banner_categories
     * @return string|null
     */
    public function getUtbannersliteBannerCategories();

    /**
     * Set utbannerslite_banner_categories
     * @param string $utbannersliteBannerCategories
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteBannerCategories($utbannersliteBannerCategories);

    /**
     * Get utbannerslite_banner_products
     * @return string|null
     */
    public function getUtbannersliteBannerProducts();

    /**
     * Set utbannerslite_banner_products
     * @param string $utbannersliteBannerProducts
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteBannerProducts($utbannersliteBannerProducts);

    /**
     * Get utbannerslite_top_banner_img
     * @return string|null
     */
    public function getUtbannersliteTopBannerImg();

    /**
     * Set utbannerslite_top_banner_img
     * @param string $utbannersliteTopBannerImg
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerImg($utbannersliteTopBannerImg);

    /**
     * Get utbannerslite_top_banner_alt
     * @return string|null
     */
    public function getUtbannersliteTopBannerAlt();

    /**
     * Set utbannerslite_top_banner_alt
     * @param string $utbannersliteTopBannerAlt
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerAlt($utbannersliteTopBannerAlt);

    /**
     * Get utbannerslite_top_banner_hover_text
     * @return string|null
     */
    public function getUtbannersliteTopBannerHoverText();

    /**
     * Set utbannerslite_top_banner_hover_text
     * @param string $utbannersliteTopBannerHoverText
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerHoverText($utbannersliteTopBannerHoverText);

    /**
     * Get utbannerslite_top_banner_link
     * @return string|null
     */
    public function getUtbannersliteTopBannerLink();

    /**
     * Set utbannerslite_top_banner_link
     * @param string $utbannersliteTopBannerLink
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerLink($utbannersliteTopBannerLink);

    /**
     * Get utbannerslite_top_banner_gift_images_enable
     * @return string|null
     */
    public function getUtbannersliteTopBannerGiftImagesEnable();

    /**
     * Set utbannerslite_top_banner_gift_images_enable
     * @param string $utbannersliteTopBannerGiftImagesEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerGiftImagesEnable($utbannersliteTopBannerGiftImagesEnable);

    /**
     * Get utbannerslite_top_banner_description
     * @return string|null
     */
    public function getUtbannersliteTopBannerDescription();

    /**
     * Set utbannerslite_top_banner_description
     * @param string $utbannersliteTopBannerDescription
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerDescription($utbannersliteTopBannerDescription);

    /**
     * Get utbannerslite_after_name_banner_img
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerImg();

    /**
     * Set utbannerslite_after_name_banner_img
     * @param string $utbannersliteAfterNameBannerImg
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerImg($utbannersliteAfterNameBannerImg);

    /**
     * Get utbannerslite_after_name_banner_alt
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerAlt();

    /**
     * Set utbannerslite_after_name_banner_alt
     * @param string $utbannersliteAfterNameBannerAlt
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerAlt($utbannersliteAfterNameBannerAlt);

    /**
     * Get utbannerslite_after_name_banner_hover_text
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerHoverText();

    /**
     * Set utbannerslite_after_name_banner_hover_text
     * @param string $utbannersliteAfterNameBannerHoverText
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerHoverText($utbannersliteAfterNameBannerHoverText);

    /**
     * Get utbannerslite_after_name_banner_link
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerLink();

    /**
     * Set utbannerslite_after_name_banner_link
     * @param string $utbannersliteAfterNameBannerLink
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerLink($utbannersliteAfterNameBannerLink);

    /**
     * Get utbannerslite_after_name_banner_gift_images_enable
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerGiftImagesEnable();

    /**
     * Set utbannerslite_after_name_banner_gift_images_enable
     * @param string $utbannersliteAfterNameBannerGiftImagesEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerGiftImagesEnable(
        $utbannersliteAfterNameBannerGiftImagesEnable
    );

    /**
     * Get utbannerslite_after_name_banner_description
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerDescription();

    /**
     * Set utbannerslite_after_name_banner_description
     * @param string $utbannersliteAfterNameBannerDescription
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerDescription($utbannersliteAfterNameBannerDescription);

    /**
     * Get utbannerslite_label_img
     * @return string|null
     */
    public function getUtbannersliteLabelImg();

    /**
     * Set utbannerslite_label_img
     * @param string $utbannersliteLabelImg
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteLabelImg($utbannersliteLabelImg);

    /**
     * Get utbannerslite_label_alt
     * @return string|null
     */
    public function getUtbannersliteLabelAlt();

    /**
     * Set utbannerslite_label_alt
     * @param string $utbannersliteLabelAlt
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteLabelAlt($utbannersliteLabelAlt);
}

