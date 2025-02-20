<?php


namespace Kowal\AttributesQuery\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Multiselect extends AbstractHelper
{

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
}
