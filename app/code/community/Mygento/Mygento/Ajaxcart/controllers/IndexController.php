<?php
class Mygento_Ajaxcart_IndexController extends Mage_Core_Controller_Front_Action
{   
	public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
			
    public function deleteAction()
    {
		$id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                Mage::getSingleton('checkout/cart')->removeItem($id)
                 ->save();
            } catch (Exception $e) {
               Mage::getSingleton('checkout/cart')->addError($this->__('Cannot remove item'));
            }
        }

		$this->loadLayout();
		$layout = $this->getLayout();
		$block_sidebar	=	$layout->getBlock('cart_sidebar');

		$cartBlockLink = $layout->createBlock('checkout/links');
		$topLinkBlock = $layout->createBlock('page/template_links')->setChild('link.cart',$cartBlockLink);
		$cartBlockLink->addCartLink();
		$link = array_shift($topLinkBlock->getLinks());

		//$arr['toplink']	=	$link->getLabel();
		$arr['sidebar']	=	$block_sidebar->toHtml();

		echo json_encode($arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);exit;
    }

	public function addtocartAction()
    {
        $this->indexAction();
    }
	
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
    }

    
}