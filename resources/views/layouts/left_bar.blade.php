<div id="left-sidebar" class="sidebar">
    <div class="sidebar-scroll">
        @if (Auth::check())
            <div class="user-account">
                <img src="{!! Auth::user()->fotoUrl !!}" class="rounded-circle user-photo" alt="User Profile Picture">
                <div class="dropdown">
                    <span>Welcome,</span>
                    <a href="javascript:void(0);" class="dropdown-toggle user-name" data-toggle="dropdown"><strong>{{ Auth::user()->name }}</strong></a>
                    <ul class="dropdown-menu dropdown-menu-right account">
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @endif

        <nav id="left-sidebar-nav" class="sidebar-nav">
            <ul id="main-menu" class="metismenu">
                <li class="{{ request()->is('beranda*') ? 'active' : '' }}">
                    <a href="{{ url('beranda') }}"><i class="icon-home"></i>
                        <span>Beranda</span></a>
                </li>

                <li class="{{ request()->is('upload*') ? 'active' : '' }}">
                    <a href="#Upload" class="has-arrow"><i class="icon-cloud-upload"></i>
                        <span>Upload</span></a>
                    <ul>
                        @can('import_pdrb')
                            <li class="{{ request()->is('upload/import*') ? 'active' : '' }}"><a href="{{ url('upload/import') }}">Angka PDRB</a></li>
                        @endcan
                        @can('import_fenomena')
                            <li class="{{ request()->is('upload/fenomena_import*') ? 'active' : '' }}"><a
                                    href="{{ url('upload/fenomena_import') }}">Fenomena</a></li>
                        @endcan
                    </ul>
                </li>

                <li
                    class="{{ request()->is('pdrb_*') || request()->is('tabel_resume') || request()->is('tabel_kabkot') || request()->is('tabel_history') ? 'active' : '' }}">
                    <a href="#Tabel" class="has-arrow"><i class="icon-calendar"></i>
                        <span>Tabel PDRB</span></a>
                    <ul>
                        @can('tabel_ringkasan')
                            <li class="{{ request()->is('pdrb_ringkasan*') ? 'active' : '' }}">
                                <a href="{{ url('pdrb_ringkasan1/1.1') }}">Tabel Ringkasan
                                </a>
                            </li>
                        @endcan
                        @if (Auth::user()->kdkab == '00')
                            @can('tabel_resume')
                                <li class="{{ request()->is('pdrb_resume*') ? 'active' : '' }}">
                                    <a href="{{ url('pdrb_resume/2.1') }}">Tabel Resume Per Komponen</a>
                                </li>
                            @endcan
                        @endif

                        @can('tabel_kabkot')
                            <li class="{{ request()->is('pdrb_kabkot*') ? 'active' : '' }}">
                                <a href="{{ url('pdrb_kabkot/3.1') }}">
                                    Tabel Per Kabupaten Kota
                                </a>
                            </li>
                        @endcan
                        @can('tabel_history')
                            <li class="{{ request()->is('pdrb_putaran*') ? 'active' : '' }}"><a href="{{ url('pdrb_putaran/3.1') }}">Tabel History
                                    Putaran</a></li>
                        @endcan
                    </ul>
                </li>

                <li class="{{ request()->is('arah_revisi_total*') || request()->is('arah_revisi_kabkota*') ? 'active' : '' }}">
                    <a href="#Revisi" class="has-arrow"><i class="icon-directions"></i>
                        <span>Arah Revisi</span></a>
                    <ul>
                        @can('arah_revisi_total')
                            <li class="{{ request()->is('revisi_total*') ? 'active' : '' }}"><a href="{{ url('revisi_total/2.1') }}">Total</a></li>
                        @endcan

                        @can('arah_revisi_kabkota')
                            <li class="{{ request()->is('revisi_kabkot*') ? 'active' : '' }}"><a href="{{ url('revisi_kabkot/301') }}">Kab/Kota</a></li>
                        @endcan
                    </ul>
                </li>

                <li>
                    <a href="{{ url('rekonsiliasi') }}" class="has-arrow">
                        <i class="fa fa-quote-right"></i>
                        <span>Fenomena</span>
                    </a>
                    <ul>
                        @can('fenomena_total')
                            <li class=""><a href="#">Total</a></li>
                        @endcan

                        @can('fenomena_kabkota')
                            <li class=""><a href="#">Kab/Kota</a></li>
                        @endcan
                    </ul>
                </li>
                <li class="{{ request()->is('rekonsiliasi*') || request()->is('simulasi*') ? 'active' : '' }}">
                    <a href="#Rekonsiliasi" class="has-arrow"><i class="fa fa-pencil-square-o"></i>
                        <span>Rekonsiliasi</span></a>
                    <ul>
                        @can('rekonsiliasi')
                            <li class="{{ request()->is('rekonsiliasi*') ? 'active' : '' }}"><a href="{{ url('rekonsiliasi') }}">Rekonsiliasi</a></li>
                        @endcan

                        @can('simulasi')
                            <li class="{{ request()->is('simulasi*') ? 'active' : '' }}"><a href="{{ url('simulasi') }}">Simulasi</a></li>
                        @endcan
                    </ul>
                </li>
                @hasrole('superadmin')
                    <li class="{{ request()->is('authorization/*') ? 'active' : '' }}">
                        <a href="#Superadmin" class="has-arrow"><i class="icon-settings"></i>
                            <span>Setting</span></a>
                        <ul>
                            <li class="{{ request()->is('authorization/permission') ? 'active' : '' }}"><a
                                    href="{{ url('authorization/permission') }}">Permission</a></li>
                            <li class="{{ request()->is('authorization/role') ? 'active' : '' }}"><a href="{{ url('authorization/role') }}">Role</a>
                            </li>
                            <li class="{{ request()->is('authorization/user') ? 'active' : '' }}"><a href="{{ url('authorization/user') }}">User
                                    Role</a></li>
                            <li class="{{ request()->is('setting_app') ? 'active' : '' }}"><a href="{{ url('setting_app') }}">Konfigurasi Aplikasi</a>
                            </li>
                        </ul>
                    </li>
                @endhasrole
            </ul>
        </nav>
    </div>
</div>
