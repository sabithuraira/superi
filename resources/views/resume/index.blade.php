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
                <h2>Tabel Resume PDRB Pengeluaran Triwulanan</h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-12">
                        <div class="row mb-2">
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <label for="tabel_filter" class="label">Tabel</label>
                                        <select name="tabel_filter" id="tabel_filter" class="form-control">
                                            <option value="2.1">Tabel 2.1. PDRB ADHB</option>
                                            <option value="2.2">Tabel 2.2. PDRB ADHK</option>
                                            <option value="2.3">Tabel 2.3. Distribusi Terhadap Pulau</option>
                                            <option value="2.4">Tabel 2.4. Distribusi Terhadap Total 34 Provinsi</option>
                                            <option value="2.5">Tabel 2.5. Distribusi Komponen Terhadap PDRB ADHB</option>
                                            <option value="2.6">Tabel 2.6. Distribusi Komponen Terhadap PDRB ADHK</option>
                                            <option value="2.7">Tabel 2.7. Indeks Implisit</option>
                                            <option value="2.8">Tabel 2.8. Indeks Implisit Kumulatif</option>
                                            <option value="2.9">Tabel 2.9. Pertumbuhan PDRB (Q-TO-Q)</option>
                                            <option value="2.10">Tabel 2.10. Pertumbuhan PDRB (Y-ON-Y)</option>
                                            <option value="2.11">Tabel 2.11. Pertumbuhan PDRB (C-TO-C)</option>
                                            <option value="2.12">Tabel 2.12. Pertumbuhan Indeks Implisit PDRB (Q-TO-Q)</option>
                                            <option value="2.13">Tabel 2.13. Pertumbuhan Indeks Implisit PDRB (Y-ON-Y)</option>
                                            <option value="2.14">Tabel 2.14. Pertumbuhan Indeks Implisit PDRB (C-TO-C)</option>
                                            <option value="2.15">Tabel 2.15. Sumber Pertumbuhan Provinsi Terhadap Total PDRB (Q-TO-Q)</option>
                                            <option value="2.16">Tabel 2.16. Sumber Pertumbuhan Provinsi Terhadap Total PDRB (Y-ON-Y)</option>
                                            <option value="2.17">Tabel 2.17. Sumber Pertumbuhan Provinsi Terhadap Total PDRB (C-TO-C)</option>
                                            <option value="2.18">Tabel 2.18. Sumber Pertumbuhan Komponen Terhadap PDRB Provinsi (Q-TO-Q)</option>
                                            <option value="2.19">Tabel 2.19. Sumber Pertumbuhan Komponen Terhadap PDRB Provinsi (Y-ON-Y)</option>
                                            <option value="2.20">Tabel 2.20. Sumber Pertumbuhan Komponen Terhadap PDRB Provinsi (C-TO-C)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label for="komponen_filter" class="label">Komponen</label>
                                        <select name="komponen_filter" id="komponen_filter" class="form-control">
                                            <option value="c_pdrb">Semua Komponen PDRB</option>
                                            @foreach($komponen as $komponen_item)
                                            <option value="{{ 'c_' . str_replace('.', '', $komponen_item->no_komponen) }}">{{ $komponen_item->no_komponen . ' ' . $komponen_item->nama_komponen }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="kab_filter" class="label">Kab/Kot</label>
                                <select name="kab_filter" id="kab_filter" class="form-control" multiple>
                                    <option value="1600" selected>Prov. Sumatera Selatan</option>
                                    <option value="1601" selected>Kab. Ogan Komering Ulu</option>
                                    <option value="1602" selected>Kab. Ogan Komering Ilir</option>
                                    <option value="1603" selected>Kab. Muara Enim</option>
                                    <option value="1604" selected>Kab. Lahat</option>
                                    <option value="1605" selected>Kab. Musi Rawas</option>
                                    <option value="1606" selected>Kab. Musi Banyuasin</option>
                                    <option value="1607" selected>Kab. Banyuasin</option>
                                    <option value="1608" selected>Kab. Ogan Komering Ulu Seletan</option>
                                    <option value="1609" selected>Kab. Ogan Komering Ulu Timur</option>
                                    <option value="1610" selected>Kab. Ogan Ilir</option>
                                    <option value="1611" selected>Kab. Empat Lawang</option>
                                    <option value="1612" selected>Kab. Penukal Abab Lematang Ilir</option>
                                    <option value="1613" selected>Kab. Musi Rawas Utara</option>
                                    <option value="1671" selected>Kota Palembang</option>
                                    <option value="1672" selected>Kota Prabumulih</option>
                                    <option value="1673" selected>Kota Pagar Alam</option>
                                    <option value="1674" selected>Kota Lubuklinggau</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="periode_filter" class="label">Periode</label>
                                <select name="periode_filter" id="periode_filter" class="form-control" multiple>
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
@endsection
<script>
    function cari() {
        var url = "{{ url("/tabel/resume") }}";
        var tabel = document.getElementById("tabel_filter").value;
        var komponen = document.getElementById("komponen_filter").value;
        var kd_kab = Array.from(document.getElementById("kab_filter").selectedOptions).map(({ value }) => value);
        var periode = Array.from(document.getElementById("periode_filter").selectedOptions).map(({ value }) => value);
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
            document.getElementById("table_column").innerHTML = "";
            document.getElementById("table_data").innerHTML = "";
            for (const col in json[0]) {
                var table_column = document.getElementById("table_column");
                var cell = table_column.insertCell(-1);
                cell.innerHTML = col == 'kd_kab' ? 'Kabupaten/Kota' : col;
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
</script>