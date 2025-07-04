<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid justify-content-between">
        <!-- Left Submenu Dropdown -->
        <div class="dropdown">
            <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-list fs-5"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Dashboard</a></li>
                <li><a class="dropdown-item" href="#">Reports</a></li>
                <li><a class="dropdown-item" href="#">Categories</a></li>
            </ul>
        </div>

        <!-- Right Profile Dropdown -->
        <div class="dropdown">
            <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1 fs-5"></i> {{ Auth::user()->username }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <div class="container my-5">
        <h2 class="mb-4">Welcome to your Dashboard</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-white bg-success shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Income</h5>
                        <p class="card-text">RM 5,000.00</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Expenses</h5>
                        <p class="card-text">RM 3,200.00</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info shadow">
                    <div class="card-body">
                        <h5 class="card-title">Current Balance</h5>
                        <p class="card-text">RM 1,800.00</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <h4>Recent Transactions</h4>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2025-06-22</td>
                    <td>Expense</td>
                    <td>Food</td>
                    <td>RM 25.00</td>
                </tr>
                <tr>
                    <td>2025-06-21</td>
                    <td>Income</td>
                    <td>Salary</td>
                    <td>RM 2,000.00</td>
                </tr>
                <tr>
                    <td>2025-06-20</td>
                    <td>Expense</td>
                    <td>Transportation</td>
                    <td>RM 10.00</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
