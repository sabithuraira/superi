<?php

namespace App;

use App\Helpers\AssetData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pdrb extends Model
{
    protected $table = 'pdrb';

    public $statusDataLabel = [
        1 => 'Aktif',
        2 => 'Approved By Provinsi',
        3 => 'Approved By Admin',
        4 => 'Reject By Admin',
    ];

    public function getPdrb($wilayah, $tahun, $triwulan, $status)
    {
        $datas = [
            'adhb' => $this->queryPdrb($wilayah, $tahun, $triwulan, 1, $status),
            'adhk' => $this->queryPdrb($wilayah, $tahun, $triwulan, 2, $status),
        ];

        return $datas;
    }

    private function queryPdrb($wilayah, $tahun, $triwulan, $adhb_or_adhk, $status)
    {
        if ($triwulan == 4) {
            $datas = [null, null, null, null, null, null, null, null, null, null, null, null];

            for ($x = $tahun - 2; $x <= $tahun; $x++) {
                $sql = "SELECT * from superi_pdrb
                    WHERE
                        kode_prov='16' AND
                        kode_kab='$wilayah' AND
                        tahun='$x' AND
                        adhb_or_adhk=$adhb_or_adhk AND
                        status_data >= $status
                    ORDER BY  q ASC, revisi_ke DESC";

                $result = DB::select(DB::raw($sql));

                $idx = $x - ($tahun - 2);
                foreach ($result as $value) {
                    if ($datas[$idx * 4 + ($value->q - 1)] == null) {
                        $datas[$idx * 4 + ($value->q - 1)] = $value;
                    }
                }
            }

            return $datas;
        } else {
            $sql = "SELECT * from superi_pdrb
                WHERE
                    kode_prov='16' AND
                    kode_kab='$wilayah' AND
                    tahun='$tahun' AND
                    adhb_or_adhk=$adhb_or_adhk AND
                    status_data>= $status
                ORDER BY  q ASC, id DESC";

            $datas = [null, null, null, null];
            $result = DB::select(DB::raw($sql));

            foreach ($result as $value) {
                if ($datas[$value->q - 1] == null) {
                    $datas[$value->q - 1] = $value;
                }
            }
            return $datas;
        }
    }

    public function get_q($thn, $q, $adhk)
    {
        $rev =  Pdrb::selectRaw('kode_kab, q, MAX(revisi_ke) as max_revisi')
            ->where('tahun', $thn)
            ->where('q', $q)
            ->where('adhb_or_adhk', $adhk)
            ->groupBy('kode_kab', 'q')
            ->get();

        return $rev;
    }

    public function get_data_cum($thn, $q, $adhk, $rev)
    {
        $str_sql_select = "";
        $list_detail_komponen = AssetData::getDetailKomponen();
        foreach ($list_detail_komponen as $item) {
            $str_sql_select .= "SUM(" . $item['select_id'] . ") as " . $item['id'] . ",";
        }
        $str_sql_select = substr($str_sql_select, 0, -1);
        $data = Pdrb::select('*')
            ->where('tahun', $thn)
            ->where('q', $q)
            ->where('adhb_or_adhk', $adhk)
            ->where(function ($query) use ($rev) {
                foreach ($rev as $r) {
                    $query->orWhere(function ($subquery) use ($r) {
                        $subquery->where('kode_kab', $r->kode_kab)
                            ->where('q', $r->q)
                            ->where('revisi_ke', $r->max_revisi);
                    });
                }
            })
            ->orderby('kode_kab', 'asc')
            ->get();
        return $data;
    }

    public function getStatusBeranda()
    {
        $tahun_berlaku = SettingApp::where('setting_name', 'tahun_berlaku')->first();
        if ($tahun_berlaku != null) {
            $tahun = $tahun_berlaku->setting_value;
        }

        $triwulan_berlaku = SettingApp::where('setting_name', 'triwulan_berlaku')->first();
        if ($triwulan_berlaku != null) {
            $triwulan = $triwulan_berlaku->setting_value;
        }
        $rev = $this->get_q($tahun, $triwulan, 2);
        $data  = $this->get_data_cum($tahun, $triwulan, 2, $rev);
        return $data;
    }

    public function getNamaWilayahAttribute()
    {
        $wilayah = config('app.wilayah');
        return $wilayah[$this->kode_kab] ?? '-';
    }
}
