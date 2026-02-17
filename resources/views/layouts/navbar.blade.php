<nav class="navbar navbar-expand-lg navbar-dark bg-masjid shadow-sm">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2 brand-text" href="/">
            <span class="brand-icon">🕌</span>

            <span class="brand-title">
                <span class="d-block d-md-inline">Masjid Jami'</span>
                <span class="d-block d-md-inline">Abi Musa Al Asy'ari</span>
            </span>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse mt-3 mt-lg-0" id="navbarNav">
            <ul class="navbar-nav ms-auto text-center text-lg-start">

                <li class="nav-item">
                    <a class="nav-link menu-text" href="/beranda">Beranda</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-text" href="/">Jadwal Sholat</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-text" href="#agenda">Agenda</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-text" href="#pengumuman">Pengumuman</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-text" href="/admin/login">Admin</a>
                </li>

                @auth
                <li class="nav-item">
                    <a class="nav-link menu-text fw-semibold text-warning"
                       href="/admin/dashboard">
                        Dashboard
                    </a>
                </li>
                @endauth

            </ul>
        </div>

    </div>
</nav>


