<?php
class DISPLAY{
  protected static function showData($text,$display){
    if($display){
      echo $text;
    }
  }
  public static function asJSON($data,$setHeader=True,$display=True){
    	if($setHeader){
        header('Content-Type: application/json');
      }
      $JSON = json_encode($data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    	self::showData($JSON,$display);
    	return $JSON;
  }
  public static function asUglyJSON($data,$setHeader=True,$display=True){
    	if($setHeader){
        header('Content-Type: application/json');
      }
      $JSON = json_encode($data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    	self::showData($JSON,$display);
    	return $JSON;
  }
  public static function asArray($data,$display=True){
    if($display){
      print_r($data);
    }
  }
  public static function asTable($data,$class="",$display=True){
    $table = '<table class="'.$class.'">';
      # First row (headers)
      $columns = array_keys($data[0]);
      $table .= "<tr>";
      foreach($columns as $column){
        $table .= "<th>$column</th>";
      }
      $table .= "</tr>";

      # All other rows
      foreach($data as $row){
        $table .= "<tr>";
        foreach($columns as $column){
          $table .= "<td>$row[$column]</td>";
        }
        $table .= "</tr>";
      }

    $table .= "</table>";
    self::showData( $table,$display);
    return $table;
  }
  public static function asAPI($object,$display=True){
    $data["status"] = $object->status();
    $data["message"] = $object->message();
    $data["data"] = $object->data;
    self::asJSON($data,$setHeader=True,$display=True);
  }
}
