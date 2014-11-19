$(document).ready(function() 
{
    
    //login
    $('#btnLogIn').click(function(){
        
        
        var username=$.trim($("#loginUsername").val());
        var password=$.trim($("#loginPass").val());
        var dataString = 'action=login&username='+username+'&password='+password;
        
        if(username.length>0 && password.length>0)
            {
                //alert("Yaay");
                $.ajax({
                    type: "POST",
                    url: "php/userfunctions.php",
                    data: dataString,
                    cache: false,
                    beforeSend: function(){ 
                        $("#loginErrMsg").removeClass('loginErrMsg');
                        $("#loginErrMsg").addClass("loginScsMsg");
                        $("#loginErrMsg").text('Loging you in..');
                    },
                    
                    success: function(data){
                        $("#btnLogIn").val('Login');
                        if(data)
                            {
                               //alert(data);
                               switch (data) {
                                    case "ERR_WRONG_PASS":
                                        $("#loginErrMsg").removeClass('loginScsMsg');
                                        $("#loginErrMsg").addClass("loginErrMsg");
                                        $("#loginErrMsg").text('Incorrect Password');
                                        break; 
                                    case "ERR_WRONG_USER":
                                       $("#loginErrMsg").removeClass('loginScsMsg');
                                        $("#loginErrMsg").addClass("loginErrMsg");
                                        $("#loginErrMsg").text('No user by that username');
                                        break; 
                                    case "SUCCESS_LOGIN":
                                        $("#loginErrMsg").removeClass('loginErrMsg');
                                       $("#loginErrMsg").addClass("loginScsMsg");
                                        $("#loginErrMsg").text('Login successful. Redirecting..');
                                       window.location.href = "index.php";
                                        break;
                                }
                            }
                        else
                            {
                                 alert("Not working");
                                //Shake animation effect.
                               // $('#box').shake();
                                //$("#login").val('Login')
                                //$("#error").html("<span style='color:#cc0000'>Error:</span> Invalid username and password. ");
                            }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                             alert("Error: " + errorThrown); 
                    }
                    
                    
                });

            }
        else{
            $("#loginErrMsg").removeClass('loginScsMsg');
            $("#loginErrMsg").addClass("loginErrMsg");
            $("#loginErrMsg").text('Dont leave any empty field!');
        }
        return false;
    });
    
    
    //logout 
    $('#logout').click(function(){
        //alert("logout ");
        $.ajax({
                type: "POST",
                url: "php/userfunctions.php",
                data: "action=logout",
                cache: false,
                success: function(data){
                    //alert(data);
                    switch(data){
                         case "SUCCESS_LOGOUT":
                            window.location.href = "index.php";
                            break;
                            
                    }
                    
                }
        });
            
            
       
        return false;
    });
    
    
      //Register 
    $('#btnSignUp').click(function(){
        //alert("logout ");
        var firstname=$.trim($("#signUpFirstname").val());
        var lastname=$.trim($("#signUpLastname").val());
        var email=$.trim($("#signUpEmail").val());
        var username=$.trim($("#signUpUsername").val());
        var password1=$.trim($("#signUpPass1").val());
        var password2=$.trim($("#signUpPass2").val());
        
        if(firstname.length>0   && lastname.length>0 &&
           email.length>0       && username.length>0 &&
           password1.length>0   && password2.length>0){
             if(password1!=password2){
                 
                    $("#SingUpErrMsg").removeClass('loginScsMsg');
                    $("#SingUpErrMsg").addClass("loginErrMsg");
                    $("#SingUpErrMsg").text('Passwords do not match');
             }
            
            else{
                var dataString = 'action=register&firstname='+firstname+'&lastname='+lastname+'&email='+email+'&username='+username+'&password='+password1;
                $.ajax({
                type: "POST",
                url: "php/userfunctions.php",
                data: dataString,
                cache: false,
                beforeSend: function(){ 
                    $("#SingUpErrMsg").removeClass('loginErrMsg');
                    $("#SingUpErrMsg").addClass("loginScsMsg");
                    $("#SingUpErrMsg").text('Registering..');
                },

                success: function(data){
                   switch (data) {
                    case "SUCCESS_USER_REGISTERED":
                        $("#SingUpErrMsg").removeClass('loginErrMgs');
                        $("#SingUpErrMsg").addClass("loginScsMsg");
                        $("#SingUpErrMsg").text('Registration successful! Please wait..');
                        window.location.href = "index.php";
                        break; 
                    case "ERROR_SIGNUP":
                       $("#loginErrMsg").removeClass('loginScsMsg');
                        $("#loginErrMsg").addClass("loginErrMsg");
                        $("#loginErrMsg").text('Could not register, try again later');
                        break; 
                    case "ERROR_USERNAME_EXISTS":
                       $("#SingUpErrMsg").removeClass('loginScsMsg');
                        $("#SingUpErrMsg").addClass("loginErrMsg");
                        $("#SingUpErrMsg").text('Sorry, that username already exists!');
                      
                        break;
                 }
                  
                    
                }
             });
                
            }             
            
        }  
        
        else{
            $("#SingUpErrMsg").removeClass('loginScsMsg');
            $("#SingUpErrMsg").addClass("loginErrMsg");
            $("#SingUpErrMsg").text('Dont leave any empty field!');
        }
            
       
        return false;
    });

});