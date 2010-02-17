<?php
	require_once 'Core.php';
	/**
	 * User Class, getting its input from XMLRPC and HTTP Requests.
	 * 
	 * @author lazlow (aka. Ioannes Amadopolis) <lorenzo.van.matterhorn@gmx.de>
	 */
	class User {
		//And now some static variables that will bore you:
		const IDTYPE_PID = 0;
		const IDTYPE_NID = 1;
		const IDTYPE_GID = 2;
		const COUNTY_MH = 3;
		const COUNTY_NB = 4;
		const COUNTY_PY = 5;
		const COUNTY_DY = 6;
		
		/**
		 * ID of the user
		 * @var Integer
		 */
		private $id;
		/**
		 * ID Type (One of IDTYPE_* constants)
		 * 
		 * @var int
		 */
		private $idtype;
		/**
		 * Users titlte (e.g. Sir)
		 * 
		 * @var string
		 */
		private $title;
		/**
		 * Users First Name (e.g. Ioannes)
		 * 
		 * @var string
		 */
		private $first_name;
		/**
		 * Users Family Name (e.g. Amadopolis)
		 * 
		 * @var string
		 */
		private $name;
		/**
		 * Users City (e.g. Klauth)
		 * 
		 * @var string
		 */
		private $city;
		/**
		 * Users County (One of COUNTY_* constraints)
		 * 
		 * @var int
		 */
		private $county;
		/**
		 * Users Birthdate - as UNIX Timestamp. (Birthday at midnight)
		 * 
		 * @var timestamp
		 */
		private $birthdate;
		/**
		 * Residency Time in Dionysos - as UNIX Timestamp
		 * 
		 * @var timestamp
		 */
		private $residency_dionysos;
		/**
		 * Residency Time in Users County
		 * 
		 * @var timestamp
		 */
		private $residency_county;
		/**
		 * Residency Time in Users City
		 * 
		 * @var timestamp
		 */
		private $residency_city;
		/**
		 * Last Seen Time as UNIX Timestamp
		 * 
		 * @var timestamp
		 */
		private $last_seen;
		/**
		 * Appointments of the User (Mayor, Chancelor etc.)
		 * 
		 * @var array 
		 */
		private $appointments = array();
		/**
		 * Firms in which the user is - not implemented yet
		 * 
		 * @todo Implement Firm Parsing
		 * @var array of DionyAPI::Firm Objects
		 */
		private $firms = array();
		/**
		 * The Party the user is in or null if none - not implemented yet
		 * 
		 * @todo Implement Party Parsing
		 * @var array of DionyAPI::Party Object
		 */
		private $partey = array();
		/**
		 * The Club the user is in  - not implemented yet
		 * 
		 * @todo Implement Club Parsing
		 * @var array of DionyAPI::Club Object
		 */
		private $clubs = array();
		/**
		 * Pointer to the XMLRPC Instance from Core
		 * @var xmlrpc
		 */
		private $xr;
		function __construct($id) {
			if (empty($id))
				throw new InvalidArgumentException("ID can not be null");
			$this->xr = &Core::getXMLRPCInstance();
			$result = $this->xr->send(new xmlrpcmsg('lesen', array(new xmlrpcval($id))));
			if (Core::checkXMLRPCResult($result)) {
				$this->id = $id;
				$typ = php_xmlrpc_decode($result->structmem('Typ'));
				switch ($typ) {
					case 'P':
						$this->idtype = self::IDTYPE_PID;
						break;
					case 'N':
						$this->idtype = self::IDTYPE_NID;
						break;
					case 'G':
						$this->idtype = self::IDTYPE_GID;
						break;
				}
				
				$this->name = php_xmlrpc_decode($result->structmem('Nachname'));
				$this->first_name = php_xmlrpc_decode($result->structmem('Vorname'));
				$this->title = php_xmlrpc_decode($result->structmem('Titel'));
				$this->city = php_xmlrpc_decode($result->structmem('Stadt'));
				$county = php_xmlrpc_decode($result->structmem('Land'));
 			 	switch ($county) {
 			 		case 'Milhet':
 			 			$this->county = self::COUNTY_MH;
 			 			break;
 			 		case 'Niederbergen':
 			 			$this->county = self::COUNTY_NB;
 			 			break;
 			 		case 'Papyrie':
 			 			$this->county = self::COUNTY_PY;
 			 			break;
 			 		case 'Doiyran':
 			 			$this->county = self::COUNTY_DY;
 			 			break;
 			 	}
 			 	
 			 	$this->residency_dionysos = self::fetchResidency($id);
			}
		}
		
		/**
		 * @return the $id
		 */
		public function getId() {
			return $this->id;
		}
	
			/**
		 * @return the $idtype
		 */
		public function getIdtype() {
			return $this->idtype;
		}
	
			/**
		 * @return the $title
		 */
		public function getTitle() {
			return $this->title;
		}
	
			/**
		 * @return the $first_name
		 */
		public function getFirst_name() {
			return $this->first_name;
		}
	
			/**
		 * @return the $name
		 */
		public function getName() {
			return $this->name;
		}
	
			/**
		 * @return the $city
		 */
		public function getCity() {
			return $this->city;
		}
	
			/**
		 * @return the $county
		 */
		public function getCounty() {
			return $this->county;
		}
	
			/**
		 * @return the $birthdate
		 */
		public function getBirthdate() {
			return $this->birthdate;
		}
	
			/**
		 * @return the $residency_dionysos
		 */
		public function getResidency_dionysos() {
			return $this->residency_dionysos;
		}
	
			/**
		 * @return the $residency_county
		 */
		public function getResidency_county() {
			return $this->residency_county;
		}
	
			/**
		 * @return the $residency_city
		 */
		public function getResidency_city() {
			return $this->residency_city;
		}
	
			/**
		 * @return the $last_seen
		 */
		public function getLast_seen() {
			return $this->last_seen;
		}
	
			/**
		 * @return the $appointments
		 */
		public function getAppointments() {
			return $this->appointments;
		}
	
			/**
		 * @return the $firms
		 */
		public function getFirms() {
			return $this->firms;
		}
	
			/**
		 * @return the $partey
		 */
		public function getPartey() {
			return $this->partey;
		}
	
		/**
		 * Not implemented
		 * 
		 * @return the $clubs
		 */
		public function getClubs() {
			return $this->clubs;
		}
		/**
		 * Fetch the Residency Date (very dirty)
		 * 
		 * @see User::__construct()
		 * @deprecated Will be moved to the Constructor sooner or later
		 * @param int $id
		 */
		public static function fetchResidency($id) {
			$html = file_get_html('http://www.republik-dionysos.de/index.php?page=gesellschaft&sub=buerger&s=profil&id=' . $id);
			$mytds = $html->find('td');
			for ($i = 0; $i < count($mytds); $i++) {
				//echo "mytds[$i] = $mytds[$i]\n";
				if (strpos($mytds[$i]->innertext,'Im Staat seit:') !== false)
					$extract = $i+1;
			}
//			echo "Extract = $extract";
//			echo "\n Extracted $mytds[$extract]\n";
			$extractdate = substr(trim($mytds[$extract]->innertext), 0, 10);
	//		echo $extractdate;
			if ($extractdate == '<b>heute</' || $extractdate == '<b>gestern')
				$date = time();
			else
				$date = @strtotime($extractdate);
			
			return $date;
		}
	}
?>