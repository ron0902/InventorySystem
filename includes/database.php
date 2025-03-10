<?php
require_once(LIB_PATH_INC.DS."config.php");

class MySqli_DB {

    private $con;
    public $query_id;

    function __construct() {
      $this->db_connect();
    }

public function db_connect()
{
  $this->con = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
  if(!$this->con)
         {
           die(" Database connection failed:". mysqli_connect_error());
         } else {
           $select_db = $this->con->select_db(database: DB_NAME);
             if(!$select_db)
             {
              die("Failed to Select Database". mysqli_connect_error());
            }
        }
}


public function db_disconnect()
{
  if(isset($this->con))
  {
    mysqli_close($this->con);
    unset($this->con);
  }
}

public function query($sql)
{
    if (trim($sql) != "") {
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->query_id = $this->con->query($sql);
        } catch (mysqli_sql_exception $e) {
            // Handle the error gracefully
            die("Error on this Query :<pre> " . $sql . "</pre><br>" . $e->getMessage());
        }
    }
    return $this->query_id;
}

/*--------------------------------------------------------------*/
/* Function for Query Helper
/*--------------------------------------------------------------*/
public function fetch_array($statement)
{
  return mysqli_fetch_array($statement);
}
public function fetch_object($statement)
{
  return mysqli_fetch_object($statement);
}
public function fetch_assoc($statement)
{
  return mysqli_fetch_assoc($statement);
}
public function num_rows($statement)
{
  return mysqli_num_rows($statement);
}
public function insert_id()
{
  return mysqli_insert_id($this->con);
}
public function affected_rows()
{
  return mysqli_affected_rows($this->con);
}
/*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
 public function escape($str){
   return $this->con->real_escape_string($str);
 }
/*--------------------------------------------------------------*/
/* Function for while loop
/*--------------------------------------------------------------*/
public function while_loop($loop){
 global $db;
   $results = array();
   while ($result = $this->fetch_array($loop)) {
      $results[] = $result;
   }
 return $results;
}

}

$db = new MySqli_DB();

?>