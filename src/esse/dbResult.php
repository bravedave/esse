<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\esse;

class dbResult {

  public function fetch(): array {

    return [];
  }

  public function dto($template = NULL): dto {

    if ($dto = $this->fetch()) {

      if (is_null($template)) return new dto($dto);
      return new $template($dto);
    }

    return null;
  }

  /**
   *	extend like:
   *		$dtoSet = $res->dtoSet( function( $dto) {
   *			return $dto;
   *
   *		});
   */
  public function dtoSet($func = null, $template = null): array {

    $ret = [];
    if (is_callable($func)) {

      while ($dto = $this->dto($template)) {

        if ($d = $func($dto)) $ret[] = $d;
      }
    } else {

      while ($dto = $this->dto($template)) {
        $ret[] = $dto;
      }
    }

    return $ret;
  }

  public function num_rows(): int {

    return 0;
  }
}
