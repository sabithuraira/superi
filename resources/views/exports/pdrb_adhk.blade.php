<table>
    <thead>
        <tr>
            <th colspan="5"><b>Tabel PDRB ADHK</b></th>
        </tr>
        
        <tr>
            <th colspan="5"></th>
        </tr>

        <tr>
            <th width="50"><b>Komponen</b></th>
            <th><b>{{ $tahun }}Q1</b></th>
            @if($triwulan>=2)
            <th><b>{{ $tahun }}Q2</b></th>
            @endif
            @if($triwulan>=3)
            <th><b>{{ $tahun }}Q3</b></th>
            @endif
            @if($triwulan>=4)
            <th><b>{{ $tahun }}Q4</b></th>
            @endif
        </tr>
    </thead>

    <tbody>
        @php 
            $null_parent = array_filter($komponen->toArray(), function ($x) {
                return $x['parent_id']==null;
            });
        @endphp

        @foreach($null_parent as $data)
            <tr>
                <td>{{ $data['no_komponen'] }} {{ $data['nama_komponen'] }}</td>
                
                @if($datas['adhk'][0]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhk'][0]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
                @endif
                
                @if($triwulan>=2)
                    @if($datas['adhk'][1]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhk'][1]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
                    @endif
                @endif
                
                @if($triwulan>=3)
                    @if($datas['adhk'][2]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhk'][2]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
                    @endif
                @endif
                
                @if($triwulan>=4)
                    @if($datas['adhk'][3]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhk'][3]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
                    @endif
                @endif
            </tr>

            @php 
                $non_null_parent = array_filter($komponen->toArray(), function ($x) use ($data) {
                    return $x['parent_id']==$data['no_komponen'];
                });
            @endphp
        
            @foreach($non_null_parent as $data2)
                <tr>
                    <td>{{ $data2['no_komponen'] }} {{ $data2['nama_komponen'] }}</td>

                    @if($datas['adhk'][0]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhk'][0]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                    @endif
                    
                    @if($triwulan>=2)
                        @if($datas['adhk'][1]==null)
                            <td></td>
                        @else 
                            <td >{{ $datas['adhk'][1]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                        @endif
                    @endif
                    
                    @if($triwulan>=3)
                        @if($datas['adhk'][2]==null)
                            <td></td>
                        @else 
                            <td >{{ $datas['adhk'][2]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                        @endif
                    @endif
                    
                    @if($triwulan>=4)
                        @if($datas['adhk'][3]==null)
                            <td></td>
                        @else 
                            <td >{{ $datas['adhk'][3]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                        @endif
                    @endif
                </tr>
            @endforeach
        @endforeach
        
        <tr>
            <td><b>PDRB</b></td>

            @if($datas['adhk'][0]==null)
                <td></td>
            @else 
                <td >{{ $datas['adhk'][0]->{'c_pdrb'} }}</td>
            @endif
            
            @if($triwulan>=2)
                @if($datas['adhk'][1]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhk'][1]->{'c_pdrb'} }}</td>
                @endif
            @endif
            
            @if($triwulan>=3)
                @if($datas['adhk'][2]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhk'][2]->{'c_pdrb'} }}</td>
                @endif
            @endif
            
            @if($triwulan>=4)
                @if($datas['adhk'][3]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhk'][3]->{'c_pdrb'} }}</td>
                @endif
            @endif
        </tr>
    </tbody>
</table>