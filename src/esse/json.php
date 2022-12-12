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

class json {

  protected array $_json = [];
  protected bool $dumpOnDestruct = true;

  static public function nak($description): json {

    return new self(['response' => 'nak', 'description' => $description]);
  }

  static public function ack($description): json {

    return new self(['response' => 'ack', 'description' => $description]);
  }

  function __construct(array $a = []) {

    if ($a) $this->_json = (array)$a;
  }

  public function __destruct() {

    if ($this->dumpOnDestruct) {

      $response = json_encode($this->_json);
      response::json_headers(0, \strlen($response));
      print $response;
    }
  }

  public function add(string $key, mixed $data): self {

    $this->_json[$key] = $data;
    return $this;  // chain
  }

  public function append(mixed $data): self {

    $this->_json[] = $data;
    return $this;  // chain
  }

  public function count(): int {

    return count($this->_json);
  }

  public function merge(array $data): self {

    $a = array_merge($this->_json, $data);
    $this->_json[] = $a;
    return $this;  // chain
  }

  public function prepend(mixed $data): self {
    array_unshift($this->_json, $data);
    return $this;  // chain

  }

  public function print(): void {

    $this->dumpOnDestruct = false;
    print json_encode($this->_json);
  }

  public function toArray(): array {

    $this->dumpOnDestruct = false;
    return $this->_json;
  }
}
