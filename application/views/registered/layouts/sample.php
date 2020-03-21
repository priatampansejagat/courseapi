<!DOCTYPE html>
<html lang="en">
<head>
  
<?php
  $this->load->view('layouts/header');

  $this->load->view('layouts/csshandler');
?>
  
</head>
<body>
  <div class="app" id="app">

<!-- ############ LAYOUT START-->

  <!-- aside -->
  <?php $this->load->view('layouts/menubar');  ?>
  <!-- / aside -->
  
  <!-- content -->
  <div id="content" class="app-content box-shadow-z3" role="main">
    
      <!-- Menu Bar Top -->
      <?php $this->load->view('layouts/menubartop');  ?>

      <!-- Footer -->
      <?php $this->load->view('layouts/footer');  ?>


      <!-- divider -->
      <div class="app-body" id="view">

      <div class="p-a white lt box-shadow">
        <div class="row">
          <div class="col-sm-6">
            <h4 class="m-b-0 _300">Welcome to Flatkit</h4>
          </div>
        </div>
      </div>
      <!-- ####################################################################################################### PAGE START-->

          <div class="padding">

          	
          	
          	

          	

          </div>

      <!-- ###################################################################################################### PAGE END-->

      </div>
  </div>
  <!-- / -->

  

<!-- ############ LAYOUT END-->

  </div>

<!-- build:js scripts/app.html.js -->
<!-- jshandler -->
<?php $this->load->view('layouts/jshandler');  ?>

<!-- endbuild -->
</body>
</html>
