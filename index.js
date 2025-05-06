
var app = {
    // Application Constructor
    initialize: function() {
        document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
        document.addEventListener("backbutton",  app.backButton, false);
    },

    // deviceready Event Handler
    //
    // Bind any cordova events here. Common events are:
    // 'pause', 'resume', etc.
    onDeviceReady: function() {
        if(device.platform == "Android"){ // Android 4.3 ve altını sapta
            var versions = device.version.split(".");
            if((parseInt(versions[0]) ==4 && parseInt(versions[1]) <= 3) || (parseInt(versions[0]) < 4)){
                resizeForOldAndroid(); // Görsel düzeltmeler uygula
            }
        }
        onInit["deviceReady"].apply(null, null);
    },

    // backButton Event Handler
    backButton: function() {
        ft_confirm("Uygulamadan çıkmak istediğinize Emin misiniz?",function(){
            navigator.app.exitApp();
        });
    },
    
    /*
    *   JSON formatında döndüren POST fonksiyonu.
    *   POST adresi, server_url [Kaynak:global.js]
    */
    post: async (to, data) => {
        await $.post(
            server_url + to, 
            data
        )
        .done(async (dt) => { result = await dt; })
        .fail(async (err) => { result = await err; });

        return $.parseJSON(result);
    },
    /*
    *   Gönderilmek istenen data JSON fornatında gelirse, 
    *   içeriğine 'usid' ve 'code' değerlerini yerletirip, 
    *   şifreleyerek gönderen özel POST fonksiyonu.
    *   POST adresi, server_url [Kaynak:global.js]
    */
    cryptedPost: async (to, data) => {
        var u_id = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");
    
        data['usid'] = await u_id;
        data['code'] = await code;
        var crypt_data = crypFT.convert(data);

        await $.post(
            server_url + to, 
            {q: crypt_data}
        )
        .done(async (dt) => { result = await dt; })
        .fail(async (err) => { result = await err; });
        
        return $.parseJSON(result);
    },

    /*
    *   JSON formatında döndüren get fonksiyonu.
    *   GET adresi, server_url [Kaynak:global.js]
    */
    get: async (to, data) => {
        await $.get(
            server_url + to,
            data
        )
        .done(async (dt) => { result = await dt; })
        .fail(async (err) => { result = await err; });

        return $.parseJSON(result);
    },
    /*
    *   Standart get fonksiyonu.
    */
    getS: async (to, data) => {
        await $.get(
            to, 
            data
        )
        .done(async (dt) => { result = await dt; })
        .fail(async (err) => { result = await err; });

        return result;
    },

    /*
    *   Gecikme fonksiyonu.
    */
    delay: async (time = 300) =>{
        return new Promise(function (resolve, reject) { setTimeout(resolve, time); });
    }

};

app.initialize();

$(".cmd-btn").click(function(){
    var t = $(this);
    var cmd = t.attr("cmd");
    var mode = t.attr("mod");
    var did = t.attr("did");
    dev[cmd].apply(null, [did, mode]);
});

$("#login #username,#login #password").focus(function () {
    $('#login .mdl-layout__header').hide();
});
$("#login #username,#login #password").focusout(function () {
    $('#login .mdl-layout__header').show();
});