<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Reports
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Reports\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mageplaza\Reports\Helper\Data;

/**
 * Class LinkBuilder
 * @package Mageplaza\Reports\Block\Adminhtml\System\Config
 */
class LinkBuilder extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Reports::system/config/link_builder.phtml';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Generate constructor.
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    )
    {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * Unset scope
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * Get the button Generate & Send
     *
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(Unused)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }
}
