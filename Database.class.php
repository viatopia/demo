<?php

class Database extends PDO
{   
    
    public function __construct($type=DB_MASTER_TYPE, $host=DB_MASTER_HOST, $database=DB_MASTER_NAME, $user=DB_MASTER_USER, $password=DB_MASTER_PASSWORD )
    {                       
        date_default_timezone_set("America/New_York");
        switch($type) {
            case "sqlsrv" :
                parent::__construct($type.':Server='.$host.';Database='.$database, $user, $password);        
                break;
            case "mysql" :
                parent::__construct($type.':host='.$host.';dbname='.$database, $user, $password);        
                break;            
        }        
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        
    }
    
    /**
     * select
     * @param string $sql An SQL string
     * @param array $array Paramters to bind
     * @param constant $fetchMode A PDO Fetch mode
     * @return mixed
     */
    public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
    {
        $sth = $this->prepare($sql);
        foreach ($array as $key => $value) {            
            $sth->bindValue("$key", $value);
        }
        
        $sth->execute();        
        $temp = $sth->fetchAll($fetchMode);
        $sth->closeCursor();
        return $temp;
    }
    
  public function insert($table, $data) {      
            $sql="INSERT INTO $table(";
            foreach ($data as $key => $value) {
                //echo $key . " " . $value . BR;
                if (strlen($value) > 0) {
                    if ($this->check_column($key,$table)) {
                        $sql = $sql . "`" . $key . "`,";
                        $parameters[$key]=$value;
                    }
              }
            }
            $sql = rtrim($sql, ", ");
            $sql = $sql . ") VALUES (";
            foreach ($data as $key => $value) {
                if (strlen($value) > 0) {
                    if ($this->check_column($key,$table)) {
                        $sql = $sql . ":" . $key . ", ";
                    }
                }
            }                        
            $sql = rtrim($sql, ", ");
            $sql = $sql . ")";                           
            $sth = $this->prepare($sql);
            //print_r($parameters, false);
            foreach ($parameters as $key => $value) {            
                $sth->bindValue(":$key", $value);
            }

            $sth->execute();      
            return DATABASE_ADD;
  }
    
    /**
     * insert
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     */
  /*
    public function insert($table, $data)
    {
        ksort($data);
        
        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));
        
        $sth = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");
        
        foreach ($data as $key => $value) {            
            $sth->bindValue(":$key", $value);
        }
        
        $sth->execute();
    }  
   */
    
  /*
    public function update($table, $data) {
            $sql="UPDATE $table SET ";
            foreach ($_POST as $key => $value) {   
                if ($this->check_column($key,$table)) {
                    $sql= $sql . $key . "=:" . $key . ", ";                
                    $parameters[$key]=$value;
                }                
            }
            
            $sql = rtrim($sql, ", ");
            $sql=$sql . " WHERE id=:id";                        
           
            $sth = $this->prepare($sql);
            foreach ($parameters as $key => $value) {                  
                $sth->bindValue(":$key", $value);
            }   
    }
   */
    /**
     * update
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     * @param string $where the WHERE query part
     */
    
    public function update($table, $data, $where)
    {
        ksort($data);        
        
        $fieldDetails = NULL;
        foreach($data as $key=> $value) {
            if ($this->check_column($key,$table)) {
                $fieldDetails .= "`$key`=:$key,";   
            }            
        }
        $fieldDetails = rtrim($fieldDetails, ',');                
        
        $sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");
        
        foreach ($data as $key => $value) {
            if ($this->check_column($key,$table)) {
                $sth->bindValue(":$key", $value);
            }
        }
        
        $sth->execute();
        return DATABASE_UPDATE;        
    }    
    
    /**
     * delete
     * 
     * @param string $table
     * @param string $where
     * @param integer $limit
     * @return integer Affected Rows
     */
    public function delete($table, $where, $limit = 1)
    {
        $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");        
        return DATABASE_DELETE;
    }
    
    public function check_column($column, $table) {                                       
        $sql="SELECT * FROM `$table` LIMIT 0";
        //echo $sql;
        $query = $this->prepare($sql);
        $query->execute();
        //echo $query->columnCount() . BR;   
        //echo $query->rowCount() . BR;
        $found = false;
        for ($x=0; $x<$query->columnCount(); $x++) {
            $meta = $query->getColumnMeta($x); // 0 indexed so 0 would be first column
            $name = $meta['name'];  
            //echo "Looking at $name for $column" . BR;
            if ($column == $name) { $found = true; }
        }
                                       
        if ($found) {    
            //echo "Found $column" . BR;
            return true;
        } else {            
            //echo "DID NOT FIND";
            return false;
        }
    }      
    
    public function rows($rs) {
        return mysqli_num_rows($rs);
    }
    
}