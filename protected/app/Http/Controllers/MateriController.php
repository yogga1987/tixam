<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Image;
use File;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Materi;
use App\User;
use App\School;
use App\Kelas;
use App\Jawab;
use App\Aktifitas;
use App\Soal;

class MateriController extends Controller
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
                              ->orderby('aktifitas.id', 'desc')->limit(5)->get();
      $materis = Materi::where('id_user', Auth::user()->id)->paginate(15);
      return view('guru.materi', compact('materis', 'user', 'aktifitas', 'school'));
    }else{
      return redirect('siswa');
    }
  }
  public function ubah($id)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', '=', Auth::user()->id)->first();
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(5)->get();
      $materi = Materi::where('id', $id)->first();
      return view('guru.ubah_materi', compact('materi', 'user', 'aktifitas', 'school'));
    }else{
      return redirect('siswa');
    }
  }
  public function simpan_materi()
  {
    $sesi = Input::get('sesi');
    $judul = Input::get('judul');
    $isi = Input::get('isi');
    $status = Input::get('status');
    if ($judul == "") {
      return "<b>Error:</b> Judul tidak boleh kosong.";
    }elseif ($isi == "") {
      return "<b>Error:</b> Isi Materi tidak boleh kosong.";
    }else{
      $cek = Materi::where('sesi', $sesi)->first();
      if ($cek == "") {
        $query = new Materi;
        $query->id_user = Auth::user()->id;
        $query->judul = $judul;
        $query->isi = $isi;
        $query->status = $status;
        $query->hits = '0';
        $query->sesi = $sesi;
        $query->save();
        return 'ok';
      }else{
        $cek->judul = $judul;
        $cek->isi = $isi;
        $cek->status = $status;
        $cek->save();
        return 'ok';
      }
    }
  }
  public function detail($id)
  {
    if (Auth::user()->status == "G" or Auth::user()->status == "A") {
      $school = School::first();
      $user = User::where('id', '=', Auth::user()->id)->first();
      $aktifitas = Aktifitas::join('users', 'aktifitas.id_user', '=', 'users.id')
                              ->select('users.nama as nama_user', 'users.gambar', 'aktifitas.*')
                              ->orderby('aktifitas.id', 'desc')->limit(5)->get();
      $materi = Materi::where('id', $id)->first();
      return view('guru.detail_materi', compact('materi', 'user', 'aktifitas', 'school'));
    }else{
      return redirect('siswa');
    }
  }
  public function upload_gambar_materi()
  {
    $id = Input::get('id');
    $sesi = Input::get('sesi');
    $filename = trim(addslashes($_FILES['file']['name']));
    $filenamehapusspasi = str_replace(' ', '_', $filename);
    $savename = md5(round(microtime(true))) . '_' . $filenamehapusspasi;
    $img = Image::make($_FILES['file']['tmp_name']);
    $img->resize(750, null, function ($constraint) {
      $constraint->aspectRatio();
    });
    $img->save('img/materi/'.$savename);
    $cek = Materi::where('sesi', $sesi)->first();
    if ($cek != "") {
      if ($cek->gambar != "") {
        File::delete('img/materi/'.$cek->gambar);
      }
      $cek->gambar = $savename;
      $cek->save();
      $aktifitas = new Aktifitas;
      $aktifitas->id_user = Auth::user()->id;
      $aktifitas->nama = "Merubah materi miliknya.";
      $aktifitas->save();
      return 'ok';
    }else{
      $query = new Materi;
      $query->id_user = Auth::user()->id;
      $query->judul = '-';
      $query->isi = '-';
      $query->gambar = $savename;
      $query->status = 'N';
      $query->hits = '0';
      $query->sesi = $sesi;
      $query->save();
      $aktifitas = new Aktifitas;
      $aktifitas->id_user = Auth::user()->id;
      $aktifitas->nama = "Menulis materi baru.";
      $aktifitas->save();
      return 'ok';
    }
  }
  public function hapus_materi()
  {
    $id = Input::get('id');
    $cek = Materi::where('id', $id)->first();
    if ($cek != "") {
      $aktifitas = new Aktifitas;
      $aktifitas->id_user = Auth::user()->id;
      $aktifitas->nama = "Menghapus materi miliknya yang berjudul ".$cek->judul.'.';
      $aktifitas->save();
      $hapus = Materi::where('id', $id)->delete();
      return 'berhasil';
    }
  }
  public function get_materi()
  {
    $q = Input::get('q');
    $materis = Materi::where('id_user', Auth::user()->id)->where('judul', 'LIKE', '%'.$q.'%')->paginate(15);
    return view('guru.ajax.get_materi', compact('materis', 'q'));
  }
}
