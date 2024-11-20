<div id="left-sidebar" class="sidebar">

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


            <li class="{{ request()->is('pdrb_*') ? 'active' : '' }}">
                <a href="#Tabel" class="has-arrow"><i class="icon-calendar"></i>
                    <span>Tabel PDRB</span></a>
                <ul>
                    <li class="{{ request()->is('pdrb_ringkasan*') ? 'active' : '' }}">
                        <a href="{{ url('pdrb_ringkasan1/1.1') }}">Tabel Ringkasan
                        </a>
                    </li>
                    <li class=""><a href="{{ url('tabel/resume') }}">Tabel Resume</a></li>

                    <li class="{{ request()->is('pdrb_kabkot*') ? 'active' : '' }}">
                        <a href="{{ url('pdrb_kabkot/3.1') }}">
                            Tabel Per Kabupaten Kota
                        </a>
                    </li>
                    <li class=""><a href="#">Tabel History Putaran</a></li>
                </ul>
            </li>

            <li class="">
                <a href="#Revisi" class="has-arrow"><i class="icon-directions"></i>
                    <span>Arah Revisi</span></a>
                <ul>
                    <li class=""><a href="{{ url('revisi/total') }}">Total</a></li>
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
