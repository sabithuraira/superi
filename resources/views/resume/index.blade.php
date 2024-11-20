@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
    <li class="breadcrumb-item">Tabel Resume</li>
</ul>
@endsection

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-auto mr-auto aligns">
                        <h2>Tabel Resume PDRB Pengeluaran Triwulanan</h2>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success btn-sm" type="button" onclick="exportToExcel()">Export Excel</button>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-12">
                        <div class="row mb-2">
                            <div class="col-12 mb-2">
                                <label for="tabel_filter" class="label">Tabel</label>
                                <select name="tabel_filter" id="tabel_filter" class="form-control">
                                    <option value="Tabel 2.1">Tabel 2.1. PDRB ADHB</option>
                                    <option value="Tabel 2.2">Tabel 2.2. PDRB ADHK</option>
                                    <option value="Tabel 2.3">Tabel 2.3. Distribusi Terhadap Provinsi</option>
                                    <option value="Tabel 2.4">Tabel 2.4. Distribusi Komponen Terhadap PDRB ADHB</option>
                                    <option value="Tabel 2.5">Tabel 2.5. Distribusi Komponen Terhadap PDRB ADHK</option>
                                    <option value="Tabel 2.6">Tabel 2.6. Indeks Implisit</option>
                                    <option value="Tabel 2.7">Tabel 2.7. Indeks Implisit Kumulatif</option>
                                    <option value="Tabel 2.8">Tabel 2.8. Pertumbuhan PDRB (Q-TO-Q)</option>
                                    <option value="Tabel 2.9">Tabel 2.9. Pertumbuhan PDRB (Y-ON-Y)</option>
                                    <option value="Tabel 2.10">Tabel 2.10. Pertumbuhan PDRB (C-TO-C)</option>
                                    <option value="Tabel 2.11">Tabel 2.11. Pertumbuhan Indeks Implisit PDRB (Q-TO-Q)</option>
                                    <option value="Tabel 2.12">Tabel 2.12. Pertumbuhan Indeks Implisit PDRB (Y-ON-Y)</option>
                                    <option value="Tabel 2.13">Tabel 2.13. Pertumbuhan Indeks Implisit PDRB (C-TO-C)</option>
                                    <option value="Tabel 2.14">Tabel 2.14. Sumber Pertumbuhan Kabupaten/Kota Terhadap PDRB Provinsi (Q-TO-Q)</option>
                                    <option value="Tabel 2.15">Tabel 2.15. Sumber Pertumbuhan Kabupaten/Kota Terhadap PDRB Provinsi (Y-ON-Y)</option>
                                    <option value="Tabel 2.16">Tabel 2.16. Sumber Pertumbuhan Kabupaten/Kota Terhadap PDRB Provinsi (C-TO-C)</option>
                                    <option value="Tabel 2.17">Tabel 2.17. Sumber Pertumbuhan Komponen Terhadap PDRB Kabupaten/Kota/Provinsi (Q-TO-Q)</option>
                                    <option value="Tabel 2.18">Tabel 2.18. Sumber Pertumbuhan Komponen Terhadap PDRB Kabupaten/Kota/Provinsi (Y-ON-Y)</option>
                                    <option value="Tabel 2.19">Tabel 2.19. Sumber Pertumbuhan Komponen Terhadap PDRB Kabupaten/Kota/Provinsi (C-TO-C)</option>
                                </select>
                            </div>
                            <div class="col-4 mb-2">
                                <label>Komponen</label>
                                <select name="komponen_filter" id="komponen_filter" class="multiselect multiselect-custom" multiple="multiple">
                                    <option value="c_pdrb" selected>Semua Komponen PDRB</option>
                                    @foreach($komponen as $komponen_item)
                                    <option value="{{ 'c_' . str_replace('.', '', $komponen_item->no_komponen) }}" selected>{{ $komponen_item->no_komponen . ' ' . $komponen_item->nama_komponen }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 mb-2">
                                <label>Kab/Kot</label>
                                <select name="kab_filter" id="kab_filter" class="multiselect multiselect-custom" multiple="multiple">
                                    <option value="1600" selected>Prov. Sumatera Selatan</option>
                                    <option value="1601">Kab. Ogan Komering Ulu</option>
                                    <option value="1602">Kab. Ogan Komering Ilir</option>
                                    <option value="1603">Kab. Muara Enim</option>
                                    <option value="1604">Kab. Lahat</option>
                                    <option value="1605">Kab. Musi Rawas</option>
                                    <option value="1606">Kab. Musi Banyuasin</option>
                                    <option value="1607">Kab. Banyuasin</option>
                                    <option value="1608">Kab. Ogan Komering Ulu Seletan</option>
                                    <option value="1609">Kab. Ogan Komering Ulu Timur</option>
                                    <option value="1610">Kab. Ogan Ilir</option>
                                    <option value="1611">Kab. Empat Lawang</option>
                                    <option value="1612">Kab. Penukal Abab Lematang Ilir</option>
                                    <option value="1613">Kab. Musi Rawas Utara</option>
                                    <option value="1671">Kota Palembang</option>
                                    <option value="1672">Kota Prabumulih</option>
                                    <option value="1673">Kota Pagar Alam</option>
                                    <option value="1674">Kota Lubuklinggau</option>
                                </select>
                            </div>
                            <div class="col-4 mb-2">
                                <label>Periode</label>
                                <select name="periode_filter" id="periode_filter" class="multiselect multiselect-custom" multiple="multiple">
                                    @foreach($periode as $periode_item)
                                    <option value="{{ $periode_item->periode }}" selected>{{ $periode_item->periode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 align-self-end">
                                <button onclick="cari()" class="btn btn-primary btn-block">Cari</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            <p id="table_title" class="mt-2"></p>
                            <div id="table-container">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr id="table_column"></tr>
                                    </thead>
                                    <tbody id="table_data"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $("#komponen_filter, #kab_filter, #periode_filter").multiselect({
        maxHeight: 300
    });
    function cari() {
        var url = "{{ url("/tabel/resume") }}";
        var tabel = document.getElementById("tabel_filter").value;
        var komponen = Array.from(document.getElementById("komponen_filter").selectedOptions).map(({ value }) => value);
        var kd_kab = Array.from(document.getElementById("kab_filter").selectedOptions).map(({ value }) => value);
        var periode = Array.from(document.getElementById("periode_filter").selectedOptions).map(({ value }) => value);
        fetch(url, {
            method: "post",
            body: JSON.stringify({
                tabel: tabel,
                komponen: komponen.toString(),
                kd_kab: kd_kab.toString(),
                periode: periode.toString()
            }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(json => {
            document.getElementById("table_title").innerHTML = document.querySelector(`option[value="${tabel}"]`).text;
            document.getElementById("table_column").innerHTML = "";
            document.getElementById("table_data").innerHTML = "";
            for (const col in json[0]) {
                var table_column = document.getElementById("table_column");
                var cell = table_column.insertCell(-1);
                cell.innerHTML = col == "kd_kab" ? "Kabupaten/Kota" : col;
            }
            for (const i in json) {
                var table_data = document.getElementById("table_data");
                var row = table_data.insertRow(-1);
                for (const j in json[i]) {
                    var cell = row.insertCell(-1);
                    cell.innerHTML = json[i][j];
                }
            }
        });
    }
    function exportToExcel() {
        var location = 'data:application/vnd.ms-excel;base64,';
        var excelTemplate = '<html> ' +
            '<head> ' +
            '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/> ' +
            '</head> ' +
            '<body> ' +
            document.getElementById("table-container").innerHTML +
            '</body> ' +
            '</html>'
        window.location.href = location + window.btoa(excelTemplate);
    }
</script>
@endsection