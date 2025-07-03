<?php

namespace App\Observers;

use App\Pdrb;
use App\PdrbFinal;
use App\Rekon;
use App\SettingApp;

class PdrbObserver
{
    /**
     * Handle the pdrb "created" event.
     *
     * @param  \App\Pdrb  $pdrb
     * @return void
     */
    public function created(Pdrb $pdrb)
    {
        // $new_pdrb_final = new PdrbFinal;

        // $pdrb_final = PdrbFinal::where('tahun', $pdrb->tahun)
        //                 ->where('q', $pdrb->q)
        //                 ->where('kode_prov', $pdrb->kode_prov)
        //                 ->where('kode_kab', $pdrb->kode_kab)
        //                 ->where('adhb_or_adhk', $pdrb->adhb_or_adhk)
        //                 ->first();

        // if($pdrb_final!=null){
        //     $new_pdrb_final = $pdrb_final;
        // }

        // $new_pdrb_final->tahun = $pdrb->tahun;
        // $new_pdrb_final->q = $pdrb->q;
        // $new_pdrb_final->adhb_or_adhk = $pdrb->adhb_or_adhk;
        // $new_pdrb_final->status_data = $pdrb->status_data;

        // $new_pdrb_final->kode_prov   = $pdrb->kode_prov;
        // $new_pdrb_final->kode_kab   = $pdrb->kode_kab;
        // $new_pdrb_final->created_by = $pdrb->created_by;
        // $new_pdrb_final->updated_by = $pdrb->updated_by;
        // /////////////////////

        // $new_pdrb_final->revisi_ke = $pdrb->revisi_ke;

        // $new_pdrb_final->c_1a     = $pdrb->c_1a;
        // $new_pdrb_final->c_1b     = $pdrb->c_1b;
        // $new_pdrb_final->c_1c     = $pdrb->c_1c;
        // $new_pdrb_final->c_1d     = $pdrb->c_1d;
        // $new_pdrb_final->c_1e     = $pdrb->c_1e;
        // $new_pdrb_final->c_1f     = $pdrb->c_1f;
        // $new_pdrb_final->c_1g     = $pdrb->c_1g;
        // $new_pdrb_final->c_1h     = $pdrb->c_1h;
        // $new_pdrb_final->c_1i     = $pdrb->c_1i;
        // $new_pdrb_final->c_1j     = $pdrb->c_1j;
        // $new_pdrb_final->c_1k     = $pdrb->c_1k;
        // $new_pdrb_final->c_1l     = $pdrb->c_1l;
        // $new_pdrb_final->c_1      = $pdrb->c_1;

        // $new_pdrb_final->c_2     = $pdrb->c_2;

        // $new_pdrb_final->c_3     = $pdrb->c_3;
        // $new_pdrb_final->c_3a     = $pdrb->c_3a;
        // $new_pdrb_final->c_3b     = $pdrb->c_3b;

        // $new_pdrb_final->c_4     = $pdrb->c_4;
        // $new_pdrb_final->c_4a     = $pdrb->c_4a;
        // $new_pdrb_final->c_4b     = $pdrb->c_4b;

        // $new_pdrb_final->c_5     = $pdrb->c_5;

        // $new_pdrb_final->c_6     = $pdrb->c_6;
        // $new_pdrb_final->c_6a     = $pdrb->c_6a;
        // $new_pdrb_final->c_6b     = $pdrb->c_6b;

        // $new_pdrb_final->c_7     = $pdrb->c_7;
        // $new_pdrb_final->c_7a     = $pdrb->c_7a;
        // $new_pdrb_final->c_7b     = $pdrb->c_7b;

        // $new_pdrb_final->c_8     = $pdrb->c_8;
        // $new_pdrb_final->c_8a     = $pdrb->c_8a;
        // $new_pdrb_final->c_8b     = $pdrb->c_8b;
        // $new_pdrb_final->c_pdrb     = $pdrb->c_pdrb;
        // $new_pdrb_final->save();
    }

