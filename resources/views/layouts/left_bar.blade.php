<div id="left-sidebar" class="sidebar">
    <div class="sidebar-scroll">
        @if (Auth::check())
            <div class="user-account">
                <img src="{!! Auth::user()->fotoUrl !!}" class="rounded-circle user-photo" alt="User Profile Picture">
                <div class="dropdown">
                    <span>Welcome,</span>
                    <a href="javascript:void(0);" class="dropdown-toggle user-name"
                        data-toggle="dropdown"><strong>{{ Auth::user()->name }}</strong></a>
                    <ul class="dropdown-menu dropdown-menu-right account">
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                    <a href="#Dashboard"><i class="icon-home"></i>
                        <span>Beranda</span></a>
                </li>

                <li class="{{ request()->is('upload*') ? 'active' : '' }}">
                    <a href="#Upload" class="has-arrow"><i class="icon-cloud-upload"></i>
                        <span>Upload</span></a>
                    <ul>
                        <li class="{{ request()->is('upload/import*') ? 'active' : '' }}"><a
                                href="{{ url('upload/import') }}">Angka PDRB</a></li>
                        <li class="{{ request()->is('upload/fenomena_import*') ? 'active' : '' }}"><a
                                href="{{ url('upload/fenomena_import') }}">Fenomena</a></li>
                    </ul>
                </li>


                <li class="{{ request()->is('pdrb_*') || request()->is('tabel/resume') ? 'active' : '' }}">
                    <a href="#Tabel" class="has-arrow"><i class="icon-calendar"></i>
                        <span>Tabel PDRB</span></a>
                    <ul>
                        <li class="{{ request()->is('pdrb_ringkasan*') ? 'active' : '' }}">
                            <a href="{{ url('pdrb_ringkasan1/1.1') }}">Tabel Ringkasan
                            </a>
                        </li>
                        <li class="{{ request()->is('tabel/resume') ? 'active' : '' }}">
                            <a href="{{ url('tabel/resume') }}">Tabel Resume</a>
                        </li>

                        <li class="{{ request()->is('pdrb_kabkot*') ? 'active' : '' }}">
                            <a href="{{ url('pdrb_kabkot/3.1') }}">
                                Tabel Per Kabupaten Kota
                            </a>
                        </li>
                        <li class=""><a href="#">Tabel History Putaran</a></li>
                    </ul>
                </li>

                <li class="{{ request()->is('revisi/*') ? 'active' : '' }}">
                    <a href="#Revisi" class="has-arrow"><i class="icon-directions"></i>
                        <span>Arah Revisi</span></a>
                    <ul>
                        <li class="{{ request()->is('revisi/total') ? 'active' : '' }}"><a href="{{ url('revisi/total') }}">Total</a></li>
                        <li class=""><a href="#">Kab/Kota</a></li>
                    </ul>
                </li>

                <li class="">
                    <a href="#Fenomena" class="has-arrow"><i class="fa fa-quote-right"></i>
                        <span>Fenomena</span></a>
                    <ul>
                        <li class=""><a href="#">Total</a></li>
                        <li class=""><a href="#">Kab/Kota</a></li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</div>
