@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Import Excel PDRB</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card" id="app_vue">
                <div class="header">
                    <h2>Import Excel PDRB</h2>
                </div>
                <div class="body">
                    <form method="post" action="{{ url('upload/import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row clearfix">

                            <div class="col-lg-3 col-md-12 left-box">
                                <div class="form-group">
                                    <label>Kabupaten/Kota:</label>

                                    <div class="input-group">
                                        <select class="form-control  form-control-sm" name="wilayah"
                                            v-model="form_data.wilayah">
                                            @foreach (config('app.wilayah') as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-12 left-box">
                                <div class="form-group">
                                    <label>Tahun:</label>

                                    <div class="input-group">
                                        <select class="form-control  form-control-sm" readonly name="tahun"
                                            v-model="form_data.tahun">
                                            @for ($i = date('Y'); $i >= 2023; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                        <div class="col-lg-3 col-md-12 left-box">
                            <div class="form-group">
                                <label>Triwulan:</label>
                                <div class="input-group">
                                <select class="form-control  form-control-sm" name="triwulan" disabled v-model="form_data.triwulan">
                                    @for ($i=1;$i<=4;$i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-12 right-box">
                            <div class="form-group">
                                <label>Pilih File:</label>
                                <input type="file" class="form-control" name="excel_file">
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-lg-6">
                            <button name="action" class="btn btn-success float-left" type="submit" value="2"><i class="fa fa-file-excel-o"></i>&nbsp; Export Excel</button>
                        </div>
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary float-right" name="action" value="1">Simpan</button>
                        </div>
                    </div>
                </form>

                <ul class="nav nav-tabs mt-2">
                    <li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#adhb">ADHB</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#adhk">ADHK</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="adhb">
                        <span class="text-muted font-italic">*dalam juta rupiah</span>
                        <table class="table table-bordered m-b-0" style="min-width:100%">
                            <tr class="text-center">
                                <th>Komponen</th>
                                <template v-if="form_data.triwulan==4">
                                    <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1)]">
                                        <th  v-for="n in 4">@{{ item }}Q@{{ n }}</th>
                                    </template>
                                </template>
                                <th>@{{ form_data.tahun }}Q1</th>
                                <th v-if="form_data.triwulan>=2">@{{ form_data.tahun }}Q2</th>
                                <th v-if="form_data.triwulan>=3">@{{ form_data.tahun }}Q3</th>
                                <th v-if="form_data.triwulan==4">@{{ form_data.tahun }}Q4</th>
                            </tr>
                            
                            <!-- WHEN TRIWULAN=4-->
                            <template v-if="form_data.triwulan==4">
                                <template v-for="(data, index) in komponen" :key="data.id">
                                    <tr>
                                        <td>
                                            <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                            <span v-if="data.no_komponen!='pdrb'">
                                                @{{ data.no_komponen }} 
                                            </span>
                                            @{{ data.nama_komponen }}
                                        </td>

                                        <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1), form_data.tahun]">
                                            <template v-for="n in 4">
                                                <td class="text-right">
                                                    <span v-if="datas['adhb'][((item - (form_data.tahun-2))*4)+(n-1)]!=null">@{{ datas['adhb'][((item - (form_data.tahun-2))*4)+(n-1)]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                                </td>
                                            </template>
                                        </template>
                                    </tr>
                                </template>
                                
                            </template>

                            <!-- WHEN TRIWULAN <=4--> 
                            <template v-else>
                                <template v-for="(data, index) in komponen" :key="data.id">
                                    <tr>
                                        <td>
                                            <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                            <span v-if="data.no_komponen!='pdrb'">
                                            @{{ data.no_komponen }} 
                                            </span>
                                            @{{ data.nama_komponen }}
                                        </td>
                                        
                                        <td class="text-right">
                                            <span v-if="datas['adhb'][0]!=null">@{{ datas['adhb'][0]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                        </td>
                                        
                                        <template v-if="form_data.triwulan>=2">
                                            <td class="text-right">
                                                <span v-if="datas['adhb'][1]!=null">@{{ datas['adhb'][1]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                            </td>
                                        </template>
                                        
                                        <template v-if="form_data.triwulan>=3">
                                            <td class="text-right">
                                                <span v-if="datas['adhb'][2]!=null">@{{ datas['adhb'][2]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                                
                            </template>
                        </table>
                    </div>

                    <!-- ADHK -->
                    <div class="tab-pane" id="adhk">
                        <span class="text-muted font-italic">*dalam juta rupiah</span>
                        <table class="table table-bordered m-b-0" style="min-width:100%">
                            <tr class="text-center">
                                <th>Komponen</th>
                                <template v-if="form_data.triwulan==4">
                                    <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1)]">
                                        <th  v-for="n in 4">@{{ item }}Q@{{ n }}</th>
                                    </template>
                                </template>
                                <th>@{{ form_data.tahun }}Q1</th>
                                <th v-if="form_data.triwulan>=2">@{{ form_data.tahun }}Q2</th>
                                <th v-if="form_data.triwulan>=3">@{{ form_data.tahun }}Q3</th>
                                <th v-if="form_data.triwulan>=4">@{{ form_data.tahun }}Q4</th>
                            </tr>
                            
                            <!-- WHEN TRIWULAN==4-->
                            <template v-if="form_data.triwulan==4">
                                <template v-for="(data, index) in komponen" :key="data.id">
                                    <tr>
                                        <td>
                                            <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                            <span v-if="data.no_komponen!='pdrb'">
                                            @{{ data.no_komponen }} 
                                            </span>
                                            @{{ data.nama_komponen }}
                                        </td>
                                        
                                        <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1), form_data.tahun]">
                                            <template v-for="n in 4">
                                                <td class="text-right">
                                                    <span v-if="datas['adhk'][((item - (form_data.tahun-2))*4)+(n-1)]!=null">@{{ datas['adhk'][((item - (form_data.tahun-2))*4)+(n-1)]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                                </td>
                                            </template>
                                        </template>
                                    </tr>
                                </template>
                                 
                            </template>

                            <!-- WHEN TRIWULAN <=4--> 
                            <template v-else>
                                <template v-for="(data, index) in komponen" :key="data.id">
                                    <tr>
                                        <td>
                                            <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                            <span v-if="data.no_komponen!='pdrb'">
                                            @{{ data.no_komponen }} 
                                            </span>
                                            @{{ data.nama_komponen }}
                                        </td>
                                        
                                        <td class="text-right">
                                            <span v-if="datas['adhk'][0]!=null">@{{ datas['adhk'][0]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                        </td>
                                        
                                        <template v-if="form_data.triwulan>=2">
                                            <td class="text-right">
                                                <span v-if="datas['adhk'][1]!=null">@{{ datas['adhk'][1]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                            </td>
                                        </template>
                                        
                                        <template v-if="form_data.triwulan>=3">
                                            <td class="text-right">
                                                <span v-if="datas['adhk'][2]!=null">@{{ datas['adhk'][2]['c_'+data.no_komponen.replaceAll('.', '')] }}</span>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                                 
                            </template>
                        </table>
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
                form_data: {
                    wilayah: {!! json_encode($wilayah) !!},
                    tahun: {!! json_encode($tahun) !!},
                    triwulan: {!! json_encode($triwulan) !!},
                },
                datas: [],
                komponen: [],
            },
            watch: {
                form_data: {
                    handler(val) {
                        this.setDatas();
                    },
                    deep: true
                }
            },
            methods: {
                setDatas: function(event) {
                    var self = this;
                    $('#wait_progres').modal('show');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    })

                    $.ajax({
                        url: "{{ url('/upload/pdrb/') }}",
                        method: 'post',
                        dataType: 'json',
                        data: {
                            wilayah: self.form_data.wilayah,
                            tahun: self.form_data.tahun,
                        },
                    }).done(function(data) {
                        self.datas = data.datas;
                        self.komponen = data.komponen;
                        self.komponen.push({
                            'id': '', 'no_komponen': 'pdrb', 'nama_komponen' : 'PDRB', 'status_aktif': '',
                            'update_at': '', 'updated_by': '', 'create_at': '', 'created_by': ''
                        });

                        // console.log(self.komponen);
                        $('#wait_progres').modal('hide');
                    }).fail(function(msg) {
                        console.log(JSON.stringify(msg));
                        $('#wait_progres').modal('hide');
                    });
                },
            }
        });

        $(document).ready(function() {
            vm.setDatas();
        });
    </script>
@endsection
