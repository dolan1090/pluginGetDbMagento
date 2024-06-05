<?php

namespace Ecentura\OptimateModule\Block;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class CustomGetRendererBlock extends Template
{
    protected $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection,
        Context $context,
        array $data = []
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    public function getNextAuctionId($currentAuctionId, $currentCampaign)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('ves_auction');

        $select = $connection->select()
            ->from(['ves_auction' => $tableName], ['auction_id'])
            ->where('auction_id > ?', $currentAuctionId)
            ->where('campaign = ?', $currentCampaign)
            ->where('status = ?', 1)
            ->order('auction_id')
            ->limit(1);

        return $connection->fetchOne($select);
    }

    public function getPreAuctionId($currentAuctionId, $currentCampaign)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('ves_auction');

        $select = $connection->select()
            ->from(['ves_auction' => $tableName], ['auction_id'])
            ->where('auction_id < ?', $currentAuctionId)
            ->where('campaign = ?', $currentCampaign)
            ->where('status = ?', 1)
            ->order('auction_id DESC')
            ->limit(1);

        return $connection->fetchOne($select);
    }

    public function getNextAuctionProductId($currentAuctionId, $currentCampaign)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('ves_auction');

        $select = $connection->select()
            ->from(['ves_auction' => $tableName], ['product_id'])
            ->where('auction_id > ?', $currentAuctionId)
            ->where('campaign = ?', $currentCampaign)
            ->where('status = ?', 1)
            ->order('auction_id')
            ->limit(1);

        return $connection->fetchOne($select);
    }

    public function getPrevAuctionProductId($currentAuctionId, $currentCampaign)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('ves_auction');

        $select = $connection->select()
            ->from(['ves_auction' => $tableName], ['product_id'])
            ->where('auction_id < ?', $currentAuctionId)
            ->where('campaign = ?', $currentCampaign)
            ->where('status = ?', 1)
            ->order('auction_id DESC')
            ->limit(1);

        return $connection->fetchOne($select);
    }

    public function getAuctionProductVarchar($entity_id)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('catalog_product_entity_varchar');

        $selectAttributeId = $connection->select()
        ->from(['eav_attribute' => 'eav_attribute'], ['attribute_id'])
        ->where('attribute_code = ?', 'url_key')
        ->where('frontend_class = ?', 'validate-trailing-hyphen')
        ->limit(1);

        $select = $connection->select()
            ->from(['catalog_product_entity_varchar' => $tableName], ['value'])
            ->where('entity_id = ?', $entity_id)
            ->where('attribute_id = ?', $connection->fetchOne($selectAttributeId))
            ->limit(1);

        return $connection->fetchOne($select);
    }
}
