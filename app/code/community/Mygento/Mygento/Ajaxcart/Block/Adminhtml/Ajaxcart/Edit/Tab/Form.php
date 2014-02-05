<?php

class Mygento_Ajaxcart_Block_Adminhtml_Ajaxcart_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('ajaxcart_form', array('legend'=>Mage::helper('ajaxcart')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('ajaxcart')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('ajaxcart')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('ajaxcart')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('ajaxcart')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('ajaxcart')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('ajaxcart')->__('Content'),
          'title'     => Mage::helper('ajaxcart')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getAjaxcartData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAjaxcartData());
          Mage::getSingleton('adminhtml/session')->setAjaxcartData(null);
      } elseif ( Mage::registry('ajaxcart_data') ) {
          $form->setValues(Mage::registry('ajaxcart_data')->getData());
      }
      return parent::_prepareForm();
  }
}