<?php


namespace Kowal\AttributesQuery\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Intiger extends AbstractHelper
{

    /**
     * @var Tools
     */
    public $tools;

    /**
     * Text constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Tools $tools
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Kowal\AttributesQuery\Helper\Tools $tools
    )
    {
        parent::__construct($context);
        $this->tools = $tools;
    }



    /**
     * @param $sku
     * @param $kod
     * @param $value
     * @param int $store_id
     * @return bool|void
     */
    public function Update($sku, $kod, $value, $store_id = 0)
    {
        if (!$this->tools->checkIfSkuExists($sku)) return false;
        //if (empty($value)) return false;

        $this->tools->store_id = $store_id;
        return $this->tools->updateAtt($sku, $value, $kod, 'catalog_product_entity_int');
    }
}
