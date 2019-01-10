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

class DataguruController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $school = School::first();
    $user = User::where('email', '=', Auth::user()->email)->first();
    $jawabs = Jawab::where('id_user', Auth::user()->id)->get();
    $kelas = Kelas::orderby('nama', 'asc')->get();
    $users = User::where('status', 'G')->orderBy('nama', 'ASC')->paginate(15);
    return view('guru.dataguru', compact('user', 'school', 'jawabs', 'kelas', 'users'));
  }

  public function get_user()
  {
    $q = Input::get('q');
    $users = User::where('status', 'G')->where('nama', 'LIKE', '%'.$q.'%')->orderBy('nama', 'ASC')->paginate(10);
    return view('guru.ajax.get_user', compact('users', 'q'));
  }

    public function simpanformguru()
    {
        if(Request::ajax()){
            if(Input::get('nama') != ""){
                if (Input::get('email') != "") {
                    if (!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)) {
                      return "<b>Error:</b> Email yang Anda masukan tidak valid";
                    }else{
                        if (Input::get('jk') != "") {
                            $user = new User;
                            $user->nama = Input::get('nama');
                            $user->no_induk = Input::get('no_induk');
                            $user->email = Input::get('email');
                            $user->jk = Input::get('jk');
                            $user->status = "G";
                            $user->password = bcrypt(123456);
                            $user->save();
                            return ('berhasil');
                        }else{
                            return "<b>Error:</b> Anda belum mengisi jenis kelamin guru";
                        }
                    }
                }else{
                    return "<b>Error:</b> Anda belum menuliskan email guru";
                }
            }else{
                return "<b>Error:</b> Anda belum menuliskan nama guru";
            }
        }
    }

  public function detailguru($id)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', $id)->first();
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(3)->get();
      return view('guru.detailguru', compact('user', 'school', 'aktifitas'));
    }else{
      return redirect('siswa');
    }
  }
}
