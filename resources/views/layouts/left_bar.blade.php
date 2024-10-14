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
                    <a href="#Upload" class="has-arrow"><i class="icon-calendar"></i>
                        <span>Upload</span></a>
                    <ul>
                        <li class="{{ request()->is('upload/upload*') ? 'active' : '' }}"><a
                                href="{{ url('upload/upload') }}">Upload</a></li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</div>
