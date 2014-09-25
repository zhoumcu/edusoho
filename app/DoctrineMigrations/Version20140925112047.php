<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140925112047 extends AbstractMigration
{
    public function up(Schema $schema)
    {
    	$this->addSql("CREATE TABLE `sign_target_statistics` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统id',
			`targetType` varchar(255) NOT NULL DEFAULT '' COMMENT '签到目标类型',
			`targetId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到目标id',
			`signedNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到人数',
			`date` int(6) unsigned NOT NULL DEFAULT '0' COMMENT '统计日期',
			`createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    	$this->addSql("CREATE TABLE `sign_user_statistics` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统id',
			`userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
			`targetType` varchar(255) NOT NULL DEFAULT '' COMMENT '签到目标类型',
			`targetId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到目标id',
			`keepDays` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '连续签到天数',
			`createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    	$this->addSql("CREATE TABLE `sign_user_log` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统id',
			`userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
			`targetType` varchar(255) NOT NULL DEFAULT '' COMMENT '签到目标类型',
			`targetId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到目标id',
			`rank` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到排名',
			`createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    	$this->addSql("DROP TABLE class_member_sign;
			DROP TABLE class_member_sign_statistics;
			DROP TABLE class_sign_statistics;");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
