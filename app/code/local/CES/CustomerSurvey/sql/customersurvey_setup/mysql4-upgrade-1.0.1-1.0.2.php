<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('customersurvey_schedule')}
CHANGE COLUMN order_id order_number int(11);
    ");
$installer->endSetup();
