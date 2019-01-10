<?php

namespace App\Http\Controllers;

use Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Input;
use Auth;
use Image;
use File;
use Datatables;
use PhpExcelReader;
use Spreadsheet_Excel_Reader;
use mysqli;

use App\User;
use App\School;
use App\Kelas;
use App\Jawab;
use App\Aktifitas;
use App\Soal;
use App\Detailsoal;
use App\Absen;

include(app_path() . '/functions/koneksi.php');

class GuruController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }
  
  public function index()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', '=', Auth::user()->id)->first();
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(3)->get();
      return view('guru.index', compact('user', 'school', 'aktifitas'));
    }else{
      return redirect('siswa');
    }
  }

  public function profil()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $user = User::where('email', '=', Auth::user()->email)->first();
      $school = School::first();
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(3)->get();
      return view('guru.profil', compact('user', 'school', 'aktifitas'));
    }else{
      return redirect('siswa');
    }
  }

  public function kelas()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $user = User::where('email', '=', Auth::user()->email)->first();
      $school = School::first();
      $kelas = Kelas::orderby('nama', 'asc')->paginate(15);
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(5)->get();
      return view('guru.kelas', compact('user', 'school', 'kelas', 'aktifitas'));
    }else{
      return redirect('siswa');
    }
  }
  public function ubah_kelas()
  {
    $id = Input::get('id');
    $nama = Input::get('nama');
    $cek = Kelas::where('id', $id)->first();
    if ($cek != "") {
      $cek->nama = $nama;
      $cek->save();
      return $nama;
    }
  }

  public function tambahkelas()
  {
      if(Request::ajax()){
          $nama = Input::get('nama');
          if ($nama=="") {
              return 'Nama tidak boleh kosong';
          }else{
              $user = new Kelas;
              $user->nama = Input::get('nama');
              $user->save();

              $aktifitas = new Aktifitas;
              $aktifitas->id_user = Input::get('id_user');
              $aktifitas->nama = "Membuat kelas dengan nama ".$nama;
              $aktifitas->save();

              return ('berhasil');
          }
      }
  }

  public function ubahkelas()
  {
      $nama = Input::get('nama');
          if ($nama=="") {
              return 'Nama tidak boleh kosong';
          }else{
              $id_kelas = Input::get('id_kelas');
              $user = Kelas::where('id', '=', $id_kelas)->first();
              $user->nama = Input::get('nama');
              $user->save();

              $aktifitas = new Aktifitas;
              $aktifitas->id_user = Auth::user()->id;
              $aktifitas->nama = "Merubah kelas dengan nama ".$nama;
              $aktifitas->save();

              return ('berhasil');
          }
  }

  public function hapuskelas()
  {
      if(Request::ajax()){
          $id_kelas = Input::get('id_kelas');
          $user = Kelas::where('id', '=', $id_kelas)->first();
          $user->delete();
          
          return ('berhasil');
      }
  }

  public function detailkelas($id)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', Auth::user()->id)->first();
      $kelas = Kelas::orderby('nama', 'asc')->get();
      $siswas = User::where('id_kelas', $id)->where('status', 'S')->orderby('nama', 'asc')->get();
      $calonsiswas = User::where('id_kelas', '!=', $id)->where('status', 'S')->orderBy('id_kelas', 'ASC')->get();
      $kelassiswa = Kelas::where('id', $id)->first();
      return view('guru.detailkelas', compact('user', 'school', 'kelas', 'kelassiswa', 'siswas', 'calonsiswas'));
    }else{
      return redirect('siswa');
    }
  }
  
  public function cekkelassiswa()
  {
    if(Request::ajax()){
      $id_siswa = Input::get('siswa');
      $siswa = User::join('kelas', 'users.id_kelas', '=', 'kelas.id')
                      ->select('kelas.nama as nama_kelas', 'users.*')
                      ->where('users.id', $id_siswa)->first();
      if ($siswa == "") {
        return 'Siswa Belum memiliki kelas.';
      }else{
        if($siswa->id_kelas == ""){
          return '<b>'.$siswa->nama.'</b> Belum memiliki kelas.';
        }else{
          $kelas = Kelas::where('id', '=', $siswa->id_kelas)->first();
          return '<b>'.$siswa->nama.'</b> sekarang kelas <b>'.$siswa->nama_kelas.'</b>. Apakah akan dipindahkan? Klik tombol <b>Simpan</b> dibawah ini apabila benar akan dipindahkan.';
        }
      }
    }
  }

  public function tambahsiswakekelas()
  {
    if(Request::ajax()){
      $siswa = Input::get('siswa');
      if ($siswa == "") {
        echo "Anda belum memilih siswa yang ingin dipindahkan kelas.";
      }else{
        $user = User::where('id', $siswa)->first();

        $user->id_kelas = Input::get('id_kelas');
        $user->save();

        $kelaslama = Kelas::where('id', $user->id_kelas)->first();
        $kelasbaru = Kelas::where('id', Input::get('id_kelas'))->first();
        if ($user->id_kelas == "") {
          $kelaslama->nama = "belum ada kelas";
        }
        $aktifitas = new Aktifitas;
        $aktifitas->id_user = Auth::user()->id;
        $aktifitas->nama = "Memindahkan kelas siswa atas nama ".$user->nama.", NIS: ".$user->no_induk." dari ".$kelaslama->nama.' ke '.$kelasbaru->nama;
        $aktifitas->save();

        echo 'berhasil';
      }
    }
  }

  public function hapuskelassiswa()
  {
      if(Request::ajax()){
          $id_siswa = Input::get('id_siswa');
          $user = User::where('id', '=', $id_siswa)->first();
          $kelaslama = Kelas::where('id', '=', $user->id_kelas)->first();
          $user->id_kelas = "";
          $user->save();

          $aktifitas = new Aktifitas;
          $aktifitas->id_user = Auth::user()->id;
          $aktifitas->nama = "Mengeluarkan kelas siswa atas nama ".$user->nama.", NIS: ".$user->no_induk." dari ".$kelaslama->nama.' dan sekarang belum memiliki kelas';
          $aktifitas->save();
          echo "berhasil";
      }
  }

  public function detailkelassiswa($id)
  {
      if (Auth::user()->status == "G" or Auth::user()->status == "A") {
          $school = School::first();
          $user = User::where('id', Auth::user()->id)->first();
          $siswa = User::join('kelas', 'users.id_kelas', '=', 'kelas.id')
                          ->select('kelas.nama as nama_kelas', 'users.*')
                          ->where('users.id', $id)->first();
          $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(5)->get();
          $jawabs = Jawab::where('id_user', $siswa->id)->where('status', 'Y')->orderby('id')->groupby('id_soal')->paginate(25);

          return view('guru.detailkelassiswa', compact('user', 'school', 'siswa', 'jawabs', 'aktifitas'));
      }else{
          return redirect('siswa');
      }
  }

  public function updateprofilfotosiswa()
  {
      if(Request::ajax()){
          if(isset($_FILES["file"]["type"])){
              $validextensions = array("jpeg", "jpg", "png");
              $temporary = explode(".", $_FILES["file"]["name"]);
              $file_extension = end($temporary);

              if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
              ) && ($_FILES["file"]["size"] < 1000000)//Approx. 1000kb files can be uploaded.
              && in_array($file_extension, $validextensions)) {
                  if ($_FILES["file"]["error"] > 0){
                      echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
                  }else{
                      if (file_exists(url('/img/') . $_FILES["file"]["name"])) {
                          echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
                      }else{
                          $uniqname = date('ymdhis').rand(00000,99999);
                          $sourcePath = $_FILES['file']['tmp_name'];
                          $targetPath = "img/".$uniqname.$_FILES['file']['name'];
                          move_uploaded_file($sourcePath,$targetPath);

                          $user = User::where('id', '=', Input::get('id_siswa'))->first();
                          File::delete('img/'.$user->gambar);
                          $user->gambar = $uniqname.$_FILES["file"]["name"];

                          $user->save();

                          echo "<span id='success'>Foto berhasil di upload...!! <i>Refresh</i> halaman untuk melihat hasilnya...</span><br/>";
                          echo "<br/><b>File Name:</b> " . $_FILES["file"]["name"] . "<br>";
                          echo "<b>Type:</b> " . $_FILES["file"]["type"] . "<br>";
                          echo "<b>Size:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                      }
                  }
              }else{
                  echo "<span id='invalid'>***Terdapat kesalahan dalam hal jenis file maupun ukuran yang melebihi batas. Maksimal ukuran file yang diterima adalah 1Mb***<span>";
              }
          }
      }
  }

  public function updateprofilsiswa()
  {
      if(Request::ajax()){
          //echo Input::get('id_siswa');
          $nama = Input::get('nama');
          $nis = Input::get('nis');
          $jk = Input::get('jk');
          $password = Input::get('password');
          $passwordsimpan = bcrypt(Input::get('password'));
          if($password != ""){
              $user = User::where('id', '=', Input::get('id_siswa'))->first();
              $user->nama = Input::get('nama');
              $user->no_induk = Input::get('nis');
              $user->jk = Input::get('jk');
              $user->password = $passwordsimpan;
              $user->save();
              return ('berhasil');
          }else{
              $user = User::where('id', '=', Input::get('id_siswa'))->first();
              $user->nama = Input::get('nama');
              $user->no_induk = Input::get('nis');
              $user->jk = Input::get('jk');
              $user->save();
              return ('berhasil');
          }
      }
  }

  public function data_siswa()
  {
      $school = School::first();
      $user = User::where('id', Auth::user()->id)->first();
      $jawabs = Jawab::where('id_user', Auth::user()->id)->get();
      $kelas = Kelas::orderby('nama', 'asc')->get();
      $users = User::join('kelas', 'users.id_kelas', '=', 'kelas.id')
                      ->select(['users.id', 'users.no_induk', 'users.nama', 'users.email', 'users.jk', 'kelas.nama as nama_kelas'])
                      ->where('users.status', '=', 'S')
                      ->orwhere('users.status', '=', 'C')->paginate(10);
      $jumlah_siswa = User::join('kelas', 'users.id_kelas', '=', 'kelas.id')
                            ->select(['users.id', 'users.no_induk', 'users.nama', 'users.email', 'users.jk', 'kelas.nama as nama_kelas'])
                            ->where('users.status', '=', 'S')->get();
      return view('guru.siswa', compact('user', 'school', 'jawabs', 'kelas', 'users', 'jumlah_siswa'));
  }
  public function get_siswa()
  {
    $q = Input::get('q');
    $users = User::join('kelas', 'users.id_kelas', '=', 'kelas.id')
                    ->select(['users.id', 'users.no_induk', 'users.nama', 'users.email', 'users.jk', 'kelas.nama as nama_kelas'])
                    ->where('users.nama', 'LIKE', '%'.$q.'%')
                    ->where('users.status', '=', 'S')
                    ->orwhere('users.status', '=', 'C')->paginate(15);
    return view('guru.ajax.get_siswa', compact('users'));
  }

  public function uploadsoal()
  {
      // error_reporting(0);
      include(app_path() . '/functions/koneksi.php');
      include(app_path() . '/functions/excel_reader2.php');

      $id_user = Auth::user()->id; 
     
      $data = new Spreadsheet_Excel_Reader($_FILES['file']['tmp_name']);
      $baris = $data->rowcount($sheet_index=0);
      $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      $sukses = 0;
      $gagal = 0;
      for($i=2; $i<=$baris;$i++){
          $id_soal = $data->val($i, 1);
          $soal = $data->val($i, 2);
          $pila = $data->val($i, 3);
          $pilb = $data->val($i, 4);
          $pilc = $data->val($i, 5);
          $pild = $data->val($i, 6);
          $pile = $data->val($i, 7);
          $kunci = $data->val($i, 8);
          $score = $data->val($i, 9);

          $query = new Detailsoal;
          $query->id_soal = $id_soal;
          $query->soal = $soal;
          $query->pila = $pila;
          $query->pilb = $pilb;
          $query->pilc = $pilc;
          $query->pild = $pild;
          $query->pile = $pile;
          $query->kunci = $kunci;
          $query->score = $score;
          $query->id_user = $id_user;
          $query->status = 'Y';
          $query->save();
          // echo "ok";
          /*$sql = $conn->query("INSERT INTO detailsoals (id_soal, soal, pila, pilb, pilc, pild, pile, kunci, score, id_user, status) VALUES ('$id_soal', '$soal', '$pila', '$pilb', '$pilc', '$pild', '$pile', '$kunci', '$score', '$id_user', 'Y')");

          if($sql){
              $sukses++;
              echo $sukses;
          }else{
              $gagal++;
              echo $gagal;
          }*/
      }
      return redirect('soal-guru');
  }

  public function uploadsiswa()
  {
      error_reporting(0);
      include(app_path() . '/functions/koneksi.php');
      include(app_path() . '/functions/excel_reader2.php');
     
      $data = new Spreadsheet_Excel_Reader($_FILES['file']['tmp_name']);
      $baris = $data->rowcount($sheet_index=0);
      $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      $sukses = 0;
      $gagal = 0;
      for($i=2; $i<=$baris;$i++){
          $id_kelas = $data->val($i, 1);
          /*$nama = mysql_real_escape_string($data->val($i, 2));
          $nis = mysql_real_escape_string($data->val($i, 3));
          $jk = mysql_real_escape_string($data->val($i, 4));
          $email = mysql_real_escape_string($data->val($i, 5));
          $password = bcrypt($data->val($i, 6));*/

          $nama = $data->val($i, 2);
          $nis = $data->val($i, 3);
          $jk = $data->val($i, 4);
          $email = $data->val($i, 5);
          $password = bcrypt($data->val($i, 6));

          $query = new User;
          $query->id_kelas = $id_kelas;
          $query->nama = $nama;
          $query->no_induk = $nis;
          $query->jk = $jk;
          $query->status = 'S';
          $query->email = $email;
          $query->password = $password;
          $query->save();

          /*$sql = $conn->query("INSERT INTO users (id_kelas, nama, no_induk, jk, status, email, password) VALUES ('$id_kelas', '$nama', '$nis', '$jk', 'S', '$email', '$password')");

          if($sql){
              $sukses++;
          }else{
              $gagal++;
          }*/
      }
      // return redirect('data-siswa');
  }

  public function uploadcalonsiswa()
  {
      error_reporting(0);
      include(app_path() . '/functions/koneksi.php');
      include(app_path() . '/functions/excel_reader2.php');
     
      $data = new Spreadsheet_Excel_Reader($_FILES['filecalon']['tmp_name']);
      $baris = $data->rowcount($sheet_index=0);
      $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      $sukses = 0;
      $gagal = 0;
      for($i=2; $i<=$baris;$i++){
          $id_kelas = $data->val($i, 1);
          $nama = mysql_real_escape_string($data->val($i, 2));
          $nis = mysql_real_escape_string($data->val($i, 3));
          $jk = mysql_real_escape_string($data->val($i, 4));
          $sekolah_asal = mysql_real_escape_string($data->val($i, 5));
          $email = mysql_real_escape_string($data->val($i, 6));
          $password = bcrypt($data->val($i, 7));

          $sql = $conn->query("INSERT INTO users (id_kelas, nama, no_induk, jk, sekolah_asal, status, email, password) VALUES ('$id_kelas', '$nama', '$nis', '$jk', '$sekolah_asal', 'C', '$email', '$password')");

          if($sql){
              $sukses++;
          }else{
              $gagal++;
          }
      }
      return redirect('data-siswa');
  }

  public function tampilsiswa()
  {
      error_reporting(0);
      include(app_path() . '/functions/excel_reader2.php');
     
      $data = new Spreadsheet_Excel_Reader('readfile\data.xls');
      $baris = $data->rowcount($sheet_index=0);

      $nik=$data->val(2, 5);

      echo bcrypt($nik);


      /*echo '<pre>';
      var_export($excel->sheets);
      echo '</pre>';*/
  }

  public function simpanformsiswa()
  {
      if(Request::ajax()){
          if(Input::get('nama') != ""){
              if (Input::get('no_induk') != "") {
                  if (Input::get('email') != "") {
                      if (!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)) {
                        return "<b>Error:</b> Email yang Anda masukan tidak valid";
                      }else{
                          if (Input::get('jk') != "") {
                              if (Input::get('id_kelas') != "") {
                                  $user = new User;
                                  $user->id_kelas = Input::get('id_kelas');
                                  $user->nama = Input::get('nama');
                                  $user->no_induk = Input::get('no_induk');
                                  $user->email = Input::get('email');
                                  $user->jk = Input::get('jk');
                                  $user->status = "S";
                                  $user->password = bcrypt(123456);
                                  $user->save();
                                  return ('berhasil');
                              }else{
                                  return "<b>Error:</b> Anda belum mengisi kelas siswa";
                              }
                          }else{
                              return "<b>Error:</b> Anda belum mengisi jenis kelamin siswa";
                          }
                      }
                  }else{
                      return "<b>Error:</b> Anda belum menuliskan email siswa";
                  }
              }else{
                  return "<b>Error:</b> Anda belum menuliskan NIS";
              }
          }else{
              return "<b>Error:</b> Anda belum menuliskan nama siswa";
          }
      }
  }

  public function hapussiswa()
  {
      if(Request::ajax()){
          $id_siswa = Input::get('id_siswa');
          $query = User::where('id', '=', $id_siswa)->first();
          if($query->gambar != ""){
              File::delete('img/'.$query->gambar);
          }
          $query->delete();
          $hapusjawab = Jawab::where('id_user', '=', $id_siswa)->delete();

          return 'berhasil';
      }
  }

  public function hapusjawabkelas()
  {
      if(Request::ajax()){
          $id_kelas = Input::get('id_kelas');
          $id_soal = Input::get('id_soal');

          $hapusjawab = Jawab::where('id_soal', '=', $id_soal)->where('id_kelas', '=', $id_kelas)->delete();

          return 'berhasil';
      }
  }

  public function hapusjawabsiswa()
  {
      if(Request::ajax()){
          $id_kelas = Input::get('id_kelas');
          $id_soal = Input::get('id_soal');
          $id_user = Input::get('id_user');

          $hapusjawab = Jawab::where('id_soal', '=', $id_soal)->where('id_kelas', '=', $id_kelas)->where('id_user', '=', $id_user)->delete();

          return 'berhasil';
      }
  }

  public function hasilguru()
  {
      return 'ini halaman untuk manajemen hasil ujian';
  }

  public function update_profil()
  {
    if(Request::ajax()){
      $id = Input::get('id');
      $nama = Input::get('nama');
      $nis = Input::get('nis');
      $jk = Input::get('jk');
      $email = Input::get('email');
      $password = Input::get('password');
      $passwordsimpan = bcrypt(Input::get('password'));

      $aktifitas = new Aktifitas;
      $aktifitas->id_user = Auth::user()->id;
      $aktifitas->nama = "Merubah data guru atas nama ".$nama;
      $aktifitas->save();

      if ($email != "") {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $cek_email = User::where('email', $email)->where('id', '!=', $id)->first();
          if ($cek_email == "") {
            if($password != ""){
              $user = User::where('id', $id)->first();
              $user->nama = Input::get('nama');
              $user->no_induk = Input::get('nis');
              $user->jk = Input::get('jk');
              $user->email = $email;
              $user->password = $passwordsimpan;
              $user->save();
              return ('berhasil');
            }else{
              $user = User::where('id', $id)->first();
              $user->nama = Input::get('nama');
              $user->no_induk = Input::get('nis');
              $user->jk = Input::get('jk');
              $user->email = $email;
              $user->save();
              return ('berhasil');
            }
          }else{
            return 'Email sudah terpakai, ganti dengan yang lain.';
          }
        }else{
          return 'Email yang Anda masukan tidak valid';
        }
      }else{
        return 'Email tidak boleh kosong.';
      }
    }
  }

  public function hapusguru($id)
  {
      $q_guru = User::where('id', '=', $id)->first();
      $aktifitas = new Aktifitas;
      $aktifitas->id_user = Auth::user()->id;
      $aktifitas->nama = "Menghapus guru atas nama ".$q_guru->nama;
      $aktifitas->save();
      User::where('id', '=', $id)->delete();
      return redirect('data-guru');
  }

  public function hapuscalonsiswa()
  {
      $aktifitas = new Aktifitas;
      $aktifitas->id_user = Auth::user()->id;
      $aktifitas->nama = "Menghapus seluruh data calon siswa (peserta PSB).";
      $aktifitas->save();
      $hapus = User::where('status', '=', 'C')->delete();
      return redirect('data-siswa');
  }

  public function tampilhasil($id, $id_soal)
  {
      $jawabs = Jawab::where('id_kelas', '=', $id)->where('id_soal', '=', $id_soal)->groupby('id_user')->get();
      return view('guru.tampil', compact('jawabs'));
  }

  public function upload_foto_user()
  {
    $id = Input::get('id');
    $filename = trim(addslashes($_FILES['file']['name']));
    $filenamehapusspasi = str_replace(' ', '_', $filename);
    $savename = md5(round(microtime(true))) . '_' . $filenamehapusspasi;
    $img = Image::make($_FILES['file']['tmp_name']);
    $img->resize(550, null, function ($constraint) {
      $constraint->aspectRatio();
    });
    $img->save('img/'.$savename);
    $cek = User::where('id', $id)->first();
    if ($cek != "") {
      if ($cek->gambar != "") {
        File::delete('img/'.$cek->gambar);
      }
      $cek->gambar = $savename;
      $cek->save();
      return 'ubah';
    }
  }

  public function aktifitas()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', '=', Auth::user()->id)->first();
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                                ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                                ->orderby('aktifitas.id', 'desc')->paginate(20);
      return view('guru.aktifitas', compact('aktifitas', 'school', 'user'));
    }else{
      return redirect('siswa');
    }
  }

  public function input_absen()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', '=', Auth::user()->id)->first();

      $kelas = Kelas::all();

      return view('guru.absen.input', compact('user', 'kelas'));
    }else{
      return redirect('siswa');
    }
  }
  public function get_siswa_absen()
  {
    $id_kelas = Input::get('id_kelas');
    $jam = Input::get('jam');
    $tanggal = Input::get('tanggal');

    $siswas = User::leftJoin('absens as absens', 'users.id', '=', 'absens.id_siswa')
                    ->join('kelas', 'users.id_kelas', '=', 'kelas.id')
                    ->select('absens.absen', 'kelas.nama as nama_kelas', 'users.*')
                    ->where('users.status', 'S')->where('users.id_kelas', $id_kelas)
                    ->orderBy('users.nama', 'ASC')->get();

    return view('guru.ajax.get_siswa_absen', compact('siswas', 'jam', 'tanggal'));
  }
  public function crud_input_absen()
  {
    $absen = Input::get('absen');

    $jam = Input::get('jam');
    $id_kelas = Input::get('id_kelas');
    $id_siswa = Input::get('id_siswa');
    $tanggal = Input::get('tanggal');

    $cek = Absen::where('id_kelas', $id_kelas)
                  ->where('id_siswa', $id_siswa)
                  ->where('jam', $jam)
                  ->where('tanggal', $tanggal)->first();

    if($cek != ""){
      if($cek->id_guru == Auth::user()->id){
        $cek->absen = $absen;
        $cek->save();
        return 'ubah';
      }else{
        return 'not allowed';
      }
    }else{
      $query = new Absen;
      $query->id_guru = Auth::user()->id;
      $query->id_kelas = $id_kelas;
      $query->id_siswa = $id_siswa;
      $query->jam = $jam;
      $query->absen = $absen;
      $query->tanggal = $tanggal;
      $query->save();
      return 'tambah baru';
    }
  }
  public function rekap_absen()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', '=', Auth::user()->id)->first();

      $kelas = Kelas::all();

      return view('guru.absen.index', compact('user', 'kelas'));
    }else{
      return redirect('siswa');
    }
  }
}
