<table>
    <thead>
        <tr>
            <th colspan="5"><b>Tabel PDRB ADHB</b></th>
        </tr>
        
        <tr>
            <th colspan="5"></th>
        </tr>

        <tr>
            <th width="50"><b>Komponen</b></th>
            <th><b>{{ $tahun }}Q1</b></th>
            <th><b>{{ $tahun }}Q2</b></th>
            <th><b>{{ $tahun }}Q3</b></th>
            <th><b>{{ $tahun }}Q4</b></th>
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
                
                @if($datas['adhb'][0]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhb'][0]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
                @endif
                
                @if($datas['adhb'][1]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhb'][1]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
                @endif
                
                @if($datas['adhb'][2]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhb'][2]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
                @endif
                
                @if($datas['adhb'][3]==null)
                    <td></td>
                @else 
                    <td >{{ $datas['adhb'][3]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}</td>
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

                    @if($datas['adhb'][0]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhb'][0]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                    @endif
                    
                    @if($datas['adhb'][1]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhb'][1]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                    @endif
                    
                    @if($datas['adhb'][2]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhb'][2]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                    @endif
                    
                    @if($datas['adhb'][3]==null)
                        <td></td>
                    @else 
                        <td >{{ $datas['adhb'][3]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}</td>
                    @endif
                </tr>
            @endforeach
        @endforeach
            
        <tr>
            <td><b>PDRB</b></td>

            @if($datas['adhb'][0]==null)
                <td></td>
            @else 
                <td >{{ $datas['adhb'][0]->{'c_pdrb'} }}</td>
            @endif
            
            @if($datas['adhb'][1]==null)
                <td></td>
            @else 
                <td >{{ $datas['adhb'][1]->{'c_pdrb'} }}</td>
            @endif
            
            @if($datas['adhb'][2]==null)
                <td></td>
            @else 
                <td >{{ $datas['adhb'][2]->{'c_pdrb'} }}</td>
            @endif
            
            @if($datas['adhb'][3]==null)
                <td></td>
            @else 
                <td >{{ $datas['adhb'][3]->{'c_pdrb'} }}</td>
            @endif
        </tr>
    </tbody>
</table>