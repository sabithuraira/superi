<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="IPDS BPS Provinsi Sumatera Selatan">
  <title>{{ env('APP_NAME', 'SUPERI') }}</title>
  <link rel="shortcut icon" href="{!! asset('sandbox/assets/img/favicon.png') !!}">
  <link rel="stylesheet" href="{!! asset('sandbox/assets/css/plugins.css') !!}">
  <link rel="stylesheet" href="{!! asset('sandbox/assets/css/style.css') !!}">
  <link rel="stylesheet" href="{!! asset('sandbox/assets/css/colors/yellow.css') !!}">
  <link rel="preload" href="{!! asset('sandbox/assets/css/fonts/thicccboi.css') !!}" as="style" onload="this.rel='stylesheet'">
</head>

<body>
  <div class="content-wrapper">
    <header class="wrapper bg-soft-primary">
      <nav class="navbar navbar-expand-lg center-nav transparent navbar-light">
        <div class="container flex-lg-row flex-nowrap align-items-center">
          <div class="navbar-brand w-100">
            <a href="./index.html">
              <img src="{!! asset('sandbox/assets/img/logo-dark.png') !!}" srcset="{!! asset('sandbox/assets/img/logo-dark@2x.png 2x') !!}" alt="" />
            </a>
          </div>
          <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
            <div class="offcanvas-header d-lg-none d-xl-none">
              <a href="./index.html"><img src="{!! asset('sandbox/assets/img/logo-light.png') !!}" srcset="{!! asset('sandbox/assets/img/logo-light@2x.png 2x') !!}" alt="" /></a>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100">
              <ul class="navbar-nav">
               
                <li class="nav-item dropdown dropdown-mega">
                  <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Documentation</a>
                  <ul class="dropdown-menu mega-menu">
                    <li class="mega-menu-content">
                      <div class="row gx-0 gx-lg-3">
                        <div class="col-lg-4">
                          <h6 class="dropdown-header">Usage</h6>
                          <ul class="list-unstyled cc-2 pb-lg-1">
                            <li><a class="dropdown-item" href="./docs/index.html">Get Started</a></li>
                            <li><a class="dropdown-item" href="./docs/forms.html">Forms</a></li>
                            <li><a class="dropdown-item" href="./docs/faq.html">FAQ</a></li>
                            <li><a class="dropdown-item" href="./docs/changelog.html">Changelog</a></li>
                            <li><a class="dropdown-item" href="./docs/credits.html">Credits</a></li>
                          </ul>
                          <h6 class="dropdown-header mt-lg-6">Styleguide</h6>
                          <ul class="list-unstyled cc-2">
                            <li><a class="dropdown-item" href="./docs/styleguide/colors.html">Colors</a></li>
                            <li><a class="dropdown-item" href="./docs/styleguide/fonts.html">Fonts</a></li>
                            <li><a class="dropdown-item" href="./docs/styleguide/icons-svg.html">SVG Icons</a></li>
                            <li><a class="dropdown-item" href="./docs/styleguide/icons-font.html">Font Icons</a></li>
                            <li><a class="dropdown-item" href="./docs/styleguide/illustrations.html">Illustrations</a></li>
                            <li><a class="dropdown-item" href="./docs/styleguide/backgrounds.html">Backgrounds</a></li>
                            <li><a class="dropdown-item" href="./docs/styleguide/misc.html">Misc</a></li>
                          </ul>
                        </div>
                        <!--/column -->
                        <div class="col-lg-8">
                          <h6 class="dropdown-header">Elements</h6>
                          <ul class="list-unstyled cc-3">
                            <li><a class="dropdown-item" href="./docs/elements/accordion.html">Accordion</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/alerts.html">Alerts</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/animations.html">Animations</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/avatars.html">Avatars</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/background.html">Background</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/badges.html">Badges</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/breadcrumb.html">Breadcrumb</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/buttons.html">Buttons</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/card.html">Card</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/carousel.html">Carousel</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/dividers.html">Dividers</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/form-elements.html">Form Elements</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/image-hover.html">Image Hover</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/image-mask.html">Image Mask</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/lightbox.html">Lightbox</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/player.html">Media Player</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/modal.html">Modal</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/progressbar.html">Progressbar</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/shadows.html">Shadows</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/shapes.html">Shapes</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/tables.html">Tables</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/tabs.html">Tabs</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/text-animations.html">Text Animations</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/text-highlight.html">Text Highlight</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/tiles.html">Tiles</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/tooltips-popovers.html">Tooltips & Popovers</a></li>
                            <li><a class="dropdown-item" href="./docs/elements/typography.html">Typography</a></li>
                          </ul>
                        </div>
                        <!--/column -->
                      </div>
                      <!--/.row -->
                    </li>
                    <!--/.mega-menu-content-->
                  </ul>
                  <!--/.dropdown-menu -->
                </li>
              </ul>
              <!-- /.navbar-nav -->
              <div class="d-lg-none mt-auto pt-6 pb-6 order-4">
                <a href="mailto:first.last@email.com" class="link-inverse">info@email.com</a>
                <br /> 00 (123) 456 78 90 <br />
                <nav class="nav social social-white mt-4">
                  <a href="#"><i class="uil uil-twitter"></i></a>
                  <a href="#"><i class="uil uil-facebook-f"></i></a>
                  <a href="#"><i class="uil uil-dribbble"></i></a>
                  <a href="#"><i class="uil uil-instagram"></i></a>
                  <a href="#"><i class="uil uil-youtube"></i></a>
                </nav>
                <!-- /.social -->
              </div>
              <!-- /offcanvas-nav-other -->
            </div>
            <!-- /.offcanvas-body -->
          </div>
          <!-- /.navbar-collapse -->
          <div class="navbar-other w-100 d-flex ms-auto">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <li class="nav-item dropdown language-select text-uppercase">
                <a class="nav-link dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">En</a>
                <ul class="dropdown-menu">
                  <li class="nav-item"><a class="dropdown-item" href="#">En</a></li>
                  <li class="nav-item"><a class="dropdown-item" href="#">De</a></li>
                  <li class="nav-item"><a class="dropdown-item" href="#">Es</a></li>
                </ul>
              </li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-info"><i class="uil uil-info-circle"></i></a></li>
              <li class="nav-item d-lg-none">
                <button class="hamburger offcanvas-nav-btn"><span></span></button>
              </li>
            </ul>
            <!-- /.navbar-nav -->
          </div>
          <!-- /.navbar-other -->
        </div>
        <!-- /.container -->
      </nav>
      <!-- /.navbar -->
      <div class="offcanvas offcanvas-end text-inverse" id="offcanvas-info" data-bs-scroll="true">
        <div class="offcanvas-header">
          <a href="./index.html"><img src="{!! asset('sandbox/assets/img/logo-light.png') !!}" srcset="{!! asset('sandbox/assets/img/logo-light@2x.png 2x') !!}" alt="" /></a>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <div class="widget mb-8">
            <p>Sandbox is a multipurpose HTML5 template with various layouts which will be a great solution for your business.</p>
          </div>
          <!-- /.widget -->
          <div class="widget mb-8">
            <h4 class="widget-title text-white mb-3">Contact Info</h4>
            <address> Moonshine St. 14/05 <br /> Light City, London </address>
            <a href="mailto:first.last@email.com">info@email.com</a><br /> 00 (123) 456 78 90
          </div>
          <!-- /.widget -->
          <div class="widget mb-8">
            <h4 class="widget-title text-white mb-3">Learn More</h4>
            <ul class="list-unstyled">
              <li><a href="#">Our Story</a></li>
              <li><a href="#">Terms of Use</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Contact Us</a></li>
            </ul>
          </div>
          <!-- /.widget -->
          <div class="widget">
            <h4 class="widget-title text-white mb-3">Follow Us</h4>
            <nav class="nav social social-white">
              <a href="#"><i class="uil uil-twitter"></i></a>
              <a href="#"><i class="uil uil-facebook-f"></i></a>
              <a href="#"><i class="uil uil-dribbble"></i></a>
              <a href="#"><i class="uil uil-instagram"></i></a>
              <a href="#"><i class="uil uil-youtube"></i></a>
            </nav>
            <!-- /.social -->
          </div>
          <!-- /.widget -->
        </div>
        <!-- /.offcanvas-body -->
      </div>
      <!-- /.offcanvas -->
    </header>
    <!-- /header -->
    <section class="wrapper bg-gradient-primary">
      <div class="container pt-10 pt-md-14 pb-8 text-center">
        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
          <div class="col-lg-7">
            <figure><img class="w-auto" src="{!! asset('sandbox/assets/img/illustrations/i2.png') !!}" srcset="{!! asset('sandbox/assets/img/illustrations/i2@2x.png 2x') !!}" alt="" /></figure>
          </div>
          <!-- /column -->
          <div class="col-md-10 offset-md-1 offset-lg-0 col-lg-5 text-center text-lg-start">
            <h1 class="display-1 mb-5 mx-md-n5 mx-lg-0">Rekonsiliasi Pertumbuhan Ekonomi</h1>
            <p class="lead fs-lg mb-7">Integrasi proses rekonsiliasi Pertumbuhan Ekonomi dengan mudah, efektif dan efisien.</p>
            <span><a class="btn btn-primary rounded-pill me-2" href="{{ url('upload/import') }}">Masuk</a></span>
          </div>
          <!-- /column -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container -->
    </section>
    <!-- /section -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="bg-navy text-inverse">
    <div class="container pt-15 pt-md-17 pb-13 pb-md-15">
      <div class="row gy-6 gy-lg-0">
        <div class="col-md-4 col-lg-3">
          <div class="widget">
            <p class="mb-4">© 2024 BPS Provinsi Sumatera Selatan. <br class="d-none d-lg-block" />All rights reserved.</p>
            <nav class="nav social social-white">
              <a href="#"><i class="uil uil-twitter"></i></a>
              <a href="#"><i class="uil uil-facebook-f"></i></a>
              <a href="#"><i class="uil uil-dribbble"></i></a>
              <a href="#"><i class="uil uil-instagram"></i></a>
              <a href="#"><i class="uil uil-youtube"></i></a>
            </nav>
            <!-- /.social -->
          </div>
          <!-- /.widget -->
        </div>
        <!-- /column -->
        <div class="col-md-4 col-lg-3">
          <div class="widget">
            <h4 class="widget-title text-white mb-3">Get in Touch</h4>
            <address class="pe-xl-15 pe-xxl-17">Moonshine St. 14/05 Light City, London, United Kingdom</address>
            <a href="mailto:#">info@email.com</a><br /> 00 (123) 456 78 90
          </div>
          <!-- /.widget -->
        </div>
        <!-- /column -->
        <div class="col-md-4 col-lg-3">
          <div class="widget">
            <h4 class="widget-title text-white mb-3">Learn More</h4>
            <ul class="list-unstyled  mb-0">
              <li><a href="#">About Us</a></li>
              <li><a href="#">Our Story</a></li>
              <li><a href="#">Projects</a></li>
              <li><a href="#">Terms of Use</a></li>
              <li><a href="#">Privacy Policy</a></li>
            </ul>
          </div>
          <!-- /.widget -->
        </div>
        <!-- /column -->
      </div>
      <!--/.row -->
    </div>
    <!-- /.container -->
  </footer>
  <div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
      <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
  </div>
  <script src="{!! asset('sandbox/assets/js/plugins.js') !!}"></script>
  <script src="{!! asset('sandbox/assets/js/theme.js') !!}"></script>
</body>

</html>