<?php



//INICIO DE LA CLASE DbMysql


class DbMysql {

	// VARIABLES PUBLICAS DE LA CLASE
	
	public $errorlog = 1;
	
	//  PROPIEDAD $dbhostname -> Dominio del servidor MySQL
	public $dbhostname = 'localhost'; 
	
	// PROPIEDAD $dbuser -> Usuario
	public $dbuser = 'root';
	
	// PROPIEDAD $dbpassword -> Contraseña
	public $dbpassword = '';
	
	// PROPIEDAD $dbselect -> Base de datos a activar
	public $dbselect = 'example'; 	  
    
    public $typearray = MYSQLI_BOTH; // MYSQLI_ASSOC, MYSQLI_NUM,  MYSQLI_BOTH
	
	
	//METODO connect() -> Conecta al servidor MySQL
	
	public function connect(){
        
        $dblink=mysqli_connect($this->dbhostname,$this->dbuser,$this->dbpassword)or die($this->errorlog());
        
			if ($dblink){
				return $dblink;
			}else{
				return false;
			}
        
	}
	

	
	// METODO close($dblink)->Cierra la conexion al servidor: parametro $dblink -> link de conexion al servidor
	 
	public function close($dblink){
		
		$conect_close=mysqli_close($dblink);
		if ($conect_close){
			return true;
		}
     	 
	}
	
	
	// METODO dbselect($dblink) -> Seleciona la base de datos que se debe activar
	
	public function dbselect($dblink){
		
		$dbselect=mysqli_select_db($dblink,$this->dbselect)or die($this->errorlog($dblink));
			if ($dbselect){
				return true;
			}else{
				return false;
			}
	}
	
	
	// METODO dbcreate($dbname='') -> Crear base de datos
	
	public function dbcreate($dbname='') {
						
		$dblink=$this->connect();
		if (!empty($dbname)){
			$result=mysqli_query($dblink,"CREATE DATABASE $dbname") or die($this->errorlog());
			return true;
		}else{
			return false;
		}
		$this->close($dblink);
		
	}
	
	
	// METODO dbdelete($dbname) -> Eliminar base de datos
	
	public function dbdelete($dbname=''){
		
		$dblink=$this->connect();
		if (!empty($dbname)){
			$result=mysqli_query($dblink,"DROP DATABASE IF EXISTS $dbname");
			return $result;
		}else{
			return false;
		}
		$this->close($dblink);
	}
	
	
	// METODO dbshow($dbname) -> listar bases de datos
	
	public function dbshow(){
		
		$dblink=$this->connect();
		$result=mysqli_query($dblink,"SHOW DATABASES");
		$result=$this->fetch_all($result);
		return $result;
		$this->close($dblink);
		
	}
	
	
	// METODO tbcreate($tabname, $fields) -> Crear tabla
	
	public function tbcreate($tabname='', $fields=array()){
		
		$query_fields='';
		
		$dblink=$this->connect();
		$dbselect = $this->dbselect($dblink);
		if ((!empty($tabname)) and (!empty($fields))){
			
			foreach ($fields as $clave => $valor){
				if (is_numeric($clave)){
					 $query_fields .= $valor.',';
				}
			}
			
			$query="CREATE TABLE IF NOT EXISTS ".PREFIX.$tabname." (";	
			$query.=rtrim($query_fields, ',');
			$query.=")  ENGINE=".$fields['ENGINE']." DEFAULT CHARSET=".$fields['CHARSET']." COLLATE=".$fields['COLLATE'].";";
			
			$result=mysqli_query($dblink,$query)or die($this->errorlog($dblink));
			
			return $result;
		}else{
			return false;
		}
		$this->close($dblink);
		
	}
	
	public function tbdelete($tabname=''){
		
		if ((!empty($tabname))){
		
			$dblink=$this->connect();
			$dbselect = $this->dbselect($dblink);

			$query=	"DROP TABLE IF EXISTS ".PREFIX.$tabname;
			$result=mysqli_query($dblink,$query)or die($this->errorlog($dblink));
		    return $result;
		}else{
		    return false;
		}
		$this->close($dblink);
	}
		
	public function tbaddcolumn(){}
		
	public function tbdelcolumn(){}
		
	public function fetch_all($result='',$method = MYSQLI_BOTH){
		$rows = mysqli_fetch_all($result, $method);
		if ($rows){
			return $rows;
		}else{
			return false;
		}
	}
	
