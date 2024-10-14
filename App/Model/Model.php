<?php

namespace App\Model;

use App\Database\Database;
use PDO;
use PDOException;

class Model extends Database {

    public static function getAll() : array 
    {
        $con = self::connect(); 
        try {
            $query = "SELECT * FROM " . static::$table_name;

            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function delete($id){
        try{
            $query = "DELETE FROM " . static::$table_name . " WHERE id = :id"; 
            
            $stmt = self::connect()->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return true;
            
        }catch(PDOException $e){
            return false;
        } 
    }

    public static function edit($new_name,$id){
       
        try{    
            $query = "UPDATE " .  static::$table_name . " SET name = '{$new_name}' WHERE id = '{$id}'";
    
            $stmt = self::connect()->prepare($query);
            $stmt->execute();
            return true;
        
        }catch(PDOException $e){
            return false;

        }
    }

    public static function create($name) {
        try {
            $query = "INSERT INTO " . static::$table_name . " (name) VALUES (:name)";
    
            $stmt = self::connect()->prepare($query);
    
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    
            if ($stmt->execute()) {
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "Error executing query: " . $errorInfo[2];
                return false;
            }
    
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        }
    }

    public static function register_user($data){

        $values = '';
        foreach($data as $key=>$value){
            if($key =='password'){
                $value = md5(($value));
            }
            $values .= "'{$value}' , ";
        }

        $cleanString = rtrim($values,", ");
        // dd($cleanString);
        $conn = self::connect();
        $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (" . $cleanString . ")");
        return $stmt->execute();

    }
    public static function get_user($data){

        $values = '';
        foreach($data as $key=>$value){
            if($key =='password'){
                $value = md5(($value));
            }
            $values .= "{$key} = '{$value}' AND ";
        }

        $cleanString = rtrim($values,"AND ");
        // dd($cleanString);
        $conn = self::connect();
        $stmt = $conn->prepare("SELECT * FROM users WHERE (" . $cleanString . ")");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    

    
}
