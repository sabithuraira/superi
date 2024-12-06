@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>                           
    <li class="breadcrumb-item">Import Excel Fenomena</li>
</ul>
@endsection

@section('content')
<div class="row clearfix">
  <div class="col-md-12">
      <div class="card" id="app_vue">
          <div class="header">
              <h2>Import Excel Fenomena</h2>
          </div>
          <div class="body">
                <form method="post" action="{{url('upload/fenomena_import')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row clearfix">

                        <div class="col-lg-3 col-md-12 left-box">
                            <div class="form-group">
                                <label>Kabupaten/Kota:</label>

                                <div class="input-group">
                                    <select class="form-control  form-control-sm" name="wilayah"  
                                        v-model="form_data.wilayah">
                                        @foreach (config('app.wilayah') as $key=>$value)
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
                                <select class="form-control  form-control-sm" name="tahun" disabled v-model="form_data.tahun">
                                    @for ($i=date('Y');$i>=2023;$i--)
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

                <br/>
                
                <table id="my_table" class="table-bordered m-b-0" style="min-width:100%">
                    <tr class="text-center">
                        <th>Komponen</th>
                        <th>Pertumbuhan</th>
                        <th>Fenomena</th>
                    </tr>
                    
                    <template v-for="(data, index) in komponen.filter(x=>x.parent_id==null)" :key="data.id">
                        <tr>
                            <td rowspan="3"><b>@{{ data.no_komponen }} @{{ data.nama_komponen }}</b></td>
                            
                            <td class="text-center">q-to-q</td>
                            <td v-if="datas['q-to-q'][form_data.triwulan-1]==null"></td>
                            <td v-else>@{{ datas['q-to-q'][form_data.triwulan-1]['fenomena_c_'+data.no_komponen.replaceAll('.', '')] }}</td>
                        </tr>

                        <tr>
                            <td class="text-center">y-o-y</td>
                            <td v-if="datas['y-o-y'][form_data.triwulan-1]==null"></td>
                            <td v-else>@{{ datas['y-o-y'][form_data.triwulan-1]['fenomena_c_'+data.no_komponen.replaceAll('.', '')] }}</td>
                        </tr>
                        
                        <tr>
                            <td class="text-center">c-to-c</td>
                            <td v-if="datas['c-to-c'][form_data.triwulan-1]==null"></td>
                            <td v-else>@{{ datas['c-to-c'][form_data.triwulan-1]['fenomena_c_'+data.no_komponen.replaceAll('.', '')] }}</td>
                        </tr>

                        <template v-for="(data2, index2) in komponen.filter(y=>y.parent_id==data.no_komponen)" :key="data2.id">
                            <tr>
                                <td rowspan="3"><b>@{{ data2.no_komponen }} @{{ data2.nama_komponen }}</b></td>
                                
                                <td class="text-center">q-to-q</td>
                                <td v-if="datas['q-to-q'][form_data.triwulan-1]==null"></td>
                                <td v-else>@{{ datas['q-to-q'][form_data.triwulan-1]['fenomena_c_'+data2.no_komponen.replaceAll('.', '')] }}</td>
                            </tr>

                            <tr>
                                <td class="text-center">y-o-y</td>
                                <td v-if="datas['y-o-y'][form_data.triwulan-1]==null"></td>
                                <td v-else>@{{ datas['y-o-y'][form_data.triwulan-1]['fenomena_c_'+data2.no_komponen.replaceAll('.', '')] }}</td>
                            </tr>
                            
                            <tr>
                                <td class="text-center">c-to-c</td>
                                <td v-if="datas['c-to-c'][form_data.triwulan-1]==null"></td>
                                <td v-else>@{{ datas['c-to-c'][form_data.triwulan-1]['fenomena_c_'+data2.no_komponen.replaceAll('.', '')] }}</td>
                            </tr>
                        </template>
                    </template>
                    
                    <tr>
                        <td rowspan="3"><b>PDRB</b></td>
                        
                        <td class="text-center">q-to-q</td>
                        <td v-if="datas['q-to-q'][form_data.triwulan-1]==null"></td>
                        <td v-else>@{{ datas['q-to-q'][form_data.triwulan-1]['fenomena_c_pdrb'] }}</td>
                    </tr>

                    <tr>
                        <td class="text-center">y-o-y</td>
                        <td v-if="datas['y-o-y'][form_data.triwulan-1]==null"></td>
                        <td v-else>@{{ datas['y-o-y'][form_data.triwulan-1]['fenomena_c_pdrb'] }}</td>
                    </tr>
                    
                    <tr>
                        <td class="text-center">c-to-c</td>
                        <td v-if="datas['c-to-c'][form_data.triwulan-1]==null"></td>
                        <td v-else>@{{ datas['c-to-c'][form_data.triwulan-1]['fenomena_c_pdrb'] }}</td>
                    </tr>
                </table>
          </div>
      </div>
  </div>
</div>
@endsection

@section('css')
  <meta name="_token" content="{{csrf_token()}}" />
  <meta name="csrf-token" content="@csrf">
@endsection

@section('scripts')
<script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>

<script>
var vm = new Vue({  
    el: "#app_vue",
    data:  {
        form_data: {
            wilayah: {!! json_encode($wilayah) !!},
            tahun: {!! json_encode($tahun) !!},
            triwulan:  {!! json_encode($triwulan) !!},
        },
        datas: [],
        komponen: []
    },
    watch: {
        form_data: {
            handler(val){
                this.setDatas();
            },
            deep: true
        }
    },
    methods: {
        setDatas: function(event){
            var self = this;
            $('#wait_progres').modal('show');

            console.log("haai")

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
            })

            $.ajax({
                url : "{{ url('/upload/fenomena/') }}",
                method : 'post',
                dataType: 'json',
                data:{
                    wilayah: self.form_data.wilayah,
                    tahun: self.form_data.tahun,
                },
            }).done(function (data) {
                self.datas = data.datas;
                self.komponen = data.komponen;

                console.log(self.datas)

                $('#wait_progres').modal('hide');
            }).fail(function (msg) {
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