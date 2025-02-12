<?php
// Jeżeli użytkownik już jest zalogowany, przekierowujemy do odpowiedniej strony
session_start();
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'dyzurny'){
       header("Location: duty_officer.php");
       exit;
    } else if($_SESSION['role'] == 'funkcjonariusz'){
       header("Location: officer.php");
       exit;
    }
}
include 'common.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SWD – Logowanie</title>
  <!-- Materialize CSS & jQuery -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <style> body { padding: 20px; } </style>
</head>
<body>
  <div class="container">
    <h3>System Wspomagania Dowodzenia Policji</h3>
    <div id="choice">
      <a id="btn-dyzurny" class="waves-effect waves-light btn">Logowanie jako Dyżurny</a>
      <a id="btn-funkcjonariusz" class="waves-effect waves-light btn">Logowanie jako Funkcjonariusz</a>
    </div>

    <!-- Formularz logowania dla dyżurnego -->
    <div id="form-dyzurny" style="display:none;">
      <h5>Logowanie jako Dyżurny</h5>
      <form method="post" action="login_dyzurny.php">
         <div class="input-field">
             <input type="password" id="password" name="password" required>
             <label for="password">Hasło</label>
         </div>
         <button type="submit" class="waves-effect waves-light btn">Zaloguj</button>
         <a id="back1" class="waves-effect waves-light btn grey">Powrót</a>
      </form>
    </div>

    <!-- Formularz logowania dla funkcjonariusza -->
    <div id="form-funkcjonariusz" style="display:none;">
      <h5>Logowanie jako Funkcjonariusz</h5>
      <form method="post" action="login_funkcjonariusz.php">
         <div class="input-field">
             <input type="text" id="call_sign" name="call_sign" required>
             <label for="call_sign">Kryptonim Patrolu</label>
         </div>
         <div class="input-field">
             <input type="text" id="composition" name="composition" required>
             <label for="composition">Skład Patrolu</label>
         </div>
         <button type="submit" class="waves-effect waves-light btn">Zaloguj</button>
         <a id="back2" class="waves-effect waves-light btn grey">Powrót</a>
      </form>
      <h5>Istniejące Patrole</h5>
      <ul class="collection">
         <?php
         $db = getDB();
         $stmt = $db->query("SELECT * FROM patrols ORDER BY created_at DESC");
         while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             echo '<li class="collection-item"><a href="select_patrol.php?id='.$row['id'].'">'
                  . htmlspecialchars($row['call_sign']) . ' - ' . htmlspecialchars($row['composition']) . '</a></li>';
         }
         ?>
      </ul>
    </div>
  </div>

  <script>
  $(document).ready(function(){
      $("#btn-dyzurny").click(function(){
          $("#choice").hide();
          $("#form-dyzurny").show();
      });
      $("#btn-funkcjonariusz").click(function(){
          $("#choice").hide();
          $("#form-funkcjonariusz").show();
      });
      $("#back1, #back2").click(function(){
          $("#form-dyzurny, #form-funkcjonariusz").hide();
          $("#choice").show();
      });
  });
  </script>
</body>
</html>
