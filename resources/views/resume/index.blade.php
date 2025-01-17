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
                        <button class="btn btn-success btn-sm" type="button" onclick="exportAllToExcel()">Export All Excel</button>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-12">
                        <div class="row mb-2">
                            <div class="col-6 mb-2">
                                <label for="tabel_filter" class="label">Tabel</label>
                                <select name="tabel_filter" id="tabel_filter" class="form-control" onchange="lihat()">
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
                            <div class="col-6 mb-2">
                                <label for="komponen_filter" class="label">Komponen</label>
                                <select name="komponen_filter" id="komponen_filter" class="form-control" onchange="lihat()">
                                    <option value="c_pdrb" selected>Semua Komponen PDRB</option>
                                    @foreach($komponen as $komponen_item)
                                    <option value="{{ $komponen_item['select_id'] }}">{{ $komponen_item['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-2">
                                <button class="btn btn-primary w-100" type="button" href="#kabkot_modal" data-toggle="modal" data-target="#kabkot_modal" onclick="modal_clicked_kab()">Pilih Kabupaten/Kota</button>
                            </div>
                            <div class="col-6 mb-2">
                                <button class="btn btn-primary w-100" type="button" href="#periode_modal" data-toggle="modal" data-target="#periode_modal" onclick="modal_clicked_periode()">Pilih Periode</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h5 id="table_title" class="text-center mt-2"></h5>
                        <div class="table-responsive">
                            <div id="table-container">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr id="table_column" class="text-center"></tr>
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
<div class="modal fade" id="kabkot_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="kabkot_modal_label">Pilih Kabupaten/Kota</h4>
            </div>
            <div class="modal-body">
                @foreach(config("app.wilayah") as $kd_kab => $nm_kab)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ '16' . $kd_kab }}" name="kabkot_filter_{{ '16' . $kd_kab }}" id="kabkot_filter_{{ '16' . $kd_kab }}" checked>
                    <label class="form-check-label" for="kabkot_filter_{{ '16' . $kd_kab }}">{{ $nm_kab }}</label>
                </div>
                @endforeach
                <br>
                <button type="button" class="btn btn-primary btn-sm" onclick="check_all_kab(true)">Pilih Semua</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="check_all_kab(false)">Kosongkan Semua</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="modal_cancel_kab()">Cancel</button>
                <button type="button" class="btn btn-success" data-dismiss="modal" onclick="lihat()">OK</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="periode_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="periode_modal_label">Pilih Periode</h4>
            </div>
            <div class="modal-body">
                @foreach($periode as $periode_item)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $periode_item->periode }}" name="periode_filter_{{ $periode_item->periode }}" id="periode_filter_{{ $periode_item->periode }}" checked>
                        <label class="form-check-label" for="periode_filter_{{ $periode_item->periode }}">{{ $periode_item->periode }}</label>
                    </div>
                @endforeach
                <br>
                <button type="button" class="btn btn-primary btn-sm" onclick="check_all_periode(true)">Pilih Semua</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="check_all_periode(false)">Kosongkan Semua</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="modal_cancel_periode()">Cancel</button>
                <button type="button" class="btn btn-success" data-dismiss="modal" onclick="lihat()">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var modal_history_kab = {};
    var modal_history_periode = {};

    var kab = [];
    @foreach(config("app.wilayah") as $kd_kab => $nm_kab)
    kab.push("16{{ $kd_kab }}");
    @endforeach

    function modal_clicked_kab() {
        modal_history_kab = {};
        kab.forEach(e => {
            modal_history_kab[e] = document.getElementById("kabkot_filter_" + e).checked;
        });
    }
    function modal_clicked_periode() {
        modal_history_periode = {};
        @foreach($periode as $periode_item)
        modal_history_periode["{{ $periode_item->periode }}"] = document.getElementById("periode_filter_{{ $periode_item->periode }}").checked;
        @endforeach
    }
    function modal_cancel_kab() {
        kab.forEach(e => {
            document.getElementById("kabkot_filter_" + e).checked = modal_history_kab[e];
        });
        modal_history_kab = {};
    }
    function modal_cancel_periode() {
        @foreach($periode as $periode_item)
        document.getElementById("periode_filter_{{ $periode_item->periode }}").checked = modal_history_periode["{{ $periode_item->periode }}"];
        @endforeach
        modal_history_periode = {};
    }
    function check_all_kab(checked) {
        kab.forEach(e => {
            document.getElementById("kabkot_filter_" + e).checked = checked;
        });
    }
    function check_all_periode(checked) {
        @foreach($periode as $periode_item)
        document.getElementById("periode_filter_{{ $periode_item->periode }}").checked = checked;
        @endforeach
    }
    function lihat() {
        var url = "{{ url("/tabel/resume") }}";

        var tabel = document.getElementById("tabel_filter").value;
        var komponen = document.getElementById("komponen_filter").value;

        var kd_kab = [];
        kab.forEach(e => {
            kab_check = document.getElementById("kabkot_filter_" + e);
            if (kab_check.checked) kd_kab.push(e);
        });

        var periode = [];
        @foreach($periode as $periode_item)
        periode_check = document.getElementById("periode_filter_{{ $periode_item->periode }}");
        if (periode_check.checked) periode.push("{{ $periode_item->periode }}");
        @endforeach

        var kab_desc = {};
        @foreach(config("app.wilayah") as $kd_kab => $nm_kab)
        kab_desc["16{{ $kd_kab }}"] = "{{ $nm_kab }}";
        @endforeach

        fetch(url, {
            method: "post",
            body: JSON.stringify({
                tabel: tabel,
                komponen: komponen,
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
            for (const i in json.columns) {
                var table_column = document.getElementById("table_column");
                var cell = table_column.insertCell(-1);
                cell.outerHTML = "<th>" + (json.columns[i] == "kd_kab" ? "Kabupaten/Kota" : json.columns[i]) + "</th>";
            }
            for (const i in json.pdrb) {
                var table_data = document.getElementById("table_data");
                var row = table_data.insertRow(-1);
                for (const j in json.pdrb[i]) {
                    var cell = row.insertCell(-1);
                    if (j == 0) cell.innerHTML = kab_desc[json.pdrb[i][j]];
                    else cell.innerHTML = json.pdrb[i][j];
                }
            }
        });
    }
    lihat();
    function exportToExcel() {
        var url = "{{ url("/tabel/resume/export") }}";

        var judul = document.querySelector(`#tabel_filter option[value='${document.getElementById("tabel_filter").value}']`).text;

        var tabel = document.getElementById("tabel_filter").value;
        var tabel_desc = document.getElementById("tabel_filter").innerHTML;
        var komponen = document.getElementById("komponen_filter").value;

        var kd_kab = [];
        kab.forEach(e => {
            kab_check = document.getElementById("kabkot_filter_" + e);
            if (kab_check.checked) kd_kab.push(e);
        });

        var periode = [];
        @foreach($periode as $periode_item)
        periode_check = document.getElementById("periode_filter_{{ $periode_item->periode }}");
        if (periode_check.checked) periode.push("{{ $periode_item->periode }}");
        @endforeach

        fetch(url, {
            method: "post",
            body: JSON.stringify({
                judul:judul,
                tabel: tabel,
                komponen: komponen,
                kd_kab: kd_kab.toString(),
                periode: periode.toString()
            }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": "{{ csrf_token() }}"
            }
        })
        .then(res => res.blob())
        .then(blob => URL.createObjectURL(blob))
        .then(href => {
            Object.assign(document.createElement('a'), {
                href,
                download: `Resume ${judul}.xlsx`,
            }).click();
        });
    }
    function exportAllToExcel() {
        var url = "{{ url("/tabel/resume/exportall") }}";

        var judul = document.querySelector(`#tabel_filter option[value='${document.getElementById("tabel_filter").value}']`).text;

        var tabel = document.getElementById("tabel_filter").value;
        var tabel_desc = document.getElementById("tabel_filter").innerHTML;

        var komponen = [];
        @foreach($komponen as $komponen_item)
        komponen.push("{{ $komponen_item['select_id'] }}");
        @endforeach

        var kd_kab = [];
        kab.forEach(e => {
            kab_check = document.getElementById("kabkot_filter_" + e);
            if (kab_check.checked) kd_kab.push(e);
        });

        var periode = [];
        @foreach($periode as $periode_item)
        periode_check = document.getElementById("periode_filter_{{ $periode_item->periode }}");
        if (periode_check.checked) periode.push("{{ $periode_item->periode }}");
        @endforeach

        fetch(url, {
            method: "post",
            body: JSON.stringify({
                judul:judul,
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
        .then(res => res.blob())
        .then(blob => URL.createObjectURL(blob))
        .then(href => {
            Object.assign(document.createElement('a'), {
                href,
                download: `All Resume ${judul}.xlsx`,
            }).click();
        });
    }
</script>
@endsection
