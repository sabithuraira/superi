<div id="left-sidebar" class="sidebar">
    <div class="sidebar-scroll">
        <div class="user-account">
            <div class="dropdown">
                <span>Welcome,</span>
                
            </div>
        </div>

        <nav id="left-sidebar-nav" class="sidebar-nav">
            <ul id="main-menu" class="metismenu">

                

                <li
                    class="{{ request()->is('upload*') ? 'active' : '' }}">
                    <a href="#Upload" class="has-arrow"><i class="icon-cloud-upload"></i>
                        <span>Upload</span></a>
                    <ul>
                        <li class="{{ request()->is('upload/import*') ? 'active' : '' }}"><a href="{{ url('upload/import') }}">Angka PDRB</a></li>
                        <li class="{{ request()->is('upload/fenomena_import*') ? 'active' : '' }}"><a href="{{ url('upload/fenomena_import') }}">Fenomena</a></li>
                    </ul>
                </li>

                
                <li
                    class="">
                    <a href="#Tabel" class="has-arrow"><i class="fa fa-table"></i>
                        <span>Tabel PDRB</span></a>
                    <ul>
                        <li class=""><a href="#">Tabel Ringkasan</a></li>
                        <li class=""><a href="{{ url('tabel/resume') }}">Tabel Resume</a></li>
                        <li class=""><a href="#">Tabel Per Provinsi</a></li>
                        <li class=""><a href="#">Tabel History Putaran</a></li>
                    </ul>
                </li>
                
                <li
                    class="">
                    <a href="#Revisi" class="has-arrow"><i class="icon-directions"></i>
                        <span>Arah Revisi</span></a>
                    <ul>
                        <li class=""><a href="{{ url('revisi/total') }}">Total</a></li>
                        <li class=""><a href="#">Kab/Kota</a></li>
                    </ul>
                </li>
                
                <li
                    class="">
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
