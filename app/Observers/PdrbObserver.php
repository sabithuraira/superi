<?php

namespace App\Observers;

use App\Pdrb;
use App\PdrbFinal;

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
    public function updated(Pdrb $pdrb){
        if($pdrb->status_data==3){
            $new_pdrb_final = new PdrbFinal;

            $pdrb_final = PdrbFinal::where('tahun', $pdrb->tahun)
                            ->where('q', $pdrb->q)
                            ->where('kode_prov', $pdrb->kode_prov)
                            ->where('kode_kab', $pdrb->kode_kab)
                            ->where('adhb_or_adhk', $pdrb->adhb_or_adhk)
                            ->first();
            
            if($pdrb_final!=null){
                $new_pdrb_final = $pdrb_final;
            }
            
            $new_pdrb_final->tahun = $pdrb->tahun;
            $new_pdrb_final->q = $pdrb->q;
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
        }
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
