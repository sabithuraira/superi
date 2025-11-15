<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CreateHasilRekonExport implements FromArray, WithEvents
{
    protected $periode_data;


    public function __construct($periode_data)
    {
        $this->periode_data = $periode_data;
    }

    public function array(): array
    {
        $rows = [];

        $header1 = ['Komponen'];
        foreach ($this->periode_data as $periode => $data) {
            $header1[] = $periode;
            $header1[] = ''; // karena colspan 2
        }

        $header2 = [''];
        $komp_1  = ['1. Pengeluaran Konsumsi Rumah Tangga'];
        $komp_1a = ['1.a. Makanan dan Minuman Non Beralkohol'];
        $komp_1b = ['1.b. Minuman Beralkohol dan Rokok'];
        $komp_1c = ['1.c. Pakaian'];
        $komp_1d = ['1.d. Perumahan, Air, Listrik, Gas dan Bahan Bakar Lainnya'];
        $komp_1e = ['1.e. Perabot, Peralatan rumahtangga dan Pemeliharaan Rutin Rumah'];
        $komp_1f = ['1.f. Kesehatan'];
        $komp_1g = ['1.g. Transportasi/Angkutan'];
        $komp_1h = ['1.h. Komunikasi'];
        $komp_1i = ['1.i. Rekreasi dan Budaya'];
        $komp_1j = ['1.j. Pendidikan'];
        $komp_1k = ['1.k. Penginapan dan Hotel'];
        $komp_1l = ['1.l. Barang Pribadi dan Jasa Perorangan'];
        $komp_2 = ['2. Pengeluaran Konsumsi LNPRT'];
        $komp_3 = ['3. Pengeluaran Konsumsi Pemerintah'];
        $komp_4 = ['4. Pembentukan Modal Tetap Bruto'];
        $komp_4a = ['4.a. Bangunan'];
        $komp_4b = ['4.b. Non Bangunan'];
        $komp_5 = ['5. Perubahan Inventori'];
        $komp_6 = ['6. Ekspor Barang dan Jasa'];
        $komp_7 = ['7. Impor Barang dan Jasa'];

        foreach ($this->periode_data as $p => $data) {
            $header2[] = 'ADHB';
            $header2[] = 'ADHK';
            $komp_1[] =  $data['adhb']['c_1a'] + $data['adhb']['c_1a_adj']
                +  $data['adhb']['c_1b'] + $data['adhb']['c_1b_adj']
                +  $data['adhb']['c_1c'] + $data['adhb']['c_1c_adj']
                +  $data['adhb']['c_1d'] + $data['adhb']['c_1d_adj']
                +  $data['adhb']['c_1e'] + $data['adhb']['c_1e_adj']
                +  $data['adhb']['c_1f'] + $data['adhb']['c_1f_adj']
                +  $data['adhb']['c_1g'] + $data['adhb']['c_1g_adj']
                +  $data['adhb']['c_1h'] + $data['adhb']['c_1h_adj']
                +  $data['adhb']['c_1i'] + $data['adhb']['c_1i_adj']
                +  $data['adhb']['c_1j'] + $data['adhb']['c_1j_adj']
                +  $data['adhb']['c_1k'] + $data['adhb']['c_1k_adj']
                +  $data['adhb']['c_1l'] + $data['adhb']['c_1l_adj'];
            $komp_1[] =  $data['adhk']['c_1a'] + $data['adhk']['c_1a_adj']
                +  $data['adhk']['c_1b'] + $data['adhk']['c_1b_adj']
                +  $data['adhk']['c_1c'] + $data['adhk']['c_1c_adj']
                +  $data['adhk']['c_1d'] + $data['adhk']['c_1d_adj']
                +  $data['adhk']['c_1e'] + $data['adhk']['c_1e_adj']
                +  $data['adhk']['c_1f'] + $data['adhk']['c_1f_adj']
                +  $data['adhk']['c_1g'] + $data['adhk']['c_1g_adj']
                +  $data['adhk']['c_1h'] + $data['adhk']['c_1h_adj']
                +  $data['adhk']['c_1i'] + $data['adhk']['c_1i_adj']
                +  $data['adhk']['c_1j'] + $data['adhk']['c_1j_adj']
                +  $data['adhk']['c_1k'] + $data['adhk']['c_1k_adj']
                +  $data['adhk']['c_1l'] + $data['adhk']['c_1l_adj'];
            $komp_1a[] =  $data['adhb']['c_1a'] + $data['adhb']['c_1a_adj'];
            $komp_1a[] =  $data['adhk']['c_1a'] + $data['adhk']['c_1a_adj'];
            $komp_1b[] =  $data['adhb']['c_1b'] + $data['adhb']['c_1b_adj'];
            $komp_1b[] =  $data['adhk']['c_1b'] + $data['adhk']['c_1b_adj'];
            $komp_1c[] =  $data['adhb']['c_1c'] + $data['adhb']['c_1c_adj'];
            $komp_1c[] =  $data['adhk']['c_1c'] + $data['adhk']['c_1c_adj'];
            $komp_1d[] =  $data['adhb']['c_1d'] + $data['adhb']['c_1d_adj'];
            $komp_1d[] =  $data['adhk']['c_1d'] + $data['adhk']['c_1d_adj'];
            $komp_1e[] =  $data['adhb']['c_1e'] + $data['adhb']['c_1e_adj'];
            $komp_1e[] =  $data['adhk']['c_1e'] + $data['adhk']['c_1e_adj'];
            $komp_1f[] =  $data['adhb']['c_1f'] + $data['adhb']['c_1f_adj'];
            $komp_1f[] =  $data['adhk']['c_1f'] + $data['adhk']['c_1f_adj'];
            $komp_1g[] =  $data['adhb']['c_1g'] + $data['adhb']['c_1g_adj'];
            $komp_1g[] =  $data['adhk']['c_1g'] + $data['adhk']['c_1g_adj'];
            $komp_1h[] =  $data['adhb']['c_1h'] + $data['adhb']['c_1h_adj'];
            $komp_1h[] =  $data['adhk']['c_1h'] + $data['adhk']['c_1h_adj'];
            $komp_1i[] =  $data['adhb']['c_1i'] + $data['adhb']['c_1i_adj'];
            $komp_1i[] =  $data['adhk']['c_1i'] + $data['adhk']['c_1i_adj'];
            $komp_1j[] =  $data['adhb']['c_1j'] + $data['adhb']['c_1j_adj'];
            $komp_1j[] =  $data['adhk']['c_1j'] + $data['adhk']['c_1j_adj'];
            $komp_1k[] =  $data['adhb']['c_1k'] + $data['adhb']['c_1k_adj'];
            $komp_1k[] =  $data['adhk']['c_1k'] + $data['adhk']['c_1k_adj'];
            $komp_1l[] =  $data['adhb']['c_1l'] + $data['adhb']['c_1l_adj'];
            $komp_1l[] =  $data['adhk']['c_1l'] + $data['adhk']['c_1l_adj'];
            $komp_2[] =  $data['adhb']['c_2'] + $data['adhb']['c_2_adj'];
            $komp_2[] =  $data['adhk']['c_2'] + $data['adhk']['c_2_adj'];
            $komp_3[] =  $data['adhb']['c_3'] + $data['adhb']['c_3_adj'];
            $komp_3[] =  $data['adhk']['c_3'] + $data['adhk']['c_3_adj'];
            $komp_4[] =  $data['adhb']['c_4'] + $data['adhb']['c_4_adj'];
            $komp_4[] =  $data['adhk']['c_4'] + $data['adhk']['c_4_adj'];
            $komp_4a[] =  $data['adhb']['c_4a'] + $data['adhb']['c_4a_adj'];
            $komp_4a[] =  $data['adhk']['c_4a'] + $data['adhk']['c_4a_adj'];
            $komp_4b[] =  $data['adhb']['c_4b'] + $data['adhb']['c_4b_adj'];
            $komp_4b[] =  $data['adhk']['c_4b'] + $data['adhk']['c_4b_adj'];
            $komp_5[] =  $data['adhb']['c_5'] + $data['adhb']['c_5_adj'];
            $komp_5[] =  $data['adhk']['c_5'] + $data['adhk']['c_5_adj'];
            $komp_6[] =  $data['adhb']['c_6'] + $data['adhb']['c_6_adj'];
            $komp_6[] =  $data['adhk']['c_6'] + $data['adhk']['c_6_adj'];
            $komp_7[] =  $data['adhb']['c_7'] + $data['adhb']['c_7_adj'];
            $komp_7[] =  $data['adhk']['c_7'] + $data['adhk']['c_7_adj'];
        }
        $rows[] = $header1;
        $rows[] = $header2;
        $rows[] = $komp_1;
        $rows[] = $komp_1a;
        $rows[] = $komp_1c;
        $rows[] = $komp_1d;
        $rows[] = $komp_1e;
        $rows[] = $komp_1f;
        $rows[] = $komp_1g;
        $rows[] = $komp_1h;
        $rows[] = $komp_1i;
        $rows[] = $komp_1j;
        $rows[] = $komp_1k;
        $rows[] = $komp_1l;
        $rows[] = $komp_2;
        $rows[] = $komp_3;
        $rows[] = $komp_4;
        $rows[] = $komp_4a;
        $rows[] = $komp_4b;
        $rows[] = $komp_5;
        $rows[] = $komp_6;
        $rows[] = $komp_7;

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $col = 2; // kolom B
                foreach ($this->periode_data as $periode => $data) {
                    $sheet->mergeCellsByColumnAndRow($col, 1, $col + 1, 1);
                    $col += 2;
                }

                // Merge kolom "Komponen"
                $sheet->mergeCells('A1:A2');

                // Styling rata tengah
                $sheet->getStyle('A1:Z2')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1:Z2')->getAlignment()->setVertical('center');
                $sheet->getStyle('A1:Z2')->getFont()->setBold(true);
            }
        ];
    }
}
