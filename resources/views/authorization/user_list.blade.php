<div id="load" class="table-responsive">
    <table class="table m-b-0">
        @if (count($datas)==0)
            <thead>
                <tr><th>Tidak ditemukan data</th></tr>
            </thead>
        @else
            <thead>
                <tr class="text-center">
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($datas as $data)
                <tr>
                    <td>{{ $data['name'] }}</td>
                    <td>{{ $data['email'] }}</td>
                    <td>
                        <ul>
                        @foreach($data->getRoleNames() as $role)
                            <li>{{ $role }}</li>
                        @endforeach
                        </ul>
                    </td>
                    <td><a href="{{action('AuthorizationController@user_edit', $data['id'])}}"><i class="icon-pencil text-info"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        @endif
    </table>
    
    {{ $datas->links() }} 
</div>