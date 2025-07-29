@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Rekonsiliasi</li>
    </ul>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ url('css/bootstrap4-toggle.min.css') }}">
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="@csrf">
    <style>
        /* Tambahkan di CSS Anda */
        .modal.fade.show {
            display: block !important;
            opacity: 1 !important;
            z-index: 1060 !important;
        }

        .modal-backdrop.fade.show {
            z-index: 1050 !important;
        }

        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
        }

        .table-container {
            max-height: 750px;
            overflow: auto;
            position: relative;
            border: 1px solid #ddd;
        }

        /* Kolom pertama yang benar-benar fixed */
        /* Header rows */
        .fixed-row-1 {
            position: sticky;
            top: 0;
            z-index: 15;
            /* background: white; */
        }

        .fixed-col {
            position: sticky;
            left: 0;
            z-index: 15;
            /* background: white; */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sticky-cell {
            position: sticky;
            left: 0;
            top: 0;
            z-index: 30;
            /* background: white; */
            box-shadow:
                2px 0 5px rgba(0, 0, 0, 0.1),
                0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .fixed-row-2 {
            position: sticky;
            top: 40px;
            /* Adjust based on row height */
            z-index: 15;
            /* background: white; */
        }

        .fixed-row-3 {
            position: sticky;
            top: 80px;
            /* Adjust based on cumulative height */
            z-index: 15;
            /* background: white; */
        }

        /* Pastikan tidak ada transform yang mengganggu */
        table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .bg-tw1 {
            background-color: #ffee8e;
        }

        .bg-tw2 {
            background-color: #a6ff98;
        }

        .bg-tw3 {
            background-color: #a3c6fa;
        }

        .bg-tw4 {
            background-color: #ff8e8e;
        }
    </style>
@endsection

@section('content')
    <div class="row clearfix" id="app_vue">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Rekonsiliasi</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col">
                            <form id="form_filter">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="komponen_filter">Komponen</label>
                                        <select name="komponen_filter" id="komponen_filter" class="form-control" v-model="komponen_filter">
                                            @foreach ($list_komponen as $komponens)
                                                <option value="{{ $komponens['id'] }}" data-id="{{ $komponens['id'] }}">
                                                    {{ $komponens['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        <label class="text-white">Periode</label>
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal" data-toggle="modal"
                                            data-target="#periodeModal">Pilih Periode</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        <label class="text-white">Simpan</label>
                                        <button class="btn btn-success w-100" type="button" id="simpan" @click="saveDatas">Simpan</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        <label class="text-white">Export</label>
                                        <button class="btn btn-success w-100" type="button" id="export" onclick="exportExcelWithCustomStyles()">Export
                                            <i class="fa fa-download"></i></button>
                                    </div>
                                    <div class="col text-right">
                                        <label class="text-white">Implisit</label>
                                        <input type="checkbox" checked data-toggle="toggle" data-on="Implisit Berubah" data-off="Implisit Tetap"
                                            id="implisit_toggle" data-onstyle="success" data-offstyle="danger">
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="table-container">
                                <table class="table text-center table-bordered " id="tabel-output">
                                    <thead id="thead-row">
                                    </thead>
                                    <tbody id="tbody-data" class="text-left"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="periodeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="title" id="periodeModalLabel">Pilih Periode</h4>
                            </div>
                            <form method="GET" action="">
                                <div class="modal-body mx-4">

                                    <div class="row">
                                        @foreach ($list_periode as $li_per)
                                            <div class="form-check  col-3 ">
                                                <input class="form-check-input" type="checkbox" value="{{ $li_per }}" name="periode_filter[]"
                                                    id="{{ 'periode_filter_' . $li_per }}"
                                                    @foreach ($periode_filter as $per_fil)
                                                    @if ($per_fil === $li_per)
                                                    checked
                                                    @endif @endforeach>
                                                <label class="form-check-label" for="{{ 'periode_filter_' . $li_per }}">
                                                    {{ $li_per }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            Pilihan
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" id="modal_periode_semua" type="button">
                                                Semua Periode
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q1" type="button">
                                                Semua Q1
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q2" type="button">
                                                Semua Q2
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q3" type="button">
                                                Semua Q3
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q4" type="button">
                                                Semua Q4
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_tahun" type="button">
                                                Semua Tahun
                                            </button>
                                            <div class="dropdown-submenu">
                                                <button class="dropdown-item dropdown-toggle" type="button">
                                                    Tahun
                                                </button>
                                                <div class="dropdown-menu">
                                                    @for ($i = 3; $i >= 0; $i--)
                                                        <button class="dropdown-item tahun-selector" id="{{ 'modal_tahun_' . ($tahun_berlaku - $i) }}"
                                                            type="button">{{ $tahun_berlaku - $i }}</button>
                                                    @endfor
                                                </div>
                                            </div>
                                            <button class="dropdown-item" id="modal_periode_hapus" type="button">
                                                Hapus Semua</button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">OK</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/bootstrap4-toggle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blob-polyfill@4.0.20210208/dist/Blob.min.js"></script><!-- Tambahkan di head atau sebelum script Anda -->
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
    <script>
        var vm = new Vue({
            el: "#app_vue",
            data: {
                komponen_filter: {!! json_encode($komponen_filter) !!},
                periode_filter: {!! json_encode($periode_filter) !!},
                storedData: null
            },
            watch: {
                komponen_filter: {

                    handler(val) {
                        this.setDatas();
                        console.log(val)
                    },

                    deep: true
                }
            },
            methods: {
                // checkSessionStorage() {
                //     const storageKey = `rekon_data_${this.komponen_filter}_${this.periode_filter}`;
                //     const storedData = sessionStorage.getItem(storageKey);
                //     if (storedData) {
                //         try {
                //             this.storedData = JSON.parse(storedData);
                //             console.log('Data diambil dari session storage:', this.storedData);
                //         } catch (e) {
                //             console.error('Gagal parsing data dari session storage', e);
                //             this.setDatas();
                //         }
                //     } else {
                //         // Jika tidak ada data di session storage, ambil dari API
                //         this.setDatas();
                //     }
                // },
                setTableHeader: function(event) {},

                setDatas: function(event) {
                    var self = this;
                    $('#wait_progres').modal('show');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    })
                    $.ajax({
                        url: "{{ url('/rekonsiliasi/get_data') }}",
                        method: 'get',
                        dataType: 'json',
                        data: {
                            komponen_filter: self.komponen_filter,
                            periode_filter: self.periode_filter,
                        },
                    }).done(function(data) {
                        $('#thead-row').empty();
                        $('#tbody-data').empty();

                        if (data.data.length === 0) return;

                        periode_header =
                            '<tr><th rowspan = 3 class="sticky-cell fixed-row-1 fixed-col bg-white">Kabupaten/Kota</th>';

                        // color_header = "";
                        self.periode_filter.forEach(periode => {
                            color_header = getHeaderColor(periode);
                            periode_header += '<th colspan=18 class="fixed-row-1 ' + color_header + '">' + periode + '</th>';
                        })

                        str_header = periode_header += '</tr>'
                        str_header = str_header += `<tr>`
                        self.periode_filter.forEach(periode => {
                            color_header = getHeaderColor(periode);
                            str_header = str_header += `
                            <th colspan = 3 class="fixed-row-2 ` + color_header + `">Berlaku</th>
                            <th colspan = 3 class="fixed-row-2 ` + color_header + `">Konstan</th>
                            <th colspan = 2 class="fixed-row-2 ` + color_header + `">Pertumbuhan QtoQ</th>
                            <th colspan = 2 class="fixed-row-2 ` + color_header + `">Pertumbuhan YtoY</th>
                            <th colspan = 2 class="fixed-row-2 ` + color_header + `">Pertumbuhan CtoC</th>
                            <th colspan = 2 class="fixed-row-2 ` + color_header + `">Indeks Implisit</th>
                            <th colspan = 2 class="fixed-row-2 ` + color_header + `">Laju Implisit QtoQ</th>
                            <th colspan = 2 class="fixed-row-2 ` + color_header + `">Laju Implisit YtoY</th>
                            `;
                        })
                        str_header = str_header += `</tr>`
                        str_header = str_header += `<tr>`
                        self.periode_filter.forEach(periode => {
                            color_header = getHeaderColor(periode);
                            str_header = str_header += `
                            <th class="fixed-row-3 ` + color_header + `">PDRB</th>
                            <th class="fixed-row-3  ` + color_header + `">Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB</th>
                            <th class="fixed-row-3 ` + color_header + `">Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB </th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB </th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB </th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB </th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB </th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB </th>
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>`;
                        })
                        str_header = str_header += `</tr>`
                        $('#thead-row').append(str_header);


                        // Isi data baris
                        data.data.forEach(row => {
                            // console.log(row)
                            let rowHtml = '<tr class ="text-right">';
                            rowHtml +=
                                `<td class = "text-left fixed-col bg-white">[${row['kode_kab']??''}] ${row['nama_kab'] ?? ''}</td>`

                            self.periode_filter.forEach(periode => {
                                // ADHB
                                const adhb = row[periode + '_adhb'] !== null && row[periode + '_adhb'] !== undefined ?
                                    parseFloat(row[periode + '_adhb']) : "";
                                const adhb_adj = row[periode + '_adhb_adj'] !== null && row[periode + '_adhb_adj'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_adj']) : 0;
                                const adhb_q1 = row[periode + '_adhb_q1'] !== null && row[periode + '_adhb_q1'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_q1']) : "";
                                const adhb_q1_adj = row[periode + '_adhb_q1_adj'] !== null && row[periode +
                                    '_adhb_q1_adj'] !== undefined ? parseFloat(row[periode + '_adhb_q1_adj']) : "";
                                const adhb_y1 = row[periode + '_adhb_y1'] !== null && row[periode + '_adhb_y1'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_y1']) : "";
                                const adhb_y1_adj = row[periode + '_adhb_y1_adj'] !== null && row[periode +
                                    '_adhb_y1_adj'] !== undefined ? parseFloat(row[periode + '_adhb_y1_adj']) : "";
                                // ADHK
                                const adhk = row[periode + '_adhk'] !== null && row[periode + '_adhk'] !==
                                    undefined ? parseFloat(row[periode + '_adhk']) : "";
                                const adhk_adj = row[periode + '_adhk_adj'] !== null && row[periode + '_adhk_adj'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_adj']) : 0;
                                // ADHK Q1
                                const adhk_q1 = row[periode + '_adhk_q1'] !== null && row[periode + '_adhk_q1'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_q1']) : "";
                                const adhk_q1_adj = row[periode + '_adhk_q1_adj'] !== null && row[periode +
                                    '_adhk_q1_adj'] !== undefined ? parseFloat(row[periode + '_adhk_q1_adj']) : "";
                                // ADHK Y1
                                const adhk_y1 = row[periode + '_adhk_y1'] !== null && row[periode + '_adhk_y1'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_y1']) : "";
                                const adhk_y1_adj = row[periode + '_adhk_y1_adj'] !== null && row[periode +
                                        '_adhk_y1_adj'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_y1_adj']) : "";
                                // ADHK C
                                const adhk_c = row[periode + '_adhk_c'] !== null && row[periode + '_adhk_c'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_c']) : "";
                                const adhk_c_adj = row[periode + '_adhk_c_adj'] !== null && row[periode +
                                    '_adhk_c_adj'] !== undefined ? parseFloat(row[periode + '_adhk_c_adj']) : "";
                                // ADHK C1
                                const adhk_c1 = row[periode + '_adhk_c1'] !== null && row[periode + '_adhk_c1'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_c1']) : "";
                                const adhk_c1_adj = row[periode + '_adhk_c1_adj'] !== null && row[periode +
                                    '_adhk_c1_adj'] !== undefined ? parseFloat(row[periode + '_adhk_c1_adj']) : "";


                                const adhk_c_q = JSON.stringify(row[periode + '_adhk_c_q']);
                                const adhk_c_q_adj = JSON.stringify(row[periode + '_adhk_c_q_adj'])
                                const adhk_c_q1 = JSON.stringify(row[periode + '_adhk_c_q1'])
                                const adhk_c_q1_adj = JSON.stringify(row[periode + '_adhk_c_q1_adj'])

                                // Pertumbuhan ALL
                                const qtq = adhk != "" && adhk_q1 != "" ? (adhk - adhk_q1) / adhk_q1 * 100 : "";
                                const qtq_adj = adhk_q1 + adhk_q1_adj != "" ?
                                    (adhk + adhk_adj - (adhk_q1 + adhk_q1_adj)) / (adhk_q1 + adhk_q1_adj) * 100 : "";

                                const yty = adhk != "" && adhk_y1 != "" ? (adhk - adhk_y1) / adhk_y1 * 100 : "";
                                const yty_adj = adhk_y1 + adhk_y1_adj != "" ?
                                    (adhk + adhk_adj - (adhk_y1 + adhk_y1_adj)) / (adhk_y1 + adhk_y1_adj) * 100 : "";

                                const ctc = adhk_c != "" && adhk_c1 != "" ? (adhk_c - adhk_c1) / adhk_c1 * 100 : "";
                                const ctc_adj = adhk_c1 + adhk_c1_adj != "" ?
                                    (adhk_c + adhk_c_adj - (adhk_c1 + adhk_c1_adj)) / (adhk_c1 + adhk_c1_adj) * 100 :
                                    "";

                                const implisit = adhb != "" && adhk != "" ? (adhb / adhk * 100) : "";
                                const implisit_adj = adhk + adhk_adj != "" ?
                                    ((adhb + adhb_adj) / (adhk + adhk_adj) * 100) : "";

                                const laju_implisit_qtq = adhb != "" && adhk != "" && adhb_q1 != "" && adhk_q1 != "" ?
                                    ((adhb / adhk * 100) / (adhb_q1 / adhk_q1 * 100) * 100 - 100) : "";
                                const laju_implisit_qtq_adj =
                                    adhk + adhk_adj != "" &&
                                    adhk_q1 + adhk_q1_adj != "" ?
                                    (((adhb + adhb_adj) / (adhk + adhk_adj) * 100) /
                                        ((adhb_q1 + adhb_q1_adj) / (adhk_q1 + adhk_q1_adj) * 100) * 100) - 100 : "";

                                const laju_implisit_yty = adhb != "" && adhk != "" && adhb_y1 != "" && adhk_y1 != "" ?
                                    ((adhb / adhk * 100) / (adhb_y1 / adhk_y1 * 100) * 100 - 100) : "";
                                const laju_implisit_yty_adj =
                                    adhk + adhk_adj != "" &&
                                    adhk_y1 + adhk_y1_adj != "" ?
                                    (((adhb + adhb_adj) / (adhk + adhk_adj) * 100) /
                                        ((adhb_y1 + adhb_y1_adj) / (adhk_y1 + adhk_y1_adj) * 100) * 100) - 100 : "";

                                rowHtml += `
                                    <td data-adhb="${adhb}"
                                        data-adhb_adj="${adhb_adj}"
                                        data-adhb_y1="${adhb_y1}"
                                        data-adhb_y1_adj="${adhb_y1_adj}"
                                        data-adhb_q1="${adhb_q1}"
                                        data-adhb_q1_adj="${adhb_q1_adj}">
                                        ${formatNumber(adhb)}
                                    </td>
                                    <td>
                                        <input id="${periode + '_adhb_adj'}"
                                        value="${formatNumber(adhb_adj)}"
                                        data-id="${row[periode + '_adhb_id']}"
                                        data-periode="${periode}"
                                        class="text_edit_adhb"
                                        type="text" inputmode="decimal"
                                        pattern="^\d+(\.\d{0,2})?$" >
                                    </td>
                                    <td id="cell_adhb_adj_${periode}">${formatNumber(adhb + adhb_adj)}</td>
                                    <td data-adhk="${adhk}"
                                        data-adhk_adj="${adhk_adj}"
                                        data-adhk_y1="${adhk_y1}"
                                        data-adhk_y1_adj="${adhk_y1_adj}"
                                        data-adhk_q1="${adhk_q1}"
                                        data-adhk_q1_adj="${adhk_q1_adj}"
                                        data-adhk_c="${adhk_c}"
                                        data-adhk_c_adj="${adhk_c_adj}"
                                        data-adhk_c1="${adhk_c1}"
                                        data-adhk_c1_adj="${adhk_c1_adj}"
                                        data-adhk_c_q='${adhk_c_q}'
                                        data-adhk_c_q_adj='${adhk_c_q_adj}'
                                        data-adhk_c_q1='${adhk_c_q1}'
                                        data-adhk_c_q1_adj='${adhk_c_q1_adj}'
                                        >
                                        ${formatNumber(adhk)}
                                    </td>
                                    <td>
                                        <input id="${periode + '_adhk_adj'}"
                                        value="${formatNumber(adhk_adj)}"
                                        data-id="${row[periode + '_adhk_id']}"
                                        data-periode="${periode}"
                                        class="text_edit_adhk" type="text"
                                        inputmode="decimal" pattern="^\d+(\.\d{0,2})?$" >
                                    </td>
                                    <td >${formatNumber(adhk + adhk_adj)}</td>
                                    <td style="${qtq > 0 ? 'background:lightgreen' : qtq < 0 ? 'background:lemonchiffon' : ''}">
                                       ${formatNumber((qtq))}
                                    </td>
                                    <td style="${qtq_adj > 0 ? 'background:lightgreen' : qtq_adj < 0 ? 'background:lemonchiffon' : ''}">
                                        <input class = "text_edit_qtq_adj" value="${formatNumber(qtq_adj)}"   data-periode="${periode}"
                                        style="${qtq_adj > 0 ? 'background:lightgreen' : qtq_adj < 0 ? 'background:lemonchiffon' : ''}">
                                    </td>
                                    <td style="${yty > 0 ? 'background:lightgreen' : yty < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(yty)}</td>
                                    <td style="${yty_adj > 0 ? 'background:lightgreen' : yty_adj < 0 ? 'background:lemonchiffon' : ''}">
                                        <input class = "text_edit_yty_adj" value="${formatNumber(yty_adj)}"   data-periode="${periode}"
                                        style="${yty_adj > 0 ? 'background:lightgreen' : yty_adj < 0 ? 'background:lemonchiffon' : ''}">
                                    </td>
                                    <td style="${ctc > 0 ? 'background:lightgreen' : ctc < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(ctc)}</td>
                                    <td style="${ctc_adj > 0 ? 'background:lightgreen' : ctc_adj < 0 ? 'background:lemonchiffon' : ''}">
                                        <input  class = "text_edit_ctc_adj" value="${formatNumber(ctc_adj)}"   data-periode="${periode}"
                                         style="${ctc_adj > 0 ? 'background:lightgreen' : ctc_adj < 0 ? 'background:lemonchiffon' : ''}">
                                    </td>
                                    <td style="${implisit > 0 ? 'background:lightgreen' : implisit < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(implisit)}</td>
                                    <td style="${implisit_adj > 0 ? 'background:lightgreen' : implisit_adj < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(implisit_adj)}</td>
                                    <td style="${laju_implisit_qtq > 0 ? 'background:lightgreen' : laju_implisit_qtq < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(laju_implisit_qtq)}</td>
                                    <td style="${laju_implisit_qtq_adj > 0 ? 'background:lightgreen' : laju_implisit_qtq_adj < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(laju_implisit_qtq_adj)}</td>
                                    <td style="${laju_implisit_yty > 0 ? 'background:lightgreen' : laju_implisit_yty < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(laju_implisit_yty)}</td>
                                    <td style="${laju_implisit_yty_adj > 0 ? 'background:lightgreen' : laju_implisit_yty_adj < 0 ? 'background:lemonchiffon' : ''}">${formatNumber(laju_implisit_yty_adj)}</td>
                                `;
                            });

                            rowHtml += '</tr>';
                            $('#tbody-data').append(rowHtml);
                        });

                        $('#wait_progres').modal('toggle')
                    }).fail(function(msg) {
                        console.log(JSON.stringify(msg));
                        $('#wait_progres').modal('toggle')
                    });
                },

                saveDatas: function(event) {
                    $('#wait_progres').modal('show');
                    var self = this;
                    var komp_id = self.komponen_filter;
                    const formData = {
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
                        data: [] // Array untuk menyimpan data input
                    };
                    // 2. Ambil semua input dan ekstrak data-id & value
                    $('#tabel-output input').each(function() {
                        const $input = $(this);
                        if ($input.data('id')) {
                            formData.data.push({
                                id: $input.data('id'), // data-id attribute
                                value: parseNumberIndonesian($input.val()), // Nilai input
                                komp_id: komp_id
                            });
                        }
                    });
                    var url = "{{ url('/rekonsiliasi/save_data') }}";
                    $.ajax({
                        url: url,
                        method: 'POST',
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: JSON.stringify(formData),
                        success: function(response) {
                            $('#wait_progres').modal('toggle')
                            if (response.success) {
                                swal("Sukses!", response.message || "Data berhasil disimpan", "success");
                                // Swal.fire({
                                //     icon: 'success',
                                //     title: 'Sukses!',
                                //     text: response.message ||
                                //         'Data berhasil disimpan', // Gunakan pesan dari response atau default
                                //     timer: 3000,
                                //     showConfirmButton: false
                                // });
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan',
                                    text: response.message || 'Terjadi kesalahan'
                                });
                            }

                            console.log('Data berhasil dikirim', response);

                        },
                        error: function(xhr) {
                            console.error('Gagal mengirim data', xhr.responseText);
                            $('#wait_progres').modal('toggle')
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan server'
                            });
                        }
                    });


                }
            }
        });

        var implisit_toggle = true;
        $(document).ready(function() {
            vm.setDatas();

            document.getElementById('modal_periode_semua').addEventListener('click', () => {
                const checkboxes = document.querySelectorAll("input[id^='periode_filter']");
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            document.getElementById('modal_periode_hapus').addEventListener('click', () => {
                const checkboxes = document.querySelectorAll("input[id^='periode_filter']");
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
            });

            document.getElementById('modal_periode_q1').addEventListener('click', () => {
                const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
                allCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                const q1Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q1']");
                q1Checkboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            document.getElementById('modal_periode_q2').addEventListener('click', () => {
                const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
                allCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                const q2Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q2']");
                q2Checkboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            document.getElementById('modal_periode_q3').addEventListener('click', () => {
                const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
                allCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                const q3Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q3']");
                q3Checkboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            document.getElementById('modal_periode_q4').addEventListener('click', () => {
                const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
                allCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                const q4Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q4']");
                q4Checkboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            document.getElementById('modal_periode_tahun').addEventListener('click', () => {
                const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
                allCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                allCheckboxes.forEach((checkbox) => {
                    const id = checkbox.id;
                    if (/periode_filter_\d{4}$/.test(id)) {
                        checkbox.checked = true;
                    }
                });
            });

            document.querySelectorAll('.tahun-selector').forEach((button) => {
                button.addEventListener('click', (event) => {
                    const buttonId = event.target.id;
                    const year = buttonId.split('_').pop();
                    const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
                    allCheckboxes.forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                    allCheckboxes.forEach((checkbox) => {
                        const id = checkbox.id;
                        if (id.includes(year)) {
                            checkbox.checked = true;
                        }
                    });

                    console.log(`Tombol untuk tahun ${year} diproses.`);
                });
            });

            document.querySelectorAll('.dropdown-submenu .dropdown-toggle').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    this.nextElementSibling.classList.toggle('show');
                });
            });

            document.addEventListener('click', function(e) {
                document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(submenu) {
                    submenu.classList.remove('show');
                });
            });
        });

        $(document).on('input', 'input[type="text"]', function() {
            this.value = this.value.replace(/[^0-9,.-]/g, ''); // hanya angka dan titik
        });

        $(document).on('input', '.text_edit_adhb', function() {
            const input = $(this);
            const periode = input.data('periode');
            let q_split = periode.split('Q');
            let year = parseInt(q_split[0]);
            let quarter = parseInt(q_split[1]);

            const adhb_adj = parseNumberIndonesian(input.val()) || 0;
            const adhk_adj = parseNumberIndonesian(input.closest('td').nextAll().eq(2).find('input').val() || 0);

            const adhb_cell = input.closest('td').prev('td');
            const adhb_adj_cell = input.closest('td').nextAll('td').eq(0);
            const adhk_cell = input.closest('td').nextAll('td').eq(1);
            const adhk_adj_cell = input.closest('td').nextAll('td').eq(2);
            const implisit_adj_cell = input.closest('td').nextAll('td').eq(11);
            const laju_implisit_qtq_adj_cell = input.closest('td').nextAll('td').eq(13);
            const laju_implisit_yty_adj_cell = input.closest('td').nextAll('td').eq(15);

            const adhb = parseNumberIndonesian(adhb_cell.text()) || 0;
            const adhb_q1 = parseNumberIndonesian(adhb_cell.data('adhb_q1'));
            const adhb_y1 = parseNumberIndonesian(adhb_cell.data('adhb_y1'));
            let adhb_q1_adj;
            let adhb_y1_adj;
            let str_q1 = quarter === 1 ? (year - 1) + 'Q4' : year + 'Q' + (quarter - 1);
            // ADHB Q1
            let input_adhb_q1 = $('#' + str_q1 + '_adhb_adj');
            if (input_adhb_q1.length > 0 && input_adhb_q1.is('input')) {
                adhb_q1_adj = parseNumberIndonesian(input_adhb_q1.val());
            } else {
                adhb_q1_adj = parseNumberIndonesian(adhb_cell.data('adhb_q1_adj'));
            }
            // ADHB Y1
            let input_adhb_y1 = $('#' + (year - 1) + 'Q' + quarter + '_adhb_adj')
            if (input_adhb_y1.length > 0 && input_adhb_y1.is('input')) {
                adhb_y1_adj = parseNumberIndonesian(input_adhb_y1.val());
            } else {
                adhb_y1_adj = parseNumberIndonesian(adhb_cell.data('adhb_y1_adj'));
            }

            // ADHK
            const adhk = parseNumberIndonesian(adhk_cell.text()) || 0;
            const adhk_q1 = parseNumberIndonesian(adhk_cell.data('adhk_q1'));
            const adhk_y1 = parseNumberIndonesian(adhk_cell.data('adhk_y1'));
            let adhk_q1_adj;
            let adhk_y1_adj;
            // cek apakah ada input periode sebelumnya di tampilan layar
            // ADHK Q1
            let input_adhk_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhk_q1.length > 0 && input_adhk_q1.is('input')) {
                adhk_q1_adj = parseNumberIndonesian(input_adhk_q1.val());
            } else {
                adhk_q1_adj = parseNumberIndonesian(adhk_cell.data('adhk_q1_adj'));
            }
            // ADHK Y1
            let input_adhk_y1 = $('#' + (year - 1) + 'Q' + quarter + '_adhk_adj')
            if (input_adhk_y1.length > 0 && input_adhk_y1.is('input')) {
                adhk_y1_adj = parseNumberIndonesian(input_adhk_y1.val());
            } else {
                adhk_y1_adj = parseNumberIndonesian(adhk_cell.data('adhk_y1_adj'));
            }

            adhb_adj_val = adhb + adhb_adj;
            implisit_adj_val = adhk + adhk_adj != 0 ? (adhb + adhb_adj) / (adhk + adhk_adj) * 100 : "";
            laju_implisit_qtq_adj_val = adhk + adhk_adj != 0 &&
                adhk_q1 + adhk_q1_adj != 0 ?
                ((((adhb + adhb_adj) / (adhk + adhk_adj) * 100) /
                    ((adhb_q1 + adhb_q1_adj) / (adhk_q1 + adhk_q1_adj) * 100) * 100) - 100) : "";
            laju_implisit_yty_adj_val =
                adhk + adhk_adj != 0 &&
                adhk_y1 + adhk_y1_adj != 0 ?
                ((((adhb + adhb_adj) / (adhk + adhk_adj) * 100) /
                    ((adhb_y1 + adhb_y1_adj) / (adhk_y1 + adhk_y1_adj) * 100) * 100) - 100) : "";


            adhb_adj_cell.text(formatNumber(adhb_adj_val));
            implisit_adj_cell.text(formatNumber(implisit_adj_val));
            laju_implisit_qtq_adj_cell.text(formatNumber(laju_implisit_qtq_adj_val));
            laju_implisit_yty_adj_cell.text(formatNumber(laju_implisit_yty_adj_val));

            implisit_adj_cell.css('background', implisit_adj_val > 0 ? 'lightgreen' : implisit_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            laju_implisit_qtq_adj_cell.css('background', laju_implisit_qtq_adj_val > 0 ? 'lightgreen' : laju_implisit_qtq_adj_val < 0 ?
                'lemonchiffon' : 'transparent');
            laju_implisit_yty_adj_cell.css('background', laju_implisit_yty_adj_val > 0 ? 'lightgreen' : laju_implisit_yty_adj_val < 0 ?
                'lemonchiffon' : 'transparent');

            formatedtext = formatText(input.val());
            $(this).val(formatedtext);

        });

        $(document).on('input', '.text_edit_adhk', function() {
            const input = $(this);
            const periode = input.data('periode');
            let q_split = periode.split('Q');
            let year = parseInt(q_split[0]);
            let quarter = parseInt(q_split[1]);

            const adhb_adj = parseNumberIndonesian(input.closest('td').prevAll().eq(2).find('input').val() || 0);
            const adhk_adj = parseNumberIndonesian(input.val()) || 0;

            const adhb_cell = input.closest('td').prevAll().eq(3);
            const adhb_adj_cell = input.closest('td').prevAll('td').eq(1);

            const adhk_cell = input.closest('td').prevAll('td').eq(0);
            const adhk_adj_cell = input.closest('td').nextAll('td').eq(0);

            const qtq_adj_cell = input.closest('td').nextAll('td').eq(2);
            const qtq_adj_input = qtq_adj_cell.find('input');

            const yty_adj_cell = input.closest('td').nextAll('td').eq(4);
            const yty_adj_input = yty_adj_cell.find('input');

            const ctc_adj_cell = input.closest('td').nextAll('td').eq(6);
            const ctc_adj_input = ctc_adj_cell.find('input');

            const implisit_adj_cell = input.closest('td').nextAll('td').eq(8);
            const laju_implisit_qtq_adj_cell = input.closest('td').nextAll('td').eq(10);
            const laju_implisit_yty_adj_cell = input.closest('td').nextAll('td').eq(12);

            // Nilai ADHB
            const adhb = parseNumberIndonesian(adhb_cell.text()) || 0;
            const adhb_q1 = parseNumberIndonesian(adhb_cell.data('adhb_q1'));
            const adhb_y1 = parseNumberIndonesian(adhb_cell.data('adhb_y1'));

            // cek apakah ada input periode sebelumnya di tampilan layar
            let adhb_q1_adj;
            let adhb_y1_adj;
            // ADHB Q1
            let str_q1 = quarter === 1 ? (year - 1) + 'Q4' : year + 'Q' + (quarter - 1);
            let input_adhb_q1 = $('#' + str_q1 + '_adhb_adj');
            if (input_adhb_q1.length > 0 && input_adhb_q1.is('input')) {
                adhb_q1_adj = parseNumberIndonesian(input_adhb_q1.val());
            } else {
                adhb_q1_adj = parseNumberIndonesian(adhb_cell.data('adhb_q1_adj'));
            }
            // ADHB Y1
            let input_adhb_y1 = $('#' + (year - 1) + 'Q' + quarter + '_adhb_adj')
            if (input_adhb_y1.length > 0 && input_adhb_y1.is('input')) {
                adhb_y1_adj = parseNumberIndonesian(input_adhb_y1.val());
            } else {
                adhb_y1_adj = parseNumberIndonesian(adhb_cell.data('adhb_y1_adj'));
            }

            // Nilai ADHK
            const adhk = parseNumberIndonesian(adhk_cell.text()) || 0;
            const adhk_y1 = parseNumberIndonesian(adhk_cell.data('adhk_y1'));
            const adhk_q1 = parseNumberIndonesian(adhk_cell.data('adhk_q1'));
            const adhk_c = parseNumberIndonesian(adhk_cell.data('adhk_c'));
            const adhk_c1 = parseNumberIndonesian(adhk_cell.data('adhk_c1'));
            // const adhk_y1_adj = parseNumberIndonesian(adhk_cell.data('adhk_y1_adj'));
            // const adhk_q1_adj = parseNumberIndonesian(adhk_cell.data('adhk_q1_adj'));
            // const adhk_c_adj = parseNumberIndonesian(adhk_cell.data('adhk_c_adj'));
            // const adhk_c1_adj = parseNumberIndonesian(adhk_cell.data('adhk_c1_adj'));
            let adhk_q1_adj;
            let adhk_y1_adj;
            let adhk_c_adj = 0;
            let adhk_c1_adj = 0;
            // cek apakah ada input periode sebelumnya di tampilan layar

            // ADHK Q1
            let input_adhk_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhk_q1.length > 0 && input_adhk_q1.is('input')) {
                adhk_q1_adj = parseNumberIndonesian(input_adhk_q1.val());
            } else {
                adhk_q1_adj = parseNumberIndonesian(adhk_cell.data('adhk_q1_adj'));
            }

            // ADHK Y1
            let input_adhk_y1 = $('#' + (year - 1) + 'Q' + quarter + '_adhk_adj')
            if (input_adhk_y1.length > 0 && input_adhk_y1.is('input')) {
                adhk_y1_adj = parseNumberIndonesian(input_adhk_y1.val());
            } else {
                adhk_y1_adj = parseNumberIndonesian(adhk_cell.data('adhk_y1_adj'));
            }

            // ADHK C
            for (var q_c = 1; q_c <= quarter; q_c++) {
                var str_c = year + 'Q' + q_c;
                var str_c1 = (year - 1) + 'Q' + q_c;
                let input_adhk_c = $('#' + str_c + '_adhk_adj')
                if (input_adhk_c.length > 0 && input_adhk_c.is('input')) {
                    adhk_c_adj = adhk_c_adj + parseNumberIndonesian(input_adhk_c.val());
                } else {
                    adhk_c_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c_adj')) : 0;
                }

                let input_adhk_c1 = $('#' + str_c1 + '_adhk_adj')
                if (input_adhk_c1.length > 0 && input_adhk_c1.is('input')) {
                    adhk_c1_adj += parseNumberIndonesian(input_adhk_c1.val());
                } else {
                    adhk_c1_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c1_adj')) : 0;
                }
            }

            adhk_adj_val = adhk + adhk_adj;
            qtq_adj_val = adhk_q1 + adhk_q1_adj != 0 ?
                ((adhk + adhk_adj) - (adhk_q1 + adhk_q1_adj)) / (adhk_q1 + adhk_q1_adj) * 100 : "";
            yty_adj_val = adhk_y1 + adhk_y1_adj != 0 ?
                ((adhk + adhk_adj) - (adhk_y1 + adhk_y1_adj)) / (adhk_y1 + adhk_y1_adj) * 100 : "";
            ctc_adj_val = adhk_c1 + adhk_c1_adj != 0 ?
                ((adhk_c + adhk_c_adj) - (adhk_c1 + adhk_c1_adj)) / (adhk_c1 + adhk_c1_adj) * 100 : "";
            implisit_adj_val = adhk + adhk_adj != 0 ? (adhb + adhb_adj) / (adhk + adhk_adj) * 100 : "";
            laju_implisit_qtq_adj_val = adhk + adhk_adj != 0 &&
                adhk_q1 + adhk_q1_adj != 0 ?
                ((((adhb + adhb_adj) / (adhk + adhk_adj) * 100) /
                    ((adhb_q1 + adhb_q1_adj) / (adhk_q1 + adhk_q1_adj) * 100) * 100) - 100) : "";
            laju_implisit_yty_adj_val =
                adhk + adhk_adj != 0 &&
                adhk_y1 + adhk_y1_adj != 0 ?
                ((((adhb + adhb_adj) / (adhk + adhk_adj) * 100) /
                    ((adhb_y1 + adhb_y1_adj) / (adhk_y1 + adhk_y1_adj) * 100) * 100) - 100) : "";

            adhk_adj_cell.text(formatNumber(adhk_adj_val));
            qtq_adj_input.val(formatNumber(qtq_adj_val));
            yty_adj_input.val(formatNumber(yty_adj_val));
            ctc_adj_input.val(formatNumber(ctc_adj_val));
            implisit_adj_cell.text(formatNumber(implisit_adj_val));
            laju_implisit_qtq_adj_cell.text(formatNumber(laju_implisit_qtq_adj_val));
            laju_implisit_yty_adj_cell.text(formatNumber(laju_implisit_yty_adj_val));

            qtq_adj_cell.css('background', qtq_adj_val > 0 ? 'lightgreen' : qtq_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            qtq_adj_input.css('background', qtq_adj_val > 0 ? 'lightgreen' : qtq_adj_val < 0 ? 'lemonchiffon' : 'transparent');

            yty_adj_cell.css('background', yty_adj_val > 0 ? 'lightgreen' : yty_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            yty_adj_input.css('background', yty_adj_val > 0 ? 'lightgreen' : yty_adj_val < 0 ? 'lemonchiffon' : 'transparent');

            ctc_adj_cell.css('background', ctc_adj_val > 0 ? 'lightgreen' : ctc_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            ctc_adj_input.css('background', ctc_adj_val > 0 ? 'lightgreen' : ctc_adj_val < 0 ? 'lemonchiffon' : 'transparent');

            implisit_adj_cell.css('background', implisit_adj_val > 0 ? 'lightgreen' : implisit_adj_val < 0 ? 'lemonchiffon' :
                'transparent');
            laju_implisit_qtq_adj_cell.css('background', laju_implisit_qtq_adj_val > 0 ? 'lightgreen' :
                laju_implisit_qtq_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            laju_implisit_yty_adj_cell.css('background', laju_implisit_yty_adj_val > 0 ? 'lightgreen' :
                laju_implisit_yty_adj_val < 0 ? 'lemonchiffon' : 'transparent');

            formatedtext = formatText(input.val());
            $(this).val(formatedtext);
        });

        $(document).on('input', '.text_edit_qtq_adj', function() {
            const input = $(this);
            const periode = input.data('periode');
            let q_split = periode.split('Q');
            let year = parseInt(q_split[0]);
            let quarter = parseInt(q_split[1]);

            // find cell and input
            const adhk_cell = input.closest('td').prevAll('td').eq(3);
            const adhk_adj_cell = input.closest('td').prevAll('td').eq(1);
            const adhk_adj_input = input.closest('td').prevAll('td').eq(2).find('input');

            const adhb_cell = input.closest('td').prevAll('td').eq(6);
            const adhb_adj_cell = input.closest('td').prevAll('td').eq(4);
            const adhb_adj_input = input.closest('td').prevAll('td').eq(5).find('input');
            const yty_adj_input = input.closest('td').nextAll('td').eq(1).find('input');
            const ctc_adj_input = input.closest('td').nextAll('td').eq(3).find('input');
            const indeks_implisit_adj_cell = input.closest('td').nextAll('td').eq(5);
            const laju_implisit_qtq_adj_cell = input.closest('td').nextAll('td').eq(7);
            const laju_implisit_yty_adj_cell = input.closest('td').nextAll('td').eq(9);

            // getting value
            const adhb = parseNumberIndonesian(adhb_cell.text()) || 0;
            const adhk = parseNumberIndonesian(adhk_cell.text()) || 0;
            const adhb_q1 = parseNumberIndonesian(adhb_cell.data('adhb_q1'));
            const adhk_q1 = parseNumberIndonesian(adhk_cell.data('adhk_q1'));
            const adhb_y1 = parseNumberIndonesian(adhb_cell.data('adhb_y1'));
            const adhk_y1 = parseNumberIndonesian(adhk_cell.data('adhk_y1'));
            const adhk_c = parseNumberIndonesian(adhk_cell.data('adhk_c'));
            const adhk_c1 = parseNumberIndonesian(adhk_cell.data('adhk_c1'));
            const qtq_adj = parseNumberIndonesian(input.val()) || 0;
            let adhk_adj_val;
            let adhb_adj_val;
            let adhb_q1_adj;
            let adhk_q1_adj;
            let adhb_y1_adj;
            let adhk_y1_adj;
            let yty_adj;
            let ctc_adj;
            let adhk_c_adj = 0;
            let adhk_c1_adj = 0;
            let indeks_implisit_adj;
            let laju_implisit_qtq_adj;
            let laju_implisit_yty_adj;


            // calculate adhk
            let str_q1 = quarter === 1 ? (year - 1) + 'Q4' : year + 'Q' + (quarter - 1);
            let input_adhk_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhk_q1.length > 0 && input_adhk_q1.is('input')) {
                adhk_q1_adj = parseNumberIndonesian(input_adhk_q1.val());
            } else {
                adhk_q1_adj = parseNumberIndonesian(adhk_cell.data('adhk_q1_adj'));
            }
            let input_adhb_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhb_q1.length > 0 && input_adhk_q1.is('input')) {
                adhb_q1_adj = parseNumberIndonesian(input_adhb_q1.val());
            } else {
                adhb_q1_adj = parseNumberIndonesian(adhb_cell.data('adhb_q1_adj'));
            }
            adhk_adj_val = ((qtq_adj * (adhk_q1 + adhk_q1_adj) / 100) + (adhk_q1 + adhk_q1_adj)) - adhk;
            adhk_adj_cell.text(formatNumber(adhk_adj_val + adhk));
            adhk_adj_input.val(formatNumber(adhk_adj_val));
            adhk_adj_cell.css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            adhk_adj_input.closest('td').css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ?
                'lemonchiffon' : 'transparent');
            adhk_adj_input.css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ? 'lemonchiffon' : 'transparent');

            // caculate yty
            let str_y1 = (year - 1) + 'Q' + quarter;
            let input_adhk_y1 = $('#' + str_y1 + '_adhk_adj');
            if (input_adhk_y1.length > 0 && input_adhk_y1.is('input')) {
                adhk_y1_adj = parseNumberIndonesian(input_adhk_q1.val());
            } else {
                adhk_y1_adj = parseNumberIndonesian(adhk_cell.data('adhk_y1_adj'));
            }
            let input_adhb_y1 = $('#' + str_y1 + '_adhk_adj');
            if (input_adhb_y1.length > 0 && input_adhb_y1.is('input')) {
                adhb_y1_adj = parseNumberIndonesian(input_adhb_y1.val());
            } else {
                adhb_y1_adj = parseNumberIndonesian(adhb_cell.data('adhb_y1_adj'));
            }

            yty_adj = adhk_y1 + adhk_y1_adj != 0 ? ((adhk + adhk_adj_val) - (adhk_y1 + adhk_y1_adj)) / (adhk_y1 + adhk_y1_adj) * 100 : "";
            yty_adj_input.val(formatNumber(yty_adj));
            yty_adj_input.css('background', yty_adj > 0 ? 'lightgreen' : yty_adj < 0 ? 'lemonchiffon' : 'transparent');
            yty_adj_input.closest('td').css('background', yty_adj > 0 ? 'lightgreen' : yty_adj < 0 ? 'lemonchiffon' : 'transparent');

            // calculate ctc
            for (var q_c = 1; q_c <= quarter; q_c++) {
                var str_c = year + 'Q' + q_c;
                var str_c1 = (year - 1) + 'Q' + q_c;
                let input_adhk_c = $('#' + str_c + '_adhk_adj')
                if (input_adhk_c.length > 0 && input_adhk_c.is('input')) {
                    adhk_c_adj += adhk_c_adj + parseNumberIndonesian(input_adhk_c.val());
                } else {
                    adhk_c_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c_adj')) : 0;
                }

                let input_adhk_c1 = $('#' + str_c1 + '_adhk_adj')
                if (input_adhk_c1.length > 0 && input_adhk_c1.is('input')) {
                    adhk_c1_adj += parseNumberIndonesian(input_adhk_c1.val());
                } else {
                    adhk_c1_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c1_adj')) : 0;
                }
            }

            ctc_adj = adhk_c1 + adhk_c1_adj != 0 ? ((adhk_c + adhk_c_adj) - (adhk_c1 + adhk_c1_adj)) / (adhk_c1 + adhk_c1_adj) * 100 : "";
            ctc_adj_input.val(formatNumber(ctc_adj));
            ctc_adj_input.css('background', ctc_adj > 0 ? 'lightgreen' : ctc_adj < 0 ? 'lemonchiffon' : 'transparent');
            ctc_adj_input.closest('td').css('background', ctc_adj > 0 ? 'lightgreen' : ctc_adj < 0 ? 'lemonchiffon' : 'transparent');

            if (implisit_toggle) {
                // implisit Berubah (adhb tetap) cari nilai implisit qtq
                adhb_adj_val = parseNumberIndonesian(adhb_adj_input.val());
                laju_implisit_qtq_adj = adhk + adhk_adj_val != 0 && adhk_q1 + adhk_q1_adj != 0 ?
                    ((((adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100) /
                        ((adhb_q1 + adhb_q1_adj) / (adhk_q1 + adhk_q1_adj) * 100) * 100) - 100) : "";
                laju_implisit_qtq_adj_cell.text(formatNumber(laju_implisit_qtq_adj));
                laju_implisit_qtq_adj_cell.css('background', laju_implisit_qtq_adj > 0 ? 'lightgreen' : laju_implisit_qtq_adj < 0 ?
                    'lemonchiffon' : 'transparent');
            } else {
                // Implisit tetap (adhb berubah) cari nilai adhb
                const laju_implisit_qtq_adj_val = parseNumberIndonesian(laju_implisit_qtq_adj_cell.text()) || 0;
                adhb_adj_val = ((adhk + adhk_adj_val) * (adhb_q1 + adhb_q1_adj) * (laju_implisit_qtq_adj_val + 100) /
                    (100 * (adhk_q1 + adhk_q1_adj))) - adhb;
                adhb_adj_cell.text(formatNumber(adhb_adj_val + adhb))
                adhb_adj_input.val(formatNumber(adhb_adj_val))
                adhb_adj_cell.css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ? 'lemonchiffon' : 'transparent');
                adhb_adj_input.css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ? 'lemonchiffon' : 'transparent');
                adhb_adj_input.closest('td').css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ?
                    'lemonchiffon' : 'transparent');
            }
            indeks_implisit_adj = (adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100;
            indeks_implisit_adj_cell.text(formatNumber(indeks_implisit_adj));
            indeks_implisit_adj_cell.css('background', indeks_implisit_adj > 0 ? 'lightgreen' : indeks_implisit_adj < 0 ?
                'lemonchiffon' : 'transparent');

            laju_implisit_yty_adj = adhk + adhk_adj_val != 0 && adhk_y1 + adhk_y1_adj != 0 ?
                ((((adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100) /
                    ((adhb_y1 + adhb_y1_adj) / (adhk_y1 + adhk_y1_adj) * 100) * 100) - 100) : "";
            laju_implisit_yty_adj_cell.text(formatNumber(laju_implisit_yty_adj));
            laju_implisit_yty_adj_cell.css('background', laju_implisit_yty_adj > 0 ? 'lightgreen' : laju_implisit_yty_adj < 0 ?
                'lemonchiffon' : 'transparent');

            formatedtext = formatText(input.val());
            $(this).val(formatedtext);
            input.css('background', parseNumberIndonesian(input.val()) > 0 ? 'lightgreen' : parseNumberIndonesian(input.val()) < 0 ?
                'lemonchiffon' : 'transparent');
            input.closest('td').css('background', parseNumberIndonesian(input.val()) > 0 ? 'lightgreen' : parseNumberIndonesian(input.val()) < 0 ?
                'lemonchiffon' : 'transparent');
        })

        $(document).on('input', '.text_edit_yty_adj', function() {
            const input = $(this);
            const periode = input.data('periode');
            let q_split = periode.split('Q');
            let year = parseInt(q_split[0]);
            let quarter = parseInt(q_split[1]);

            const adhk_cell = input.closest('td').prevAll('td').eq(5);
            const adhk_adj_cell = input.closest('td').prevAll('td').eq(3);
            const adhk_adj_input = input.closest('td').prevAll('td').eq(4).find('input');

            const adhb_cell = input.closest('td').prevAll('td').eq(8);
            const adhb_adj_cell = input.closest('td').prevAll('td').eq(6);
            const adhb_adj_input = input.closest('td').prevAll('td').eq(7).find('input');
            const qtq_adj_input = input.closest('td').prevAll('td').eq(1).find('input');
            const ctc_adj_input = input.closest('td').nextAll('td').eq(1).find('input');
            const indeks_implisit_adj_cell = input.closest('td').nextAll('td').eq(3);
            const laju_implisit_qtq_adj_cell = input.closest('td').nextAll('td').eq(5);
            const laju_implisit_yty_adj_cell = input.closest('td').nextAll('td').eq(7);

            // getting value
            const adhb = parseNumberIndonesian(adhb_cell.text()) || 0;
            const adhk = parseNumberIndonesian(adhk_cell.text()) || 0;
            const adhb_q1 = parseNumberIndonesian(adhb_cell.data('adhb_q1'));
            const adhk_q1 = parseNumberIndonesian(adhk_cell.data('adhk_q1'));
            const adhb_y1 = parseNumberIndonesian(adhb_cell.data('adhb_y1'));
            const adhk_y1 = parseNumberIndonesian(adhk_cell.data('adhk_y1'));
            const adhk_c = parseNumberIndonesian(adhk_cell.data('adhk_c'));
            const adhk_c1 = parseNumberIndonesian(adhk_cell.data('adhk_c1'));
            const yty_adj = parseNumberIndonesian(input.val()) || 0;
            let adhk_adj_val;
            let adhb_adj_val;
            let adhb_q1_adj;
            let adhk_q1_adj;
            let adhb_y1_adj;
            let adhk_y1_adj;
            let qtq_adj;
            let ctc_adj;
            let adhk_c_adj = 0;
            let adhk_c1_adj = 0;
            let indeks_implisit_adj;
            let laju_implisit_qtq_adj;
            let laju_implisit_yty_adj;

            // calculate adhk
            let str_y1 = (year - 1) + 'Q' + quarter;
            let input_adhk_y1 = $('#' + str_y1 + '_adhk_adj');
            if (input_adhk_y1.length > 0 && input_adhk_y1.is('input')) {
                adhk_y1_adj = parseNumberIndonesian(input_adhk_y1.val());
            } else {
                adhk_y1_adj = parseNumberIndonesian(adhk_cell.data('adhk_y1_adj'));
            }
            let input_adhb_y1 = $('#' + str_y1 + '_adhb_adj');
            if (input_adhb_y1.length > 0 && input_adhb_y1.is('input')) {
                adhb_y1_adj = parseNumberIndonesian(input_adhb_y1.val());
            } else {
                adhb_y1_adj = parseNumberIndonesian(adhb_cell.data('adhb_y1_adj'));
            }
            adhk_adj_val = ((yty_adj * (adhk_y1 + adhk_y1_adj) / 100) + (adhk_y1 + adhk_y1_adj)) - adhk;
            adhk_adj_cell.text(formatNumber(adhk_adj_val + adhk));
            adhk_adj_input.val(formatNumber(adhk_adj_val));
            adhk_adj_cell.css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            adhk_adj_input.closest('td').css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ? 'lemonchiffon' :
                'transparent');
            adhk_adj_input.css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ? 'lemonchiffon' : 'transparent');

            // calculate qtq
            let str_q1 = quarter === 1 ? (year - 1) + 'Q4' : year + 'Q' + (quarter - 1);
            let input_adhk_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhk_q1.length > 0 && input_adhk_q1.is('input')) {
                adhk_q1_adj = parseNumberIndonesian(input_adhk_q1.val());
            } else {
                adhk_q1_adj = parseNumberIndonesian(adhk_cell.data('adhk_q1_adj'));
            }
            let input_adhb_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhb_q1.length > 0 && input_adhk_q1.is('input')) {
                adhb_q1_adj = parseNumberIndonesian(input_adhb_q1.val());
            } else {
                adhb_q1_adj = parseNumberIndonesian(adhb_cell.data('adhb_q1_adj'));
            }
            qtq_adj = adhk_q1 + adhk_q1_adj != 0 ? ((adhk + adhk_adj_val) - (adhk_q1 + adhk_q1_adj)) / (adhk_q1 + adhk_q1_adj) * 100 : "";
            qtq_adj_input.val(formatNumber(qtq_adj));
            qtq_adj_input.css('background', qtq_adj > 0 ? 'lightgreen' : qtq_adj < 0 ? 'lemonchiffon' : 'transparent');
            qtq_adj_input.closest('td').css('background', qtq_adj > 0 ? 'lightgreen' : qtq_adj < 0 ? 'lemonchiffon' : 'transparent');

            // calcutate ctc
            for (var q_c = 1; q_c <= quarter; q_c++) {
                var str_c = year + 'Q' + q_c;
                var str_c1 = (year - 1) + 'Q' + q_c;
                let input_adhk_c = $('#' + str_c + '_adhk_adj')
                if (input_adhk_c.length > 0 && input_adhk_c.is('input')) {
                    adhk_c_adj += adhk_c_adj + parseNumberIndonesian(input_adhk_c.val());
                } else {
                    adhk_c_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c_adj')) : 0;
                }

                let input_adhk_c1 = $('#' + str_c1 + '_adhk_adj')
                if (input_adhk_c1.length > 0 && input_adhk_c1.is('input')) {
                    adhk_c1_adj += parseNumberIndonesian(input_adhk_c1.val());
                } else {
                    adhk_c1_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c1_adj')) : 0;
                }
            }
            ctc_adj = adhk_c1 + adhk_c1_adj != 0 ? ((adhk_c + adhk_c_adj) - (adhk_c1 + adhk_c1_adj)) / (adhk_c1 + adhk_c1_adj) * 100 : "";
            ctc_adj_input.val(formatNumber(ctc_adj));
            ctc_adj_input.css('background', ctc_adj > 0 ? 'lightgreen' : ctc_adj < 0 ? 'lemonchiffon' : 'transparent');
            ctc_adj_input.closest('td').css('background', ctc_adj > 0 ? 'lightgreen' : ctc_adj < 0 ? 'lemonchiffon' : 'transparent');


            if (implisit_toggle) {
                // implisit Berubah (adhb tetap) cari nilai implisit qtq
                adhb_adj_val = parseNumberIndonesian(adhb_adj_input.val());
                laju_implisit_yty_adj = adhk + adhk_adj_val != 0 && adhk_y1 + adhk_y1_adj != 0 ?
                    ((((adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100) /
                        ((adhb_y1 + adhb_y1_adj) / (adhk_y1 + adhk_y1_adj) * 100) * 100) - 100) : "";
                laju_implisit_yty_adj_cell.text(formatNumber(laju_implisit_yty_adj));
                laju_implisit_yty_adj_cell.css('background', laju_implisit_yty_adj > 0 ? 'lightgreen' : laju_implisit_yty_adj < 0 ?
                    'lemonchiffon' : 'transparent');
            } else {
                // Implisit tetap (adhb berubah) cari nilai adhb
                laju_implisit_yty_adj = parseNumberIndonesian(laju_implisit_yty_adj_cell.text()) || 0;
                adhb_adj_val = ((adhk + adhk_adj_val) * (adhb_y1 + adhb_y1_adj) * (laju_implisit_yty_adj + 100) /
                    (100 * (adhk_y1 + adhk_y1_adj))) - adhb;
                adhb_adj_cell.text(formatNumber(adhb_adj_val + adhb))
                adhb_adj_input.val(formatNumber(adhb_adj_val))
                adhb_adj_cell.css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ? 'lemonchiffon' : 'transparent');
                adhb_adj_input.css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ? 'lemonchiffon' : 'transparent');
                adhb_adj_input.closest('td').css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ?
                    'lemonchiffon' : 'transparent');
            }
            indeks_implisit_adj = (adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100;
            indeks_implisit_adj_cell.text(formatNumber(indeks_implisit_adj));
            indeks_implisit_adj_cell.css('background', indeks_implisit_adj > 0 ? 'lightgreen' : indeks_implisit_adj < 0 ?
                'lemonchiffon' : 'transparent');

            laju_implisit_qtq_adj = adhk + adhk_adj_val != 0 && adhk_q1 + adhk_q1_adj != 0 ?
                ((((adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100) /
                    ((adhb_q1 + adhb_q1_adj) / (adhk_q1 + adhk_q1_adj) * 100) * 100) - 100) : "";
            laju_implisit_qtq_adj_cell.text(formatNumber(laju_implisit_qtq_adj));
            laju_implisit_qtq_adj_cell.css('background', laju_implisit_qtq_adj > 0 ? 'lightgreen' : laju_implisit_qtq_adj < 0 ?
                'lemonchiffon' : 'transparent');

            formatedtext = formatText(input.val());
            $(this).val(formatedtext);
            input.css('background', parseNumberIndonesian(input.val()) > 0 ? 'lightgreen' : parseNumberIndonesian(input.val()) < 0 ?
                'lemonchiffon' : 'transparent');
            input.closest('td').css('background', parseNumberIndonesian(input.val()) > 0 ? 'lightgreen' : parseNumberIndonesian(input.val()) < 0 ?
                'lemonchiffon' : 'transparent');

        })

        $(document).on('input', '.text_edit_ctc_adj', function() {
            const input = $(this);
            const periode = input.data('periode');
            let q_split = periode.split('Q');
            let year = parseInt(q_split[0]);
            let quarter = parseInt(q_split[1]);

            const adhk_cell = input.closest('td').prevAll('td').eq(7);
            const adhk_adj_cell = input.closest('td').prevAll('td').eq(5);
            const adhk_adj_input = input.closest('td').prevAll('td').eq(6).find('input');

            const adhb_cell = input.closest('td').prevAll('td').eq(10);
            const adhb_adj_cell = input.closest('td').prevAll('td').eq(8);
            const adhb_adj_input = input.closest('td').prevAll('td').eq(9).find('input');
            const qtq_adj_input = input.closest('td').prevAll('td').eq(3).find('input');
            const yty_adj_input = input.closest('td').prevAll('td').eq(1).find('input');
            const indeks_implisit_adj_cell = input.closest('td').nextAll('td').eq(1);
            const laju_implisit_qtq_adj_cell = input.closest('td').nextAll('td').eq(3);
            const laju_implisit_yty_adj_cell = input.closest('td').nextAll('td').eq(5);

            // getting value
            const adhb = parseNumberIndonesian(adhb_cell.text()) || 0;
            const adhk = parseNumberIndonesian(adhk_cell.text()) || 0;
            const adhb_q1 = parseNumberIndonesian(adhb_cell.data('adhb_q1'));
            const adhk_q1 = parseNumberIndonesian(adhk_cell.data('adhk_q1'));
            const adhb_y1 = parseNumberIndonesian(adhb_cell.data('adhb_y1'));
            const adhk_y1 = parseNumberIndonesian(adhk_cell.data('adhk_y1'));
            const adhk_c = parseNumberIndonesian(adhk_cell.data('adhk_c'));
            const adhk_c1 = parseNumberIndonesian(adhk_cell.data('adhk_c1'));
            const ctc_adj = parseNumberIndonesian(input.val()) || 0;
            let adhk_adj_val;
            let adhb_adj_val;
            let adhb_q1_adj;
            let adhk_q1_adj;
            let adhb_y1_adj;
            let adhk_y1_adj;
            let qtq_adj;
            let yty_adj;
            let adhk_c_adj = 0;
            let adhk_c1_adj = 0;
            let indeks_implisit_adj;
            let laju_implisit_qtq_adj;
            let laju_implisit_yty_adj;
            let adhk_c_q1_adj = 0;

            // calculate adhk
            for (var q_c = 1; q_c <= quarter; q_c++) {
                var str_c = year + 'Q' + q_c;
                var str_c1 = (year - 1) + 'Q' + q_c;
                let input_adhk_c = $('#' + str_c + '_adhk_adj')

                if (input_adhk_c.length > 0 && input_adhk_c.is('input')) {
                    adhk_c_adj += adhk_c_adj + parseNumberIndonesian(input_adhk_c.val());
                } else {
                    adhk_c_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c_adj')) : 0;
                }

                let input_adhk_c1 = $('#' + str_c1 + '_adhk_adj')
                if (input_adhk_c1.length > 0 && input_adhk_c1.is('input')) {
                    adhk_c1_adj += parseNumberIndonesian(input_adhk_c1.val());
                } else {
                    adhk_c1_adj += adhk_cell.data('adhk_c_adj') ? parseNumberIndonesian(adhk_cell.data('adhk_c1_adj')) : 0;
                }

                if (q_c < quarter) {
                    adhk_c_q1_adj += adhk_c_adj;
                }
            }

            adhk_c_adj_val = ((ctc_adj * (adhk_c1 + adhk_c1_adj) / 100) + (adhk_c1 + adhk_c1_adj)) - adhk_c;
            adhk_adj_val = adhk_c_adj_val - adhk_c_q1_adj;

            adhk_adj_cell.text(formatNumber(adhk_adj_val + adhk));
            adhk_adj_input.val(formatNumber(adhk_adj_val));
            adhk_adj_cell.css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ? 'lemonchiffon' : 'transparent');
            adhk_adj_input.closest('td').css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ?
                'lemonchiffon' : 'transparent');
            adhk_adj_input.css('background', adhk_adj_val > 0 ? 'lightgreen' : adhk_adj_val < 0 ? 'lemonchiffon' : 'transparent');


            // calcutate yty
            let str_y1 = (year - 1) + 'Q' + quarter;
            let input_adhk_y1 = $('#' + str_y1 + '_adhk_adj');
            if (input_adhk_y1.length > 0 && input_adhk_y1.is('input')) {
                adhk_y1_adj = parseNumberIndonesian(input_adhk_y1.val());
            } else {
                adhk_y1_adj = parseNumberIndonesian(adhk_cell.data('adhk_y1_adj'));
            }
            let input_adhb_y1 = $('#' + str_y1 + '_adhb_adj');
            if (input_adhb_y1.length > 0 && input_adhb_y1.is('input')) {
                adhb_y1_adj = parseNumberIndonesian(input_adhb_y1.val());
            } else {
                adhb_y1_adj = parseNumberIndonesian(adhb_cell.data('adhb_y1_adj'));
            }
            yty_adj = adhk_y1 + adhk_y1_adj != 0 ? ((adhk + adhk_adj_val) - (adhk_y1 + adhk_y1_adj)) / (adhk_y1 + adhk_y1_adj) * 100 : "";
            yty_adj_input.val(formatNumber(yty_adj));
            yty_adj_input.css('background', yty_adj > 0 ? 'lightgreen' : yty_adj < 0 ? 'lemonchiffon' : 'transparent');
            yty_adj_input.closest('td').css('background', yty_adj > 0 ? 'lightgreen' : yty_adj < 0 ? 'lemonchiffon' : 'transparent');


            // calculate qtq
            let str_q1 = quarter === 1 ? (year - 1) + 'Q4' : year + 'Q' + (quarter - 1);
            let input_adhk_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhk_q1.length > 0 && input_adhk_q1.is('input')) {
                adhk_q1_adj = parseNumberIndonesian(input_adhk_q1.val());
            } else {
                adhk_q1_adj = parseNumberIndonesian(adhk_cell.data('adhk_q1_adj'));
            }
            let input_adhb_q1 = $('#' + str_q1 + '_adhk_adj');
            if (input_adhb_q1.length > 0 && input_adhk_q1.is('input')) {
                adhb_q1_adj = parseNumberIndonesian(input_adhb_q1.val());
            } else {
                adhb_q1_adj = parseNumberIndonesian(adhb_cell.data('adhb_q1_adj'));
            }
            qtq_adj = adhk_q1 + adhk_q1_adj != 0 ? ((adhk + adhk_adj_val) - (adhk_q1 + adhk_q1_adj)) / (adhk_q1 + adhk_q1_adj) * 100 : "";
            qtq_adj_input.val(formatNumber(qtq_adj));
            qtq_adj_input.css('background', qtq_adj > 0 ? 'lightgreen' : qtq_adj < 0 ? 'lemonchiffon' : 'transparent');
            qtq_adj_input.closest('td').css('background', qtq_adj > 0 ? 'lightgreen' : qtq_adj < 0 ? 'lemonchiffon' : 'transparent');


            if (implisit_toggle) {
                // implisit Berubah (adhb tetap) cari nilai implisit qtq
                adhb_adj_val = parseNumberIndonesian(adhb_adj_input.val());
                laju_implisit_yty_adj = adhk + adhk_adj_val != 0 && adhk_y1 + adhk_y1_adj != 0 ?
                    ((((adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100) /
                        ((adhb_y1 + adhb_y1_adj) / (adhk_y1 + adhk_y1_adj) * 100) * 100) - 100) : "";
                laju_implisit_yty_adj_cell.text(formatNumber(laju_implisit_yty_adj));
                laju_implisit_yty_adj_cell.css('background', laju_implisit_yty_adj > 0 ? 'lightgreen' : laju_implisit_yty_adj < 0 ?
                    'lemonchiffon' : 'transparent');
            } else {
                // Implisit tetap (adhb berubah) cari nilai adhb
                laju_implisit_yty_adj = parseNumberIndonesian(laju_implisit_yty_adj_cell.text()) || 0;
                adhb_adj_val = ((adhk + adhk_adj_val) * (adhb_y1 + adhb_y1_adj) * (laju_implisit_yty_adj + 100) /
                    (100 * (adhk_y1 + adhk_y1_adj))) - adhb;
                adhb_adj_cell.text(formatNumber(adhb_adj_val + adhb))
                adhb_adj_input.val(formatNumber(adhb_adj_val))
                adhb_adj_cell.css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ? 'lemonchiffon' : 'transparent');
                adhb_adj_input.css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ? 'lemonchiffon' : 'transparent');
                adhb_adj_input.closest('td').css('background', adhb_adj_val > 0 ? 'lightgreen' : adhb_adj_val < 0 ?
                    'lemonchiffon' : 'transparent');
            }
            indeks_implisit_adj = (adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100;
            indeks_implisit_adj_cell.text(formatNumber(indeks_implisit_adj));
            indeks_implisit_adj_cell.css('background', indeks_implisit_adj > 0 ? 'lightgreen' : indeks_implisit_adj < 0 ?
                'lemonchiffon' : 'transparent');

            laju_implisit_qtq_adj = adhk + adhk_adj_val != 0 && adhk_q1 + adhk_q1_adj != 0 ?
                ((((adhb + adhb_adj_val) / (adhk + adhk_adj_val) * 100) /
                    ((adhb_q1 + adhb_q1_adj) / (adhk_q1 + adhk_q1_adj) * 100) * 100) - 100) : "";
            laju_implisit_qtq_adj_cell.text(formatNumber(laju_implisit_qtq_adj));
            laju_implisit_qtq_adj_cell.css('background', laju_implisit_qtq_adj > 0 ? 'lightgreen' : laju_implisit_qtq_adj < 0 ?
                'lemonchiffon' : 'transparent');

            formatedtext = formatText(input.val());
            $(this).val(formatedtext);
            input.css('background', parseNumberIndonesian(input.val()) > 0 ? 'lightgreen' : parseNumberIndonesian(input.val()) < 0 ?
                'lemonchiffon' : 'transparent');
            input.closest('td').css('background', parseNumberIndonesian(input.val()) > 0 ? 'lightgreen' : parseNumberIndonesian(input.val()) < 0 ?
                'lemonchiffon' : 'transparent');

        })

        $('#periodeModal').on('hidden.bs.modal', function() {
            // Pindahkan fokus ke tombol yang relevan di luar modal
            $('body').find('.modal-backdrop').remove();
            // $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').focus();
        });
        $('#periodeModal').on('shown.bs.modal', function() {
            // Pastikan z-index modal lebih tinggi dari backdrop
            $(this).css('z-index', 1060);
            $('.modal-backdrop').css('z-index', 1050);
        });

        $('#implisit_toggle').change(function() {
            implisit_toggle = $(this).prop('checked');
        })

        function formatNumber(num) {
            // Pastikan num adalah number, jika tidak, return "0,00"
            const number = parseFloat(num);
            if (isNaN(number)) return "0,00";

            // Pakai toLocaleString untuk pemisah ribuan, lalu pastikan 2 desimal
            return number.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function parseNumberIndonesian(numStr) {
            // Jika input sudah number, langsung return
            if (typeof numStr === 'number') return numStr;
            // console.log(numStr)
            // Hapus semua titik (ribuan), ganti koma (desimal) dengan titik
            const cleaned = numStr.toString()
                .replace(/\./g, '') // Hapus semua .
                .replace(/,/g, '.'); // Ganti , dengan .

            return parseFloat(cleaned) || 0; // Konversi ke float
        }

        function formatText(input) {
            // 1. Hapus semua karakter kecuali angka dan koma
            value = input.replace(/[^\d,-]/g, '');

            // 2. Pisahkan bagian bulat dan desimal
            let parts = value.split(',');
            let bagianBulat = parts[0];
            let bagianDesimal = parts.length > 1 ? ',' + parts[1] : '';

            // 3. Tambahkan titik sebagai pemisah ribuan (jika > 3 digit)
            bagianBulat = bagianBulat.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // 4. Gabungkan kembali
            let formattedValue = bagianBulat + bagianDesimal;

            // 5. Update nilai input
            return formattedValue
        }

        function getHeaderColor(periode) {
            triwulan = periode.substring(5, 6)
            switch (triwulan) {
                case "1":
                    color_header = "bg-tw1"
                    break;
                case "2":
                    color_header = "bg-tw2"
                    break;
                case "3":
                    color_header = "bg-tw3"
                    break;
                case "4":
                    color_header = "bg-tw4"
                    break;
                default:
                    color_header = ""
            }
            return color_header;
        }


        function exportExcelWithCustomStyles() {
            // 1. Clone tabel
            const table = document.getElementById('tabel-output').cloneNode(true);

            // 2. Handle input values
            $(table).find('td input').each(function() {
                const value = $(this).val();
                $(this).parent().html(value);
            });

            // 3. Tambahkan referensi cell
            $(table).find('td, th').each(function() {
                const $cell = $(this);
                const row = $cell.parent().index();
                const col = $cell.index();
                $cell.attr('data-xls-ref', XLSX.utils.encode_cell({
                    r: row,
                    c: col
                }));
            });

            // 4. Ekspor ke workbook
            const workbook = XLSX.utils.table_to_book(table, {
                raw: true,
                display: false,
                sheetRows: 0,
                cellStyles: true
            });

            // 5. Apply styling
            const ws = workbook.Sheets[workbook.SheetNames[0]];
            ws['!cols'] = []; // Untuk set column width
            ws['!rows'] = []; // Untuk set row height
            const range = XLSX.utils.decode_range(ws['!ref']);

            for (let R = range.s.r; R <= range.e.r; ++R) {
                ws['!rows'][R] = {
                    hpx: 20,
                    alignment: {
                        vertical: 'center',
                        horizontal: 'center'
                    }
                };

                for (let C = range.s.c; C <= range.e.c; ++C) {
                    const cell_ref = XLSX.utils.encode_cell({
                        c: C,
                        r: R
                    });
                    if (!ws[cell_ref]) continue;

                    // Inisialisasi style
                    ws[cell_ref].t = 's'; // Tipe data: string
                    ws[cell_ref].s = {
                        alignment: {
                            horizontal: 'center',
                            vertical: 'center'
                        }
                    };

                    const cell_dom = $(table).find(`[data-xls-ref="${cell_ref}"]`)[0];
                    if (!cell_dom) continue;

                    // Override alignment jika ada spesifik
                    const align = $(cell_dom).css('text-align') ||
                        ($(cell_dom).hasClass('text-left') ? 'left' :
                            $(cell_dom).hasClass('text-right') ? 'right' : null);

                    if (align) {
                        ws[cell_ref].s.alignment.horizontal = align;
                    }

                    // Handle warna (dari solusi sebelumnya)
                    applyCellColors(ws, cell_ref, cell_dom);
                }
            }

            // 6. Export
            XLSX.writeFile(workbook, "Rekomens_output.xlsx", {
                bookType: 'xlsx',
                type: 'array',
                cellStyles: true
            });
        }

        // Helper function untuk warna
        function applyCellColors(ws, cell_ref, cell_dom) {
            const colorMap = {
                'bg-tw1': 'FFEE8E',
                'bg-tw2': 'A6FF98',
                'bg-tw3': 'A3C6FA',
                'bg-tw4': 'FF8E8E',
                'lightgreen': '90EE90',
                'lemonchiffon': 'fffacd'
            };

            // Cek inline style
            const inlineBg = $(cell_dom).css('background-color');
            if (inlineBg && inlineBg !== 'rgba(0, 0, 0, 0)') {
                ws[cell_ref].s.fill = {
                    patternType: 'solid',
                    fgColor: {
                        rgb: rgbToHex(inlineBg)
                    }
                };
            }

            // Cek class
            const classes = $(cell_dom).attr('class') || '';
            Object.entries(colorMap).forEach(([cls, hex]) => {
                if (classes.includes(cls)) {
                    ws[cell_ref].s.fill = {
                        patternType: 'solid',
                        fgColor: {
                            rgb: hex
                        }
                    };
                }
            });
        }

        function rgbToHex(rgb) {
            // Konversi rgb(r,g,b) ke hex
            const match = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
            if (!match) return null;

            const r = parseInt(match[1]).toString(16).padStart(2, '0');
            const g = parseInt(match[2]).toString(16).padStart(2, '0');
            const b = parseInt(match[3]).toString(16).padStart(2, '0');

            return r.toUpperCase() + g.toUpperCase() + b.toUpperCase();
        }
    </script>
@endsection
