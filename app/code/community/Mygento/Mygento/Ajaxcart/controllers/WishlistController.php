<?php
require_once 'app/code/core/Mage/Wishlist/controllers/IndexController.php';
class Mygento_Ajaxcart_WishlistController extends Mage_Wishlist_IndexController
{
	/**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     */
    public function cartAction()
    {
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel('wishlist/item')->load($itemId);
        if (!$item->getId()) {
            return $this->_redirect('*/*');
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->_redirect('*/*');
        }

        // Set qty
        $qty = $this->getRequest()->getParam('qty');
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }
        $qty = $this->_processLocalizedQty($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        /* @var $session Mage_Wishlist_Model_Session */
        $session    = Mage::getSingleton('wishlist/session');
        $cart       = Mage::getSingleton('checkout/cart');

        $redirectUrl = Mage::getUrl('*/*');

        try {
            $options = Mage::getModel('wishlist/item_option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                array('current_config' => $item->getBuyRequest())
            );

            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($cart, true);
            $cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            Mage::helper('wishlist')->calculate();
			
            if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
                $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
            } else if ($this->_getRefererUrl()) {
                $redirectUrl = $this->_getRefererUrl();
            }
            Mage::helper('wishlist')->calculate();
			$product = $item->getProduct();			
			
			$message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
			$img = "<img src='".Mage::helper('catalog/image')->init($product, 'image')->resize(60,null)."' />";
			$tmp_product = '<div id="message_ajax"><div class="ajaxcart_image">'.$img.'</div><div class="ajaxcart_message">'.$message.'</div></div>';
			$check = 'success';
			
        } catch (Mage_Core_Exception $e) {
			$check = 'failed';
            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
				$tmp_product	=	'<div id="message_ajax">'.Mage::helper('wishlist')->__('This product(s) is currently out of stock').'</div>';
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
				$tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';
            } else {
               $tmp_product	=	'<div id="message_ajax">'.Mage::helper('core')->escapeHtml($e->getMessage()).'</div>';
            }
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('wishlist')->__('Cannot add item to shopping cart'));
        }

        Mage::helper('wishlist')->calculate();
		
		$url = $this->getRequest()->getParam('return_url');
		if ($url) {
			header("Location:".$url);exit;
		}
		
		$this->loadLayout();
		$layout = $this->getLayout();
		$block_sidebar	=	$layout->getBlock('cart_sidebar');
		$wishlist_sidebar	=	$layout->getBlock('wishlist_sidebar');

		$cartBlockLink = $layout->createBlock('checkout/links');		
		$topLinkBlock = $layout->createBlock('page/template_links')->setChild('link.cart',$cartBlockLink);
		
		$wishBlockLink = $layout->createBlock('wishlist/links','link.wishlist');
		$cartBlockLink->addCartLink();
		$topLinkBlock->addLinkBlock('link.wishlist',$wishBlockLink);
		foreach($topLinkBlock->getLinks() as $link)
		{
			if ($link instanceof Mage_Core_Block_Abstract)	
				$wish_link	=	$link->toHtml();
			else
				$wish_link	=	$link->getLabel();
		}
		//$wishBlockLink->addLinkBlock('link.wishlist');
		//print_r($topLinkBlock->getLinks());exit;
		$tmp = $topLinkBlock->getLinks();
		$link = array_shift($tmp);

		$arr['check'] 				= 	$check;
		$arr['msg']					= 	$tmp_product;
		if($check == 'success'){
			$arr['total']				= 	Mage::helper('checkout')->formatPrice($cart->getQuote()->getSubtotal());
			//$arr['toplink']				=	$link->getLabel();
			$arr['sidebar']				=	$block_sidebar->toHtml();

			$arr['w_check']				=	1;
			$arr['w_sub']['text']		=	'<div style="color:red;font-weight:bold">'.$product->getName().' has been removed to wishlist Wishlist</div>';
			$arr['w_sub']['link']		=	strip_tags($wish_link);
			$arr['w_sub']['sidebar']	=	$wishlist_sidebar->toHtml();
		}
		echo json_encode($arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);exit;
    }
	/**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            //$this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
}