<?php
class User{ //this-iga  saab kätte kui on klass
	
	
	private $connection;
	
	//see funktsioon käivitub kui tekitame uue instantsi. 
	//nt: new User()
	function __construct($mysqli){ //siin võtame mysqli ühenduse vastu, mis tektiati functions.php üleval.
		
		
		$this->connection = $mysqli; //$this-> on see klass e User. ja see aitab private $connectionit siia functionisse kättesaadavaks teha.
		
	}
	function logInUser($email, $hash){
		
		$response = new StdClass(); //selline muutuja võiks iga fn üleval esimene olla.
		
		//päring kas email on andmebaasis olemas
		$stmt = $this->connection->prepare("SELECT id FROM user_sample WHERE email=?");
		$stmt->bind_param("s", $email);
		$stmt->bind_result($id);
		$stmt->execute();
		//kontrollime, kas saime rea andmeid.

		//kas selline email on?
		if(!$stmt->fetch()){
			//ei ole 
			$error = new StdClass();
			$error->id = 0;
			$error->message = "E-mail on vale!";
			$response->error = $error;
			return $response;
		}
		
		//OLULINE: tuleb kahe SELECTi vahel stmt closeda.
		$stmt->close();
		
		
		$stmt = $this->connection->prepare("SELECT id, email FROM user_sample WHERE email=? AND password=?"); //muutus siin. läbi connectioni.
        $stmt->bind_param("ss", $email, $hash);
        $stmt->bind_result($id_from_db, $email_from_db);
		$stmt->execute();
		
			if($stmt->fetch()){
				
				//selline kasutaja on olemas.
				$success = new StdClass();
				$success->message = "Sai edukalt sisse logitud!";
				
				$user = new StdClass();
				$user->id = $id_from_db;
				$user->email = $email_from_db;
				
				$success->user = $user;
				
				$response->success = $success;
				
            }else{
				
				
				$error = new StdClass();
				$error->id = 1;
				$error->message = "Vale parool";
				$response->error = $error;
			}
				
            
			
            $stmt->close();
			 
			return $response;
	}
	
	function createUser($create_email, $hash){
		
		//objekt, kus tagastame errori (id, message) või success'i (message).
		$response = new StdClass();
		
		$stmt = $this->connection->prepare("SELECT id FROM user_sample WHERE email=?");
		$stmt->bind_param("s", $create_email);
		$stmt->bind_result($id);
		$stmt->execute();
		//kontrollime, kas saime rea andmeid. 
		if($stmt->fetch()){
			
			//siis saime ja kui saime, siis email on juba olemas. ei saa regada.
			$error = new StdClass();
			$error->id = 0;
			$error->message = "E-mail on juba kasutusel!";
			
			$response->error = $error;
			
			return $response; //kuna ei taha, et alumine pool üldse käivituks. ehk kasutaja loomine jääks katki.
			
		}
		
		//siia tuleb siis kui email on vaba, saab regada.
	
	
		$stmt = $this->connection->prepare("INSERT INTO user_sample (email, password) VALUES (?,?)");
		$stmt->bind_param("ss", $create_email, $hash);
		if($stmt->execute()){
			//sisestamine õnnestus kui siiani on jõudnud.
			
			$success = new StdClass();
			$success->message = "Kasutaja loodud!";
			$response->success = $success;
			
		}else{
			
			//ei õnnestunud
			$error = new StdClass();
			$error->id = 1;
			$error->message = "Midagi läks nihu!";
			
			$response->error = $error;
		}
        $stmt->close();
		
		return $response;
	}
	
} ?>