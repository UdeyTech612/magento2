<?php
namespace Udeytech\FreeSamplePage\Controller\Ajaxpostb;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Ajpostb
 * @package Udeytech\FreeSamplePage\Controller\Ajaxpostb
 */
class Ajpostb extends Action
{
    /**
     *
     */
    const BUNDLE_SELECTION_TABLE = "catalog_product_bundle_selection";
    protected $cart;
    protected $formKey;
    protected $productFactory;
    protected $resultPageFactory;
    private $resourceConnection;
    public function __construct(
        Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        PageFactory $resultPageFactory,
        ProductFactory $productFactory,
        ResourceConnection $resourceConnection,
        Cart $cart){
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->formKey = $formKey;
        $this->productFactory = $productFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }
    public function execute(){
        $post = $this->getRequest()->getPostValue();
        try {
            $myJSON = preg_replace('/[^0-9,. -]/', '', $post['selected']);
            $selectedItems = explode(",", $myJSON);
            foreach ($selectedItems as $key => $selectedItem) {
                $params = array(
                    'form_key' => $this->formKey->getFormKey(),
                    'product_id' => $selectedItem,
                    'qty' => 1);
                $_product = $this->productFactory->create()->load($selectedItem);
                $this->cart->addProduct($_product, $params);
            }
            $this->cart->save();
            $this->messageManager->addSuccess(__('Add to cart successfully.'));
        } catch (\LocalizedException $e) {
            $this->messageManager->addException($e, __('%1', $e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('error.'));
        }
    }
}
