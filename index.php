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

    public static function getAllPrenotazioni($conn) {

      $sql = "
              SELECT * FROM prenotazioni
              WHERE month(created_at) = 05
              ORDER BY created_at DESC
            ";

      $result = $conn -> query($sql);

      if ($result -> num_rows > 0) {

        $prenotazioni = [];
        while ($row = $result -> fetch_assoc()) {

          $prenotazioni[] = new Prenotazione(

            $row["id"],
            $row["stanza_id"],
            $row["configurazione_id"],
            $row["created_at"]
          );
        }
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

  $prenotazioni = Prenotazione::getAllPrenotazioni($conn);
  // var_dump($prenotazioni); die();

  foreach ($prenotazioni as $key => $prenotazione) {

    // var_dump($key, $prenotazione); die();
    echo "Prenotazione: " . ($key+1) . "<br>" .
    "Data: " . $prenotazione -> getCreatedAt() . "<br>" .
    "Stanza Id: " . $prenotazione -> getStanzaId() . "<br>" .
    "Configurazione Id: " . $prenotazione -> getConfigurazioneId() . "<br><br>";
  }

  $conn -> close();
 ?>
