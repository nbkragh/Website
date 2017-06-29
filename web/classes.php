<?php
class Database{
	public $connect;
	
	function __construct($database,  $server="localhost",$root="root",$pw = "" ){
		
		$this->connect = mysqli_connect($server,$root,$pw, $database);

		mysqli_set_charset($this->connect, "utf8");
	}

	public function insert_sql($table,$content){
		$sql = "INSERT INTO `".$table."` ";
		$sql .= "(`".implode("`,`", array_keys($content))."`) ";
		$sql .="VALUES ('".implode("','", $content)."');";
		
		return $sql;
	}
	// example : $Databaseobject->insert("tabelnavn",array("kolonnenavn1"=>"værdi1","kolonnenavn2"=>"værdi2", ... ));


	public function update_sql($table, $content, $condition){
		$sql = "UPDATE `".$table."` SET ";
		reset($content);
		$sql .= "`".key($content)."`='".$content[key($content)]."'";
		//definér en kolonne to gange!
		foreach ($content as $key => $value) {
			$sql .= ", `".$key."`='".$value."' ";
		}
		$sql .= $this->autoWHERE($condition).";";

		return $sql;
	}
	// ex. : $Databaseobject->update_sql("tabelnavn", array("indhold"=>"nyt"), array("inhold"=>"gammelt", "moderne"=>array("meget", "rigtig meget")) )


	public function delete_sql($table, $condition){
		$sql = "DELETE FROM `".$table."` ".$this->autoWHERE($condition).";";
		return $sql;
	}


	public function autoWHERE($condition){
		if (!empty($condition) && is_array($condition) ) {
			$where =" WHERE 1=1 ";
			foreach ($condition as $key => $value) {
				$where .= "AND `".$key."` IN ( 1=1";
				if (is_array($value) ) {
					foreach ($value as $possible) {
						$where .=", '".$possible."' ";
					}
				}else{
					$where .=", '".$value."' ";
				}
				$where .= ") ";
			}
			return $where;			
		}
	}
	// kan tage 2dimensionelle arrays, for at en betingelse kan have flere værdier til én kolonne. 
	//ex: $Databaseobject->autoWHERE(array("kolonne1"=>"værdi","kolonne2"=>array("værdi2","værdi3"))) 

	public function query($sql){
			$result = mysqli_query($this->connect, $sql) or die (mysqli_error($this->connect));
			return $result ;
		}
		// tilføj " or die (mysqli_error($this->connect)) " til $result ved troubleshooting

		
	public function sanitizer($input){
		if (is_array($input)) {
			foreach ($input as $key => $value) {
				$value = trim($value);
				// $value = htmlspecialchars($value);
				$value = mysqli_real_escape_string($this->connect,$value);
			}
			return $input;
		}else{
			$input = trim($input);
			// $input = htmlspecialchars($input);
			$input = mysqli_real_escape_string($this->connect,$input);
			return $input;
		}	
	}

	public function save_message($string){
		if(!isset($_SESSION)){ 
	        session_start(); 
	    };
	    
	    $_SESSION['message']['aktuel'] = $string;
	}
	public function load_message(){
		if(!isset($_SESSION)){ 
	        session_start(); 
	    }
	    if (!empty($_SESSION['message']['aktuel'])) {

	    	$message = $_SESSION['message']['aktuel'];
	    	$_SESSION['message'][] = $_SESSION['message']['aktuel'];
	    	empty($_SESSION['message']['aktuel']);
	    	unset($_SESSION['message']['aktuel']);

	    	return $message;
	    }else{
	    	return "";
	    }
	}

	public function save_forminput(){
		if(!isset($_SESSION)){ 
	        session_start(); 
	    };
	    empty($_SESSION['forminput']);
	    unset($_SESSION['forminput']);
	    $_SESSION['forminput'] = $_POST;
	}

	public function load_forminput($key){
		if(!isset($_SESSION)){ 
	        session_start(); 
	    };
	    if (!empty($_SESSION['forminput'][$key])) {
	    	$input = $_SESSION['forminput'][$key];
	    	unset($_SESSION['forminput'][$key]);
	    	return $input;
	    }else{
	    	return "";
	    }
	}

}
class IMG{
	private $userid;
	public $error = array();
	public $billedfiler = array();

	function __construct($files_array, $userid){
		$this->userid = $userid;
		foreach ($files_array['error'] as $key => $value) {
			if ($value == 0) {
				$this->billedfiler["old_name"][$key] = $files_array['name'][$key];
				$this->billedfiler["new_name"][$key] = $this->renameIMG($files_array['name'][$key]);
				$this->billedfiler["type"][$key] = $files_array['type'][$key];
				$this->billedfiler["size"][$key] = $files_array['size'][$key];
				$this->billedfiler["tmp_name"][$key] = $files_array['tmp_name'][$key];
			}else{
				$this->error[$key] = $files_array['name'][$key];
				echo $this->error;
			}
		}
	}

	private function renameIMG($img_name){
		$img_name = time()."_".$this->userid."-".$img_name;
		return $img_name;
	}

