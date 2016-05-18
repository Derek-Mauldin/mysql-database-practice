<?php


/** user class **/
class User {

	/**
	 * User Id; this is the primary key
	 * @var int $userId
	 */
	private $userId;

	/**
	 * User Name
	 * @var string $userName
	 */
	private $userName;

	/**
	 * userEmail address of member used to sign in
	 * @var string $userEmail
	 */
	private $userEmail;

	/**
	 * date user account create
	 * @var DateTime $dateCreated
	 */
	private $dateCreated;




	/**
	 * @param mixed $newUserId of the user or null if new user
	 * @param string $newUserName
	 * @param string $newUserEmail
	 * @param DateTime $newDateCreated
	 * @throws Exception if some other exception is thrown
	 * @throws RangeException if data values are out of bounds
	 * @throws InvalidArgumentException if data types are invalid or insecure
	 */

	public function __construct($newUserId = null, $newUserName, $newUserEmail, $newDateCreated = null) {
		try {
			
			$this->setUserId($newUserId);
			$this->setUserName($newUserName);
			$this->setEmail($newUserEmail);
			$this->setDateCreated($newDateCreated);

		} catch(InvalidArgumentException $invalidArgument) {
			//rethrow the exception to the caller
			throw(new InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));

		} catch(RangeException $range) {
			//rethrow the exception to the caller
			throw (new RangeException($range->getMessage(), 0, $range));

		} catch(Exception $exception) {
			//rethrow the generic exception
			throw (new Exception ($exception->getMessage(), 0, $exception));
		}
	}

	/**
	 * accessor method for user id
	 *
	 * @return int for user id
	 */

	public function getUserId() {
		return ($this->userId);
	}

	/**
	 *mutator method for user id
	 * @param int $newUserId new value of member id
	 * @throws InvalidArgumentException if $memberId id not an integer
	 * @throws RangeException if $memberId is not positive
	 */

	public function setUserId($newUserId) {
		// if new member id is null SQL will assign a new id
		if($newUserId === null) {
			$this->userId = null;
			return;
		}
		// verify the member id is valid
		$newUserId = filter_var($newUserId, FILTER_VALIDATE_INT);
		if($newUserId === false) {
			throw(new InvalidArgumentException("User Id is not a valid integer"));
		}
		// verify the member id is positive
		if($newUserId <= 0) {
			throw(new RangeException ("User Id is Not Positive"));
		}
		//convert and store the member id
		$this->userId = intval($newUserId);
	}

	/**
	 * accessor method for user name
	 * @return string value of access level
	 */
	public function getUserName() {
		return ($this->userName);
	}

	/**
	 * mutator method for userName
	 *
	 * @param string $newUserName
	 * @throws InvalidArgumentException if $newAccessLevel is not a,s,u
	 * @throws RangeException if $newAccessLevel is not a,s,u
	 **/

	public function setUserName($newUserName) {

		$newUserName = trim($newUserName);
		
		//verify the access level is a,s,u
		$newUserName = filter_var($newUserName, FILTER_SANITIZE_STRING);
		if($newUserName === false) {
			throw(new InvalidArgumentException("User name entered is insecure."));
		}

		$this->userName = $newUserName;

	}

	/**
	 * accessor method for user email
	 *
	 * @return string value of userEmail
	 **/

	public function getUserEmail() {
		return ($this->userEmail);
	}

	/**
	 * mutator method for userEmail
	 *
	 * @param string $newUserEmail new userEmail
	 * @throws InvalidArgumentException if $newUserEmail is not a string or insecure
	 * @throws RangeException if $newEmail is > 50 characters
	 */

	public function setEmail($newUserEmail) {

		$newUserEmail = trim($newUserEmail);

		//verify the memberEmail is secure	

		if(empty($newUserEmail) === true) {
			throw(new InvalidArgumentException("userEmail can't be empty."));
		}

		$newUserEmail = filter_var($newUserEmail, FILTER_SANITIZE_EMAIL);

		if(empty($newUserEmail) === true) {
			throw(new InvalidArgumentException("memberEmail content is empty or insecure"));
		}
		//verify the memberEmail is not > 255 characters

		if(strlen($newUserEmail) > 50) {
			throw(new RangeException("memberEmail address too long"));
		}

		//store the memberEmail address

		$this->userEmail = $newUserEmail;
	}

	/**
	 * accessor for dateCreated
	 *
	 * @return DateTime
	 */

	public function getDateCreated() {
		return ($this->dateCreated);
	}

	/**
	 * mutator for dateCreated
	 *
	 * @param string $newDateCreated
	 * @throws InvalidArgumentException if $newDateCreated is not a DateTime object
	 */

	public function setDateCreated($newDateCreated) {

		// if $newDateCreated is null, the database will set the date
		if ($newDateCreated === null) {
			$this->dateCreated = null;
			return;
		}

	//	if (is_object($newDateCreated) === false && get_class($newDateCreated) !== 'DateTime') {
	//		throw (new InvalidArgumentException ("Date Created must be a DateTime object."));
	//	}

		$this->dateCreated = $newDateCreated;

	}


	/**
	 * inserts the new user into mySQL
	 *
	 * @param PDO $pdo connectection object
	 * @throws PDO exception when mySQL related errors occur
	 *
	 **/

	public function insert(PDO $pdo) {

		// wont insert a user id that has already been created
		if($this->userId !== null) {
			throw (new PDOException("not a new member"));
		}

		//creates the query template
		$query = "INSERT INTO user (userName, userEmail) VALUES (:userName, :userEmail)";


		$statement = $pdo->prepare($query);

		//attaches the atributes to the right places in the template

		$parameters = array(
			"userName" => $this->userName,
			"userEmail" => $this->userEmail,
		);

		$statement->execute($parameters);

		//updates the null return with what the SQL has provided
		$this->userId = intval($pdo->lastInsertId());
	}

	/**
	 * deletes the user from mySQL
	 *
	 * @param PDO $pdo connectection object
	 * @throws PDO exception when mySQL related errors occur
	 *
	 **/

	public function delete(PDO $pdo) {

		//makes sure a member id is not null
		if($this->userId === null) {
			throw(new PDOException("unable to delete a user that does not exist"));
		}
		//creates query template
		$query = "DELETE FROM user WHERE userId = :userId";
		$statement = $pdo->prepare($query);

		//attaches atributes to the right place in the template
		$parameters = array("userId" => $this->userId);
		$statement->execute($parameters);
	}

	/**
	 * updates a user in mySQL
	 *
	 * @param PDO $pdo connection object
	 * @throws PDOException when mySQL errors occur
	 */
	public function update(PDO $pdo) {

		// wont update a member that has not been inserted into the database
		if($this->userId === null) {
			throw(new PDOException("unable to update a user that does not exist"));
		}
		//creates the query
		$query = "UPDATE user SET userName = :userName, userEmail = :userEmail WHERE userId = :userId";
		$statement = $pdo->prepare($query);

		//attaches attributes to the right place in the template
		$parameters = array("userName" => $this->userName, "userEmail" => $this->userEmail);
		$statement->execute($parameters);
	}	

	/**
	 *get user by user id
	 *
	 * @param PDO $pdo PDO connection object
	 * @param int $userId search for user by id number
	 * @return mixed user found or null if not valid
	 * @return PDOException when mySQL errors occur
	 */

	public static function getUserByUserId(PDO $pdo, $userId) {

		//sanitize and verify the user id
		$userId = filter_var($userId, FILTER_VALIDATE_INT);

		if($userId === false) {
			throw(new InvalidArgumentException("member id is not an integer"));
		}

		if($userId <= 0) {
			throw(new RangeException("MEMBER id is not positive"));
		}
		//create the query template
		$query = "SELECT userId, userName, userEmail, dateCreated FROM user WHERE userId = :userId";
		$statement = $pdo->prepare($query);

		//attached attributes to the right place in the template
		$parameters = array("userId" => $userId);
		$statement->execute($parameters);

		//gets the member from mySQL
		try {
			$user = null;
			$statement->setFetchMode(PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$user = new User($row["userId"], $row["userName"], $row["userEmail"], $row["dateCreated"]);
			}
		} catch(Exception $exception) {

			//if the row cannot be created rethrow exception
			throw(new PDOException($exception->getMessage(), 0, $exception));
		}
		return ($user);

	}

	/**
	 * get all users
	 *
	 * @param PDO $pdo pdo connection object
	 *
	 * @return SplFixedArray all users
	 * @throws PDOException if mySQL related errors occur
	 **/

	public static function getAllUsers(PDO $pdo) {


		//create the query template
		$query = "SELECT userId, userName, userEmail, dateCreated FROM user";
		$statement = $pdo->prepare($query);

		// execute
		$statement->execute();

		//call the function to build an array of the values
		$users = null;
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$users = new SplFixedArray($statement->rowCount());

		while(($row = $statement->fetch()) !== false) {
			try {
				if($row !== false) {
					$user = new User($row["userId"], $row["userName"], $row["userEmail"], $row["dateCreated"]);
					$users[$users->key()] = $user;
					$users->next();
				}
			} catch(Exception $exception) {
				
				throw(new PDOException($exception->getMessage(), 0, $exception));
			}
		}
		
		return $users;
	}

}