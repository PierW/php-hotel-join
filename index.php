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

      if ($result -> num_rows > 0) {

        $prenotazioni = [];
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
      else {
        echo "0 Risultati";
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

  foreach ($prenotazioni as $key => $prenotazione) {

    // var_dump($key, $prenotazione); die();
    echo "Prenotazione: " . ($key+1) . "<br>" .
          "Data: " . $prenotazione -> getCreatedAt() . "<br>" .
          "Stanza Id: " . $prenotazione -> getStanzaId() .
          " || Numero Stanza: " . $prenotazione -> getRoomNumber() .
          " || Numero Piano: " . $prenotazione -> getFloor() .
          " || Numero Letti: " . $prenotazione -> getBeds() . "<br>" .
          "Configurazione Id: " . $prenotazione -> getConfigurazioneId() . "<br><br>";
  }

  $conn -> close();
 ?>
