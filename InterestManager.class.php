<?php
class InterestManager {
		
	private $connection;
	private $user_id;
	
	
	
	function __construct($mysqli, $user_id){ //käivitub siis, kui käivitame klassi. Käivitame selle klassi data.php lehel.
		
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
		//panen eelmise käsu kinni
		$stmt->close();
	
		$stmt = $this->connection->prepare("INSERT INTO interests (name) VALUES (?)");
		$stmt->bind_param("s", $name);
		
		if($stmt->execute()){
			// edukalt salvestas
			$success = new StdClass();
			$success->message = "Huviala edukalt salvestatud";
			$response->success = $success;
			
		}else{
			// midagi läks katki
			$error = new StdClass();
			$error->id =1;
			$error->message = "Midagi läks katki!";
			$response->error = $error;
		}
		
		$stmt->close();
		
		return $response;
    }
	
	function createDropdown(){ //rippmenüü
		
		$html = '';
		//liidan eelmisele juurde "."
		$html .= '<select name = "dropdown_interest">';
		
		$stmt = $this->connection->prepare("SELECT id, name FROM interests");
		$stmt->bind_result($id, $name);
		$stmt->execute();
		
		//iga rea kohta, mis andmebaasis olemas on
		while($stmt->fetch()){
			
			//value tuleb aadressireale, aga optioni sisu näidatakse. see on selleks, et saaks id järgi aadressi realt GET.
			$html .= '<option value="'.$id.'">'.$name.'</option>';
		}
		
		$stmt->close();
		
		//$html .= '<option selected>Test 2</option>';
		
		$html .= '</select>';
		return $html;
		
	}
	function addUserInterest($interest_id){
		
		
        
		$response = new StdClass();
		//kas selline interest olemas
		$stmt = $this->connection->prepare("SELECT id FROM user_interests WHERE interests_id = ?");
        echo $this->connection->error;
		$stmt->bind_param("i", $interest_id);
        echo $stmt->error;
		$stmt->execute();
		
		//kas oli 1 rida andmeid
		if($stmt->fetch()){
			$error = new StdClass();
			$error->id = 0;
			$error->message = "Kasutajal see huviala on juba olemas!";
			$response->error = $error;
			return $response;
		}
	
		//*************************
		//******* OLULINE *********
		//*************************
		//panen eelmise käsu kinni
		$stmt->close();
	
		$stmt = $this->connection->prepare("INSERT INTO user_interests (user_id, interests_id) VALUES (?,?)");
		$stmt->bind_param("ii", $this->user_id, $interest_id);
		
		if($stmt->execute()){
			// edukalt salvestas
			$success = new StdClass();
			$success->message = "Huviala edukalt salvestatud";
			$response->success = $success;
			
		}else{
			// midagi läks katki
			$error = new StdClass();
			$error->id =1;
			$error->message = "Midagi läks katki!";
			$response->error = $error;
		}
		
		$stmt->close();
		
		return $response;
    }
	function getUserInterests(){ //saada kõik aksutaja huvialad kätte.
		
		//on olemas kasutaja id $this->user_id;
		//kõik tema huvialade nimed
		
		$stmt = $this->connection->prepare("SELECT interests.name FROM user_interests INNER JOIN interests ON user_interests.interests_id = interests.id WHERE user_interests.user_id = ?");
		$stmt->bind_param("i", $this->user_id);
		$stmt->bind_result($name);
		$stmt->execute();
		
		while($stmt->fetch()){
			
			echo $name.", ";
		}
		//ja selle järgi saab leida nt sarnaste huvidega kasutajaid ja neid loetleda.
	}
	
		
} ?>

