<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Input;
use Auth;
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
use App\Distribusisoal;
use App\Materi;

class SoalController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function soal_guru()
  {
    $school = School::first();
    $user = User::where('id', '=', Auth::user()->id)->first();
    if (Auth::user()->status == "A"){
      $jawabs = Jawab::orderby('id', 'desc')->get();
    }elseif (Auth::user()->status == "G") {
      $jawabs = Jawab::where('id_user', Auth::user()->id)->get();
    }
    $kelas = Kelas::orderby('nama', 'asc')->get();
    // $soals = Soal::all();

    $id_user = Auth::user()->id;
    if (Auth::user()->status == "A"){
      $soals = Soal::paginate(10);
      $materis = Materi::join('soals', 'materis.id', '=', 'soals.materi')
                          ->select('materis.*')
                          ->where('materis.id_user', Auth::user()->id)->where('materis.id', '!=', 'soals.materi')->get();
    }elseif (Auth::user()->status == "G") {
      $soals = Soal::where('id_user', $id_user)->paginate(10);
      $materis = Materi::join('soals', 'materis.id', '=', 'soals.materi')
                          ->select('materis.*')
                          ->where('materis.id', '!=', 'soals.materi')->get();
    }

    return view('guru.soal', compact('user', 'school', 'jawabs', 'kelas', 'soals', 'materis'));

  }
  public function get_soal_guru()
  {
    $user = User::where('id', '=', Auth::user()->id)->first();
    $q = Input::get('q');
    $id_user = Auth::user()->id;
    if (Auth::user()->status == "A"){
      $soals = Soal::where('paket', 'LIKE', '%'.$q.'%')->paginate(15);
    }elseif (Auth::user()->status == "G") {
      $soals = Soal::where('paket', 'LIKE', '%'.$q.'%')->where('id_user', $id_user)->paginate(15);
    }
    return view('guru.ajax.get_soal_guru', compact('soals', 'user'));
  }

  public function anyData()
  {
  	$id_user = Auth::user()->id;
      if (Auth::user()->status == "A"){
          $user = Soal::select(['id', 'paket', 'deskripsi', 'kkm', 'waktu', 'created_at']);
      }elseif (Auth::user()->status == "G") {
          $user = Soal::select(['id', 'paket', 'deskripsi', 'kkm', 'waktu', 'created_at'])->where('id_user', '=', $id_user);
      }

      return Datatables::of($user)
          ->addColumn('action', function ($user) {
              return '<a href=edit-soal/'.$user->id.' class="btn btn-xs btn-success"><i class="fa fa-pencil-square-o"></i> Edit</a> | <a href=detail-soal/'.$user->id.' class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Detail</a> | <a href=hapus-soal/'.$user->id.' class="btn btn-xs btn-danger" target="_blank"><i class="fa fa-search"></i> Detail</a>';
          })
          ->editColumn('waktu', '{{$waktu / 60}} menit')
          ->make(true);
  }

  public function editsoal($id)
  {
  	if (Auth::user()->status != "S") {
          $school = School::first();
          $user = User::where('email', '=', Auth::user()->email)->first();
          $id_soal = $id;
          $soal = Soal::where('id', '=', $id)->first();
          return view('guru.editsoal', compact('user', 'school', 'id_soal', 'soal'));
      }else{
          return redirect('siswa');
      }
  }

  public function simpanformsoal()
  {
  	if(Request::ajax()){
      if(Input::get('jenis') != ""){
        if(Input::get('paket') != ""){
        	if(Input::get('deskripsi') != ""){
        		if(Input::get('kkm') != ""){
        			if(Input::get('waktu') != ""){
                if (Input::get('jenis') == 2) {
                  $materi = Input::get('materi');
                  if ($materi == "") {
                    return "<b>Error:</b> Anda belum memilih materi soal";
                  }else{
                    $simpan = new Soal;
                    $simpan->jenis = Input::get('jenis');
                    $simpan->materi = Input::get('materi');
                    $simpan->paket = Input::get('paket');
                    $simpan->deskripsi = Input::get('deskripsi');
                    $simpan->kkm = Input::get('kkm');
                    $simpan->waktu = Input::get('waktu');
                    $simpan->id_user = Auth::user()->id;
                    $simpan->save();
                    return 'berhasil';
                  }
                }else{
          				$simpan = new Soal;
                  $simpan->jenis = Input::get('jenis');
                  $simpan->paket = Input::get('paket');
                  $simpan->deskripsi = Input::get('deskripsi');
                  $simpan->kkm = Input::get('kkm');
                  $simpan->waktu = Input::get('waktu');
                  $simpan->id_user = Auth::user()->id;
                  $simpan->save();
          				return 'berhasil';
                }
              }else{
              	return "<b>Error:</b> Anda belum menuliskan waktu soal";
              }
            }else{
            	return "<b>Error:</b> Anda belum menuliskan kkm soal";
            }
          }else{
          	return "<b>Error:</b> Anda belum menuliskan deskripsi soal";
          }
        }else{
        	return "<b>Error:</b> Anda belum menuliskan paket soal";
        }
      }else{
        return "<b>Error:</b> Anda belum memilih jenis soal";
      }
    }
  }

  public function updateformsoal()
  {
  	if(Request::ajax()){
          if(Input::get('paket') != ""){
          	if(Input::get('deskripsi') != ""){
          		if(Input::get('kkm') != ""){
          			if(Input::get('waktu') != ""){
          				$simpan = Soal::where('id', '=', Input::get('id_soal'))->first();
                          $simpan->paket = Input::get('paket');
                          $simpan->deskripsi = Input::get('deskripsi');
                          $simpan->kkm = Input::get('kkm');
                          $simpan->waktu = Input::get('waktu');
                          $simpan->save();
          				return 'berhasil';
		            }else{
		            	return "<b>Error:</b> Anda belum menuliskan waktu soal";
		            }
	            }else{
	            	return "<b>Error:</b> Anda belum menuliskan kkm soal";
	            }
            }else{
            	return "<b>Error:</b> Anda belum menuliskan deskripsi soal";
            }
          }else{
          	return "<b>Error:</b> Anda belum menuliskan paket soal";
          }
      }
  }

  public function detailsoal($id)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
    	$school = School::first();
      $user = User::where('email', '=', Auth::user()->email)->first();
      $id_soal = $id;
      $soal = Soal::where('id', '=', $id)->first();
      $detailsoals = Detailsoal::where('id_soal', '=', $soal->id)->get();
      $daftarsoals = Soal::where('id_user', Auth::user()->id)->orderby('paket', 'asc')->get();
      $kelas = Kelas::orderby('nama', 'asc')->get();
      return view('guru.detailsoal', compact('user', 'school', 'id_soal', 'soal', 'detailsoals', 'daftarsoals', 'kelas'));
    }else{
      return redirect('siswa');
    }
  }

  public function hapusdetailsoal()
  {
      if(Request::ajax()){
        $id_soal = Input::get('id_soal');
        $hapus = Detailsoal::where('id', '=', $id_soal)->first();
        $hapus->delete();
        return ('Soal diatas berhasil di hapus...<i>Refresh halaman untuk melihat hasil perubahan</i>');
      }
  }

  public function simpanformdetailsoal()
  {
      if(Request::ajax()){
          $soal = Input::get('soal');
          $pila = Input::get('pila');
          $pilb = Input::get('pilb');
          $pilc = Input::get('pilc');
          $pild = Input::get('pild');
          $pile = Input::get('pile');
          $kunci = Input::get('kunci');
          $score = Input::get('score');
          $status = Input::get('status');
          $paket = Input::get('paket');
          if ($soal!="" and $pila!="" and $pilb!="" and $pilc!="" and $pild!="" and $pile!="" and $paket!="" and $score!="" and $status!="" and $score!="") {
              $simpan = new Detailsoal;
              $simpan->id_soal = $paket;
              $simpan->soal = $soal;
              $simpan->pila = $pila;
              $simpan->pilb = $pilb;
              $simpan->pilc = $pilc;
              $simpan->pild = $pild;
              $simpan->pile = $pile;
              $simpan->kunci = $kunci;
              $simpan->score = $score;
              $simpan->status = $status;
              $simpan->id_user = Auth::user()->id;
              $simpan->save();
              return 'berhasil';   
          }else{
              return '<b>Error:</b> Data Soal belum lengkap. Silahkan lengkapi data diatas...';
          }
      }
  }

  public function ubahdetailsoal($id)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('email', '=', Auth::user()->email)->first();
      $id_soal = $id;
      $detailsoals = Detailsoal::where('id', '=', $id)->first();
      $soal = Soal::where('id', '=', $detailsoals->id_soal)->first();
      $daftarsoals = Soal::where('id_user', Auth::user()->id)->orderby('paket', 'asc')->get();
      return view('guru.ubahdetailsoal', compact('user', 'school', 'id_soal', 'soal', 'detailsoals', 'daftarsoals'));
    }else{
      return redirect('siswa');
    }
  }

  public function ubahformdetailsoal()
  {
      if(Request::ajax()){
        $soal = Input::get('soal');
        $pila = Input::get('pila');
        $pilb = Input::get('pilb');
        $pilc = Input::get('pilc');
        $pild = Input::get('pild');
        $pile = Input::get('pile');
        $kunci = Input::get('kunci');
        $score = Input::get('score');
        $status = Input::get('status');
        $paket = Input::get('paket');
        if ($soal!="" and $pila!="" and $pilb!="" and $pilc!="" and $pild!="" and $pile!="" and $paket!="" and $score!="" and $status!="" and $score!="") {
            //$simpan = new Detailsoal;
            $simpan = Detailsoal::where('id', '=', Input::get('id_soal'))->first();
            //$simpan->id_soal = $paket;
            $simpan->soal = $soal;
            $simpan->pila = $pila;
            $simpan->pilb = $pilb;
            $simpan->pilc = $pilc;
            $simpan->pild = $pild;
            $simpan->pile = $pile;
            $simpan->kunci = $kunci;
            $simpan->score = $score;
            $simpan->status = $status;
            $simpan->save();
            return 'berhasil';   
        }else{
            return '<b>Error:</b> Data Soal belum lengkap. Silahkan lengkapi data diatas...';
        }
      }
  }

  public function simpandistribusikelas()
  {
      if(Request::ajax()){
          $simpan = new Distribusisoal;
          $simpan->id_soal = Input::get('id_soal');
          $simpan->id_kelas = Input::get('id_kelas');
          $simpan->save();
      }
  }

  public function hapusdistribusikelas()
  {
      if(Request::ajax()){
        $id_soal = Input::get('id_soal');
        $id_kelas = Input::get('id_kelas');
        $hapus = Distribusisoal::where('id_soal', '=', $id_soal)->where('id_kelas', '=', $id_kelas)->first();
        $hapus->delete();
      }
  }

  public function hapussoal()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      return view('guru.hapussoal');
    }else{
      return redirect('siswa');
    }
  }

  public function eksekusihapuspaketsoal($id)
  {
     Soal::where('id', '=', $id)->delete();
     echo  "<script type='text/javascript'> window.close(); </script>";
  }
}
