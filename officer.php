<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'funkcjonariusz'){
   header("Location: index.php");
   exit;
}
include 'common.php';
$call_sign = $_SESSION['call_sign'];
$db = getDB();
$stmt = $db->prepare("SELECT * FROM patrols WHERE call_sign = ?");
$stmt->execute([$call_sign]);
$patrol = $stmt->fetch(PDO::FETCH_ASSOC);
$current_status = $patrol ? $patrol['status'] : 'Wolny';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Strona Funkcjonariusza – SWD</title>
  <!-- Materialize CSS & jQuery -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <style>
    .status-btn { margin: 5px; }
    #messages { margin-top: 20px; }
  </style>
</head>
<body>
  <div class="container">
    <!-- Przycisk wylogowania -->
    <div style="text-align: right; margin-top:10px;">
      <a href="logout.php" class="waves-effect waves-light btn red">Wyloguj się</a>
    </div>
    <h4>Funkcjonariusz – Patrol: <?php echo htmlspecialchars($call_sign); ?></h4>
    <div>
      <h5>Status Patrolu: <span id="current-status"><?php echo htmlspecialchars($current_status); ?></span></h5>
      <div>
         <button class="btn green status-btn" data-status="Wolny">Wolny</button>
         <button class="btn yellow status-btn" data-status="W drodze">W drodze</button>
         <button class="btn red lighten-2 status-btn" data-status="Interwencja">Interwencja</button>
         <button class="btn red darken-4 status-btn" data-status="Konwój, doprowadzenie">Konwój, doprowadzenie</button>
         <button class="btn purple status-btn" data-status="Dokumentacja">Dokumentacja</button>
      </div>
    </div>
    <div style="margin-top:20px;">
       <button class="btn" id="send-message-btn">Wyślij Komunikat</button>
    </div>
    <!-- Przyciski do interwencji funkcjonariusza -->
    <div style="margin-top:20px;">
       <button class="btn" id="officer-add-intervention-btn">Dodaj interwencję</button>
       <button class="btn red" id="officer-delete-intervention-btn">Usuń interwencję</button>
    </div>
    <!-- Panel wyświetlania interwencji (łącznie z interwencjami wysłanymi przez dyżurnego) -->
    <div id="intervention-panel" style="margin-top:20px;">
       <h5>Interwencje:</h5>
       <div id="intervention-content">
         Brak interwencji.
       </div>
    </div>
    <!-- Panel wyświetlania komunikatów -->
    <div id="officer-messages" style="margin-top:20px;">
       <h5>Komunikaty:</h5>
       <div id="messages-list">
         <!-- Komunikaty będą ładowane przez AJAX -->
       </div>
    </div>
  </div>

  <!-- Modal wysyłania komunikatu -->
  <div id="sendMessageModal" class="modal">
    <div class="modal-content">
      <h5>Wyślij Komunikat</h5>
      <div class="input-field">
        <textarea id="message-content" class="materialize-textarea"></textarea>
        <label for="message-content">Treść komunikatu</label>
      </div>
    </div>
    <div class="modal-footer">
      <a href="#!" id="sendMessageConfirm" class="modal-close waves-effect waves-green btn">Wyślij</a>
    </div>
  </div>

  <!-- Modal dodawania interwencji przez funkcjonariusza -->
  <div id="officerInterventionModal" class="modal">
    <div class="modal-content">
      <h5>Dodaj Interwencję</h5>
      <div class="input-field">
        <input type="text" id="officer-intv-type">
        <label for="officer-intv-type">Typ interwencji</label>
      </div>
      <div class="input-field">
        <input type="text" id="officer-intv-location">
        <label for="officer-intv-location">Lokalizacja</label>
      </div>
      <div class="input-field">
        <input type="text" id="officer-intv-reporter">
        <label for="officer-intv-reporter">Zgłaszający</label>
      </div>
      <p>
        <label>
          <input type="checkbox" id="officer-intv-urgent" />
          <span>Pilne</span>
        </label>
      </p>
    </div>
    <div class="modal-footer">
      <a href="#!" id="saveOfficerIntervention" class="modal-close waves-effect waves-green btn">Zapisz</a>
    </div>
  </div>

  <script>
  $(document).ready(function(){
      $('.modal').modal();

      // Zmiana statusu – natychmiastowa aktualizacja widocznego napisu
      $('.status-btn').click(function(){
           var status = $(this).data('status');
           $.post('update_status.php', {status: status}, function(data){
                if(data.success){
                    $('#current-status').text(status);
                }
           }, 'json');
      });

      // Obsługa wysyłania komunikatu
      $('#send-message-btn').click(function(){
           $('#message-content').val('');
           M.updateTextFields();
           $('#sendMessageModal').modal('open');
      });
      $('#sendMessageConfirm').click(function(){
           var content = $('#message-content').val();
           if(content.trim() != ''){
                $.post('send_message.php', {content: content}, function(data){
                    if(data.success){
                        M.toast({html: 'Komunikat wysłany'});
                    }
                }, 'json');
           }
      });

      // Obsługa przycisku dodania interwencji przez funkcjonariusza
      $('#officer-add-intervention-btn').click(function(){
           $('#officer-intv-type').val('');
           $('#officer-intv-location').val('');
           $('#officer-intv-reporter').val('');
           $('#officer-intv-urgent').prop('checked', false);
           M.updateTextFields();
           $('#officerInterventionModal').modal('open');
      });

      // Zapis interwencji przez funkcjonariusza
      $('#saveOfficerIntervention').click(function(){
           var type = $('#officer-intv-type').val();
           var location = $('#officer-intv-location').val();
           var reporter = $('#officer-intv-reporter').val();
           var urgent = $('#officer-intv-urgent').is(':checked') ? 1 : 0;
           $.post('officer_new_intervention.php', {
                type: type,
                location: location,
                reporter: reporter,
                urgency: urgent
           }, function(data){
                if(data.success){
                    M.toast({html: 'Interwencja dodana'});
                    checkInterventions(); // odśwież panel interwencji
                } else {
                    M.toast({html: 'Błąd przy dodawaniu interwencji'});
                }
           }, 'json');
      });

      // Obsługa przycisku usunięcia interwencji
      $('#officer-delete-intervention-btn').click(function(){
           $.post('officer_delete_intervention.php', {}, function(data){
                if(data.success){
                    M.toast({html: 'Interwencja usunięta'});
                    checkInterventions();
                } else {
                    M.toast({html: 'Błąd przy usuwaniu interwencji'});
                }
           }, 'json');
      });

      // Polling – sprawdzanie nowych komunikatów
      setInterval(checkOfficerMessages, 10000);
      setInterval(loadOfficerMessages, 10000);
      loadOfficerMessages();

      // Polling – sprawdzanie interwencji (zarówno wysłanych przez dyżurnego, jak i utworzonych przez funkcjonariusza)
      setInterval(checkInterventions, 10000);
      checkInterventions();

      function checkOfficerMessages(){
           $.getJSON('check_messages.php', function(data){
                if(data.new){
                    alert("Nowy komunikat: " + data.content);
                    var audio = new Audio('notification.mp3');
                    audio.play().catch(function(e){
                        console.log('Audio play error: ', e);
                    });
                }
           });
      }

      function loadOfficerMessages(){
           $("#messages-list").load("get_messages_officer.php");
      }

      function checkInterventions(){
           $.getJSON('check_interventions.php', function(data){
                $('#intervention-content').html(data.html);
                if(data.updated){
                    var audio = new Audio('intervention.mp3');
                    audio.play().catch(function(e){
                        console.log('Intervention audio error: ', e);
                    });
                }
           });
      }
  });
  </script>
</body>
</html>
