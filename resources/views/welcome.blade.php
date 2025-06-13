<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Selamat Datang</title>
  <link rel="icon" href="{{ asset('assets/favicon/favicon.ico') }}" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <style>
    body {
      background-image: url('/assets/img/kdesa.png');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-family: 'Comic Sans MS', cursive, sans-serif;
    }
  </style>
</head>
<body onclick="window.location.href='/login'">

  <div class="text-center text-white position-fixed d-none d-sm-block top-0 end-0 p-3">
    <h4 class="fw-bold">Selamat Datang</h4>
    <span class="lead">"Awali hari dengan kehadiran, <br> lanjutkan dengan kontribusi, <br> akhiri dengan pencapaian."</span><hr>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
