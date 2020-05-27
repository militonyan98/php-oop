<?php

	class Database{
        
        protected $connection;
        protected $user;
        protected $name;
        protected $host;
        protected $password;
        
		function __construct($host="localhost", $user="root", $password="", $name=""){
            $this->connection = new mysqli($host, $user, $password, $name);
            if ($this->connection->connect_errno) {
                $this->error("Failed to connect to the database: " . $this->connection->connect_error);
                exit;
            }
            return $this->connection;
		}
		
		public function select($query){
            $data = $this->connection->prepare($query);
            $data->execute();
            if($data){
                while ($row =  mysqli_fetch_assoc($data)) {
                    $result[] = $row;
                    return $result; //array
                }
            }
            else{
                $this->error("Failed to select from the table: ".$this->connection->connect_error);
            }
			
        }
        
		public function insert($table, $data){
            $keys = $values = "";
            foreach($data as $key => $value){
                $keys .= $this->connnection->real_escape_string($key).", "; 
                $values .= "'".$this->connnection->real_escape_string($value)."'".", ";
            }

            $keys = substr($keys, 0, -1);
            $values = substr($values, 0, -1);

            $query = "INSERT INTO $table ($keys) VALUES $values";
            $result = $this->connection->prepare($query);
            $result->execute();
			if($result){
                return $result;
            }
            else{
                $this->error("Failed to insert into ".$table.": ".$this->conneection->connect_error);
            }

        }
        
		public function update($table, $data, $where){
            $values = "";
            foreach ($data as $key => $value) {
                $values .= $this->connection->real_escape_string($key)
                ." = "
                ."'"
                .$this->connection->real_escape_string($value)
                ."'"
                .", ";
            }
            $values = substr($values, 0, -2);

            $query = "UPDATE $table SET $values WHERE " .$where;
            $result = $this->connection->prepare($query);
            $result->execute();
            if($result) {
                return $result;
            }else {
                $this->error("Failed to update table ".$table.$this->connection->connect_error);
            }
		}
		
		public function delete($table, $where){
            $query = "DELETE FROM ".$table;
            if($where){
                $query.=" WHERE ".$where;
            }
            $result = $this->connection->prepare($query);
            $result->execute();
            if($result){
                return $result;
            }
            else{
                $this->error("Failed to delete: ".$this->connection->connect_error);
            }
        }
        
         private function error($error){
            echo $error;
        }
	}