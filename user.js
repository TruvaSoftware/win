
var user = {

    firstLogin: async () =>{
        var username = await window.localStorage.getItem("usern");
        var password = await window.localStorage.getItem("pass");
        var logkey = await window.localStorage.getItem("log-key");
        if(username ==null || password== null){
            login_status = await 0;
        }
        else{
            const data = await app.cryptedPost( "ajax/user/first_login.php", { 'username': username , 'password': password, 'log-key': logkey, 'log-firm-key':firm_key});

            if(data.status == "OK"){ 
                login_status = 1;
                console.log("Login successfully");
            }
            else if(data.status == "NO"){
                login_status = 0;
                console.log("Login broken");
            }
        }

        return login_status;
    },

    Login: async () =>{
        var errStatus = await 0;
        await $("#login-form .text-error").each(function (index) { $(this).html(""); });
        await $("#login-form .require").each(function (index) {
            var t = $(this);
            if(t.val() == "" || t.val() == null){ 
                $("#login-form .text-error").eq(index).html( t.attr("rel") +  " boş bırakılamaz"); 
                errStatus = 1;
            }
        });

        if(errStatus == 0){
            // Start Login
            const data = await app.cryptedPost( "ajax/user/login.php", $("#login-form").serializeArray());
            if(data.status == "OK"){
                await window.localStorage.setItem("usern",$("#username").val());
                await window.localStorage.setItem("pass",$("#password").val());
                await window.localStorage.setItem("mid",data.mid);
                await window.localStorage.setItem("uid",data.uid);
                await window.localStorage.setItem("sub",data.sub);
                await window.localStorage.setItem("log-key",$("#log-key").val());
                master_db_id =await user.getMasterUserID();
                await Page.route("home");
                login_status = await 1;
            }
            else if(data.status == "NO"){
                await Page.showSnackMessage(data.error);
                login_status = await 0;
            }
        }
    },


    forgotPassword: async ()=>{
        $("#forgot-mail-error, #forgot-password-success").html("");
        var mail = await $("#forgot-mail").val();
        const data = await app.cryptedPost(
            "ajax/user/forgot.php", 
            {
                'mail': mail
            }
        );        
        if(data.status == "OK"){
            $("#forgot-password-success").html(data.message);
        }
        else if(data.status == "NO"){
            $("#forgot-mail-error").html(data.error);
        }
    },
    
    qrLogin: async () =>{
        await cordova.plugins.barcodeScanner.scan( async (result) =>{
            if (!result.cancelled && result.format == "QR_CODE") {
                var data = await $.parseJSON(result.text)
                var pass = await $.md5(`Tekno${data.u.split("_")[1]}Win`);
                await console.log(pass);
                if(pass == data.p){
                    await $("#username").val(data.u).parent().addClass("is-focused");
                    await $("#password").val(data.p).parent().addClass("is-focused");
                    await user.Login();
                }
            }
        }, async (error) =>{ await Page.showSnackMessage(error); } , qr_settings );
    },

    Logout: function (confirm=false){
        if(confirm){
            tw_confirm("Çıkış yapmak istediğinize Emin Misiniz?", function(fback){
                if(fback==1){
                    var wifis = window.localStorage.getItem("wifis");
                    window.localStorage.clear();
                    window.localStorage.setItem("wifis", wifis);
                    login_status = 0;
                    Page.route("login");
                }
            });
        }
        else{
            var wifis = window.localStorage.getItem("wifis");
            window.localStorage.clear();
            window.localStorage.setItem("wifis", wifis);
            login_status = 0;
            Page.route("login");
        }
        
    },

    edit: async (a) =>{
        await $("#user-edit-form #user-id").val(a);
        await Page.route("editUser");
    },
    register: async () =>{
        var errStatus = await 0;
        await $("#user-register-form .text-error").each(function (index) { $(this).html(""); });

        const data = await app.cryptedPost( "ajax/user/user_add.php", $("#user-register-form").serializeArray());
        
        if(data.status == "OK"){
            //await alert("Kayıt Başarılı"); 
            await Page.showSnackMessage("Kullanıcı kaydedildi.");
            await Page.route('editAuthority',[data.uid]);
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#user-register-form #${key}-err`).html(value);
            });
            await Page.showSnackMessage("Kayıt başarısız.");
        }
    },
    
    getUsers: async (user_id) =>{
        var is_master_rv = {
            true:1,
            false:0
        }
        const data = await app.cryptedPost( "ajax/c_user.php", {'log-cmd':'get-users', 'utype': is_master_rv[is_master] , 'uid': user_id});

        if(data.status == "OK"){
            return data.data;
        }
        else if(data.status == "NO"){
            await Page.showSnackMessage("Kullanıcılar görüntülenemiyor.");
        }
    },

    masterUserRegister: async () =>{
        var errStatus = await 0;
        await $("#master-user-register-form .text-error").each(function (index) { $(this).html(""); });

        const data = await app.cryptedPost( "ajax/user/register.php", $("#master-user-register-form").serializeArray());
        
        if(data.status == "OK"){
            //await alert("Kayıt Başarılı"); 
            
            await Page.showSnackMessage("Ana kullanıcı kaydedildi.");

            await window.localStorage.setItem("usern",data.usr);
            await window.localStorage.setItem("pass",data.pas);
            await window.localStorage.setItem("mid",data.mid);
            await window.localStorage.setItem("uid",data.uid);
            await window.localStorage.setItem("sub",data.sub);
            await window.localStorage.setItem("log-key",data.code);

            await Page.route("home");
            
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#master-user-register-form #${key}-err`).html(value);
            });

            await Page.showSnackMessage("Kayıt başarısız.");
        }
    },

    editSave: async() =>{
        var errStatus =await 0;
        await $("#user-edit-form .text-error").each(function(){ $(this).html(""); });

        const data = await app.cryptedPost( "ajax/user/user_edit_save.php", $("#user-edit-form").serializeArray());
        
        if(data.status == "OK"){
            await Page.showSnackMessage("Değişiklikler kaydedildi.");
            await Page.route("users");
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#user-edit-form #${key}-err`).html(value);
            });
            await Page.showSnackMessage("Kayıt başarısız.");
        }
    },

    settingsSave: async() =>{
        var errStatus =await 0;
        await $("#user-settings-form .text-error").each(function(){ $(this).html(""); });

        const data = await app.cryptedPost( "ajax/user/user_settings_save.php", $("#user-settings-form").serializeArray());
        
        if(data.status == "OK"){
            await Page.showSnackMessage("Değişiklikler kaydedildi.");

            if(data.pass_change == 1){
                await window.localStorage.setItem("pass",$("#user-setting-password").val());
            }

            await Page.route("home");
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#user-settings-form #${key}-err`).html(value);
            });
            await Page.showSnackMessage("Kayıt başarısız.");
        }
    },
    getMasterUserID: function (){
        is_master = false;
        var uid = window.localStorage.getItem("mid");
        if(uid==0){ uid = window.localStorage.getItem("uid"); is_master = true; }

        return uid;
    },
    authoritySave: async()=>{
        const data = await app.cryptedPost( "ajax/user/set_authority.php", $("#edit-authority-form").serializeArray());
                 
        if(data.status == "OK"){
            await Page.showSnackMessage("Değişiklikler kaydedildi.");
            await Page.route("users");
        }
        else if(data.status == "NO"){
            await Page.showSnackMessage("Kayıt başarısız.");
        }
    },
    changeAuthorityText: ()=>{
        var authorities = "";
        $.each($(".authority-switchs"), function() {
            authorities += $(this).prop("checked") ? `,${$(this).val()}`:"";
        });
        $("#u-eauth-switchs").val(authorities);
    },
    delete: async(uid, name)=>{
        await ft_confirm(`${name} adlı kullanıcıyı silmek istediğinize Emin Misiniz?`,async()=>{
            const data =await  app.cryptedPost("ajax/user/user_del.php", { 'uid': uid});
            data.status == "OK"&& await $(`#trial-${uid}`).remove();
        });
    }
};

