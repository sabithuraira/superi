<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rekon extends Model
{
    protected $table = 'rekon';
    protected $fillable = [
        'id',
        'tahun',
        'q',
        'kode_prov',
        'kode_kab',
        'revisi_ke',
        'putaran',
        'upload_tahun',
        'upload_q',
        'adhb_or_adhk',
        'status_data',
        'c_1',
        'c_1a',
        'c_1a_adj',
        'c_1b',
        'c_1b_adj',
        'c_1c',
        'c_1c_adj',
        'c_1d',
        'c_1d_adj',
        'c_1e',
        'c_1e_adj',
        'c_1f',
        'c_1f_adj',
        'c_1g',
        'c_1g_adj',
        'c_1h',
        'c_1h_adj',
        'c_1i',
        'c_1i_adj',
        'c_1j',
        'c_1j_adj',
        'c_1k',
        'c_1k_adj',
        'c_1l',
        'c_1l_adj',
        'c_2',
        'c_2_adj',
        'c_3',
        'c_3_adj',
        'c_4',
        'c_4a',
        'c_4a_adj',
        'c_4b',
        'c_4b_adj',
        'c_5',
        'c_5_adj',
        'c_6',
        'c_6_adj',
        'c_7',
        'c_7_adj',
        'c_pdrb',
        'ketua_tim_id',
        'pimpinan_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'];

    protected $guarded = [];
}
