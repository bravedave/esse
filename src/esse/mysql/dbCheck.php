<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse\mysql;

use config;
use bravedave\esse\dao;
use bravedave\esse\logger;
use RuntimeException;

class dbCheck extends dao {
  public $temporary = false;

  protected string $table = '';
  protected string $pk = "id";
  protected array $structure = [];
  protected array $indexs = [];

  public function __construct(db $db = null, $table, $pk = "id") {

    $this->db = $db;
    // parent::__construct( $db );

    $this->table = $table;
    $this->pk = $pk;
  }

  public function defineField(string $name = '', string $type = 'varchar', ?int $len = null, int $dec = 0, string $default = ''): void {

    if ($name != "") {

      if ($type == "date" && $default == "") {

        $default = "0000-00-00";
      } elseif ($type == "datetime" && $default == "") {

        $default = "0000-00-00 00:00:00";
      } elseif (($type == "int" || $type == "bigint" || $type == "double" || $type == "float") && $default == "") {

        $default = "0";
      }

      if (is_null($len) || (int)$len < 1) {

        if (($type == "int")) {
          $len = 11;
        } elseif (($type == "varbinary")) {

          $len = 32;
        } elseif (($type == "bigint" || $type == "double" || $type == "decimal" || $type == "float")) {

          $len = 20;
        } else {

          $len = 45;  // probably varchar
        }
      }

      $this->structure[] = [
        "name" => $name,
        "type" => $type,
        "length" => $len,
        "decimal" => $dec,
        "default" => $default
      ];
    }
  }

  public function defineIndex($key, $field): void {
    $this->indexs[] = [
      'key' => $key,
      'field' => $field

    ];
  }

