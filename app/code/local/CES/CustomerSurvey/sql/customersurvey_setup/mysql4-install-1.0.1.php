<?php
/**
 *
 */
$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('customersurvey_schedule')};
CREATE TABLE {$this->getTable('customersurvey_schedule')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `first_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `order_id` int(11) NOT NULL default 0,
  `sent` int(11) unsigned NOT NULL default 0,
  `sent_time` datetime NULL,
  `token` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();
