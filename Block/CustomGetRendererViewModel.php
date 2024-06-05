<?php

namespace Promostore\OptimateModule\Block;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomGetRenderer implements ArgumentInterface
{
    protected $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function getProductChild($id_product_parent)
    {
        $connection = $this->resourceConnection->getConnection();
        $bundleTable = $this->resourceConnection->getTableName('catalog_product_bundle_selection');
        $entityVarcharTable = $this->resourceConnection->getTableName('catalog_product_entity_varchar');
        $entityTable = $this->resourceConnection->getTableName('catalog_product_entity');
        $bundleOptionTable = $this->resourceConnection->getTableName('catalog_product_bundle_option');
    
        // get product_id and selection_qty
        $selectProductChildInfo = $connection->select()
            ->from(['bps' => $bundleTable], ['product_id', 'selection_qty'])
            ->join(
                ['cpe' => $entityTable],
                'bps.product_id = cpe.entity_id',
                []
            )
            ->join(
                ['cbo' => $bundleOptionTable],
                'bps.option_id = cbo.option_id',
                []
            )
            ->where('bps.parent_product_id = ?', $id_product_parent)
            ->where('cpe.type_id != ?', 'virtual')
            ->where('cbo.product_attribute = ?', 'color');
    
        $productChildInfo = $connection->fetchAll($selectProductChildInfo);
    
        // get value from catalog_product_entity_varchar
        foreach ($productChildInfo as &$child) {
            $selectValue = $connection->select()
                ->from(['cpev' => $entityVarcharTable], ['value'])
                ->where('cpev.entity_id = ?', $child['product_id']);
            $child['value'] = $connection->fetchOne($selectValue);
        }
    
        return $productChildInfo;
    }
}
