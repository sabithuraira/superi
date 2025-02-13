@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
    <li class="breadcrumb-item">Arah Revisi Total</li>
</ul>
@endsection

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-auto mr-auto aligns">
                        <h2>Arah Revisi Total</h2>
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
                            <div class="col-4 mb-2">
                                <label for="tabel_filter" class="label">Tabel</label>
                                <select name="tabel_filter" id="tabel_filter" class="form-control" onchange="lihat()">
                                    <option value="Tabel 2.1">Tabel 2.1. PDRB ADHB (juta Rp)</option>
                                    <option value="Tabel 2.2">Tabel 2.2. PDRB ADHK (juta Rp)</option>
                                    <option value="Tabel 2.3">Tabel 2.3. Distribusi Terhadap Provinsi (persen)</option>
                                    <option value="Tabel 2.4">Tabel 2.4. Distribusi Komponen Terhadap PDRB ADHB (persen)</option>
                                    <option value="Tabel 2.5">Tabel 2.5. Distribusi Komponen Terhadap PDRB ADHK (persen)</option>
                                    <option value="Tabel 2.6">Tabel 2.6. Indeks Implisit</option>
                                    <option value="Tabel 2.7">Tabel 2.7. Indeks Implisit Kumulatif</option>
                                    <option value="Tabel 2.8">Tabel 2.8. Pertumbuhan PDRB (Q-TO-Q), (persen)</option>
                                    <option value="Tabel 2.9">Tabel 2.9. Pertumbuhan PDRB (Y-ON-Y), (persen)</option>
                                    <option value="Tabel 2.10">Tabel 2.10. Pertumbuhan PDRB (C-TO-C)</option>
                                    <option value="Tabel 2.11">Tabel 2.11. Pertumbuhan Indeks Implisit PDRB (Q-TO-Q), (persen)</option>
                                    <option value="Tabel 2.12">Tabel 2.12. Pertumbuhan Indeks Implisit PDRB (Y-ON-Y), (persen)</option>
                                    <option value="Tabel 2.13">Tabel 2.13. Pertumbuhan Indeks Implisit PDRB (C-TO-C), (persen)</option>
                                    <option value="Tabel 2.14">Tabel 2.14. Sumber Pertumbuhan Kabupaten/Kota Terhadap PDRB Provinsi (Q-TO-Q), (persen)</option>
                                    <option value="Tabel 2.15">Tabel 2.15. Sumber Pertumbuhan Kabupaten/Kota Terhadap PDRB Provinsi (Y-ON-Y), (persen)</option>
                                    <option value="Tabel 2.16">Tabel 2.16. Sumber Pertumbuhan Kabupaten/Kota Terhadap PDRB Provinsi (C-TO-C), (persen)</option>
                                    <option value="Tabel 2.17">Tabel 2.17. Sumber Pertumbuhan Komponen Terhadap PDRB Kabupaten/Kota/Provinsi (Q-TO-Q), (persen)</option>
                                    <option value="Tabel 2.18">Tabel 2.18. Sumber Pertumbuhan Komponen Terhadap PDRB Kabupaten/Kota/Provinsi (Y-ON-Y), (persen)</option>
                                    <option value="Tabel 2.19">Tabel 2.19. Sumber Pertumbuhan Komponen Terhadap PDRB Kabupaten/Kota/Provinsi (C-TO-C), (persen)</option>
                                </select>
                            </div>
                            <div class="col-4 mb-2">
                                <label for="kab_filter" class="label">Kabupaten/Kota</label>
                                <select name="kab_filter" id="kab_filter" class="form-control" onchange="lihat()">
                                    @foreach(config("app.wilayah") as $kd_kab => $nm_kab)
                                    <option value="{{ '16' . $kd_kab }}">{{ $nm_kab }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 mb-2">
                                <label class="invisible">Periode</label>
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
    var modal_history_periode = {};
    function modal_clicked_periode() {
        modal_history_periode = {};
        @foreach($periode as $periode_item)
        modal_history_periode["{{ $periode_item->periode }}"] = document.getElementById("periode_filter_{{ $periode_item->periode }}").checked;
        @endforeach
        console.log(modal_history_periode);
    }
    function modal_cancel_periode() {
        @foreach($periode as $periode_item)
        document.getElementById("periode_filter_{{ $periode_item->periode }}").checked = modal_history_periode["{{ $periode_item->periode }}"];
        @endforeach
        modal_history_periode = {};
    }
    function check_all_periode(checked) {
        @foreach($periode as $periode_item)
        document.getElementById("periode_filter_{{ $periode_item->periode }}").checked = checked;
        @endforeach
    }
    function lihat() {
        var url = "{{ url("/revisi/total") }}";

        var tabel = document.getElementById("tabel_filter").value;
        var kd_kab = document.getElementById("kab_filter").value;

        var periode = [];
        @foreach($periode as $periode_item)
        periode_check = document.getElementById("periode_filter_{{ $periode_item->periode }}");
        if (periode_check.checked) periode.push("{{ $periode_item->periode }}");
        @endforeach

        fetch(url, {
            method: "post",
            body: JSON.stringify({
                tabel: tabel,
                kd_kab: kd_kab,
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
                cell.outerHTML = "<th>" + col + "</th>";
            }
            for (const i in json) {
                var table_data = document.getElementById("table_data");
                var row = table_data.insertRow(-1);
                for (const j in json[i]) {
                    var cell = row.insertCell(-1);
                    if (json[i]['Komponen'].includes("BOLD")) {
                        if (json[i][j]) if (json[i][j].includes("WARNING")) cell.outerHTML = "<td class='bg-warning font-weight-bold'>" + json[i][j].replace("WARNING", "").replace("BOLD", "") + "</td>";
                        else if (json[i][j]) if (json[i][j].includes("CENTER")) cell.outerHTML = "<td class='text-center' style='background-color:#f2f2f2'>" + json[i][j].replace("CENTER", "").replace("BOLD", "") + "</td>";
                        else cell.outerHTML = "<td class='font-weight-bold' style='background-color:#f2f2f2'>" + json[i][j].replace("BOLD", "") + "</td>";
                    }
                    else {
                        if (json[i][j]) if (json[i][j].includes("WARNING")) cell.outerHTML = "<td class='bg-warning'>" + json[i][j].replace("WARNING", "") + "</td>";
                        else if (json[i][j]) if (json[i][j].includes("CENTER")) cell.outerHTML = "<td class='text-center'>" + json[i][j].replace("CENTER", "") + "</td>";
                        else cell.outerHTML = "<td>" + json[i][j] + "</td>";

                    }
                }
            }
        });
    }
    lihat();
    function exportToExcel() {
        var url = "{{ url("/revisi/total/export") }}";

        var judul = document.querySelector(`#tabel_filter option[value='${document.getElementById("tabel_filter").value}']`).text;

        var tabel = document.getElementById("tabel_filter").value;
        var kd_kab = document.getElementById("kab_filter").value;

        var periode = [];
        @foreach($periode as $periode_item)
        periode_check = document.getElementById("periode_filter_{{ $periode_item->periode }}");
        if (periode_check.checked) periode.push("{{ $periode_item->periode }}");
        @endforeach

        fetch(url, {
            method: "post",
            body: JSON.stringify({
                judul: judul,
                tabel: tabel,
                kd_kab: kd_kab,
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
                download: `Arah Revisi Total ${judul}.xlsx`,
            }).click();
        });
    }
    function exportAllToExcel() {
        var url = "{{ url("/revisi/total/exportall") }}";

        var judul = document.querySelector(`#tabel_filter option[value='${document.getElementById("tabel_filter").value}']`).text;

        var tabel = document.getElementById("tabel_filter").value;

        var kd_kab = [];
        @foreach(config("app.wilayah") as $kd_kab => $nm_kab)
        kd_kab.push("{{ '16' . $kd_kab }}");
        @endforeach

        var periode = [];
        @foreach($periode as $periode_item)
        periode_check = document.getElementById("periode_filter_{{ $periode_item->periode }}");
        if (periode_check.checked) periode.push("{{ $periode_item->periode }}");
        @endforeach

        fetch(url, {
            method: "post",
            body: JSON.stringify({
                judul: judul,
                tabel: tabel,
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
                download: `All Arah Revisi Total ${judul}.xlsx`,
            }).click();
        });
    }
</script>
@endsection
