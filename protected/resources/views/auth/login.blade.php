@extends('layouts/login')
@section('content')
<!-- <style type="text/css" media="screen">
  body{
    color: #fff;
  }
</style> -->
<link rel="icon" href="{{ ('img/favicon.png') }}">
<hr class="prettyline">
<div class="bungkuslogin" style="background-color:rgba(255, 150, 13, 0.5); color:#d5d9e2; padding:15px;">
    <center>
    <?php
        include(app_path() . '/functions/koneksi.php');
        $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM schools";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $namasekolah = $row["nama"];
                $logosekolah = $row['logo'];
            }
        } else {
            $namasekolah = "";
        }
        $conn->close();
    ?>
    <h2>Aplikasi Ujian Berbasis Komputer</h2>
    <h1><b>{{ $namasekolah }}</b></h1>
    <h3>Silahkan Login untuk mengakses halaman Aplikasi Ujian</h3>
    <em>Created by: <a href="http://www.tipa.co.id" target="blank" title="Tipamedia | IT Learning, Consulting and Developing" style="color: #97b5fc;">Tipamedia</a></em>
    <br>
    <a href="{{ url('/') }}"><button type="button" class="btn btn-success btn-lg" data-toggle="tooltip" title="Kembali kehalaman depan"><span class="glyphicon glyphicon-home"></span> Home</button></a>
    <button class="btn btn-primary btn-lg" href="#signup" data-toggle="modal" data-target=".bs-modal-sm" style="margin: 15px 0 15px 0;" id="logtooltip" title="Login ke halaman Anda"><span class="glyphicon glyphicon-lock"></span> Login</button>
    </center>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
<hr class="prettyline">
   <!-- Modal -->
  <div class="modal fade bs-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
          <br>
          <div class="bs-example bs-example-tabs">
              <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#signin" data-toggle="tab">Sign In</a></li>
                <li class=""><a href="#why" data-toggle="tab">About?</a></li>
              </ul>
          </div>
        <div class="modal-body">
          <div id="myTabContent" class="tab-content">
          <div class="tab-pane fade in" id="why">
          <p>Aplikasi ujian ini di kembangkan dengan desain responsive, sehingga bisa diakses dengan baik oleh berbagai perangkat.</p>
          <p></p><br> Jika mengalami kendala silahkan sampaikan ke <a mailto:href="support@tipa.co.id"></a>Support@Tipa.co.id</a> atau sms/telp 0823 2033 7777 untuk mendapatkan bantuan teknis.</p>
          </div>
          <div class="tab-pane fade active in" id="signin">
              <form method="POST" action="{{ url('/auth/login') }}">
              {!! csrf_field() !!}
              <fieldset>
              <div class="control-group">
                <label class="control-label" for="userid">Email:</label>
                <div class="controls">
                  <input type="email" name="email" class="form-control" value="" placeholder="Email">
                </div>
              </div>

              <!-- Password input-->
              <div class="control-group">
                <label class="control-label" for="passwordinput">Password:</label>
                <div class="controls">
                  <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                </div>
              </div>

              <!-- Multiple Checkboxes (inline) -->
              <div class="control-group">
                <label class="control-label" for="rememberme">&nbsp;</label>
                <div class="controls">
                  <label class="checkbox inline" for="rememberme-0">
                    <input type="checkbox" name="remember" id="remember" value="Remember me" style="margin: 0 0 0 0;">
                    <span style="margin: 0 0 0 25px;">Remember me</span>
                  </label>
                </div>
              </div>

              <!-- Button -->
              <div class="control-group">
                <label class="control-label" for="signin"></label>
                <div class="controls">
                  <button id="signin" name="signin" class="btn btn-success">Login</button>
                  <button type="submit" class="btn btn-danger" data-dismiss="modal">Batal</button>
                </div>
              </div>
              </fieldset>
              </form>
          </div>
      </div>
        </div>
        
      </div>
    </div>
  </div>

<?php
    /*}else{
      echo "selamat datang";
    }*/
  ?>
@endsection