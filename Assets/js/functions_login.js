$('.login-content [data-toggle="flip"]').click(function() {
      	$('.login-box').toggleClass('flipped');
      	return false;
      });

     document.addEventListener("DOMContentLoaded", function(){
        if(document.querySelector("#formlogin")){
            let formLogin = document.querySelector("#formlogin");
            formLogin.onsubmit = function(e){
                e.preventDefault();
                let strEmail = document.querySelector("#txtEmail").value;
                let strPassword = document.querySelector("#txtPassword").value;

                if(strEmail == "" || strPassword == "")
                    {
                    swal("Por favor", "Escribe usuario y contraseña.", "error");
                    return false;
                    }else{
                        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                        var ajaxUrl = base_url+'/Login/loginUser';
                        var formData = new FormData(formLogin);
                        request.open("POST",ajaxUrl,true);
                        request.send(formData);

                        console.log(request);
                    }
            }
        }
     }, false);