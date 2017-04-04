<?php
class DISPLAY{
  protected static function showData($text,$display){
    if($display){
      echo $text;
    }
  }
  protected static function NoDataMessage(){
    return
    '<div style="display: block; clear: both;">
      <strong style="color: red !important;">
        No hay datos para ésta búsqueda.
      </strong>
    </div>';
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
    if( sizeof($data)>0){
    $table = '<table class="'.$class.'">';
        # First row (headers)
        $columns = array_keys($data[0]);
        $table .= "<thead><tr>";
        foreach($columns as $column){
          $table .= "<th>$column</th>";
        }
        $table .= "</tr></thead>";

        # All other rows
      $table .= "<tbody>";
        foreach($data as $row){
          $table .= "<tr>";
          foreach($columns as $column){
            $table .= "<td>$row[$column]</td>";
          }
          $table .= "</tr>";
        }
      $table .= "</tbody>";
      $table .= "</table>";
    }else{
      $table = self::NoDataMessage();
    }
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