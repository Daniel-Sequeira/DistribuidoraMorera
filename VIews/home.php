<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Landing Page">
    <title>Landing Page</title>

    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="icons/icomoon.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

    <!-- Contenedor Header -->
    <header id="linkHome">
        <nav id="navFixed" class="navbar fixed-top navbar-expand-lg navbar-light bg-light p-2">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/images/DMLogo.png" alt="Logo" style="width: 150px;"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link scroll active" aria-current="page" href="#linkHome">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link scroll" href="#linkAbout">About</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link scroll" href="#linkContacto">Contacto</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- Fin Contenedor Header -->
    <!-- Contenedor Main -->
    <main>
         <!-- Slider Carousel -->
        <div id="carouselExampleCaptions" class="carousel slide">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="Assets\images\vecteezy_loading-goods-onto-electric-delivery-vehicle-for-sustainable_56863164.jpeg" class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Logística y Distribución</h5>
                        <p>Optimiza tus entregascon nuestra plataforma de gestión eficiente.</p>
                        <small>
                            <a href="https://es.vecteezy.com/fotos-gratis/log%C3%ADstica-y-distribuci%C3%B3n" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-underline">
                                Logística Y Distribución Fotos de Stock por Vecteezy
                            </a>
                        </small>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Second slide label</h5>
                        <p>Some representative placeholder content for the second slide.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Third slide label</h5>
                        <p>Some representative placeholder content for the third slide.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="imgHeader">
            <img src="images/landing_1.png" alt="">
        </div>
        </div>



        <!-- Sección Contacto -->
        <section class="boxContacto" id="linkContacto">
            <h3>Get In Touch</h3>

            <div class="container contacto">
                <form id="miForm">
                    <!-- Bootstrap 5 usa row en lugar de form-row -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="inputName" class="form-label"><b>First Name</b></label>
                            <input type="text" class="form-control" id="inputName" placeholder="First Name..." required>
                        </div>
                        <div class="col-md-6">
                            <label for="inputLastName" class="form-label"><b>Last Name</b></label>
                            <input type="text" class="form-control" id="inputLastName" placeholder="Last Name..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="inputEmail" class="form-label"><b>Email</b></label>
                        <input type="email" class="form-control" id="inputEmail" placeholder="Email..." required>
                    </div>

                    <div class="mb-3">
                        <label for="inputSubject" class="form-label"><b>Subject</b></label>
                        <input type="text" class="form-control" id="inputSubject" placeholder="Subject..." required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label"><b>Message</b></label>
                        <textarea class="form-control" rows="5" id="message" placeholder="Message..." required></textarea>
                    </div>

                    <button type="submit" class="botonEnviarMensaje">Send Message</button>
                </form>
            </div>
        </section>
        <!-- Fin Sección Contacto -->

    </main>
    <!-- Fin Contenedor Main -->

    <!-- Contenedor Footer -->
    <footer class="footer">
        <div class="boxTextFooter"></div>
    </footer>
    <!-- Fin Contenedor Footer -->

    <!-- Bootstrap 5 ya no usa jQuery ni Popper individual para el bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/dataApi.js"></script>
    <script src="js/app.js"></script>

</body>

</html>