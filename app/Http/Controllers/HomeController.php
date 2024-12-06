<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = 'hai';

    public function beranda(Request $request){
        return view('home.beranda');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return redirect('ckp');
        // return view('home');
        $provider = new \JKD\SSO\Client\Provider\Keycloak([
            'authServerUrl'         => 'https://sso.bps.go.id',
            'realm'                 => 'pegawai-bps',
            'clientId'              => '11600-musi-p93',
            'clientSecret'          => 'c9da3629-8a2d-4563-b9a9-043590445e45',
            'redirectUri'           => 'https://webapps.bps.go.id/sumsel/superi/beranda'
        ]);


        //////////
            $request->session()->put('oauth2state', $provider->getState());
        //////////

        if (!$request->has('code')) {
            // Untuk mendapatkan authorization code
            $authUrl = $provider->getAuthorizationUrl();
            header('Location: '.$authUrl);
            dd($request->session()->all());
            exit;
        
        } else {
            $token = "";
            try {
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $request->get('code')
                ]);
                $request->session()->put('token', $token);
            } catch (Exception $e) {
                print_r('Gagal mendapatkan akses token : '.$e->getMessage());
            }

            
            $data_request = array(
                $this->username() =>'admin@email.com',
                'password'  => 'admin123'
            );
            if ($this->attemptLogin($data_request)) {
                return $this->sendLoginResponse($data_request);
            }
        
            // Opsional: Setelah mendapatkan token, anda dapat melihat data profil pengguna
            // SETELAH LOGIN LANGSUNG SYNC DATA PEGAWAI DG SIMPEG (KALO SIMPEG DILUAR SUMSEL GA BISA AKSES)
            try {
                $user = $provider->getResourceOwner($token);

                $c_nip = $user->getNip();

                $service_url    = "https://apisimpeg.web.bps.go.id/bps16"; //'https://simpeg.bps.go.id/api/bps16'; //'https://backoffice.bps.go.id/simpeg_api/bps16';
                $curl           = curl_init($service_url);
                $curl_post_data = array(
                    // "apiKey" => '4vl8i/WeNeRlRxM4KDk93VqdT0/LZ9g+GBITo+OiHVs=',
                    "apiKey"    => "L2cvVWtDaE5sczVzTHFPaHBZT0Rzdz09.".$token,
                    "kategori"=> 'view_pegawai',
                    "nip" => $c_nip,
                );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                $curl_response = json_decode(curl_exec($curl));
                
                // print_r($curl_response);die();
    
                if(!isset($curl_response->error)){
                    if($curl_response->total==0){
                        if (!$request->expectsJson()) {
                            return route('guest');
                        }
                    }
                    else{
                        $model= \App\User::where('email','=', $c_nip)->first();
                        $c_model = $curl_response->pegawai[0];
        
                        if($model==null){
                            $model = new \App\User;
                            $model->email = $c_model->niplama;
                            $model->nip_baru = $c_model->nipbaru;
                            $model->name = $c_model->namagelar;
                            $model->password = Hash::make($c_model->niplama);
                        }
                        
                        $model->urutreog = $c_model->urutreog;
                        $model->kdorg = $c_model->kdorg;
                        $model->nmorg = $c_model->nmorg;
                        $model->nmjab = $c_model->nmjab;
                        $model->flagwil = $c_model->flagwil;
                        $model->kdprop = $c_model->kdprop;
                        $model->kdkab = $c_model->kdkab;
                        $model->kdkec = $c_model->kdkec;
                        $model->nmwil = $c_model->nmwil;
                        $model->kdgol = $c_model->kdgol;
                        $model->nmgol = $c_model->nmgol;
                        $model->kdstjab = $c_model->kdstjab;
                        $model->nmstjab = $c_model->nmstjab;
                        $model->kdesl = $c_model->kdesl;
                        $model->foto = $c_model->foto;
                        $model->save();
        
                        $data_request = array(
                            $this->username()=>$model->email,
                            'password'  =>$model->email
                        );
                        try {
                            $service_url    =  "https://apisimpeg.web.bps.go.id/bps16"; //'https://simpeg.bps.go.id/api/bps16';
                            $curl           = curl_init($service_url);
                            $curl_post_data = array(
                                "apiKey" =>  "L2cvVWtDaE5sczVzTHFPaHBZT0Rzdz09.".$token,
                                "kategori"=> 'view_jabatan',
                                "nip" => $c_nip,
                            );
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                            $curl_response = json_decode(curl_exec($curl));
                            if(!isset($curl_response->error)){
                                // print_r($curl_response);die();
                                if(sizeof($curl_response)==0){
                                    print_r("Tidak ada Data");
                                }
                                else{
                                    foreach($curl_response as $res){
                                    $tmt = date("Y-m-d", strtotime($res->tmt) );
                                    $tglsk = null;
                                    if($res->tglsk){
                                        $tglsk = date("Y-m-d", strtotime($res->tglsk) );
                                    }
                                    \DB::table('riwayat_sk')->updateOrInsert(
                                        [ 'niplama' => $res->niplama, 'nosk' => $res->nosk],
                                        [
                                            'flagstjab' => $res->flagstjab,
                                            'kdstjab' => $res->kdstjab,
                                            'urutreog' => $res->urutreog,
                                            'kdorg' => $res->kdorg,
                                            'flagwil' => $res->flagwil,
                                            'kdprop' => $res->kdprop,
                                            'kdkab' => $res->kdkab,
                                            'kdkec' => $res->kdkec,
                                            'tmt' => $tmt,
                                            'tglsk' => $tglsk,
                                            'penugasan' => $res->penugasan,
                                            'kdstkerja' => $res->kdstkerja,
                                            'nmstjab' => $res->nmstjab,
                                            'nmorg' => $res->nmorg,
                                            'nmwil' => $res->nmwil,
                                            'created_by' => 1,
                                        ]
                                    );
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            print_r('Gagal Mendapatkan Riwayat SK: '.$e->getMessage());
                            die();
                        }
                        
                        if ($this->attemptLogin($data_request)) {
                            return $this->sendLoginResponse($data_request);
                        }
                    }
                }
            } catch (Exception $e) {
                print_r('Gagal Mendapatkan Data Pengguna: '.$e->getMessage());
                die();
                
                if (!$request->expectsJson()) {
                    return route('guest');
                }
            }
        }
        // $this->incrementLoginAttempts($request);
    }

    protected function attemptLogin($params){
        return $this->guard()->attempt(
            $params, true
        );
    }

    protected function sendLoginResponse($params){
        return redirect('beranda');
    }


    // public function hai(){
    //     return view('hai');
    // }
}
