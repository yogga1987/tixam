<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

use Input;
use Auth;
use File;
use Datatables;
use PhpExcelReader;
use Spreadsheet_Excel_Reader;
use mysqli;
use Excel;

use App\User;
use App\School;
use App\Kelas;
use App\Jawab;
use App\Aktifitas;
use App\Soal;
use App\Detailsoal;
use App\Distribusisoal;

class HasilController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  
  public function hasil_guru()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('email', '=', Auth::user()->email)->first();
      $kelas = Kelas::orderby('nama', 'asc')->get();
      $id_user = Auth::user()->id;
      if (Auth::user()->status == "A") {
        $jawabs = Jawab::join('soals', 'jawabs.id_soal', '=', 'soals.id')
                      ->select(['jawabs.*', 'soals.paket', 'soals.deskripsi', 'soals.kkm', 'soals.waktu'])
                      ->groupby('jawabs.id_soal')->paginate(10);
      }else{
        $jawabs = Jawab::join('soals', 'jawabs.id_soal', '=', 'soals.id')
                      ->select(['jawabs.*', 'soals.paket', 'soals.deskripsi', 'soals.kkm', 'soals.waktu'])
                      ->where('soals.id_user', Auth::user()->id)
                      ->groupby('jawabs.id_soal')->paginate(10);
      }
      if (Auth::user()->status == "A"){
        $soals = Soal::paginate(10);
      }elseif (Auth::user()->status == "G") {
        $soals = Soal::where('id_user', $id_user)->paginate(10);
      }
      return view('guru.hasil', compact('user', 'school', 'jawabs', 'kelas', 'soals'));
    }else{
      return redirect('siswa');
    }
  }
  public function get_hasil_guru()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $user = User::where('id', '=', Auth::user()->id)->first();
      $q = Input::get('q');
      $id_user = Auth::user()->id;
      if (Auth::user()->status == "A"){
        $jawabs = Jawab::join('soals', 'jawabs.id_soal', '=', 'soals.id')
                      ->select(['jawabs.*', 'soals.paket', 'soals.deskripsi', 'soals.kkm', 'soals.waktu'])
                      ->where('soals.paket', 'LIKE', '%'.$q.'%')->groupby('jawabs.id_soal')->paginate(15);
      }elseif (Auth::user()->status == "G") {
        $jawabs = Jawab::join('soals', 'jawabs.id_soal', '=', 'soals.id')
                      ->select(['jawabs.*', 'soals.paket', 'soals.deskripsi', 'soals.kkm', 'soals.waktu'])
                      ->where('soals.paket', 'LIKE', '%'.$q.'%')->where('soals.id_user', Auth::user()->id)
                      ->groupby('jawabs.id_soal')->paginate(15);
      }
      return view('guru.ajax.get_hasil_guru', compact('jawabs', 'user'));
    }else{
      return redirect('siswa');
    }
  }

  public function anyData()
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $id_user = Auth::user()->id;
      if (Auth::user()->status == "A") {
          $jawab = Jawab::join('soals', 'jawabs.id_soal', '=', 'soals.id')->select(['jawabs.id', 'jawabs.id_soal', 'soals.paket', 'soals.deskripsi', 'soals.kkm', 'soals.waktu', 'jawabs.created_at', 'jawabs.updated_at'])->groupby('jawabs.id_soal');
      }else{
          $jawab = Jawab::join('soals', 'jawabs.id_soal', '=', 'soals.id')->select(['jawabs.id', 'jawabs.id_soal', 'soals.paket', 'soals.deskripsi', 'soals.kkm', 'soals.waktu', 'jawabs.created_at', 'jawabs.updated_at'])->where('soals.id_user', '=', Auth::user()->id)->groupby('jawabs.id_soal');
      }

      return Datatables::of($jawab)
          ->addColumn('action', function ($jawab) {
              return '<a href=detail-hasil/'.$jawab->id_soal.' class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Detail</a>';
          })
          ->editColumn('waktu', '{{$waktu / 60}} menit')
          ->make(true);
    }else{
      return redirect('siswa');
    }
  }

  public function detailhasil($id)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('email', '=', Auth::user()->email)->first();
      $soal = Soal::where('id', '=', $id)->first();
      $jawab = Jawab::where('id_soal', '=', $id)->groupBy('id_kelas')->first();
      if($jawab != ""){
        $user_siswa = User::where('id', '=', $jawab->id_user)->first();
      }else{
        $user_siswa = 'N';
      }
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(3)->get();
      $jawabs = Jawab::join('kelas', 'jawabs.id_kelas', '=', 'kelas.id')
                        ->select('kelas.nama as nama_kelas', 'kelas.id as id_kelas', 'jawabs.*')
                        ->where('jawabs.id_soal', $id)
                        ->groupBy('jawabs.id_kelas')->paginate(15);
      
      return view('guru.detailhasil', compact('user', 'school', 'soal', 'jawabs', 'user_siswa', 'aktifitas'));
    }else{
      return redirect('siswa');
    }
  }

  public function downloadlaporanperkelas($id, $id_soal)
  {
    /*$jawabs = Jawab::join('users', 'jawabs.id_user', '=', 'users.id')
                      ->select('users.no_induk as nis', 'users.nama as nama_siswa', 'jawabs.*')
                      ->where('jawabs.id_kelas', $id)->where('jawabs.id_soal', $id_soal)
                      ->groupBy('jawabs.id_user')->get();*/
    $jawab = Jawab::join('kelas', 'jawabs.id_kelas', '=', 'kelas.id')
                    ->select('kelas.nama as nama_kelas', 'jawabs.*')
                    ->where('jawabs.id_kelas', $id)->first();
    $jumlah_soal = Detailsoal::where('id_soal', $id_soal)->where('status', 'Y')->count();
    $paket_soal = Soal::where('id', $id_soal)->first();
    $jawabs = Jawab::select(DB::raw("SUM(score) as score, COUNT(score) as jumlah_benar, id_user, id_soal, id_kelas"))
                    ->where('id_kelas', $id)
                    ->where('id_soal', $id_soal)
                    ->where('status', 'Y')
                    ->groupby('id_user')
                    ->get();

    Excel::create($jawab->nama_kelas, function($excel) use($jawab, $jawabs, $jumlah_soal, $paket_soal){
      $excel->sheet('1', function($sheet) use($jawab, $jawabs, $jumlah_soal, $paket_soal){
        $sheet->loadView('guru.download_laporan_per_kelas', compact('jawab', 'jawabs', 'jumlah_soal', 'paket_soal'));
     });
    })->export('xls');
  }

  public function detailhasilsoal($id, $id_soal)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', Auth::user()->id)->first();
      $soal = Soal::where('id', $id_soal)->first();
      $kelas = Kelas::where('id', $id)->first();

      $jawabs = Jawab::where('id_kelas', $id)->where('id_soal', $id_soal)->groupBy('id_user')->get();
      $prosentasejawabs = Jawab::where('id_kelas', $id)->where('id_soal', $id_soal)->groupBy('no_soal_id')->get();
      
      return view('guru.detailhasilsoal', compact('user', 'school', 'soal', 'kelas', 'jawabs', 'prosentasejawabs'));
    }else{
      return redirect('siswa');
    }
  }

}
