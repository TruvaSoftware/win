
var Page = {

    route: async (id, params = null) =>{
        await Page.showPreloader();
        await listener.stop();
        if(id=="intro" || id=="login" || id=="deviceReady" || id=="setWifi" || id=="masterUserRegister" || id=="forgotPassword"){
            await Page.routeCore(id, params);
        }else{
            await listener.start();
            if(active_page != "login" && active_page != "intro"){
                const checkLoginOnRoute = await user.firstLogin();

                if(checkLoginOnRoute == 1){ await Page.routeCore(id, params); } else{ await user.Logout(); }
            }
            else{ await Page.routeCore(id, params); }
        }
    },

    routeCore: async(id ,params)=>{
        
        (typeof onClose[active_page] === "function")&&await onClose[active_page].apply(null, null);
        (typeof onInit[id] === "function")&&await onInit[id].apply(null, params);
        await Page.change(id);
        await setTimeout(function(){ Page.hidePreloader(); },300);
    },

    change: async(id)=>{
        if( $(`#${id}`).length == 1 ){
            await $(".ft-page").removeClass("ft-page-active")
            await $(`#${id}`).addClass("ft-page-active")
            active_page = await id;
        }
        else if( $(`#${id}`).length < 1 ){
            console.log("Böyle bir sayfa bulunamadı.");
        }
        else{
            console.log("Aynı ID'yle birden fazla sayfa oluşturamazsın.");
        }
    },
    getAvaibleMenu: async () =>{
        const mid = await window.localStorage.getItem("mid");
        return  mid == 0 ? await masterUserMenu : await userMenu;
    },

    showPreloader: function(){
        $("#preLoader").addClass("loader-active");
    },

    hidePreloader: function(){
        $("#preLoader").removeClass("loader-active")
    },

    showBottomPage: function(){
        $("#sensors").addClass("ft-bottom-page-active");
    },

    hideBottomPage:  function(){
        $("#sensors").removeClass("ft-bottom-page-active");
    },

    showSnackMessage:  function(m, hold=3000){
        document.querySelector("#ft-snack-message").MaterialSnackbar.showSnackbar({message: m, timeout: hold});
    },
    showBottomPageButton: function(){
        $("#stop-alarm-btn-box").show();
        $("#sensors .ft-bottom-page-content").css("height","calc(100vh - 335px)");

    }, 

    hideMenu: function(){
        $(".mdl-layout__drawer-button").attr("aria-expanded",true);
        $(".mdl-layout__drawer").removeClass("is-visible").attr("aria-hidden",true);
        $(".mdl-layout__obfuscator").removeClass("is-visible");
    },

    hideBottomPageButton: function(){
        $("#stop-alarm-btn-box").hide();
        $("#sensors .ft-bottom-page-content").css("height","calc(100vh - 265px)");
    },
    enableModBtns: function(){
        $("#home-in-btn, #home-out-btn, #night-btn").prop("disabled", false).removeClass("mod-btn-passive").addClass("mod-btn-active");
    },
    disableModBtns: function(){
        $("#home-in-btn, #home-out-btn, #night-btn").prop("disabled", true).removeClass("mod-btn-active").addClass("mod-btn-passive");
    },
    enableSMBtns: function(){
        $("#m-set-active-btn, #m-set-passive-btn").prop("disabled", false).removeClass("mod-btn-passive").addClass("mod-btn-active");
    },
    disableSMBtns: function(){
        $("#m-set-active-btn, #m-set-passive-btn").prop("disabled", true).removeClass("mod-btn-active").addClass("mod-btn-passive");
    },
    centerBtnSetActive: function(){
        $("#center-btn").removeClass("bg-warning").removeClass("center-btn-bg-passive").addClass("center-btn-bg-active");
        $("#center-btn .center-circle").removeClass("circle-passive").removeClass("circle-warning").addClass("circle-active");

    },
    centerBtnSetPassive: function(){
        $("#center-btn").removeClass("bg-warning").removeClass("center-btn-bg-active").addClass("center-btn-bg-passive");
        $("#center-btn .center-circle").removeClass("circle-active").removeClass("circle-warning").addClass("circle-passive");

    },
    centerBtnSetWarning: function(){
        $("#center-btn").removeClass("center-btn-bg-active").removeClass("center-btn-bg-passive").addClass("bg-warning");                        
        $("#center-btn .center-circle").removeClass("circle-passive").removeClass("circle-active").addClass("circle-warning");
    },
    reverseSMBtns: function(a, p){
        $(a).prop("disabled", false).removeClass("mod-btn-passive").addClass("mod-btn-active");
        $(p).prop("disabled", true).removeClass("mod-btn-active").addClass("mod-btn-passive");
    }
};