	public function execute($query=''){
		
 		if (empty($query)){return false;}
        $dblink=$this->connect();
		$dbselect = $this->dbselect($dblink);
		$result_query=mysqli_query($dblink,$query)or die($this->errorlog($dblink));
		$rows = mysqli_fetch_all($result_query, $this->typearray);
		$this->close($dblink);
		return $rows;
	}
	
	public function dtselect( $table, $field='*', $condition='', $limit='0, 10'){
		
		if (empty($field)){$field = '*';}
		if (!empty($condition)){$condition = 'WHERE '.$condition;}
		if (!empty($limit)){$limit = 'LIMIT '.$limit;}
		if (empty($table)){return false;
						   
			}else{

			$dblink=$this->connect();
			$dbselect = $this->dbselect($dblink);
			$result_query=mysqli_query($dblink,"SELECT $field FROM $table $condition $limit")or die($this->errorlog($dblink));
			$rows = mysqli_fetch_all($result_query, $this->typearray );
			$this->close($dblink);
			return $rows;
		}
	} 
	
	public function dtinsert($tbname='', $data = array()){
		
		$data_fields = '';
		$data_values = '';

			$dblink=$this->connect();
			$dbselect = $this->dbselect($dblink);
			
		
		    foreach ($data as $clave => $valor){
				if(is_numeric($clave)){
					if ($clave == 0){

						foreach( $valor as $clave => $valor){

							$data_fields .= "`".$valor."`,"; 
						}

					}else{

						$data_values .= "(";
						foreach( $valor as $clave => $valor){

							$data_values .= "'".$valor."',";
						}
						$data_values  =	rtrim($data_values, ',');
						$data_values .="),";

					}
				}
			}
		
			$data_fields  = rtrim($data_fields, ',');
			$data_values  =	rtrim($data_values, ',');
		

			$query=	"INSERT INTO `$tbname` ($data_fields) VALUES $data_values ;";
			$result=mysqli_query($dblink,$query) or die($this->errorlog($dblink));
			$this->close($dblink);
			return $result;
	} 
	
	public function dtupdate($tbname='',$fields=array()){
		
		$order='';
		$data_fields='';
		$limit = '';
		

			$dblink=$this->connect();
			$dbselect = $this->dbselect($dblink);
		
			foreach( $fields as $clave => $valor){
				
				if ($clave=='WHERE'){$where = "WHERE $valor";}
				if ($clave=='LIMIT'){$limit = "LIMIT $valor";}
				if ($clave=='ORDER'){$order = "ORDER BY $valor";}
				if ($clave=='FIELDS'){
					
					foreach($valor as $clave => $valor){
						$data_fields.= "`$clave` = '$valor',";
					}
					
				}
				
			}
			$data_fields  = rtrim($data_fields, ',');
			$query = "UPDATE `$tbname` SET $data_fields $where $order $limit";	
			$result=mysqli_query($dblink,$query) or die($this->errorlog($dblink));
            $rows_affect = mysqli_affected_rows($dblink);
			$this->close($dblink);
			return $rows_affect;
	} 
	
	public function dtdelete($tbname='', $condition=''){
		
		$dblink=$this->connect();
		$dbselect = $this->dbselect($dblink);
		if (empty($condition)){
			$result = '<strong>El parametro $condition esta vacio;</strong><br />'.
			'Por seguridad el parametro $condition no puede estar vacio, si lo que pretende es eliminar todo el contenido de la tabla envie la palabra ALL en el parametro $condition ';
		}else{
			if ($condition=='ALL'){
			$query= "DELETE FROM $tbname";
			}else{
			$condition = " WHERE $condition";
			$query = "DELETE FROM $tbname $condition";
			$result = mysqli_query($dblink,$query) or die($this->errorlog($dblink));
		   }
		}	
		return $result;
	} 
    
    
    public function mysql_server(){
        
              $dblink=$this->connect();
        
       return mysqli_get_host_info($dblink).'  version->'.mysqli_get_server_info($dblink);
        
    }
	
	public function errorlog($dblink='') {
		
		
		
		if (empty($dblink)){
		
		$error = "[". date("d/m/y , h:i:s")."] ". 'Error MYSQL Connect: '.mysqli_connect_errno()." : ".mysqli_connect_error()."\r\n";
		
		}else{
				
		$error = "[". date("d/m/y , h:i:s")."] ". 'Error MYSQL:'. mysqli_errno($dblink) . " : " . mysqli_error($dblink)."\r\n";
		
		}
		
		$archivo = fopen('errorlog.txt','a');
		fputs($archivo, $error); 
		fclose($archivo); 
		if ($this->errorlog!=0)return $error;
		 
	}
		

}



	
?>
