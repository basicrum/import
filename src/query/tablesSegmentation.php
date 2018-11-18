<?php

declare(strict_types=1);

class BasicRum_Import_Csv_Query_TableSegmentation
{

    public function createTable(int $segment)
    {
        $createTable =
"CREATE TABLE IF NOT EXISTS `resource_timings_segment_%s` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_view_id` int(11) NOT NULL,
  `base36` char(%s) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=4;";

        return sprintf($createTable, $segment, $segment);
    }

}