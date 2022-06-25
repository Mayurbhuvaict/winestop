<?php

namespace Meetanshi\ShippingRates\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Rate extends AbstractDb
{
    public function _construct()
    {
        $this->_init('shippingrate', 'rate_id');
    }

    public function bulkInsert($methodId, $data)
    {
        $error = '';

        $sql = '';
        if (isset($data)) {
            for ($i = 0, $n = count($data); $i < $n; ++$i) {
                $sql .= ' (NULL,' . $methodId;
                foreach ($data[$i] as $v) {
                    $sql .= ', "' . $v . '"';
                }
                $sql .= '),';
            }

            if ($sql) {
                $connection = $this->getConnection();
                $rateTable = $this->getConnection()->getTableName('shippingrate');
                $sql = 'INSERT INTO `' . $rateTable . '` VALUES ' . substr($sql, 0, -1);
                try {
                    $connection->query($sql);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }


        return $error;
    }

    public function deleteBy($methodId)
    {
        $connection = $this->getConnection();
        $rateTable = $this->getConnection()->getTableName('shippingrate');
        $connection->delete($rateTable, 'method_id=' . (int)$methodId);
    }
}
