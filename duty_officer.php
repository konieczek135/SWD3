<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dyzurny'){
   header("Location: index.php");
   exit;
}
include 'common.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Strona Dyżurnego – SWD</title>
  <!-- Materialize CSS & jQuery -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <style>
    #patrols { float: left; width: 30%; border: 1px solid #ccc; height: 500px; overflow-y: scroll; }
    #interventions { float: right; width: 30%; border: 1px solid #ccc; height: 500px; overflow-y: scroll; }
    #messages { clear: both; border: 1px solid #ccc; height: 200px; overflow-y: scroll; margin-top: 20px; }
    .context-menu {
        position: absolute;
        z-index: 1000;
        background: #fff;
        border: 1px solid #ccc;
        display: none;
    }
    .context-menu li {
        list-style: none;
        padding: 5px 10px;
        cursor: pointer;
    }
    .context-menu li:hover { background: #eee; }
  </style>
</head>
<body>
  <div class="container">
    <h4>Dyżurny</h4>
    <div id="patrols-section">
       <h5>Patrole</h5>
       <div id="patrols">
         <!-- Lista patroli ładowana przez AJAX -->
       </div>
    </div>
    <div id="interventions-section">
       <h5>Interwencje</h5>
       <div id="interventions">
         <!-- Lista interwencji ładowana przez AJAX -->
       </div>
    </div>
    <div id="messages-section">
       <h5>Komunikaty</h5>
       <div id="messages">
         <!-- Lista komunikatów ładowana przez AJAX -->
       </div>
    </div>
  </div>

  <!-- Menu kontekstowe dla sekcji patroli -->
  <ul id="context-patrols" class="context-menu">
      <li data-action="new_intervention">Nowa interwencja</li>
      <li data-action="new_message">Nowy komunikat</li>
      <li data-action="new_patrol">Nowy patrol</li>
      <li data-action="delete_patrol">Usuń patrol</li>
  </ul>

  <!-- Menu kontekstowe dla sekcji interwencji i komunikatów -->
  <ul id="context-other" class="context-menu">
      <li data-action="delete">Usuń</li>
  </ul>

  <!-- Modal: Nowy patrol -->
  <div id="newPatrolModal" class="modal">
    <div class="modal-content">
      <h5>Nowy Patrol</h5>
      <div class="input-field">
        <input type="text" id="new-call_sign">
        <label for="new-call_sign">Kryptonim Patrolu</label>
      </div>
      <div class="input-field">
        <input type="text" id="new-composition">
        <label for="new-composition">Skład Patrolu</label>
      </div>
    </div>
    <div class="modal-footer">
      <a href="#!" id="saveNewPatrol" class="modal-close waves-effect waves-green btn">Zapisz</a>
    </div>
  </div>

  <!-- Modal: Nowy komunikat -->
  <div id="newMessageModal" class="modal">
    <div class="modal-content">
      <h5>Nowy Komunikat</h5>
      <div class="input-field">
        <textarea id="new-message-content" class="materialize-textarea"></textarea>
        <label for="new-message-content">Treść komunikatu</label>
      </div>
    </div>
    <div class="modal-footer">
      <a href="#!" id="saveNewMessage" class="modal-close waves-effect waves-green btn">Zapisz</a>
    </div>
  </div>

  <!-- Modal: Nowa interwencja -->
  <div id="newInterventionModal" class="modal">
    <div class="modal-content">
      <h5>Nowa Interwencja</h5>
      <div class="input-field">
        <input type="text" id="intv-type">
        <label for="intv-type">Typ interwencji</label>
      </div>
      <div class="input-field">
        <input type="text" id="intv-location">
        <label for="intv-location">Lokalizacja</label>
      </div>
      <div class="input-field">
        <input type="text" id="intv-reporter">
        <label for="intv-reporter">Zgłaszający</label>
      </div>
      <p>
        <label>
          <input type="checkbox" id="intv-urgent" />
          <span>Pilne</span>
        </label>
      </p>
    </div>
    <div class="modal-footer">
      <a href="#!" id="saveNewIntervention" class="modal-close waves-effect waves-green btn">Zapisz</a>
    </div>
  </div>
  <div style="text-align: right; margin-top:10px;">
    <a href="logout.php" class="waves-effect waves-light btn red">Wyloguj się</a>
  </div>

  <script>
  $(document).ready(function(){
      $('.modal').modal();

      // Ładowanie danych
      loadPatrols();
      loadInterventions();
      loadMessages();

      // Odświeżanie co 5 sekund
      setInterval(loadPatrols, 5000);
      setInterval(loadInterventions, 5000);
      setInterval(loadMessages, 5000);

      // Menu kontekstowe dla patroli
      $("#patrols").on("contextmenu", ".patrol-item", function(e){
          e.preventDefault();
          var patrolId = $(this).data("id");
          $("#context-patrols").data("id", patrolId).css({top: e.pageY, left: e.pageX}).show();
      });

      // Menu kontekstowe dla interwencji i komunikatów
      $("#interventions, #messages").on("contextmenu", ".item", function(e){
          e.preventDefault();
          var itemId = $(this).data("id");
          $("#context-other").data("id", itemId).css({top: e.pageY, left: e.pageX}).show();
      });

      // Ukrywanie menu po kliknięciu gdziekolwiek
      $(document).click(function(){
          $(".context-menu").hide();
      });

      // Obsługa menu dla patroli
      $("#context-patrols li").click(function(){
          var action = $(this).data("action");
          var patrolId = $("#context-patrols").data("id");
          if(action == "new_intervention"){
              $("#newInterventionModal").data("patrolId", patrolId).modal("open");
          } else if(action == "new_message"){
              $("#newMessageModal").data("patrolId", patrolId).modal("open");
          } else if(action == "new_patrol"){
              $("#newPatrolModal").modal("open");
          } else if(action == "delete_patrol"){
              $.post('delete_patrol.php', {id: patrolId}, function(data){
                  if(data.success){
                      M.toast({html: 'Patrol usunięty'});
                      loadPatrols();
                  }
              }, 'json');
          }
          $("#context-patrols").hide();
      });

      // Obsługa menu dla interwencji i komunikatów (wspólna opcja "Usuń")
      $("#context-other li").click(function(){
          var action = $(this).data("action");
          var itemId = $("#context-other").data("id");
          if(action == "delete"){
              // W zależności od typu (interwencja lub komunikat) usuwamy odpowiedni wpis
              var itemType = $("#interventions .item[data-id='"+itemId+"']").length ? 'intervention' : 'message';
              $.post('delete_item.php', {type: itemType, id: itemId}, function(data){
                  if(data.success){
                      M.toast({html: itemType.charAt(0).toUpperCase() + itemType.slice(1) + ' usunięty'});
                      if(itemType == 'intervention'){
                          loadInterventions();
                      } else {
                          loadMessages();
                      }
                  }
              }, 'json');
          }
          $("#context-other").hide();
      });

      // Zapis nowego patrolu
      $("#saveNewPatrol").click(function(){
          var call_sign = $("#new-call_sign").val();
          var composition = $("#new-composition").val();
          $.post('new_patrol.php', {call_sign: call_sign, composition: composition}, function(data){
              if(data.success){
                  M.toast({html: 'Patrol dodany'});
                  loadPatrols();
              }
          }, 'json');
      });

      // Zapis nowego komunikatu wysłanego przez dyżurnego
      $("#saveNewMessage").click(function(){
          var patrolId = $("#newMessageModal").data("patrolId");
          var content = $("#new-message-content").val();
          $.post('new_message_dyzurny.php', {patrol_id: patrolId, content: content}, function(data){
              if(data.success){
                  M.toast({html: 'Komunikat wysłany'});
                  loadMessages();
              }
          }, 'json');
      });

      // Zapis nowej interwencji
      $("#saveNewIntervention").click(function(){
          var patrolId = $("#newInterventionModal").data("patrolId");
          var type = $("#intv-type").val();
          var location = $("#intv-location").val();
          var reporter = $("#intv-reporter").val();
          var urgent = $("#intv-urgent").is(":checked") ? 1 : 0;
          $.post('new_intervention.php', {patrol_id: patrolId, type: type, location: location, reporter: reporter, urgency: urgent}, function(data){
              if(data.success){
                  M.toast({html: 'Interwencja dodana'});
                  loadInterventions();
              }
          }, 'json');
      });

      // Funkcje ładujące zawartość sekcji
      function loadPatrols(){
          $("#patrols").load("get_patrols.php");
      }
      function loadInterventions(){
          $("#interventions").load("get_interventions.php");
      }
      function loadMessages(){
          $("#messages").load("get_messages.php");
      }
      function checkDyzurnyMessages(){
    $.getJSON('check_messages_dyzurny.php', function(data){
        if(data.new){
            M.toast({html: 'Nowy komunikat: ' + data.content});
            var audio = new Audio('notification.mp3');
            audio.play().catch(function(e){
               console.log('Audio play failed on duty officer page:', e);
            });
            loadMessages();
        }
    });
}

  });
  </script>
</body>
</html>
