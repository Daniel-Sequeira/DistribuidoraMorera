<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Distribuidora Morera">
    <title>Distribuidora Morera</title>

    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="icons/icomoon.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
</head>

<body>
    <!-- Navbar Bootstrap 5 -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <img src="<?php echo media(); ?>images/DMLogo.png" alt="Logo" style="width: 70px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?php echo base_url(); ?>Login">Login <span class="visually-hidden">(current)</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <!-- Carrusel -->
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
            <!-- Indicadores -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            </div>
            <!-- Slider Carousel -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?= media(); ?>images/vecteezy_trucks.jpeg" class="d-block w-100" style="height: 650px; object-fit: cover;" alt="Slide 1">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Distribución y Logística</h5>
                        <p>Líderes en la zona norte</p>
                    </div>
                </div>

                <div class="carousel-item">
                    <img src="<?= media(); ?>images/vecteezy_vehicle.jpeg" class="d-block w-100" style="height: 650px; object-fit: cover;" alt="Slide 2">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Distribución y Logística</h5>
                        <p>Líderes en la zona norte.</p>
                    </div>
                </div>
            </div>

            <!-- Controles -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <br><br>

        <!-- Tarjetas Bootstrap 5  -->
        <div class="container">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100">
                        <img src="<?= media(); ?>images/card1.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Control</h5>
                            <p class="card-text"> Tu logística en nuestras manos.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <img src="<?= media(); ?>images/card2.jpg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Confianza</h5>
                            <p class="card-text">Responsabilidad durante todo el proceso.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <img src="<?= media(); ?>images/card3.jpg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Seguridad</h5>
                            <p class="card-text">Empaque seguro y amigable con el ambiente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="boxTextFooter text-center">
            <p class="mb-0">Somos tu mejor opción</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/dataApi.js"></script>
    <script src="js/app.js"></script>

</body>

</html>