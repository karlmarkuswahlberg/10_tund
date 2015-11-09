<?php
class InterestManager {
		
	private $connection;
	private $user_id;
	
	
	
	function __construct($mysqli, $user_id){ //k�ivitub siis, kui k�ivitame klassi. K�ivitame selle klassi data.php lehel.
		
		$this->connection = $mysqli;
		$this->user_id = $user_id;
		
		
	}

	function addInterest($name){ //nimi ongi see, mille siia fn saadame tagasi.
 		
		$response = new StdClass();
		//kas selline interest olemas
		$stmt = $this->connection->prepare("SELECT id FROM interests WHERE name = ?");
        echo $this->connection->error;
		$stmt->bind_param("s", $name);
		$stmt->execute();
		
		//kas oli 1 rida andmeid
		if($stmt->fetch()){
			$error = new StdClass();
			$error->id = 0;
			$error->message = "Huviala '".$name."' on juba olemas!";
			$response->error = $error;
			return $response;
		}
	
		//*************************
		//******* OLULINE *********
		//*************************
		//panen eelmise k�su kinni
		$stmt->close();
	
		$stmt = $this->connection->prepare("INSERT INTO interests (name) VALUES (?)");
		$stmt->bind_param("s", $name);
		
		if($stmt->execute()){
			// edukalt salvestas
			$success = new StdClass();
			$success->message = "Huviala edukalt salvestatud";
			$response->success = $success;
			
		}else{
			// midagi l�ks katki
			$error = new StdClass();
			$error->id =1;
			$error->message = "Midagi l�ks katki!";
			$response->error = $error;
		}
		
		$stmt->close();
		
		return $response;
    }
	
	function createDropdown(){ //rippmen��
		
		$html = '';
		//liidan eelmisele juurde "."
		$html .= '<select name = "dropdown_interest">';
		
		$stmt = $this->connection->prepare("SELECT id, name FROM interests");
		$stmt->bind_result($id, $name);
		$stmt->execute();
		
		//iga rea kohta, mis andmebaasis olemas on
		while($stmt->fetch()){
			
			//value tuleb aadressireale, aga optioni sisu n�idatakse. see on selleks, et saaks id j�rgi aadressi realt GET.
			$html .= '<option value="'.$id.'">'.$name.'</option>';
		}
		
		$stmt->close();
		
		//$html .= '<option selected>Test 2</option>';
		
		$html .= '</select>';
		return $html;
	}
		
		
} ?>

