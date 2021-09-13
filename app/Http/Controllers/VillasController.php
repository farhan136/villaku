<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Villa;
use App\Models\Pemesanan;
use App\Models\User;
use App\Models\Testi;
use App\Models\Provil;
use Carbon\Carbon;

class VillasController extends Controller
{

    public function index()
    {
        // $jumlah = json_decode($yes);
        $laris = Pemesanan::select('villa_id')->groupBy('villa_id')->orderBy('villa_id', 'desc')->get();
        // $yes = DB::table('tbl_pemesanan')->selectRaw("villa_id, count(villa_id) GROUP BY villa_id ORDER BY count DESC")->get();
        $yes = DB::table('tbl_pemesanan')->selectRaw("villa_id, count(villa_id) as jumlah")->groupBy('villa_id')->orderBy('jumlah', 'DESC')->pluck('villa_id');

        // $tesWaktu = Pemesanan::select('mulai', 'selesai')->get();
        
        // foreach ($tesWaktu as $tW) {
        //     for ($i=strtotime($tW->mulai); $i <= strtotime($tW->selesai); $i = $i + (60*60*24)) { 
        //         $xyz = date("Y-m-d", $i);
        //         $zyx[] = $xyz;
        //     }
        // }
        // $zyx = array_unique($zyx);
        // dd($zyx);
        $x = 'villa_id';
        $profil = Provil::first();
        $bd = Villa::where('kategori', "Bukit Danau")->get();
        $ci = Villa::where('kategori', "Cipanas")->get();
        $co = Villa::where('kategori', "Coolibah")->get();
        $kb = Villa::where('kategori', "Kota Bunga")->get();
        $testi = Testi::all();
        return view('user.index', ['bd' => $bd, 'ci' => $ci, 'co' => $co, 'kb' => $kb, 'laris'=>$laris->unique($x), 'testi'=>$testi, 'yes'=>$yes , 'profil'=>$profil]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([ //saat di validate, maka semua form harus diisi, jika tidak diisi maka tidak akan berpindah
            'foto_utama' => 'required|mimes:jpg,bmp,png', //mimes berguna untuk membatasi tipe file yang diperbolehkan diupload
            'foto_indoor' => 'required|mimes:jpg,bmp,png', 
            'foto_outdoor' => 'required|mimes:jpg,bmp,png', 
            'kategori' => 'required',
            'villa' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required', 
            'nomor_hp' => 'required',
            'alamat' => 'required',
        ]);
        $villa = new Villa;

        if (isset($_POST['add_villa'])) {

            $foto_utama = $request->file('foto_utama'); 
            $foto_indoor = $request->file('foto_indoor'); 
            $foto_outdoor = $request->file('foto_outdoor');


            //nama file di dalam folder ketika disimpan
            $nama_utama = $foto_utama->getClientOriginalName();
            $nama_indoor = $foto_indoor->getClientOriginalName();
            $nama_outdoor = $foto_outdoor->getClientOriginalName();

            $tujuan_upload = 'image';
            $foto_utama->move($tujuan_upload,$nama_utama);
            $foto_indoor->move($tujuan_upload,$nama_indoor);
            $foto_outdoor->move($tujuan_upload,$nama_outdoor);
            $villa->foto_utama = 'image/'.$nama_utama;
            $villa->foto_indoor = 'image/'.$nama_indoor;
            $villa->foto_outdoor = 'image/'.$nama_outdoor;
            $villa->kategori = $request->kategori;
            $villa->villa = $request->villa;
            $villa->alamat = $request->alamat;
            $villa->deskripsi = $request->deskripsi;
            $villa->harga = $request->harga;
            $villa->nomor_hp = $request->nomor_hp;

            $villa->save();
        }
        return redirect('/admin');
    }

    public function testi(Request $request)
    {
        $validated = $request->validate([
            'pendapat' => 'required',
        ]);
        $testi = new Testi;
        if(isset($_POST['tambahtesti'])){
            $testi->user_id = auth()->user()->id;
            $testi->testimoni = $request->pendapat;
            $testi->bintang = $request->bintang;
            $testi->save();
        }
        return redirect('/');
    }

    public function show($id)
    {
        $villa = DB::table('tbl_villa')->where('id', $id)->get();
        $profil = Provil::first();
        return view('user.properties', ['villa'=>$villa[0], 'profil'=>$profil]);
    }

    public function detail($id)
    {
        $detail = Villa::where('id', $id)->first();//tampilkan data dari tabel villa yang id nya = $id
        return view('admin.detail', ['detail'=>$detail]);
    }
}
