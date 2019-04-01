<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 18/01/19
 * Time: 11:19 AM
 */

namespace Veem\Payment\Block\Adminhtml\System\Config\Form\Fieldset;

use Magento\Config\Block\System\Config\Form\Fieldset;

class Payment extends Fieldset
{
    protected function _getHeaderHtml($element)
    {
        if ($element->getIsNested()) {
            $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">';
        } else {
            $html = '<div class="' . $this->_getFrontendClass($element) . '">';
        }

        $html .= '<div class="entry-edit-head admin__collapsible-block">' .
            '<span id="' .
            $element->getHtmlId() .
            '-link" class="entry-edit-head-link"></span>';

        $html .= $this->_getHeaderTitleHtml($element);

        $html .= '</div>';
        $html .= '<input id="' .
            $element->getHtmlId() .
            '-state" name="config_state[' .
            $element->getId() .
            ']" type="hidden" value="' .
            (int)$this->_isCollapseState(
                $element
            ) . '" />';
        $html .= '<fieldset class="' . $this->_getFieldsetCss() . '" id="' . $element->getHtmlId() . '">';

        $html .= $this->_getHeadingHtml($element);

        $html .= $this->_getHeaderCommentHtml($element);



        // field label column
        $html .= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html .= '<colgroup class="use-default" />';
        }
        $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';

        return $html;
    }

    protected function _getHeadingHtml($element)
    {
        $html = '<div class="config-heading" >';

        $html .= '<div class="heading"><strong>' . $element->getLegend() . '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }
        $html .='<div class="config-alt">'. __('Global bank transfer and online international payments.'). '<a class="vlearn" href="https://www.veem.com/" target="_blank">'. __(' Learn More.').'></a></div>';
        $html .= '</div></div>';

        return $html;
    }
}