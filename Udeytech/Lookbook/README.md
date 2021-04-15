# Mage2 Module Udeytech Lookbook

    ``udeytech/module-lookbook``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Developed By : Udey Technology India Pvt. Ltd.

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Udeytech`
 - Enable the module by running `php bin/magento module:enable Udeytech_Lookbook`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require udeytech/module-lookbook`
 - enable the module by running `php bin/magento module:enable Udeytech_Lookbook`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - info (lookbook/info/info)

 - enabled (lookbook/settings/enabled)

 - divider_settings_1 (lookbook/settings/divider_settings_1)

 - marker (lookbook/settings/marker)

 - tooltip (lookbook/settings/tooltip)

 - mapster (lookbook/settings/mapster)

 - divider_settings_2 (lookbook/settings/divider_settings_2)

 - img_position (lookbook/settings/img_position)

 - title_marker (lookbook/styles/title_marker)

 - marker_image (lookbook/styles/marker_image)

 - marker_image_width (lookbook/styles/marker_image_width)

 - marker_image_height (lookbook/styles/marker_image_height)

 - title_marker_desc (lookbook/styles/title_marker_desc)

 - marker_border_width (lookbook/styles/marker_border_width)

 - marker_border_radius (lookbook/styles/marker_border_radius)

 - marker_border_color (lookbook/styles/marker_border_color)

 - title_divider_10 (lookbook/styles/title_divider_10)

 - marker_text_padding (lookbook/styles/marker_text_padding)

 - marker_text_color (lookbook/styles/marker_text_color)

 - marker_font (lookbook/styles/marker_font)

 - marker_text_shadow (lookbook/styles/marker_text_shadow)

 - title_divider_11 (lookbook/styles/title_divider_11)

 - bg_marker_opacity (lookbook/styles/bg_marker_opacity)

 - bg_marker_color (lookbook/styles/bg_marker_color)

 - title_divider_12 (lookbook/styles/title_divider_12)

 - marker_speed (lookbook/styles/marker_speed)

 - title_tooltip (lookbook/styles/title_tooltip)

 - tooltip_bg_image (lookbook/styles/tooltip_bg_image)

 - tooltip_bg_image_width (lookbook/styles/tooltip_bg_image_width)

 - tooltip_bg_image_height (lookbook/styles/tooltip_bg_image_height)

 - tooltip_position (lookbook/styles/tooltip_position)

 - tooltip_hor_offset (lookbook/styles/tooltip_hor_offset)

 - tooltip_text_color (lookbook/styles/tooltip_text_color)

 - tooltip_font (lookbook/styles/tooltip_font)

 - tooltip_text_shadow (lookbook/styles/tooltip_text_shadow)

 - tooltip_padding_top (lookbook/styles/tooltip_padding_top)

 - title_popup (lookbook/styles/title_popup)

 - popup_background_color (lookbook/styles/popup_background_color)

 - popup_duration (lookbook/styles/popup_duration)

 - title_divider_70 (lookbook/styles/title_divider_70)

 - popup_overlay_color (lookbook/styles/popup_overlay_color)

 - title_divider_80 (lookbook/styles/title_divider_80)

 - popup_border_color (lookbook/styles/popup_border_color)

 - popup_border_width (lookbook/styles/popup_border_width)

 - popup_border_radius (lookbook/styles/popup_border_radius)

 - popup_padding (lookbook/styles/popup_padding)

 - popup_shadow (lookbook/styles/popup_shadow)

 - title_divider_90 (lookbook/styles/title_divider_90)

 - popup_close (lookbook/styles/popup_close)

 - popup_close_image (lookbook/styles/popup_close_image)

 - title_divider_95 (lookbook/styles/title_divider_95)

 - popup_image_width (lookbook/styles/popup_image_width)

 - popup_image_height (lookbook/styles/popup_image_height)

 - title_divider_100 (lookbook/styles/title_divider_100)

 - popup_name (lookbook/styles/popup_name)

 - popup_name_font_color (lookbook/styles/popup_name_font_color)

 - popup_name_font (lookbook/styles/popup_name_font)

 - title_divider_200 (lookbook/styles/title_divider_200)

 - popup_price (lookbook/styles/popup_price)

 - popup_price_font_color (lookbook/styles/popup_price_font_color)

 - popup_price_font (lookbook/styles/popup_price_font)

 - title_mapster (lookbook/styles/title_mapster)

 - mapster_fill_opacity (lookbook/styles/mapster_fill_opacity)

 - mapster_fill_color (lookbook/styles/mapster_fill_color)

 - mapster_stroke_color (lookbook/styles/mapster_stroke_color)

 - mapster_stroke_opacity (lookbook/styles/mapster_stroke_opacity)

 - mapster_stroke_width (lookbook/styles/mapster_stroke_width)

 - mapster_fade (lookbook/styles/mapster_fade)

 - mapster_fade_duration (lookbook/styles/mapster_fade_duration)

 - title_settings (lookbook/advanced/title_settings)

 - export (lookbook/advanced/export)

 - import (lookbook/advanced/import)

 - reset (lookbook/advanced/reset)

 - title_customcss (lookbook/advanced/title_customcss)

 - customcss (lookbook/advanced/customcss)


## Specifications

 - Helper
	- Udeytech\Lookbook\Helper\Data

 - Controller
	- adminhtml > udeytech_lookbook/index/index


## Attributes

 - Product - udeytech_lookbook_imagepos (udeytech_lookbook_imagepos)

 - Product - udeytech_lookbook_image (udeytech_lookbook_image)

