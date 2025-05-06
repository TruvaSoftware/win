
var onClose = {

    intro: function(){
        // Intro ekranından çıkarken


    },

    login : function(){
        // Kullanıcı girişi ekranından çıkarken

        $("#login-form .require").each(function () { $(this).val(""); });
        $("#username, #password").parent().removeClass("is-focused");
    },

    home : function(){
        // Anasayfa ekranından çıkarken
        $("#place-room-list").empty();

        Page.hideMenu();
        Page.hideBottomPage();
    },

    users : function(){
        // Kullanıcılar ekranı kapanırken


    },
    addEvent: function(){
        $("#event-name").val("").parent().removeClass("is-dirty").removeClass("is-focused");
        $("#smart-switchs").val("");
        $("#event-add-form .mdl-tabs__tab,#event-add-form .mdl-tabs__panel").removeClass("is-active");
        $("#event-add-form .mdl-tabs__tab").eq(0).addClass("is-active");
        $("#lamps-panel").addClass("is-active");
        $("#event-add-form .text-error").each(function(){ $(this).html(""); });
    },
    editEvent: function(){
        $("#event-edit-form .text-error").each(function(){ $(this).html(""); });
        $("#event-edit-list .mdl-card__supporting-text").animate({ scrollTop: 0 }, 500);
    },
    addUser : function(){
        // Kullanıcı ekle ekranı kapanırken

        $("#user-register-form .require").each(function () { $(this).val("").parent(".mdl-textfield").removeClass("is-focused, is-dirty"); });
        $("#user-register-form .text-error").each(function () { $(this).html(""); });
    },

    masterUserRegister: function(){
        // Ana kullanıcı kayıt ekranı kapanırken


    },
    setHomeOutDelay: function(){
        // Evden çıkış süresi değiştirme sayfası kapanırken yapılacaklar

        $(".time-list-box .mdl-radio__button").each(function(index){ $(this).prop("checked", false).parent().removeClass('is-checked'); });
    },
    editDevice: function(){
        // Cihaz düzenleme sayfası kapanırken yapılacaklar
        $("#edit-device #dev-id, #edit-device #dev-name, #edit-device .text-error").empty();

    },
    editUser: function(){
        // Kullanıcı düzenleme sayfası kapanırken yapılacaklar

    },
    setWifi: async()=>{
        $("#set-wifi-form #wifi-ssid, #set-wifi-form #wifi-pass").val("").parent().removeClass("is-dirty").removeClass("is-focused");
    },
    setPrecision: async()=>{
        
    },
    settings: async()=>{

    },
    syncSmartHome: async()=>{

    },
    smartHomeList: async()=>{
        
    },
    addPlace: () =>{
        $("#place-name").val("").parent().removeClass("is-dirty").removeClass("is-focused");
        $("#place-add-form .text-error").each(function(){ $(this).html(""); });
        
    },
    addRoom: () =>{
        $("#room-name").val("").parent().removeClass("is-dirty").removeClass("is-focused");
        $("#room-sub").val("");
        $("#room-add-form .text-error").each(function(){ $(this).html(""); });

    },
    alarmRoom: async()=>{

        $("#stop-edit-btn").hide();

    },
    alarmDevice: async()=>{
        active_dev_page = await "";

    }
};

