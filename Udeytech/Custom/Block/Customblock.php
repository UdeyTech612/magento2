<?php
namespace Udeytech\Custom\Block;
class  Customblock extends \Magento\Framework\View\Element\Template
{    
    protected $_categoryFactory;
    protected $_category;
    protected $_categoryHelper;

    protected $_categoryRepository;
    protected $category;
    protected $_registry;
    protected $imageHelper;
    protected $productRepository;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\Category $category, 
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Block\Product\ListProduct $listBlock,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    )
    {
        $this->_categoryFactory = $categoryFactory;
        $this->_category = $category;
        $this->_registry = $registry;
        $this->listBlock = $listBlock;
        $this->imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

public function getItemImage($productId)
{
    try {
        $_product = $this->productRepository->getById($productId);
    } catch (NoSuchEntityException $e) {
        return 'product not found';
    }
    $image_url = $this->imageHelper->init($_product, 'product_base_image')->getUrl();
    return $image_url;
}
    
    public function getAddtoCartUrl($product)
{
    return $this->listBlock->getAddToCartUrl($product);
}

    public function getCategoryProducts($categoryId) 
    {
        $products = $this->getCategory($categoryId)->getProductCollection();
        $products->addAttributeToSelect('*');
        return $products;
    }
    public function getCategoryImage($categoryId) 
    {
     return $this->_category->load($categoryId);       
    }
    public function getCategory($categoryId) 
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);        
        return $this->_category;
    }
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

}
?>
