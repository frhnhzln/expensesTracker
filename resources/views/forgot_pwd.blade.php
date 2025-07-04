<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>

    <!-- ✅ Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h2 class="mb-4 text-center">Forgot Password</h2>
            <div id="alert-placeholder"></div>

            <form id="passwordForm" class="card card-body shadow">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" name="email" id="email" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ✅ Set CSRF Token for all AJAX requests -->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();

        let formData = {
            username: $('#username').val(),
            email: $('#email').val()
        };

        $.post('/email-process', formData)
            .done(response => {
                $('#alert-placeholder').html(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>${response.message}</strong><br>
                        ${response.qr ? `<img src="${response.qr}" class="mt-2 border rounded shadow-sm" style="width:200px;">` : ''}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
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
</body>
</html>
