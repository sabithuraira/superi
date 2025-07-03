@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Rekonsiliasi</li>
    </ul>
@endsection

@section('css')
    <style>
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
                                        <label for="periode_filter" class="text-white">Periode</label>
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal" data-toggle="modal"
                                            data-target="#periodeModal">Pilih Periode</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        <label for="simpan" class="text-white">Simpan</label>
                                        <button class="btn btn-success w-100" type="button" id="simpan" @click="saveDatas">Simpan</button>
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

@section('css')
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="@csrf">
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>

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

                            periode_header += '<th colspan=16 class="fixed-row-1 ' + color_header + '">' + periode + '</th>';
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
                            <th colspan = 2 class="fixed-row-2 ` + color_header + `">Laju Implisit</th>
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
                            <th class="fixed-row-3 ` + color_header + `">PDRB+Adj</th>`;
                        })
                        str_header = str_header += `</tr>`
                        $('#thead-row').append(str_header);


                        // Isi data baris
                        data.data.forEach(row => {
                            let rowHtml = '<tr class ="text-right">';
                            rowHtml +=
                                `<td class = "text-left fixed-col bg-white">[${row['kode_kab']??''}] ${row['nama_kab'] ?? ''}</td>`

                            self.periode_filter.forEach(periode => {
                                const adhb = row[periode + '_adhb'] !== null && row[periode + '_adhb'] !== undefined ?
                                    parseFloat(row[periode + '_adhb']) : "";
                                const adhb_adj = row[periode + '_adhb_adj'] !== null && row[periode + '_adhb_adj'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_adj']) : 0;
                                const adhb_q1 = row[periode + '_adhb_q1'] !== null && row[periode + '_adhb_q1'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_q1']) : "";
                                const adhb_y1 = row[periode + '_adhb_y1'] !== null && row[periode + '_adhb_y1'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_y1']) : "";
                                const adhb_c = row[periode + '_adhb_c'] !== null && row[periode + '_adhb_c'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_c']) : "";
                                const adhb_c1 = row[periode + '_adhb_c1'] !== null && row[periode + '_adhb_c1'] !==
                                    undefined ? parseFloat(row[periode + '_adhb_c1']) : "";
                                const adhk = row[periode + '_adhk'] !== null && row[periode + '_adhk'] !==
                                    undefined ? parseFloat(row[periode + '_adhk']) : "";
                                const adhk_adj = row[periode + '_adhk_adj'] !== null && row[periode + '_adhk_adj'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_adj']) : 0;
                                const adhk_y1 = row[periode + '_adhk_y1'] !== null && row[periode + '_adhk_y1'] !==
                                    undefined ? parseFloat(row[periode + '_adhk_y1']) : "";


                                const qtq = adhb != "" && adhb_q1 != "" ? (adhb - adhb_q1) / adhb_q1 * 100 : "";
                                const qtq_adj = adhb != "" && adhb_q1 != "" ?
                                    (adhb + adhb_adj - adhb_q1) / adhb_q1 * 100 : "";

                                const yty = adhb != "" && adhb_y1 != "" ? (adhb - adhb_y1) / adhb_y1 * 100 : "";
                                const yty_adj = adhb != "" && adhb_y1 != "" ?
                                    (adhb + adhb_adj - adhb_y1) / adhb_y1 * 100 : "";

                                const ctc = adhb_c != "" && adhb_c1 != "" ? (adhb_c - adhb_c1) / adhb_c1 * 100 : "";
                                const ctc_adj = adhb_c != "" && adhb_c1 != "" ?
                                    (adhb_c + adhb_adj - adhb_c1) / adhb_c1 * 100 : "";

                                const implisit = adhb != "" && adhk != "" ? (adhb / adhk * 100) : "";
                                const implisit_adj = adhb != "" && adhk != "" ?
                                    ((adhb + adhb_adj) / (adhk + adhk_adj) * 100) : "";

                                const laju_implisit = adhb != "" && adhk != "" && adhb_y1 != "" && adhk_y1 != "" ?
                                    ((adhb / adhk * 100) / (adhb_y1 / adhk_y1 * 100) * 100 - 100) : "";
                                const laju_implisit_adj = adhb != "" && adhk != "" && adhb_y1 != "" && adhk_y1 != "" ?
                                    (((adhb + adhb_adj) / (adhk + adhk_adj) * 100) / (adhb_y1 / adhk_y1 * 100) *
                                        100) - 100 : "";

                                rowHtml += `
                                    <td data-adhb="${adhb}"
                                        data-adhb_adj="${adhb_adj}"
                                        data-adhb_q1="${adhb_q1}"
                                        data-adhb_y1="${adhb_y1}"
                                        data-adhb_c="${adhb_c}"
                                        data-adhb_c1="${adhb_c1}" >
                                        ${formatNumber(adhb)}
                                    </td>
                                    <td>
                                        <input id="${periode + '_adhb_adj'}"
                                        value="${formatNumber(adhb_adj)}"
                                        data-id="${row[periode + '_adhb_id']}"
                                        class="text_edit_adhb"
                                        type="text" inputmode="decimal"
                                        pattern="^\d+(\.\d{0,2})?$" >
                                    </td>
                                    <td>${formatNumber(adhb + adhb_adj)}</td>
                                    <td data-adhk="${adhk}"
                                        data-adhk_adj="${adhk_adj}"
                                        data-adhk_y1="${adhk_y1}" >
                                        ${formatNumber(adhk)}
                                    </td>
                                    <td>
                                        <input id="${periode + '_adhk_adj'}"
                                        value="${formatNumber(adhk_adj)}"
                                        data-id="${row[periode + '_adhk_id']}"
                                        class="text_edit_adhk" type="text"
                                        inputmode="decimal" pattern="^\d+(\.\d{0,2})?$" >
                                    </td>
                                    <td>${formatNumber(adhk + adhk_adj)}</td>
                                    <td>${formatNumber(qtq)}</td>
                                    <td>${formatNumber(qtq_adj)}</td>
                                    <td>${formatNumber(yty)}</td>
                                    <td>${formatNumber(yty_adj)}</td>
                                    <td>${formatNumber(ctc)}</td>
                                    <td>${formatNumber(ctc_adj)}</td>
                                    <td>${formatNumber(implisit)}</td>
                                    <td>${formatNumber(implisit_adj)}</td>
                                    <td>${formatNumber(laju_implisit)}</td>
                                    <td>${formatNumber(laju_implisit_adj)}</td>
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
            const adhb_adj = parseNumberIndonesian(input.val()) || 0;
            const adhk_adj = parseNumberIndonesian(input.closest('td').nextAll().eq(2).find('input').val() || 0);

            const adhb_cell = input.closest('td').prev('td');
            const adhb_adj_cell = input.closest('td').nextAll('td').eq(0);
            const adhk_cell = input.closest('td').nextAll('td').eq(1);
            const qtq_adj_cell = input.closest('td').nextAll('td').eq(5);
            const yty_adj_cell = input.closest('td').nextAll('td').eq(8);
            const ctc_adj_cell = input.closest('td').nextAll('td').eq(9);
            const implisit_adj_cell = input.closest('td').nextAll('td').eq(11);
            const laju_implisit_adj_cell = input.closest('td').nextAll('td').eq(13);

            const adhb = parseNumberIndonesian(adhb_cell.text()) || 0;
            const adhk = parseNumberIndonesian(adhk_cell.text()) || 0;
            const adhb_q1 = parseNumberIndonesian(adhb_cell.data('adhb_q1'));
            const adhb_y1 = parseNumberIndonesian(adhb_cell.data('adhb_y1'));
            const adhb_c = parseNumberIndonesian(adhb_cell.data('adhb_c'));
            const adhb_c1 = parseNumberIndonesian(adhb_cell.data('adhb_c1'));
            const adhk_y1 = parseNumberIndonesian(adhk_cell.data('adhk_y1'));

            adhb_adj_cell.text(formatNumber(adhb + adhb_adj));
            qtq_adj_cell.text(adhb_q1 != "" && adhb_q1 != 0 ? formatNumber((adhb + adhb_adj - adhb_q1) / adhb_q1 * 100) : "");
            yty_adj_cell.text(adhb_y1 != "" && adhb_y1 != 0 ? formatNumber((adhb + adhb_adj - adhb_y1) / adhb_y1 * 100) : "");
            ctc_adj_cell.text(adhb_c1 != "" && adhb_c1 != 0 ? formatNumber((adhb_c + adhb_adj - adhb_c1) / adhb_c1 * 100) : "");
            implisit_adj_cell.text(adhk != "" && adhk != 0 ? formatNumber((adhb + adhb_adj) / (adhk + adhk_adj) * 100) : "");
            laju_implisit_adj_cell.text(adhk != "" && adhk != 0 && adhk_y1 != "" && adhk_y1 != 0 ?
                formatNumber((((adhb + adhb_adj) / (adhk + adhk_adj) * 100) / (adhb_y1 / adhk_y1 * 100) * 100) - 100) : "");

            formatedtext = formatText(input.val());
            $(this).val(formatedtext);

        });

        $(document).on('input', '.text_edit_adhk', function() {
            const input = $(this);
            const adhb_adj = parseNumberIndonesian(input.closest('td').prevAll().eq(2).find('input').val() || 0);
            const adhk_adj = parseNumberIndonesian(input.val()) || 0;

            const adhb_cell = input.closest('td').prevAll().eq(3);
            const adhb_adj_cell = input.closest('td').prevAll('td').eq(0);
            const adhk_cell = input.closest('td').prevAll('td').eq(0);
            const adhk_adj_cell = input.closest('td').nextAll('td').eq(0);
            const qtq_adj_cell = input.closest('td').nextAll('td').eq(2);
            const yty_adj_cell = input.closest('td').nextAll('td').eq(4);
            const ctc_adj_cell = input.closest('td').nextAll('td').eq(6);
            const implisit_adj_cell = input.closest('td').nextAll('td').eq(8);
            const laju_implisit_adj_cell = input.closest('td').nextAll('td').eq(10);

            const adhb = parseNumberIndonesian(adhb_cell.text()) || 0;
            const adhk = parseNumberIndonesian(adhk_cell.text()) || 0;
            const adhb_y1 = parseNumberIndonesian(adhb_cell.data('adhb_y1'));
            const adhk_y1 = parseNumberIndonesian(adhk_cell.data('adhk_y1'));

            adhk_adj_cell.text(formatNumber(adhk + adhk_adj));
            implisit_adj_cell.text(adhk != "" && adhk != 0 ? formatNumber((adhb + adhb_adj) / (adhk + adhk_adj) * 100) : "");
            laju_implisit_adj_cell.text(adhk != "" && adhk != 0 && adhk_y1 != "" && adhk_y1 != 0 ?
                formatNumber((((adhb + adhb_adj) / (adhk + adhk_adj) * 100) / (adhb_y1 / adhk_y1 * 100) * 100) - 100) : "");

            formatedtext = formatText(input.val());
            $(this).val(formatedtext);
        });

        $('#periodeModal').on('hidden.bs.modal', function() {
            // Pindahkan fokus ke tombol yang relevan di luar modal
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').focus();
        });

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
    </script>
@endsection
