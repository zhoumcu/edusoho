<?php

use Phpmig\Migration\Migration;

class BizOrderAddInvoiceSn extends Migration
{
    public function up()
    {
        $biz = $this->getContainer();
        $connection = $biz['db'];

        $connection->exec("
            ALTER TABLE `biz_order` ADD COLUMN `invoice_sn` varchar(64) default '' COMMENT '申请开票sn' 
        ");
    }

    public function down()
    {
        $biz = $this->getContainer();
        $connection = $biz['db'];

        $connection->exec('ALTER TABLE `biz_order` DROP COLUMN `invoice_sn`;');
    }
}
