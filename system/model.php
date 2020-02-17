<?php
    class Model{
        protected $_db;
        public $_tabela;
        public function  __construct(){

            try {                                
                $this->_db = new PDO('mysql:host=localhost;dbname=bitrix24', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                
            }
            catch( PDOException $Exception ) {                
                die($Exception->getMessage() ." ". $Exception->getCode() );
            }

        }

        public function insert( Array $dados ){
            $campos = implode(", ", array_keys($dados));
            $valores = "";
            foreach ( $dados as $ind => $val ){
                //$dados[$ind] = mysql_escape_string($val);
                //$dados[$ind] = $this->_db->quote($val);
                $dados[$ind] = $this->escapeString($val);
                $valores .= "?,";
            }            
            $valores = substr($valores, 0, -1);            
            //$valores = "'".implode("','", array_values($dados))."'";
            //echo " INSERT INTO `{$this->_tabela}` ({$campos}) VALUES ({$valores}) "."<br>"; die;
            //return $this->_db->query(" INSERT INTO `{$this->_tabela}` ({$campos}) VALUES ({$valores}) ");
            $stmt = $this->_db->prepare(" INSERT INTO `{$this->_tabela}` ({$campos}) VALUES ({$valores}) ");
            return $stmt->execute(array_values($dados));
        }

        public function read( $where = null, $limit = null, $offset = null, $orderby = null, $sql = null, $groupby = null ){            
            if ($sql == null){
                $params = ($where != null ? $where['params'] : "");            
                $where = ($where != null ? "WHERE ".$where['cmd']."" : "");                 
                $limit = ($limit != null ? "LIMIT {$limit}" : "");
                $offset = ($offset != null ? "OFFSET {$offset}" : "");
                $groupby = ($groupby != null ? "GROUP BY {$groupby}" : "");
                $orderby = ($orderby != null ? "ORDER BY {$orderby}" : "");                
                $sql = " SELECT * FROM `{$this->_tabela}` {$where} {$groupby} {$orderby} {$limit} {$offset} ";
            }else{                
                $params = ($sql['params']?$sql['params']:"");
                $sql = $sql['cmd'];
            }
            //echo "sql: <pre>$sql</pre><br>"; 
            //echo "params: <pre>"; print_r($params); echo "</pre><br>"; 
            //die;
            //$q = $this->_db->query($sql['cmd']);
            $q = $this->_db->prepare($sql);
            if ($params){
                $q->execute($params);
            }else{
                $q->execute();
            }
            $q->setFetchMode(PDO::FETCH_ASSOC);
            return $q->fetchAll();           
            
        }

        public function update( Array $dados, $where ){
            foreach ( $dados as $ind => $val ){
                //$campos[] = "{$ind} = '".mysql_escape_string($val)."'";
                //$campos[] = "{$ind} = '".$this->_db->quote($val)."'";                
                //if ($val != null){
                    //$campos[] = "{$ind} = '".$this->escapeString($val)."'";                    
                //}else{
                  //  $campos[] = "{$ind} = null";
                //}
                $campos[] = "{$ind} = ?";
            }
            $campos = implode(", ", $campos);
            //echo " UPDATE `{$this->_tabela}` SET {$campos} WHERE {$where} "."<br>";
            //return $this->_db->query(" UPDATE `{$this->_tabela}` SET {$campos} WHERE {$where} ");
            $stmt = $this->_db->prepare(" UPDATE `{$this->_tabela}` SET {$campos} WHERE {$where} ");
            return $stmt->execute(array_values($dados));
        }

        public function delete( $where ){
            //echo " DELETE FROM `{$this->_tabela}` WHERE {$where} ";
            return $this->_db->query(" DELETE FROM `{$this->_tabela}` WHERE {$where} ");
        }

        public function lastInsertId(){
            return $this->_db->lastInsertId();
        }

        public function escapeString($val){
            //return  mysql_escape_string($val);
            //return mysql_real_escape_string($val);
            return  $val;
        }

    }
