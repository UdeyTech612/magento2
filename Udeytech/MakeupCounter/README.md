# Udeytech MakeupCounter

    ``udeytech/module-makeupcounter``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Developed By: Udey Technologies

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Udeytech`
 - Enable the module by running `php bin/magento module:enable Udeytech_MakeupCounter`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require udeytech/module-makeupcounter`
 - enable the module by running `php bin/magento module:enable Udeytech_MakeupCounter`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Block
	- Lookbook > lookbook.phtml

 - Block
	- Makeupcounter > makeupcounter.phtml

 - Block
	- Adminhtml\Closeups > adminhtml/closeups.phtml

 - Block
	- Adminhtml\Product > adminhtml/product.phtml

 - Block
	- Lookbook\Productinfo > lookbook/productinfo.phtml

 - Observer
	- makeupcounter_model_observer > Udeytech\MakeupCounter\Observer\Makeupcounter\ModelObserver

 - Controller
	- adminhtml > closeupscontroller/closeups/closeups

 - Controller
	- frontend > closeupscontroller/productinfo/index

 - Helper
	- Udeytech\MakeupCounter\Helper\Data


## Attributes

 - Product - Lookbook Day (belvg_lookbook_image)

 - Product - Related Kit SKU (makeupcounter_kit)

 - Product - Make Up Tips CMS Block (makeup_tips)

 - Product - Lookbook Image Position (belvg_lookbook_imagepos)

 - Product - Look type (look_type)

