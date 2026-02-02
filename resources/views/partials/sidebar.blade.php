<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand justify-content-center" href="{{ url('/') }}">        
        <img src="{{ asset('img/zc_logo-3.jpg') }}" class="sidebar-logo" style="width:100%;">
        <div class="sidebar-brand-text mx-3">GST Invoice</div>
    </a>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="{{ url('/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ url('/customers') }}">
            <i class="fas fa-file-invoice"></i>
            <span>Customers</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/invoices') }}">
            <i class="fas fa-file-invoice"></i>
            <span>Invoices</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/items') }}">
            <i class="fas fa-file-invoice"></i>
            <span>Product & Services</span>
        </a>
    </li>

</ul>
