<?php

class Jobs {       
    
    public function __construct() {
        
    }
    
    public function get_pure_vision($where, $limit) {
        $database = new Database();
        $sql="SELECT * FROM pure_vision 
            LEFT OUTER JOIN customer_sources ON pure_vision.`Customer Number` = customer_sources.`key`
            LEFT OUTER JOIN customers ON customer_sources.customer_id = customers.id ";
        if (strlen($where) > 0) {
            $sql.="WHERE $where ";
        }
        if (strlen($limit) > 0) {
            $sql.="WHERE $limit ";
        }        
        return $database->select($sql);
    }
    
    public function get_file_maker($where, $limit) {
        $database = new Database("sqlsrv", "WEB01\SQLEXPRESS", "MNTRANS", "Reportviewsuser", "Reportviewuserpwd1");
        $sql="select ";
        if (strlen($limit) > 0) {
            $sql.="TOP $limit ";
        }              
        $sql.="* from vwAccountList ";
        if (strlen($where) > 0) {
            $sql.="WHERE $where ";
        }
        return $database->select($sql);        
    }
    
}
?>
