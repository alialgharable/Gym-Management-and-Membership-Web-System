<!DOCTYPE html>
<html>

<head>
    <title>Gym System</title>
</head>

<body>

    <!-- NAVBAR -->
    <nav style="background: #333; padding: 10px;">
        <a href="/" style="color:white; margin-right:10px;">Home</a>

        <!-- Member Links -->
        <a href="/member/dashboard" style="color:white; margin-right:10px;">Member Dashboard</a>
        <a href="/bookings" style="color:white; margin-right:10px;">My Bookings</a>

        <!-- Trainer Links -->
        <a href="/trainer/dashboard" style="color:white; margin-right:10px;">Trainer Dashboard</a>

        <!-- Admin Links -->
        <a href="/admin/dashboard" style="color:white;">Admin Dashboard</a>
    </nav>

    <hr>

    <!-- PAGE CONTENT -->
    <div style="padding: 20px;">
        @yield('content')
    </div>

</body>

</html>