<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 19/12/18
 * Time: 04:03 PM
 */

namespace Veem\Payment\Block\Adminhtml\System\Config\Connect;

use Magento\Config\Block\System\Config\Form\Field;

class Button extends Field
{
    protected $_template = "Veem_Payment::system/config/connect/button.phtml";

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->toHtml();
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->toHtml();
        }
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }
}