	public function saveIMG($destination, $resize_bredde ="", $resize_hojde=""){
		if(!empty($this->billedfiler)){
			foreach ($this->billedfiler['tmp_name'] as $key => $value) {

					copy($this->billedfiler["tmp_name"][$key], $destination."/".$this->billedfiler["old_name"][$key]);

				if (!empty($resize_bredde) && $resize_bredde > 0 && !empty($resize_hojde) && $resize_hojde > 0 ) {

					$original_size = getimagesize($destination."/".$this->billedfiler["old_name"][$key]);
					$original_bredde = $original_size[0];
					$original_hojde = $original_size[1];

					$ratio = $original_bredde / $original_hojde;

						$ny_bredde = $resize_bredde;
						$ny_hojde = $resize_hojde;

					if ($original_bredde > $original_hojde) {
						$ny_hojde = $resize_bredde / $ratio;
						
					}elseif($original_bredde < $original_hojde){
						$ny_bredde = $resize_hojde * $ratio;
						
					}elseif($original_bredde == $original_hojde) {

						$ny_hojde = ($resize_hojde<$resize_bredde) ? $resize_hojde : $resize_bredde ;
						$ny_bredde = $ny_hojde;
					}

					$fil_type = strrchr($this->billedfiler["new_name"][$key], ".");
					$ramme = imagecreatetruecolor($ny_bredde, $ny_hojde);

					switch ($fil_type) {
						case '.jpeg':
						// denne tomme case udfører den derefter følgende case...
						case '.jpg':
							$motiv = imagecreatefromjpeg($destination."/".$this->billedfiler["old_name"][$key]);
							imagecopyresampled($ramme, $motiv, 0, 0, 0, 0, $ny_bredde, $ny_hojde, $original_bredde, $original_hojde);
							imagejpeg($ramme, $destination."/".$this->billedfiler["new_name"][$key]);
							
							break;
						case '.png':
							$motiv = imagecreatefrompng($destination."/".$this->billedfiler["old_name"][$key]);
							imagecopyresampled($ramme, $motiv, 0, 0, 0, 0, $ny_bredde, $ny_hojde, $original_bredde, $original_hojde);
							imagepng($ramme, $destination."/".$this->billedfiler["new_name"][$key]);
							
							break;
						case '.gif':
							$motiv = imagecreatefromgif($destination."/".$this->billedfiler["old_name"][$key]);
							imagecopyresampled($ramme, $motiv, 0, 0, 0, 0, $ny_bredde, $ny_hojde, $original_bredde, $original_hojde);
							imagejpeg($ramme, $destination."/".$this->billedfiler["new_name"][$key]);
							
							break;
						default:
							return false;
							break;
					}
					chmod($destination."/".$this->billedfiler["new_name"][$key], 0600);
					if(is_file($destination."/".$this->billedfiler["old_name"][$key])){
						unlink($destination."/".$this->billedfiler["old_name"][$key]);
					}	
				}	
			}
		}
	}
}

class Kurv{
	private $bruger;
	function __construct(){
		if(!isset($_SESSION)){ 
	    	session_start(); 
    	};
		if (!empty($_SESSION['bruger']['id'])) {
			$this->bruger = $_SESSION['bruger']['id'];
		}else{
			$this->bruger = 0;
		}
		if (empty($_SESSION['kurv'])) {
			$_SESSION['kurv']['bruger'] = $this->bruger;
		}
	}

	public function tilfoj_vare($vare_id, $vare_pris, $vare_navn){
		$ny = TRUE;
		if (!empty($_SESSION['kurv']['poster'])) {
			foreach ($_SESSION['kurv']['poster'] as $key => $value) {
				if ($value['vare_id'] == $vare_id) {
					++$_SESSION['kurv']['poster'][$key]['vare_antal'];
					$_SESSION['kurv']['poster'][$key]['post_sum'] +=floatval($vare_pris);
					$ny = FALSE;
				}
			}
		}
		if ($ny) {
			$_SESSION['kurv']['poster'][] = array("vare_id" => $vare_id, "vare_navn" => $vare_navn, "vare_pris" => floatval($vare_pris), "vare_antal" => 1, "post_sum" => floatval($vare_pris));
		}
	}

	public function fjern_vare($vare_id, $vare_pris){
		foreach ($_SESSION['kurv']['poster'] as $key => $value) {
			if ($value['vare_id'] == $vare_id) {
				--$_SESSION['kurv']['poster'][$key]['vare_antal'];
				$_SESSION['kurv']['poster'][$key]['post_sum'] -=floatval($vare_pris);
				if ($_SESSION['kurv']['poster'][$key]['vare_antal'] < 1) {
					$_SESSION['kurv']['poster'][$key] = "";
					unset($_SESSION['kurv']['poster'][$key]);
				}		
			}

		}
		if (empty($_SESSION['kurv']['poster'])) {
			$_SESSION['kurv']['poster'] = "";
			unset($_SESSION['kurv']['poster']);
		}
	}
	public function set_antal($vare_id, $antal){
		foreach ($_SESSION['kurv']['poster'] as $key => $value) {
			if ($value['vare_id'] == $vare_id) {
				$_SESSION['kurv']['poster'][$key]['vare_antal'] = $antal;
				$_SESSION['kurv']['poster'][$key]['post_sum'] = $antal*floatval($_SESSION['kurv']['poster'][$key]['vare_pris']);
				if ($_SESSION['kurv']['poster'][$key]['vare_antal'] < 1) {
					$_SESSION['kurv']['poster'][$key] = "";
					unset($_SESSION['kurv']['poster'][$key]);
				}		
			}

		}
	}
}

?>

