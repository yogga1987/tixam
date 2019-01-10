<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jawab extends Model
{
	public function kelas()
	{
		return $this->belongsTo("App\Kelas", "id_kelas", "id");
	}

	public function user()
	{
		return $this->belongsTo("App\User", "id_user", "id");
	}

	public function getSoal()
	{
		return $this->belongsTo("App\Soal", "id_soal", "id");
	}
}
