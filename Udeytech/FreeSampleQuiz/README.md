# Udeytech FreeSampleQuiz

    ``udeytech/module-freesamplequiz``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Free Sample Quiz

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Udeytech`
 - Enable the module by running `php bin/magento module:enable Udeytech_FreeSampleQuiz`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require udeytech/module-freesamplequiz`
 - enable the module by running `php bin/magento module:enable Udeytech_FreeSampleQuiz`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - Free sample quiz (freesamplequiz/general/cms_freekit_page)

 - Quiz passed text (freesamplequiz/general/quiz_final_text)

 - Base categories (freesamplequiz/general/base_categories)

 - Free sample color categories (freesamplequiz/general/free_sample_color_categories)

 - Header of Formula Guide Block (freesamplequiz/cms_blocks/formula_guide_header_text)

 - Header of Skin Type Info Block (freesamplequiz/cms_blocks/skin_type_info_header_text)

 - makeup_tip_header_text (freesamplequiz/cms_blocks/makeup_tip_header_text)

 - FreeKit Already in Cart Message (freesamplequiz/total_customization/freekit_already_in_cart_message)





