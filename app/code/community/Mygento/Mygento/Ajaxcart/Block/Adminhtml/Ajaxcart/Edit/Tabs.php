<?php

class Mygento_Ajaxcart_Block_Adminhtml_Ajaxcart_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('ajaxcart_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('ajaxcart')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('ajaxcart')->__('Item Information'),
          'title'     => Mage::helper('ajaxcart')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('ajaxcart/adminhtml_ajaxcart_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}