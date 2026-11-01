//Ingreso solo numeros
function controlTag(e){
    let tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8 || tecla == 0 || tecla == 9) return true;
    let patron = /[0-9\s]/;
    let n = String.fromCharCode(tecla);
    return patron.test(n);
}
//Validar nombres o apellidos
function testText(txtString){
    let stringText = new RegExp(/^[a-zA-ZÑñÁáÉéÍíÓóÚú\s]+$/);
    return stringText.test(txtString);
}

//Validar numeros enteros   
function testEntero(intCant){
    let intCantidad = new RegExp(/^[0-9]*$/);
    return intCantidad.test(intCant);
}

//Validar expresion regular para correo
function emailValidate(email){
    let stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9-]{2,4})+$/);
    return stringEmail.test(email);
}

//asignar funciones validacion a los inputs
function initValidations(){
    let validTextElements = document.querySelectorAll('.validText');
    validTextElements.forEach(function(element){
        element.removeEventListener('keyup', validateTextHandler);
        element.addEventListener('keyup', validateTextHandler);
    });

    let validNumberElements = document.querySelectorAll('.validNumber');
    validNumberElements.forEach(function(element){
        element.removeEventListener('keyup', validateNumberHandler);
        element.addEventListener('keyup', validateNumberHandler);
    });

    let validEmailElements = document.querySelectorAll('.validEmail');
    validEmailElements.forEach(function(element){
        element.removeEventListener('keyup', validateEmailHandler);
        element.addEventListener('keyup', validateEmailHandler);
    });
}

// Handlers separados para poder removerlos y evitar eventos duplicados
function validateTextHandler() {
    if(!testText(this.value)) this.classList.add('is-invalid');
    else this.classList.remove('is-invalid');
}

function validateNumberHandler() {
    if(!testEntero(this.value)) this.classList.add('is-invalid');
    else this.classList.remove('is-invalid');
}

function validateEmailHandler() {
    if(!emailValidate(this.value)) this.classList.add('is-invalid');
    else this.classList.remove('is-invalid');
}