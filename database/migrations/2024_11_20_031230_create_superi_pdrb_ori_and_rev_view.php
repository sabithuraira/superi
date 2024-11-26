<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuperiPdrbOriAndRevView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE VIEW superi_pdrb_ori_and_rev_view AS
            SELECT *
            FROM superi_pdrb
            WHERE revisi_ke = 0
            UNION
            SELECT p.*
            FROM superi_pdrb p
            JOIN (
                SELECT tahun, q, adhb_or_adhk, kode_kab, MAX(updated_at) AS last_updated
                FROM superi_pdrb
                GROUP BY tahun, q, adhb_or_adhk, kode_kab
            ) t ON CONCAT(p.tahun, p.q, p.adhb_or_adhk, p.kode_kab) = CONCAT(t.tahun, t.q, t.adhb_or_adhk, t.kode_kab) AND p.updated_at = t.last_updated
            WHERE p.revisi_ke > 0
        ');
    }

    /* FOR MYSQL NEWER VERSION, USER THIS QUERY:
CREATE VIEW superi_pdrb_ori_and_rev_view_1 AS
    SELECT tahun, q, adhb_or_adhk, kode_kab, MAX(updated_at) AS last_updated
                FROM superi_pdrb
                GROUP BY tahun, q, adhb_or_adhk, kode_kab
                
CREATE VIEW superi_pdrb_ori_and_rev_view AS
            SELECT *
            FROM superi_pdrb
            WHERE revisi_ke = 0
            UNION
            SELECT p.*
            FROM superi_pdrb p
            JOIN superi_pdrb_ori_and_rev_view_1 t ON CONCAT(p.tahun, p.q, p.adhb_or_adhk, p.kode_kab) = CONCAT(t.tahun, t.q, t.adhb_or_adhk, t.kode_kab) AND p.updated_at = t.last_updated
            WHERE p.revisi_ke > 0
    */

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW superi_pdrb_ori_and_rev_view');
    }
}
