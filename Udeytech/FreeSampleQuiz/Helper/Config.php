<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Config
 * @package Udeytech\FreeSampleQuiz\Helper
 */
class Config extends AbstractHelper
{
    /**
     *
     */
    const XPATH_FORMULA_GUIDE_HEADER_TEXT = 'freesamplequiz/cms_blocks/formula_guide_header_text';
    /**
     *
     */
    const XPATH_SKIN_TYPE_INFO_HEADER_TEXT = 'freesamplequiz/cms_blocks/skin_type_info_header_text';
    /**
     *
     */
    const XPATH_MAKEUP_TIP_HEADER_TEXT = 'freesamplequiz/cms_blocks/makeup_tip_header_text';
    /**
     *
     */
    const FINAL_TXT = 'freesamplequiz/general/quiz_final_text';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getFormulaGuideHeaderText()
    {
        return $this->scopeConfig->getValue(self::XPATH_FORMULA_GUIDE_HEADER_TEXT);
    }

    /**
     * @return mixed
     */
    public function getSkinTypeInfoHeaderText()
    {
        return $this->scopeConfig->getValue(self::XPATH_SKIN_TYPE_INFO_HEADER_TEXT);
    }

    /**
     * @return mixed
     */
    public function getMakeupTipHeaderText()
    {
        return $this->scopeConfig->getValue(self::XPATH_MAKEUP_TIP_HEADER_TEXT);
    }

    /**
     * @return mixed
     */
    public function getFinalTxt()
    {
        return $this->scopeConfig->getValue(SELF::FINAL_TXT);
    }
}

