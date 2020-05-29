<?php

	class Database{
        
        private $connection;
        
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
            //$data->bind_param("is", $id);
            $data->execute();
            $output = ["error" => false, "msg" => "", "data" => []];
            if($data){
                while ($row = $data->$this->connection->fetch_assoc()) {
                    $output["data"][] = $row;
                }
            }
            else{
                $output["error"] = true;
                $output["msg"] = "Failed to select from the table: ".$this->connection->connect_error;
            }
			return $output;
        }
        
		public function insert($table, $data){
            $keys = $values = "";
            foreach($data as $key => $value){
                //$keys .= $this->connnection->real_escape_string($key).", "; 
                $values .= "'".$this->connnection->real_escape_string($value)."'".", ";
            }

            $keys = substr($keys, 0, -1);
            $values = substr($values, 0, -1);

            $query = "INSERT INTO $table ($keys) VALUES $values";
            $result = $this->connection->prepare($query);
            $result->execute();
			if(!$result){
                $this->error("Failed to insert into ".$table.": ".$this->conneection->connect_error);
            }
            return $result;

        }
        
		public function update($table, $data, $where=1){
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
            if(!$result) {
                $this->error("Failed to update table ".$table.$this->connection->connect_error);
            }
            return $result;
		}
		
		public function delete($table, $where=1){
            $query = "DELETE FROM ".$table." WHERE ".$where;
            $result = $this->connection->prepare($query);
            $result->execute();
            if(!$result){
                $this->error("Failed to delete: ".$this->connection->connect_error);
            }
            return $result;
        }
        
         private function error($error){
            echo $error;
        }
	}