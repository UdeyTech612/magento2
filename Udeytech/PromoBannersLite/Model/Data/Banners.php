<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\PromoBannersLite\Model\Data;
use Udeytech\PromoBannersLite\Api\Data\BannersInterface;

/**
 * Class Banners
 * @package Udeytech\PromoBannersLite\Model\Data
 */
class Banners extends \Magento\Framework\Api\AbstractExtensibleObject implements BannersInterface{
    /**
     * Get banners_id
     * @return string|null
     */
    public function getBannersId(){
        return $this->_get(self::BANNERS_ID);
    }
    /**
     * Set banners_id
     * @param string $bannersId
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setBannersId($bannersId){
        return $this->setData(self::BANNERS_ID, $bannersId);
    }
    /**
     * Get utbanner_id
     * @return string|null
     */
    public function getUtbannerId(){
        return $this->_get(self::UTBANNER_ID);
    }
    /**
     * Set utbanner_id
     * @param string $utbannerId
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannerId($utbannerId){
        return $this->setData(self::UTBANNER_ID, $utbannerId);
    }
    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersExtensionInterface|null
     */
    public function getExtensionAttributes(){
        return $this->_getExtensionAttributes();
    }
    /**
     * Set an extension attributes object.
     * @param \Udeytech\PromoBannersLite\Api\Data\BannersExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Udeytech\PromoBannersLite\Api\Data\BannersExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId()
    {
        return $this->_get(self::RULE_ID);
    }
    /**
     * Set rule_id
     * @param string $ruleId
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setRuleId($ruleId){
        return $this->setData(self::RULE_ID, $ruleId);
    }
    /**
     * Get utbannerslite_banner_type
     * @return string|null
     */
    public function getUtbannersliteBannerType()
    {
        return $this->_get(self::UTBANNERSLITE_BANNER_TYPE);
    }
    /**
     * Set utbannerslite_banner_type
     * @param string $utbannersliteBannerType
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteBannerType($utbannersliteBannerType)
    {
        return $this->setData(self::UTBANNERSLITE_BANNER_TYPE, $utbannersliteBannerType);
    }
    /**
     * Get utbannerslite_top_banner_enable
     * @return string|null
     */
    public function getUtbannersliteTopBannerEnable()
    {
        return $this->_get(self::UTBANNERSLITE_TOP_BANNER_ENABLE);
    }
    /**
     * Set utbannerslite_top_banner_enable
     * @param string $utbannersliteTopBannerEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerEnable($utbannersliteTopBannerEnable)
    {
        return $this->setData(self::UTBANNERSLITE_TOP_BANNER_ENABLE, $utbannersliteTopBannerEnable);
    }
    /**
     * Get utbannerslite_label_enable
     * @return string|null
     */
    public function getUtbannersliteLabelEnable()
    {
        return $this->_get(self::UTBANNERSLITE_LABEL_ENABLE);
    }
    /**
     * Set utbannerslite_label_enable
     * @param string $utbannersliteLabelEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteLabelEnable($utbannersliteLabelEnable)
    {
        return $this->setData(self::UTBANNERSLITE_LABEL_ENABLE, $utbannersliteLabelEnable);
    }
    /**
     * Get utbannerslite_after_name_banner_enable
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerEnable()
    {
        return $this->_get(self::UTBANNERSLITE_AFTER_NAME_BANNER_ENABLE);
    }
    /**
     * Set utbannerslite_after_name_banner_enable
     * @param string $utbannersliteAfterNameBannerEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerEnable($utbannersliteAfterNameBannerEnable)
    {
        return $this->setData(self::UTBANNERSLITE_AFTER_NAME_BANNER_ENABLE, $utbannersliteAfterNameBannerEnable);
    }
    /**
     * Get utbannerslite_banner_categories
     * @return string|null
     */
    public function getUtbannersliteBannerCategories()
    {
        return $this->_get(self::UTBANNERSLITE_BANNER_CATEGORIES);
    }
    /**
     * Set utbannerslite_banner_categories
     * @param string $utbannersliteBannerCategories
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteBannerCategories($utbannersliteBannerCategories)
    {
        return $this->setData(self::UTBANNERSLITE_BANNER_CATEGORIES, $utbannersliteBannerCategories);
    }
    /**
     * Get utbannerslite_banner_products
     * @return string|null
     */
    public function getUtbannersliteBannerProducts()
    {
        return $this->_get(self::UTBANNERSLITE_BANNER_PRODUCTS);
    }
    /**
     * Set utbannerslite_banner_products
     * @param string $utbannersliteBannerProducts
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteBannerProducts($utbannersliteBannerProducts)
    {
        return $this->setData(self::UTBANNERSLITE_BANNER_PRODUCTS, $utbannersliteBannerProducts);
    }
    /**
     * Get utbannerslite_top_banner_img
     * @return string|null
     */
    public function getUtbannersliteTopBannerImg()
    {
        return $this->_get(self::UTBANNERSLITE_TOP_BANNER_IMG);
    }
    /**
     * Set utbannerslite_top_banner_img
     * @param string $utbannersliteTopBannerImg
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerImg($utbannersliteTopBannerImg)
    {
        return $this->setData(self::UTBANNERSLITE_TOP_BANNER_IMG, $utbannersliteTopBannerImg);
    }
    /**
     * Get utbannerslite_top_banner_alt
     * @return string|null
     */
    public function getUtbannersliteTopBannerAlt()
    {
        return $this->_get(self::UTBANNERSLITE_TOP_BANNER_ALT);
    }
    /**
     * Set utbannerslite_top_banner_alt
     * @param string $utbannersliteTopBannerAlt
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerAlt($utbannersliteTopBannerAlt)
    {
        return $this->setData(self::UTBANNERSLITE_TOP_BANNER_ALT, $utbannersliteTopBannerAlt);
    }
    /**
     * Get utbannerslite_top_banner_hover_text
     * @return string|null
     */
    public function getUtbannersliteTopBannerHoverText()
    {
        return $this->_get(self::UTBANNERSLITE_TOP_BANNER_HOVER_TEXT);
    }
    /**
     * Set utbannerslite_top_banner_hover_text
     * @param string $utbannersliteTopBannerHoverText
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerHoverText($utbannersliteTopBannerHoverText)
    {
        return $this->setData(self::UTBANNERSLITE_TOP_BANNER_HOVER_TEXT, $utbannersliteTopBannerHoverText);
    }
    /**
     * Get utbannerslite_top_banner_link
     * @return string|null
     */
    public function getUtbannersliteTopBannerLink()
    {
        return $this->_get(self::UTBANNERSLITE_TOP_BANNER_LINK);
    }
    /**
     * Set utbannerslite_top_banner_link
     * @param string $utbannersliteTopBannerLink
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerLink($utbannersliteTopBannerLink)
    {
        return $this->setData(self::UTBANNERSLITE_TOP_BANNER_LINK, $utbannersliteTopBannerLink);
    }
    /**
     * Get utbannerslite_top_banner_gift_images_enable
     * @return string|null
     */
    public function getUtbannersliteTopBannerGiftImagesEnable()
    {
        return $this->_get(self::UTBANNERSLITE_TOP_BANNER_GIFT_IMAGES_ENABLE);
    }
    /**
     * Set utbannerslite_top_banner_gift_images_enable
     * @param string $utbannersliteTopBannerGiftImagesEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerGiftImagesEnable($utbannersliteTopBannerGiftImagesEnable)
    {
        return $this->setData(self::UTBANNERSLITE_TOP_BANNER_GIFT_IMAGES_ENABLE, $utbannersliteTopBannerGiftImagesEnable);
    }
    /**
     * Get utbannerslite_top_banner_description
     * @return string|null
     */
    public function getUtbannersliteTopBannerDescription()
    {
        return $this->_get(self::UTBANNERSLITE_TOP_BANNER_DESCRIPTION);
    }
    /**
     * Set utbannerslite_top_banner_description
     * @param string $utbannersliteTopBannerDescription
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteTopBannerDescription($utbannersliteTopBannerDescription)
    {
        return $this->setData(self::UTBANNERSLITE_TOP_BANNER_DESCRIPTION, $utbannersliteTopBannerDescription);
    }
    /**
     * Get utbannerslite_after_name_banner_img
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerImg()
    {
        return $this->_get(self::UTBANNERSLITE_AFTER_NAME_BANNER_IMG);
    }
    /**
     * Set utbannerslite_after_name_banner_img
     * @param string $utbannersliteAfterNameBannerImg
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerImg($utbannersliteAfterNameBannerImg)
    {
        return $this->setData(self::UTBANNERSLITE_AFTER_NAME_BANNER_IMG, $utbannersliteAfterNameBannerImg);
    }
    /**
     * Get utbannerslite_after_name_banner_alt
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerAlt()
    {
        return $this->_get(self::UTBANNERSLITE_AFTER_NAME_BANNER_ALT);
    }
    /**
     * Set utbannerslite_after_name_banner_alt
     * @param string $utbannersliteAfterNameBannerAlt
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerAlt($utbannersliteAfterNameBannerAlt)
    {
        return $this->setData(self::UTBANNERSLITE_AFTER_NAME_BANNER_ALT, $utbannersliteAfterNameBannerAlt);
    }
    /**
     * Get utbannerslite_after_name_banner_hover_text
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerHoverText()
    {
        return $this->_get(self::UTBANNERSLITE_AFTER_NAME_BANNER_HOVER_TEXT);
    }
    /**
     * Set utbannerslite_after_name_banner_hover_text
     * @param string $utbannersliteAfterNameBannerHoverText
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerHoverText($utbannersliteAfterNameBannerHoverText)
    {
        return $this->setData(self::UTBANNERSLITE_AFTER_NAME_BANNER_HOVER_TEXT, $utbannersliteAfterNameBannerHoverText);
    }

    /**
     * Get utbannerslite_after_name_banner_link
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerLink()
    {
        return $this->_get(self::UTBANNERSLITE_AFTER_NAME_BANNER_LINK);
    }

    /**
     * Set utbannerslite_after_name_banner_link
     * @param string $utbannersliteAfterNameBannerLink
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerLink($utbannersliteAfterNameBannerLink)
    {
        return $this->setData(self::UTBANNERSLITE_AFTER_NAME_BANNER_LINK, $utbannersliteAfterNameBannerLink);
    }

    /**
     * Get utbannerslite_after_name_banner_gift_images_enable
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerGiftImagesEnable()
    {
        return $this->_get(self::UTBANNERSLITE_AFTER_NAME_BANNER_GIFT_IMAGES_ENABLE);
    }

    /**
     * Set utbannerslite_after_name_banner_gift_images_enable
     * @param string $utbannersliteAfterNameBannerGiftImagesEnable
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerGiftImagesEnable(
        $utbannersliteAfterNameBannerGiftImagesEnable
    ) {
        return $this->setData(self::UTBANNERSLITE_AFTER_NAME_BANNER_GIFT_IMAGES_ENABLE, $utbannersliteAfterNameBannerGiftImagesEnable);
    }

    /**
     * Get utbannerslite_after_name_banner_description
     * @return string|null
     */
    public function getUtbannersliteAfterNameBannerDescription()
    {
        return $this->_get(self::UTBANNERSLITE_AFTER_NAME_BANNER_DESCRIPTION);
    }

