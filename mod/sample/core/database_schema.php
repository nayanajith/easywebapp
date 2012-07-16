<?php

$schema_version=1;
         
$program_table_schemas['%stest']="CREATE TABLE `%stest` (
  `rid`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       	VARCHAR(20) NOT NULL,
  `note`          VARCHAR(300) DEFAULT NULL,
  PRIMARY KEY (`rid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//In order to migrate from previous version (0) to current(1) execute these queries;
//db v0 v1
$program_table_migrate[1]="
ALTER TABLE %stest ADD test_param VARCHAR(100);
";

$schema_prefix='sample';
add_table_prefix($program_table_schemas,$schema_prefix);

?>
