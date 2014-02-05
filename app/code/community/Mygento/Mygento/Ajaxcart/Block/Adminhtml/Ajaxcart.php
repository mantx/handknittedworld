<?php
class Mygento_Ajaxcart_Block_Adminhtml_Ajaxcart extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ajaxcart';
    $this->_blockGroup = 'ajaxcart';
    $this->_headerText = Mage::helper('ajaxcart')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('ajaxcart')->__('Add Item');
    parent::__construct();
  }
}