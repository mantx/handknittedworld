<?php

class Mygento_Ajaxcart_Block_Adminhtml_Ajaxcart_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ajaxcart';
        $this->_controller = 'adminhtml_ajaxcart';
        
        $this->_updateButton('save', 'label', Mage::helper('ajaxcart')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('ajaxcart')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ajaxcart_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'ajaxcart_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'ajaxcart_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('ajaxcart_data') && Mage::registry('ajaxcart_data')->getId() ) {
            return Mage::helper('ajaxcart')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('ajaxcart_data')->getTitle()));
        } else {
            return Mage::helper('ajaxcart')->__('Add Item');
        }
    }
}