<?php

  include 'databaseinfo.php';

  /**
   *
   */
  class Prenotazione
  {
      private $id;
      private $stanza_id;
      private $configurazione_id;
      private $created_at;

    function __construct($id, $stanza_id, $configurazione_id, $created_at)
    {
      $this -> id = $id;
      $this -> stanza_id = $stanza_id;
      $this -> configurazione_id = $configurazione_id;
      $this -> created_at = $created_at;
    }

    public function getId(){
      return $this -> id;
    }

    public function setId($id)
    {
      $this-> id = $id;
    }

    public function getStanzaId(){
      return $this -> stanza_id;
    }

    public function setStanzaId($stanza_id)
    {
      $this-> stanza_id = $stanza_id;
    }

    public function getConfigurazioneId(){
      return $this -> configurazione_id;
    }

    public function setConfigurazioneId($configurazione_id)
    {
      $this-> configurazione_id = $configurazione_id;
    }

    public function getCreatedAt(){
      return $this -> created_at;
    }

    public function setCreatedAt($created_at)
    {
      $this-> created_at = $created_at;
    }

    // public static function getAllPrenotazioni($conn) {
    //
    //   $sql = "
    //           SELECT * FROM prenotazioni
    //           WHERE month(created_at) = 05
    //           ORDER BY created_at DESC
    //         ";
    //
    //   $result = $conn -> query($sql);
    //
    //   if ($result -> num_rows > 0) {
    //
    //     $prenotazioni = [];
    //     while ($row = $result -> fetch_assoc()) {
    //
    //       $prenotazioni[] = new Prenotazione(
    //
    //         $row["id"],
    //         $row["stanza_id"],
    //         $row["configurazione_id"],
    //         $row["created_at"]
    //       );
    //     }
    //     return $prenotazioni;
    //   }
    //   else {
    //     echo "0 Risultati";
    //   }
    // }
  }

  /**
   *
   */
  class Stanza extends Prenotazione
  {
    private $room_number;
    private $floor;
    private $beds;

    function __construct($id, $stanza_id, $configurazione_id, $created_at, $room_number, $floor, $beds)
    {
      parent::__construct($id, $stanza_id, $configurazione_id, $created_at);

      $this -> room_number = $room_number;
      $this -> floor = $floor;
      $this -> beds = $beds;
    }

    public function setRoomNumber($room_number)
    {
      $this -> room_number = $room_number;
    }

    public function getRoomNumber()
    {
      return $this -> room_number;
    }

    public function setFloor($floor)
    {
      $this -> floor = $floor;
    }

    public function getFloor()
    {
      return $this -> floor;
    }

    public function setBeds($beds)
    {
      $this -> beds = $beds;
    }

    public function getBeds()
    {
      return $this -> beds;
    }

    public static function getAllRoom($conn){

      $sql = "
              SELECT prenotazioni.id, prenotazioni.stanza_id, prenotazioni.configurazione_id, prenotazioni.created_at, stanze.room_number, stanze.floor, stanze.beds
              FROM prenotazioni
              JOIN stanze
              ON prenotazioni.stanza_id = stanze.id
              WHERE month(prenotazioni.created_at) = 05
              ORDER BY prenotazioni.created_at DESC
              ";

      $result = $conn -> query($sql);
      // var_dump($result); die();

      $prenotazioni = []; //Mettendolo prima dell'if in caso di nessun risultato non ho Null ma un array vuoto.
      if ($result -> num_rows > 0) {

        while ($row = $result -> fetch_assoc()) {
          // var_dump($row); die();
          $prenotazioni[] = new Stanza(

            $row["id"],
            $row["stanza_id"],
            $row["configurazione_id"],
            $row["created_at"],
            $row["room_number"],
            $row["floor"],
            $row["beds"]
          );
        }
        // var_dump($prenotazioni); die();
        return $prenotazioni;
      }
    }
  }

  /**
   *
   */
  class Ospite
  {
    private $id;
    private $name;
    private $lastname;

    function __construct($id, $name, $lastname)
    {
      $this -> id = $id;
      $this -> name = $name;
      $this -> lastname = $lastname;
    }

    public function getId()
    {
      return $this -> id;
    }

    public function getName()
    {
      return $this -> name;
    }

    public function getLastName()
    {
      return $this -> lastname;
    }

    public static function getAllOspiti($conn, $id)
    {
      $sql = "
              SELECT *
              FROM prenotazioni_has_ospiti
              WHERE prenotazione_id = $id";

      $result = $conn -> query($sql);

      if ($result -> num_rows > 0) {

        $row = $result -> fetch_assoc(); // In questo caso io ho creato due query ma si poteva fare anche una join. Essendo una relazione molti a molti
        $idOspite = $row["ospite_id"];   // Ci possono essere anche più risultati quindi bisogna usare il primo approccio (array vuoto + while) ma in questo
        // var_dump($idOspite); die();   // caso essendo un db fake sono certo che ne ho solo uno e non si ripetono quindi salvo il risultato in $row.

        $sql2 = "
                SELECT id, name, lastname
                FROM ospiti
                WHERE id = $idOspite
                ";
        $result2 = $conn -> query($sql2);

        if ($result2 -> num_rows > 0) {

          $row = $result2 -> fetch_assoc();
          $ospite = new Ospite(
            $row["id"],
            $row["name"],
            $row["lastname"]
          );

          return $ospite;
        }
      }
    }
  }


  // ----------------------------------Inizio connessione DB
  $conn = new mysqli($server, $user, $password, $database);

  if ($conn -> connect_errno) {

    echo "Errore di connessione " . $conn -> connect_error;
    return;
  }
  // ------------------------------------------------------

  // $prenotazioni = Prenotazione::getAllPrenotazioni($conn);
  // var_dump($prenotazioni); die();
  $prenotazioni = Stanza::getAllRoom($conn);

  if(count($prenotazioni) > 0) {
       foreach ($prenotazioni as $key => $prenotazione) {
         // var_dump($key, $prenotazione); die();

        $ospite = Ospite::getAllOspiti($conn, $prenotazione -> getId()); // Potrei anche inserirlo dentro a $prenotazione semplicmenete con: $prenotazione -> ospite = $ospite;
        // var_dump($ospite);
        echo  "Prenotazione: " . ($key+1) . "<br>" .
              "Data: " . $prenotazione -> getCreatedAt() . "<br>" .
              "Stanza Id: " . $prenotazione -> getStanzaId() .
              " || Numero Stanza: " . $prenotazione -> getRoomNumber() .
              " || Numero Piano: " . $prenotazione -> getFloor() .
              " || Numero Letti: " . $prenotazione -> getBeds() . "<br>" .
              "Ospite ID: " . $ospite -> getId() . " || Nome: " . $ospite -> getName() . " || Cognome: " . $ospite -> getLastName() . "<br>" .
              "Configurazione Id: " . $prenotazione -> getConfigurazioneId() . "<br><br>";
          }
        }
        else {
           echo "0 risultati";
        }

  $conn -> close();
 ?>