    /**
     * Handle the pdrb "updated" event.
     *
     * @param  \App\Pdrb  $pdrb
     * @return void
     */
    public function updated(Pdrb $pdrb)
    {
        //////
        $upload_tahun = date('Y');
        $upload_triwulan = 1;

        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) $upload_tahun = $tahun_berlaku->setting_value;

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) $upload_triwulan = $triwulan_berlaku->setting_value;
        /////////

        $pdrb_final = PdrbFinal::where('tahun', $pdrb->tahun)
            ->where('q', $pdrb->q)
            ->where('kode_prov', $pdrb->kode_prov)
            ->where('kode_kab', $pdrb->kode_kab)
            ->where('adhb_or_adhk', $pdrb->adhb_or_adhk)
            ->where('upload_tahun', $upload_tahun)
            ->where('upload_q', $upload_triwulan)
            ->first();
        $rekon = Rekon::where('tahun', $pdrb->tahun)
            ->where('q', $pdrb->q)
            ->where('kode_prov', $pdrb->kode_prov)
            ->where('kode_kab', $pdrb->kode_kab)
            ->where('adhb_or_adhk', $pdrb->adhb_or_adhk)
            ->where('upload_tahun', $upload_tahun)
            ->where('upload_q', $upload_triwulan)
            ->first();

        if ($pdrb->status_data == 2) {
            $new_pdrb_final = new PdrbFinal;
            $new_rekon = new Rekon();

            if ($pdrb_final != null) {
                $new_pdrb_final = $pdrb_final;
            }

            $new_pdrb_final->tahun = $pdrb->tahun;
            $new_pdrb_final->q = $pdrb->q;

            $new_pdrb_final->upload_tahun = $upload_tahun;
            $new_pdrb_final->upload_q = $upload_triwulan;

            $new_pdrb_final->adhb_or_adhk = $pdrb->adhb_or_adhk;
            $new_pdrb_final->status_data = $pdrb->status_data;
            $new_pdrb_final->putaran = $pdrb->putaran;

            $new_pdrb_final->kode_prov   = $pdrb->kode_prov;
            $new_pdrb_final->kode_kab   = $pdrb->kode_kab;
            $new_pdrb_final->created_by = $pdrb->created_by;
            $new_pdrb_final->updated_by = $pdrb->updated_by;
            /////////////////////

            $new_pdrb_final->revisi_ke = $pdrb->revisi_ke;

            $new_pdrb_final->c_1a     = $pdrb->c_1a;
            $new_pdrb_final->c_1b     = $pdrb->c_1b;
            $new_pdrb_final->c_1c     = $pdrb->c_1c;
            $new_pdrb_final->c_1d     = $pdrb->c_1d;
            $new_pdrb_final->c_1e     = $pdrb->c_1e;
            $new_pdrb_final->c_1f     = $pdrb->c_1f;
            $new_pdrb_final->c_1g     = $pdrb->c_1g;
            $new_pdrb_final->c_1h     = $pdrb->c_1h;
            $new_pdrb_final->c_1i     = $pdrb->c_1i;
            $new_pdrb_final->c_1j     = $pdrb->c_1j;
            $new_pdrb_final->c_1k     = $pdrb->c_1k;
            $new_pdrb_final->c_1l     = $pdrb->c_1l;
            $new_pdrb_final->c_1      = $pdrb->c_1;

            $new_pdrb_final->c_2     = $pdrb->c_2;

            $new_pdrb_final->c_3     = $pdrb->c_3;
            $new_pdrb_final->c_3a     = $pdrb->c_3a;
            $new_pdrb_final->c_3b     = $pdrb->c_3b;

            $new_pdrb_final->c_4     = $pdrb->c_4;
            $new_pdrb_final->c_4a     = $pdrb->c_4a;
            $new_pdrb_final->c_4b     = $pdrb->c_4b;

            $new_pdrb_final->c_5     = $pdrb->c_5;

            $new_pdrb_final->c_6     = $pdrb->c_6;
            $new_pdrb_final->c_6a     = $pdrb->c_6a;
            $new_pdrb_final->c_6b     = $pdrb->c_6b;

            $new_pdrb_final->c_7     = $pdrb->c_7;
            $new_pdrb_final->c_7a     = $pdrb->c_7a;
            $new_pdrb_final->c_7b     = $pdrb->c_7b;

            $new_pdrb_final->c_8     = $pdrb->c_8;
            $new_pdrb_final->c_8a     = $pdrb->c_8a;
            $new_pdrb_final->c_8b     = $pdrb->c_8b;
            $new_pdrb_final->c_pdrb     = $pdrb->c_pdrb;
            $new_pdrb_final->save();

            if ($rekon != null) {
                $new_rekon = $rekon;
            }
            $new_rekon->revisi_ke = $rekon->revisi_ke;
            $new_rekon->putaran = $rekon->putaran;
            $new_rekon->status_data = $rekon->status_data;
            $new_rekon->c_1 = $rekon->c_1;
            $new_rekon->c_1a = $rekon->c_1a;
            $new_rekon->c_1a_adj = null;
            $new_rekon->c_1b = $rekon->c_1b;
            $new_rekon->c_1b_adj = null;
            $new_rekon->c_1c = $rekon->c_1c;
            $new_rekon->c_1c_adj = null;
            $new_rekon->c_1d = $rekon->c_1d;
            $new_rekon->c_1d_adj = null;
            $new_rekon->c_1e = $rekon->c_1e;
            $new_rekon->c_1e_adj = null;
            $new_rekon->c_1f = $rekon->c_1f;
            $new_rekon->c_1f_adj = null;
            $new_rekon->c_1g = $rekon->c_1g;
            $new_rekon->c_1g_adj = null;
            $new_rekon->c_1h = $rekon->c_1h;
            $new_rekon->c_1h_adj = null;
            $new_rekon->c_1i = $rekon->c_1i;
            $new_rekon->c_1i_adj = null;
            $new_rekon->c_1j = $rekon->c_1j;
            $new_rekon->c_1j_adj = null;
            $new_rekon->c_1k = $rekon->c_1k;
            $new_rekon->c_1k_adj = null;
            $new_rekon->c_1l = $rekon->c_1l;
            $new_rekon->c_1l_adj = null;
            $new_rekon->c_2 = $rekon->c_2;
            $new_rekon->c_2_adj = null;
            $new_rekon->c_3 = $rekon->c_3;
            $new_rekon->c_3_adj = null;
            $new_rekon->c_4 = $rekon->c_4;
            $new_rekon->c_4a = $rekon->c_4a;
            $new_rekon->c_4a_adj = null;
            $new_rekon->c_4b = $rekon->c_4b;
            $new_rekon->c_4b_adj = null;
            $new_rekon->c_5 = $rekon->c_5;
            $new_rekon->c_5_adj = null;
            $new_rekon->c_6 = $rekon->c_6;
            $new_rekon->c_6_adj = null;
            $new_rekon->c_7 = $rekon->c_7;
            $new_rekon->c_7_adj = null;
            $new_rekon->c_pdrb = $rekon->c_pdrb;
            $new_rekon->ketua_tim_id = $rekon->ketua_tim_id;
            $new_rekon->pimpinan_id = $rekon->pimpinan_id;
            $new_rekon->created_by = $rekon->created_by;
            $new_rekon->updated_by = $rekon->updated_by;
            $new_rekon->created_at = now();
            $new_rekon->updated_at = now();
        }
        // elseif($pdrb->status_data==1){
        //     if($pdrb_final!=null){
        //         $pdrb_final->delete();
        //     }
        // }
    }

    /**
     * Handle the pdrb "deleted" event.
     *
     * @param  \App\Pdrb  $pdrb
     * @return void
     */
    public function deleted(Pdrb $pdrb)
    {
        //
    }

    /**
     * Handle the pdrb "restored" event.
     *
     * @param  \App\Pdrb  $pdrb
     * @return void
     */
    public function restored(Pdrb $pdrb)
    {
        //
    }

    /**
     * Handle the pdrb "force deleted" event.
     *
     * @param  \App\Pdrb  $pdrb
     * @return void
     */
    public function forceDeleted(Pdrb $pdrb)
    {
        //
    }
}
