$('.login-content [data-toggle="flip"]').click(function () {
    $('.login-box').toggleClass('flipped');
    return false;
});

var divLoading = document.querySelector("#divLoading");
document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector("#formlogin")) {
        let formLogin = document.querySelector("#formlogin");
        formLogin.onsubmit = function (e) {
            e.preventDefault();
            let strEmail = document.querySelector("#txtEmail").value;
            let strPassword = document.querySelector("#txtPassword").value;

            if (strEmail == "" || strPassword == "") {
                swal("Por favor", "Escribe usuario y contraseña.", "error");
                return false;
            } else {
                divLoading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url + 'Login/loginUser';
                var formData = new FormData(formLogin);
                request.open("POST", ajaxUrl, true);
                request.send(formData);

                request.onreadystatechange = function () {
                    if (request.readyState != 4) return;
                    divLoading.style.display = "none";

                    if (request.status == 200) {
                        try {
                            var objData = JSON.parse(request.responseText);
                            if (objData.status) {
                                window.location = base_url + 'dashboard';
                            } else {
                                swal("Atención", objData.msg, "error");
                                document.querySelector('#txtPassword').value = "";
                            }
                        } catch (error) {
                            console.error("Respuesta no válida del servidor:", request.responseText);
                            swal("Error del servidor", "Ocurrió un error interno. Revisa la consola o pestaña Network.", "error");
                        }
                    } else {
                        swal("Atención", "Error en el proceso", "error");
                    }
                    return false;
                }
            }
        }
    }
    if(document.querySelector("#formRecetPass")){
                    let formRecetPass = document.querySelector("#formRecetPass");
                    formRecetPass.onsubmit = function(e){
                        e.preventDefault();
                        let strEmail = document.querySelector("#txtEmailReset").value;     
                        if(strEmail == "")
                        {
                            swal("Por favor", "Escribe tu correo electrónico.", "error");
                            return false;
                        }else{
                            divLoading.style.display = "flex";
                            var request = (window.XMLDocument) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                            var ajaxUrl = base_url+'Login/resetPass';
                            var formData = new FormData(formRecetPass);
                            request.open("POST",ajaxUrl,true);
                            request.send(formData);

                            request.onreadystatechange = function(){
                                if(request.readyState != 4) return;
                                divLoading.style.display = "none";

                                if(request.status == 200){
                                   try {
                            var objData = JSON.parse(request.responseText);
                            if (objData.status) {
                                swal({
                                    title: "",
                                    text: objData.msg,
                                    type: "success",
                                    confirmButtonText: "Aceptar",
                                    closeOnConfirm: false,
                                }, function (isConfirm) {
                                    if (isConfirm) {
                                        window.location = base_url;
                                    }
                                });
                            } else {
                                swal("Atención", objData.msg, "error");
                            }
                        } catch (error) {
                            console.error("Respuesta no válida del servidor:", request.responseText);
                            swal("Error del servidor", "No se pudo procesar la solicitud.", "error");
                        }
                    } else {
                        swal("Atención", "Error en el proceso", "error");
                    }
                    return false;
                }
            }
        }
    }

                if(document.querySelector("#formCambiarPass")){
                    let formCambiarPass = document.querySelector("#formCambiarPass");
                    formCambiarPass.onsubmit = function(e){
                        e.preventDefault();

                        let strPassword = document.querySelector("#txtPassword").value;
                        let strPasswordConfirm = document.querySelector("#txtPasswordConfirm").value;

                        if (strPassword == "" || strPasswordConfirm == "") {
                            swal("Por favor", "Escribe la nueva contraseña.", "error");
                            return false;
                        }
                        if (strPassword.length < 8) {
                            swal("Atención", "La contraseña debe tener un mínimo de 8 caracteres.", "info");
                            return false;
                        }
                        if (strPassword != strPasswordConfirm) {
                            swal("Atención", "Las contraseñas no coinciden.", "info");
                            return false;
                        }

                        divLoading.style.display = "flex";
                        var request = (window.XMLDocument) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                        var ajaxUrl = base_url+'Login/setPassword';
                        var formData = new FormData(formCambiarPass);
                        request.open("POST",ajaxUrl,true);
                        request.send(formData);
                        request.onreadystatechange = function(){
                            if(request.readyState != 4) return;
                            divLoading.style.display = "none";

                            if(request.status == 200){
                                try {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Iniciar sesión",
                                closeOnConfirm: false,
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    window.location = base_url + 'login';
                                }
                            });
                        } else {
                            swal("Atención", objData.msg, "error");
                        }
                    } catch (error) {
                        console.error("Respuesta no válida del servidor:", request.responseText);
                        swal("Error del servidor", "No se pudo actualizar la contraseña.", "error");
                    }
                } else {
                    swal("Atención", "Error en el proceso", "error");
                }
            }
        }
    }
         }, false);   

