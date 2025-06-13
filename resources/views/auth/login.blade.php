<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Form</title>
    <link rel="icon" href="{{ asset('assets/favicon/favicon.ico') }}" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body style="background-color: lightgray; font-family: 'Comic Sans MS', cursive, sans-serif;">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-top border-primary border-4 rounded-4" style="width: 25rem; border-right: none; border-bottom: none; border-left: none;">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/img/badak.png') }}" style="width: 140px;">
                </div>
                <h4 class="card-title text-center text-primary fw-bold mb-2">Pintu Masuk</h4>
                @if ($errors->any())
                    <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> Ih, kok salah? 🤪
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-user"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-check mt-2 ms-1">
                            <input type="checkbox" class="form-check-input" onclick="showHide()">
                            <label class="form-check-label" for="showPassword">Show Password</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100 py-2 rounded-3 mt-4">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script>
        function showHide() {
            var inputan = document.getElementById("password");
            if (inputan.type === "password") {
                inputan.type = "text";
            } else {
                inputan.type = "password";
            }
        }
    </script>
</body>
</html>