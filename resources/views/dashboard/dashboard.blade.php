<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ✅ jQuery (Add this BEFORE your script uses $) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        <form id="txnForm" class="card card-body shadow">
            <div class="row g-4">
                <div class="col-md-4">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="text" name="amount" id="amount" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Type</label>
                    <div class="input-group">
                        <select name="type_id" id="type" class="form-select" required onchange="loadCategories()">
                            <option value="" disabled selected>Select a type</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->type_id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select name="category_id" id="category" class="form-select" required>
                        <option value="" disabled selected>Select a category</option>
                        @foreach ($categories as $ctgry)
                            <option value="{{ $ctgry->category_id }}">{{ $ctgry->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div><br>

            <div class="row g-4">
                <div class="col-md-4 d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                <i id="successIcon" class="bi bi-check-circle-fill text-success fs-4 d-none"></i>
                </div>
            </div>
        </form>

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

<script>
document.getElementById('amount').addEventListener('input', function (e) {
    let value = e.target.value;

    // Allow only numbers and a single decimal
    value = value.replace(/[^0-9.]/g, '');
    value = value.replace(/(\..*)\./g, '$1'); // prevent more than one dot

    e.target.value = value;
});

function loadCategories() {
    const typeSelect = document.getElementById('type');
    const selectedTypeId = typeSelect.value;

    const categorySelect = document.getElementById('category');

    // Reset category dropdown
    categorySelect.innerHTML = `<option value="" disabled selected>Select a category</option>`;

    if (!selectedTypeId) return;

    fetch(`/fetch-categories/${selectedTypeId}`)
        .then(response => response.json())
        .then(data => {
            data.sort((a, b) => a.name.localeCompare(b.name));
            data.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.category_id;
                option.textContent = cat.name;
                categorySelect.appendChild(option);
            });
        });
}

// Optional: preload on page load if a type is selected
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('type').value) {
        loadCategories();
    }
});

//Fetch every 10 seconds
setInterval(loadCategories, 10000);

//Initial load
loadCategories();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

//submit txn
$('#txnForm').on('submit', function(e) {
    e.preventDefault();

    let formData = $(this).serialize();

    $.post('/transaction', formData)
        .done(response => {
            // ✅ Clear the form
            $('#txnForm')[0].reset();

            // ✅ Reset category dropdown
            $('#category').html('<option value="" disabled selected>Select a category</option>');

            $('html, body').animate({ scrollTop: 0 }, 300);

            // ✅ Show success overlay
            $('#successOverlay').fadeIn(300);
            setTimeout(() => {
                $('#successOverlay').fadeOut(300);
            }, 2500);
        })
        .fail(xhr => {
            let html = `<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul>`;
            if (xhr.responseJSON?.errors) {
                $.each(xhr.responseJSON.errors, function(key, val) {
                    html += `<li>${val}</li>`;
                });
            } else {
                html += `<li>Unknown error occurred.</li>`;
            }
            html += `</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
            $('#alert-placeholder').html(html);
        });
});

</script>
@include('components.success-popup')
</body>
</html>