<?php
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'anm56');
define('USERNAME', 'anm56');
define('PASSWORD', 'anshul2311');
define('CONNECTION', 'sql1.njit.edu');
class dbConn{
    //variable to hold connection object.
    protected static $db;
    //private construct - class cannot be instantiated externally.
    private function __construct() {
        try {
            // assign PDO object to db variable
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch (PDOException $e) {
            //Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
        }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection() {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) {
            //new connection object.
            new dbConn();
        }
        //return connection.
        return self::$db;
    }
}
class collection {
protected $html;
    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
}
class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
class model {
//-----------------
protected $tableName;
public function save()
    
    {
        if ($this->id != '') {
            $sql = $this->update($this->id);
        } else {
           $sql = $this->insert();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
        foreach (array_flip($array) as $key=>$value){
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
    }
    private function insert() {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        print_r($columnString);
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }
    private function update($id) {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value){
            if( ! empty($value)) {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
            }
        }
        $sql .= ' WHERE id='.$id;
        return $sql;
    }
    public function delete($id) {
        $db = dbConn::getConnection();
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $sql = 'DELETE FROM '.$tableName.' WHERE id='.$id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}
    
//---------------------------
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
        $tableName='accounts';
        return $tableName;
    }
}
//-----------------------------------
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
        $tableName='todos';
        return $tableName;
    }
}
//---------- Accounts Table----------
//---------- Find All ----------
echo"<h2> Accounts Table</h2>";
$records = accounts::findAll();
// to print all accounts records in html table  
  $html = '<table border = 2><tbody>';
  // Displaying Header Row
  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
    print_r($html);
//--------------------------- Find Unique Record---------------
    echo"<h2>Search account table by id</h2>";
$record = accounts::findOne(4);
 // Displaying Header Row 
  
  $html = '<table border = 2><tbody>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
    
    print_r($html);
//-------------------------- Insert Record---------------------
 echo "<h2>Insert One Record</h2>";
$record = new account();
$record->email="test@njit.edu";
$record->fname="aa";
$record->lname="mmmm";
$record->phone="987654";
$record->birthday="11-23-1994";
$record->gender="male";
$record->password="123456";
$record->save();
$records = accounts::findAll();
$html = '<table border = 2><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
echo "<h2>After Inserting</h2>";
print_r($html);
//------------------------- Delete Record -------------------
echo "<h2>Delete One Record</h2>";
$record= new account();
$id=7;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';
//'<h3>After Delete</h3>';
$record = accounts::findAll();
//print_r($records);
$html = '<table border = 2><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
echo "<h3>After Deleteing</h3>";
print_r($html);
//----------Update Record----------
echo "<h2>Update One Record</h2>";
$id=4;
$record = new account();
$record->id=$id;
$record->fname="fname_Update";
$record->lname="lname_Update";
$record->gender="gender_Update";
$record->save();
$record = accounts::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table border = 2><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
 
 print_r($html);
//----------End Of Account Table ----------
 echo"<h1> TODO TABLE FUNCTIONS</h1>";
//---------- Todo Table----------
 echo "<h2>Search all for todo table</h2>";
 $records = todos::findAll();
 // to print all accounts records in html table  
  $html = '<table border = 2><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
    echo "Todo table";
    print_r($html);
//----------Find Unique id----------
    echo"<h2>Search by unique id</h2>";
 $record = todos::findOne(3);
 // Displaying Header Row 
  print_r("Todo table id - 3");
  
  $html = '<table border = 2><tbody>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
    
    print_r($html);
//----------Insert Record----------
   echo "<h2>Insert One Record</h2>";
        $record = new todo();
        $record->owneremail="anm56@njit.edu";
        $record->ownerid=23;
        $record->createddate="11-17-2017";
        $record->duedate="11-19-2017";
        $record->message="Active record Pratice";
        $record->isdone=1;
        $record->save();
        $records = todos::findAll();
        echo"<h2>After Inserting</h2>";
 
     $html = '<table border = 2><tbody>';
  // Displaying Header Row 
  
      $html .= '<tr>';
      foreach($records[0] as $key=>$value)
         {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
//echo "<h2>After Inserting</h2>";
print_r($html);
//----------Delete record for todo ----------
echo "<h2>Delete One Record</h2>";
$record= new todo();
$id=7;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';
//'<h3>After Delete</h3>';
$record = todos::findAll();
//print_r($records);
$html = '<table border = 2><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
echo "<h2>After Deleteing</h2>";
print_r($html);
//----------Update todos record ----------
echo "<h2>Update One Record</h2>";
$id=4;
$record = new todo();
$record->id=$id;
$record->owneremail="anm56@njit.edu";
$record->ownerid="23";
$record->createddate="11-17-2017";
$record->duedate="11-19-2017";
$record->message="This is Anshul";
$record->isdone="1";
$record->save();
$record = todos::findAll();
echo "<h2>Record update with id: ".$id."</h2>";
        
$html = '<table border = 2><tbody>';
  // Displaying Header Row 
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows 
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</tbody></table>';
 
 print_r($html);
