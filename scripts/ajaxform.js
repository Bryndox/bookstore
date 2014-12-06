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
    
    //get all books
   $("#loadBooksBtn").click(function(){
       var elem=$(this);
       if(elem.hasClass("loaded")){
           return;
       }
       $.ajax({
                
                type: "POST",
                dataType: "json",
                url: "php/bookFunctions.php",
                data: "action=GET_ALL_BOOKS&page=1",
                cache: false,
                beforeSend: function(){ 
                    $("#loadBooksBtn").text('Loading. Please wait..');
                },

                success: function(data){
                    
                    var i;
                    var pagesList='<li>page</li>';
                    for (i = 1; i <= data["pages"]; i++) {
                            if(i==1){
                                pagesList += '<li class="book_list_page_no book_list_page_no_active">'+i+'</li>';
                            }
                        else
                            pagesList += '<li class="book_list_page_no">'+i+'</li>';
                    }
                    $(".list_page_numbers"). html(pagesList);
                    $(".list_page_numbers").css("background", "#34495E");
                   $("#mainBody_books_table").html(data["data"]);
                    elem.addClass("loaded");
                    $("#mainBody_books_table").css("visibility","visible" );
                    $("#loadBooksBtn").text('All The Books');
                    
                         //get section of books
                           $(".book_list_page_no").click(function(){
                               var listItem=$(this);
                                var page=listItem.text();
                               $.ajax({



                                        type: "POST",
                                        dataType: "json",
                                        url: "php/bookFunctions.php",
                                        data: "action=GET_ALL_BOOKS&page="+page,
                                        cache: false,
                                        beforeSend: function(){ 
                                            $("#loadBooksBtn").text('Fetching list..');
                                            $("#mainBody_books_table").css("opacity","0.5" );
                                            $(".book_list_page_no_active").removeClass("book_list_page_no_active");
                                        },

                                        success: function(data){
                                           
                                           $("#mainBody_books_table").html(data["data"]);
                                            $("#loadBooksBtn").text('All The Books');
                                            $("#mainBody_books_table").css("opacity","1" );
                                            
                                            listItem.addClass("book_list_page_no_active");
                                           //alert(data["pages"]);
                                        }
                                     });

                           });
                   //alert(data["pages"]);
                }
             });
       
   });
    
     //Enabling/disabling textboxes with radio buttons 
    $("input[type=radio]").click(function(){
        var choice=$(this).attr('value');
        switch(choice){
            case "user":
             $("#ratings_form_user").removeAttr("disabled"); 
             $("#ratings_form_user").css("color", "#000");  
             $("#ratings_form_book").attr("disabled", "disabled"); 
             $("#ratings_form_book").css("background", "#ededed");   
             $("#ratings_form_book").css("color", "#ededed");   
             break;
                
            case "book":
             $("#ratings_form_book").removeAttr("disabled"); 
             $("#ratings_form_book").css("background", "#fff");  
             $("#ratings_form_book").css("color", "#000");  
             $("#ratings_form_user").attr("disabled", "disabled"); 
             $("#ratings_form_user").css("background", "#ededed");
             $("#ratings_form_user").css("color", "#ededed");
            break;
        }
        
    });
    
     //Sesrching for ratings 
    $("#ratingsBtn").click(function(){
        
        var value;
        var elem=$("input[name=rating]:checked");
        var choice=elem.attr('value');
        
         switch(choice){
                case "user":
                 value=$.trim($("#ratings_form_user").val());
                 break;
                 
                 case "book":
                 value=$.trim($("#ratings_form_book").val());
                 break; 
                 
                 default: 
                 $("#ratingsFormErrMsg").text("Please select one option"); 
                 return false;     
         }
        
        if(value.length<1 ){
            
           $("#ratingsFormErrMsg").text("Enter a value.."); 
            return false;
        }
        $("#ratingsFormErrMsg").text(""); 
        $.ajax({
                type: "POST",
                dataType: "json",
                url: "php/bookFunctions.php",
                data: "action=GET_RATINGS&option="+choice+"&value="+value,
                cache: false,
                beforeSend: function(){ 
                
                    $("#ratingsBtn").val('Fetching list..');
                    $("#mainBody_ratings_table").css("opacity","0.5");
                },

                success: function(data){
                    // alert(data);
                     if(data["rows"]>0){
                         $(".ratings_info").text("There are "+data["rows"]+" total ratings");
                         var i;
                        var pagesList='<li>page</li>';
                        for (i = 1; i <= data["pages"]; i++) {
                                if(i==1){
                                    pagesList += '<li class="rating_list_page_no rating_list_page_no_active">'+i+'</li>';
                                }
                            else
                                pagesList += '<li class="rating_list_page_no">'+i+'</li>';
                        }
                    
                         $(".list_page_numbers_ratings"). html(pagesList);
                         $(".list_page_numbers_ratings").css("background", "#34495E");
                          $(".list_page_numbers_ratings").css("visibility", "visible");
                     }
                    else{
                        $(".ratings_info").text("");
                         $(".list_page_numbers_ratings").css("visibility", "hidden");
                    }
                     $("#ratingsBtn").val('Find Ratings');                     
                     $("#mainBody_ratings_table").html(data["data"]);
                      $("#mainBody_ratings_table").css("opacity","1");
                    
                    
                     //get section of ratings
                           $(".rating_list_page_no").click(function(){
                               var listItem=$(this);
                                var page=listItem.text();
                               $.ajax({



                                        type: "POST",
                                        dataType: "json",
                                        url: "php/bookFunctions.php",
                                        data: "action=GET_RATINGS&option="+choice+"&value="+value+"&page="+page,
                                        cache: false,
                                        beforeSend: function(){ 
                                    
                                            $("#mainBody_ratings_table").css("opacity","0.5" );
                                            $(".rating_list_page_no_active").removeClass("rating_list_page_no_active");
                                        },

                                        success: function(data){
                                           $("#mainBody_ratings_table").html(data["data"]);
                                            $("#mainBody_ratings_table").css("opacity","1" );
                                            
                                            listItem.addClass("rating_list_page_no_active");
                                           //alert(data["pages"]);
                                        }
                                     });

                           });
                }

        });
        return false;
    });
 

});