    /**
     * Set utbannerslite_after_name_banner_description
     * @param string $utbannersliteAfterNameBannerDescription
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteAfterNameBannerDescription($utbannersliteAfterNameBannerDescription)
    {
        return $this->setData(self::UTBANNERSLITE_AFTER_NAME_BANNER_DESCRIPTION, $utbannersliteAfterNameBannerDescription);
    }

    /**
     * Get utbannerslite_label_img
     * @return string|null
     */
    public function getUtbannersliteLabelImg()
    {
        return $this->_get(self::UTBANNERSLITE_LABEL_IMG);
    }

    /**
     * Set utbannerslite_label_img
     * @param string $utbannersliteLabelImg
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteLabelImg($utbannersliteLabelImg)
    {
        return $this->setData(self::UTBANNERSLITE_LABEL_IMG, $utbannersliteLabelImg);
    }

    /**
     * Get utbannerslite_label_alt
     * @return string|null
     */
    public function getUtbannersliteLabelAlt()
    {
        return $this->_get(self::UTBANNERSLITE_LABEL_ALT);
    }

    /**
     * Set utbannerslite_label_alt
     * @param string $utbannersliteLabelAlt
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     */
    public function setUtbannersliteLabelAlt($utbannersliteLabelAlt)
    {
        return $this->setData(self::UTBANNERSLITE_LABEL_ALT, $utbannersliteLabelAlt);
    }
}

