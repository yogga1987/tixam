<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Requests;

use App\User;
use App\Soal;

use Datatables;

use App\Http\Controllers\Controller;


class DatatablesController extends Controller
{
	
    /*public function getIndex()
	{
	    return view('datatables.index');
	}

	public function anyData()
	{

	    return Datatables::of(User::select('*'))->make(true);
	}*/

	public function getIndex()
    {
        return view('datatables.index');
    }

    public function anyData()
    {
        $soals = Soal::join('users', 'soals.id_user', '=', 'users.id')
            ->select(['soals.id', 'soals.paket', 'users.nama', 'users.email', 'soals.created_at', 'soals.updated_at']);

 
        return Datatables::of($soals)
            ->editColumn('paket', '{!! str_limit($paket, 60) !!}')
            /*->editColumn('nama', function ($model) {
                return \Html::mailto($model->email, $model->nama);
            })*/
            ->make(true);
    }
}