  public function check(): void {

    $debug = false;
    // $debug = true;

    if ($debug) logger::info(sprintf('<%s> %s', $this->table, __METHOD__));
    if (!$this->table) throw new RuntimeException('table name is empty');

    $fields = [$this->pk . " bigint(20) NOT NULL auto_increment"];
    foreach ($this->structure as $fld) {

      if ($fld["type"] == "varchar") {
        $fields[] = "`" . $fld["name"] . "` varchar(" . (string)$fld["length"] . ") default '" . $this->db->escape($fld["default"]) . "'";
      } elseif ($fld["type"] == "date" || $fld["type"] == "datetime") {
        $fields[] = "`" . $fld["name"] . "` " . $fld["type"] . " default '" . $this->db->escape($fld["default"]) . "'";
      } elseif ($fld["type"] == "timestamp") {
        $fields[] = "`" . $fld["name"] . "` " . $fld["type"];
      } elseif ($fld["type"] == "text") {
        $fields[] = "`" . $fld["name"] . "` text";
      } elseif ($fld["type"] == "mediumtext") {
        $fields[] = "`" . $fld["name"] . "` mediumtext";
      } elseif ($fld["type"] == "longtext") {
        $fields[] = "`" . $fld["name"] . "` longtext";
      } elseif ($fld["type"] == "bigint") {
        $fields[] = "`" . $fld["name"] . "` bigint(" . (string)$fld["length"] . ") default '" . (int)$fld["default"] . "'";
      } elseif ($fld["type"] == "tinyint") {
        $fields[] = "`" . $fld["name"] . "`  tinyint(1) default 0";
      } elseif ($fld["type"] == "int") {
        $fields[] = "`" . $fld["name"] . "`  int default '" . (int)$fld["default"] . "'";
      } elseif ($fld["type"] == "decimal") {
        $fields[] = sprintf(
          '`%s` decimal(%d,%d) default %d',
          $fld["name"],
          $fld["length"],
          $fld["decimal"],
          (int)$fld["default"]
        );
      } elseif ($fld["type"] == "double") {
        $fields[] = "`" . $fld["name"] . "`  double default '" . (int)$fld["default"] . "'";
      } elseif ($fld["type"] == "float") {
        $fields[] = "`" . $fld["name"] . "`  float default '" . (int)$fld["default"] . "'";
      } elseif ($fld["type"] == "varbinary") {
        $fields[] = sprintf('`%s` varbinary(%s)', $fld["name"], (string)$fld["length"]);
      } elseif ($fld["type"] == "blob") {
        $fields[] = "`" . $fld["name"] . "`  blob";
      } elseif ($fld["type"] == "mediumblob") {
        $fields[] = "`" . $fld["name"] . "`  mediumblob";
      } elseif ($fld["type"] == "longblob") {
        $fields[] = "`" . $fld["name"] . "`  longblob";
      } else {
        die("unknown field type dbCheck => check -> " . $fld["type"]);
      }
    }

    $fields[] = "PRIMARY KEY  (`" . $this->pk . "`)";
    foreach ($this->indexs as $key) {
      $fields[] = " KEY `" . $key["key"] . "` (" . $key["field"] . ")";
    }

    $sql = sprintf(
      'CREATE %s TABLE IF NOT EXISTS `%s`(%s)',
      $this->temporary ? 'TEMPORARY' : '',
      $this->table,
      implode(',', $fields)

    );
    //~ print "<pre>" . print_r( $fields, TRUE ) . "</pre>";
    //~ print $sql;
    $this->db->Q($sql);

    $fields = $this->db->fieldList($this->table);
    $fieldStructures = $this->db->fetchFields($this->table);
    $charset = $this->db->getCharSet();
    $after = "";
    foreach ($this->structure as $fld) {

      if (in_array($fld["name"], $fields)) {

        if ($fld["type"] == "varchar") {

          if (config::$DB_ALTER_FIELD_STRUCTURES) {

            /*---[ we want to know if we should alter the field ]---*/
            // get structure for this field
            if ($charset = 'utf8') {
              foreach ($fieldStructures as $fieldStructure) {
                if ($fieldStructure->name == $fld["name"]) {
                  $fieldLength = ((int)$fieldStructure->length / 3);  // utf8 conversion
                  if ((int)$fieldLength < (int)$fld["length"]) {
                    // logger::info( sprintf( 'bingo baby :: %s : %s != %s', $fieldStructure->name, $fieldStructure->length, $fld["length"]));
                    $sql = sprintf(
                      'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` varchar(%s) default "%s"',
                      $this->table,
                      $fld["name"],
                      $fld["name"],
                      (string)$fld["length"],
                      $this->db->escape($fld["default"])
                    );

                    logger::info(sprintf('field length %s < %s', $fieldLength, ((int)$fld["length"])));
                    logger::sql($sql);
                  }

                  break;
                }
              }
            }
            /*---[ end: we want to know if we should alter the field ]---*/
          }
        }
      } else {

        if ($fld["type"] == "varchar") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` varchar(" . (string)$fld["length"] . ") default '" . $this->db->escape($fld["default"]) . "' $after";
        } elseif ($fld["type"] == "date" || $fld["type"] == "datetime") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` " . $fld["type"] . " default '" . $this->db->escape($fld["default"]) . "' $after";
        } elseif ($fld["type"] == "timestamp") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` timestamp $after";
        } elseif ($fld["type"] == "text") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` text $after";
        } elseif ($fld["type"] == "mediumtext") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` mediumtext $after";
        } elseif ($fld["type"] == "longtext") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` longtext $after";
        } elseif ($fld["type"] == "bigint") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` bigint(" . (string)$fld["length"] . ") default '" . (int)$fld["default"] . "' $after";
        } elseif ($fld["type"] == "int") {

          $sql = sprintf(
            'alter table `%s` add column `%s` int default %s%s',
            $this->table,
            $this->escape($fld["name"]),
            $this->quote((int)$fld["default"]),
            $after
          );
        } elseif ($fld["type"] == "decimal") {

          $sql = sprintf(
            'alter table `%s` add column `%s` decimal(%d,%d) default %d%s',
            $this->table,
            $fld["name"],
            $fld["length"],
            $fld["decimal"],
            (int)$fld["default"],
            $after
          );
        } elseif ($fld["type"] == "double") {

          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` double default '" . (int)$fld["default"] . "' $after";
        } elseif ($fld["type"] == "float") {

          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` float default '" . (int)$fld["default"] . "' $after";
        } elseif ($fld["type"] == "varbinary") {

          $sql = sprintf(
            'alter table `%s` add column `%s` varbinary(%s) %s',
            $this->table,
            $fld["name"],
            (string)$fld["length"],
            $after
          );
        } elseif ($fld["type"] == "tinyint") {

          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` tinyint(1) default 0 $after";
        } elseif ($fld["type"] == "blob") {

          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` blob $after";
        } elseif ($fld["type"] == "mediumblob") {

          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` mediumblob $after";
        } elseif ($fld["type"] == "longblob") {

          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` longblob $after";
        } else {

          die("unknown field type dbCheck x> check -> " . $fld["type"]);
        }

        $this->db->Q($sql);
      }

      $after = " after `" . $fld["name"] . "`";
    }

    foreach ($this->indexs as $index) {

      $res = $this->db->Result(sprintf("SHOW INDEX FROM `%s` WHERE Key_name = '%s'", $this->table, $index['key']));
      $indexFound = false;
      if ($res->num_rows() > 0) {
        if ($row = $res->fetch()) {
          logger::info(sprintf("INDEX found `%s` => %s(%s)", $this->table, $index['key'], $row["Column_name"]), 2);
          $indexFound = true;
        }
      }

      if (!$indexFound) {

        $sql = sprintf(
          "ALTER TABLE `%s` ADD INDEX `%s` (%s)",
          $this->escape($this->table),
          $this->escape($index['key']),
          $this->escape($index['field'])
        );
        logger::info($sql, 2);
        $this->Q($sql);
        logger::info(sprintf("INDEX created `%s` => %s(%s)", $this->table, $index['key'], $index['field']), 2);
      }
    }
  }
}
