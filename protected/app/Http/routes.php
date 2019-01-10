<?php
use App\User;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/lobby-siswa', function(){
    return view('lobbysiswa');
});

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('/auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('/auth/register', 'Auth\AuthController@postRegister');
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('/password/email', 'Auth\PasswordController@postEmail');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('/password/reset', 'Auth\PasswordController@postReset');

Route::get('/aktifitas', 'GuruController@aktifitas');

//Route::resource('guru', 'GuruController@index');
Route::get('guru', 'GuruController@index');
Route::get('profil-guru', 'GuruController@profil');
Route::get('kelas', 'GuruController@kelas');
Route::post('/ajax/ubah-kelas', 'GuruController@ubah_kelas');
Route::post('/tambahkelas', 'GuruController@tambahkelas');
Route::post('/ubahkelas', 'GuruController@ubahkelas');
Route::post('/hapuskelas', 'GuruController@hapuskelas');
Route::get('detail-kelas/{id}', 'GuruController@detailkelas');
Route::post('/cekkelassiswa', 'GuruController@cekkelassiswa');
Route::post('/tambahsiswakekelas', 'GuruController@tambahsiswakekelas');
Route::post('/hapuskelassiswa', 'GuruController@hapuskelassiswa');
Route::get('detail-kelas-siswa/{id}', 'GuruController@detailkelassiswa');
Route::post('/updateprofilfotosiswa', 'GuruController@updateprofilfotosiswa');
Route::post('/updateprofilsiswa', 'GuruController@updateprofilsiswa');
Route::post('/uploadsiswa', 'GuruController@uploadsiswa');
Route::post('/uploadcalonsiswa', 'GuruController@uploadcalonsiswa');
Route::get('/hapuscalonsiswa', 'GuruController@hapuscalonsiswa');
Route::post('/uploadsoal', 'GuruController@uploadsoal');
Route::get('tampilsiswa', 'GuruController@tampilsiswa');
Route::post('/hapussiswa', 'GuruController@hapussiswa');
Route::post('/hapusjawabkelas', 'GuruController@hapusjawabkelas');
Route::post('/hapusjawabsiswa', 'GuruController@hapusjawabsiswa');
Route::get('/hapusguru/{id}', 'GuruController@hapusguru');

// Route upload foto user
Route::post('/upload-foto-user', 'GuruController@upload_foto_user');

Route::get('/data-siswa', 'GuruController@data_siswa');
Route::post('/get-siswa', 'GuruController@get_siswa');

Route::post('/simpanformsiswa', 'GuruController@simpanformsiswa');

Route::get('/data-guru', 'DataguruController@index');
Route::post('/get-user', 'DataguruController@get_user');
Route::post('/simpanformguru', 'DataguruController@simpanformguru');
Route::get('detail-guru/{id}', 'DataguruController@detailguru');

Route::get('soal-guru', 'SoalController@soal_guru');
Route::post('get-soal-guru', 'SoalController@get_soal_guru');

Route::get('edit-soal/{id}', 'SoalController@editsoal');
Route::post('/simpanformsoal', 'SoalController@simpanformsoal');
Route::post('/updateformsoal', 'SoalController@updateformsoal');
Route::get('detail-soal/{id}', 'SoalController@detailsoal');
Route::post('/simpanformdetailsoal', 'SoalController@simpanformdetailsoal');
Route::get('ubah-detail-soal/{id}', 'SoalController@ubahdetailsoal');
Route::post('/ubahformdetailsoal', 'SoalController@ubahformdetailsoal');
Route::post('/simpandistribusikelas', 'SoalController@simpandistribusikelas');
Route::post('/hapusdistribusikelas', 'SoalController@hapusdistribusikelas');

Route::post('/hapusdetailsoal', 'SoalController@hapusdetailsoal');

Route::get('/hasil-guru', 'HasilController@hasil_guru');
Route::post('/get-hasil-guru', 'HasilController@get_hasil_guru');

Route::get('detail-hasil/{id}', 'HasilController@detailhasil');
Route::get('downloadlaporanperkelas/{id}/{id_soal}', 'HasilController@downloadlaporanperkelas');
Route::get('downloadlaporanfullperkelas/{id}/{id_soal}', 'HasilController@downloadlaporanfullperkelas');
Route::get('detail-hasil-soal/{id}/{id_soal}', 'HasilController@detailhasilsoal');

//Route::resource('siswa', 'SiswaController@index');
Route::resource('siswa', 'SiswaController@index');
Route::get('profil-siswa', 'SiswaController@profil');
Route::post('/updateprofil', 'GuruController@update_profil');
Route::post('/updateprofilfoto', 'SiswaController@updateprofilfoto');
Route::get('soal-siswa', 'SiswaController@soal');
Route::get('soal-siswa/{id}', 'SiswaController@detailsoal');
Route::get('detail-ujian/{id}', 'SiswaController@detailujian');

Route::get('/ajax/halaman/soals', function(){
    $detailsoals = Detailsoal::join('soals', 'detailsoals.id_soal', '=', 'soals.id')
                        ->select('soals.paket', 'detailsoals.*')
                        ->where('detailsoals.id_soal', $id)->paginate(1);
    return View::make('/soal-siswa/')->with('detailsoals')->render();
});

Route::get('/proses-ujian/{id}', 'SiswaController@prosesUjian');
Route::post('/simpanjawabankliksiswa', 'SiswaController@simpanjawabankliksiswa');
Route::post('/kirimjawaban', 'SiswaController@kirimjawaban');

Route::get('hasil-siswa', 'SiswaController@hasil_siswa');
Route::get('hasil-siswa/detail/{id}', 'SiswaController@detail_hasil_siswa');
Route::post('/get-hasil', 'SiswaController@get_hasil');

Route::post('/updateprofilguru', 'GuruController@updateprofilguru');
Route::get('/tampilhasil/{idkelas}/{idsoal}', 'GuruController@tampilhasil');

//update baru
Route::get('hapus-soal/{id}', 'SoalController@hapussoal');
Route::get('eksekusi-hapus-paket-soal/{id}', 'SoalController@eksekusihapuspaketsoal');


// Route test
Route::get('/test', 'SiswaController@test');

Route::post('/test/nilai', 'SiswaController@nilai');

Route::post('/get-soal/{id}', 'SiswaController@get_soal');

// Route untuk Latihan siswa
Route::get('/latihan', 'LatihanController@index');
Route::get('/latihan/read/{id}/{judul}', 'LatihanController@detail');

// Route untuk Materi guru
Route::get('/materi', 'MateriController@index');
Route::get('/materi/ubah/{id}', 'MateriController@ubah');
Route::post('/simpan-materi', 'MateriController@simpan_materi');
Route::get('/materi/detail/{id}', 'MateriController@detail');
Route::post('/upload-gambar-materi', 'MateriController@upload_gambar_materi');
Route::post('/hapus_materi', 'MateriController@hapus_materi');
Route::post('/get-materi', 'MateriController@get_materi');

// Route untuk Absensi siswa oleh guru
Route::get('/input-absen', 'GuruController@input_absen');

Route::post('/ajax/get_siswa_absen', 'GuruController@get_siswa_absen');
Route::post('/crud/input-absen', 'GuruController@crud_input_absen');

Route::get('/rekap-absen', 'GuruController@rekap_absen');