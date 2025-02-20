<?php


namespace Kowal\AttributesQuery\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Tools extends AbstractHelper
{

    public $store_id = 0;
    protected $resourceConnection;
    public $connection_read;
    public $connection_write;


    /**
     * Tools constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context     $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;

        $this->connection_read = $this->resourceConnection->getConnection('core_read');
        $this->connection_write = $this->resourceConnection->getConnection('core_write');
    }


    /**
     * @param $tableName
     * @return mixed
     */
    private function _getTableName($tableName)
    {
        return $this->resourceConnection->getTableName($tableName);
    }

    /**
     * @param $attributeCode
     * @return mixed
     */
    private function _getAttributeId($attributeCode)
    {
        $sql = "SELECT attribute_id FROM " . $this->_getTableName('eav_attribute') . " WHERE entity_type_id = ? AND attribute_code = ?";
        return $this->connection_read->fetchOne(
            $sql,
            [
                $this->_getEntityTypeId('catalog_product'),
                $attributeCode
            ]
        );
    }

    /**
     * @param $entityTypeCode
     * @return mixed
     */
    private function _getEntityTypeId($entityTypeCode)
    {
        $sql = "SELECT entity_type_id FROM " . $this->_getTableName('eav_entity_type') . " WHERE entity_type_code = ?";
        return $this->connection_read->fetchOne(
            $sql,
            [
                $entityTypeCode
            ]
        );
    }

    /**
     * @param $sku
     * @return mixed
     */
    private function _getIdFromSku($sku)
    {
        $sql = "SELECT entity_id FROM " . $this->_getTableName('catalog_product_entity') . " WHERE sku = ?";
        return $this->connection_read->fetchOne(
            $sql,
            [
                $sku
            ]
        );
    }

    /**
     * @param $sku
     * @return mixed
     */
    public function checkIfSkuExists($sku)
    {
        $sql = "SELECT COUNT(*) AS count_no FROM " . $this->_getTableName('catalog_product_entity') . " WHERE sku = ?";
        return $this->connection_read->fetchOne($sql, [$sku]);
    }

    /**
     * @param $sku
     * @return mixed
     */
    public function checkIfAttrExists($id, $table_name, $entityId)
    {
        $sql = "SELECT COUNT(*) AS count_no FROM " . $this->_getTableName($table_name) . " WHERE attribute_id = ? AND store_id = ? AND entity_id = ?";
        return $this->connection_read->fetchOne($sql, [$id, $this->store_id, $entityId]);
    }

    /**
     * @param $sku // SKU produktu
     * @param $value // Wartość attr
     * @param $attr_code // Kod atrybutu
     * @param $table_name // Nazwa tabeli
     */
    public function updateAtt($sku, $value, $attr_code, $table_name)
    {
        $entityId = $this->_getIdFromSku($sku);
        $attributeId = $this->_getAttributeId($attr_code);

        $sql = "INSERT INTO " . $this->_getTableName($table_name) . " (attribute_id, store_id, entity_id, value) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE value=VALUES(value)";
        $values = [
            $attributeId,
            $this->store_id,
            $entityId,
            $value
        ];
        $this->connection_write->query($sql, $values);
    }

    public function getAttributeValue($productId, $attributeKey)
    {
        try {
            $attribute_type = $this->_getAttributeType($attributeKey);
            $attributeId = $this->_getAttributeId($attributeKey);

            if ($attribute_type == 'static') return false;

            $sql = "SELECT value FROM " . $this->_getTableName('catalog_product_entity_' . $attribute_type) . " cped
			WHERE  cped.attribute_id = ?
			AND cped.entity_id = ?";
            return $this->connection_read->fetchOne($sql, array($attributeId, $productId));

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteAttrValue($sku, $attr_code, $table_name)
    {
        $entityId = $this->_getIdFromSku($sku);
        $attributeId = $this->_getAttributeId($attr_code);

        $sql = "DELETE FROM " . $this->_getTableName($table_name) . " WHERE attribute_id = ? AND store_id = ? AND entity_id = ?";
        $values = [
            $attributeId,
            $this->store_id,
            $entityId
        ];

        $this->connection_write->query($sql, $values);

    }

    public function _getAttributeType($attribute_code = 'price')
    {
        $sql = "SELECT backend_type
				FROM " . $this->_getTableName('eav_attribute') . "
			WHERE
				entity_type_id = ?
				AND attribute_code = ?";
        $entity_type_id = $this->_getEntityTypeId('catalog_product');
        return $this->connection_read->fetchOne($sql, array($entity_type_id, $attribute_code));
    }
}
