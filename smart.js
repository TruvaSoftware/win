
var smart = {
    login: async () =>{
        var errStatus = await 0;
        await $("#smart-login-form .text-error").each(function (index) { $(this).html(""); });
        await $("#smart-login-form .require").each(function (index) {
            var t = $(this);
            if(t.val() == "" || t.val() == null){ 
                $("#smart-login-form .text-error").eq(index).html(`${t.attr("rel")} boş bırakılamaz`); 
                errStatus = 1;
            }
        });

        if(errStatus == 0){

            const data = await app.cryptedPost( "ajax/smart/login.php", $("#smart-login-form").serializeArray());
            if(data.status == "OK"){
                await Page.showSnackMessage("Giriş Başarılı.");
                await window.localStorage.setItem("suid",data.suid);
                if($("#s-log-event-id").val()==null || $("#s-log-event-id").val()==""){
                    await Page.route('addEvent');
                }
                else{
                    await Page.route('editEvent', [$("#s-log-event-id").val()]);
                }
                
            }
            else if(data.status == "NO"){
                await Page.showSnackMessage(data.error);
            }
        }
    },
    save: async()=>{
        const data = await app.cryptedPost( "ajax/c_smarts.php",{'s-cmd': 'save', 's-mid': $("#s-mid").val(), 'list-switchs': $("#s-switchs").val()});
        if(data.status == "OK"){
            await Page.showSnackMessage("Kaydedildi.");
            await Page.route("home");
        }
        else if(data.status == "NO"){
            await Page.showSnackMessage(data.error);
        }
    },
    listStatus: async(id)=>{
        var rv = {true:false,false:true};
        var opt = await rv[$(`#list-checkbox-${id}`).parent().hasClass("is-checked")];
        if(opt){
            await $(`#smart-list-item-${id}`).addClass("lamp_list_item_active");
            await $(`#smart-list-item-${id} .mdl-switch`).removeClass("is-disabled");

            await $(`#list-switch-${id}`).prop("disabled",false);
            await smart.putSelectedLamps();

            await componentHandler.upgradeDom();

            

        }else{
            await $(`#smart-list-item-${id}`).removeClass("lamp_list_item_active");
            await $(`#smart-list-item-${id} .mdl-switch`).addClass("is-disabled");
            await $(`#list-switch-${id}`).prop("disabled",true);
            await smart.putSelectedLamps();

            await componentHandler.upgradeDom();

        }

    },
    putSelectedItems : function(){
        $("#smart-switchs").val("");
        var a = "";
        var c_type_smarts = {true:2,false:1};
        $.each($(".s-switch"), function(){
            if(!$(this).prop("disabled")){
                a +=`|${$(this).val()}-${c_type_smarts[$(this).prop("checked")]}`;
            }
        });
        $("#smart-switchs").val(`${a.substring(1, a.length)}`);
    },
    putSelectedItemsForEdit : function(){
        $("#smart-edit-switchs").val("");
        var a = "";
        var c_type_smarts = {true:2,false:1};
        $.each($(".s-edit-switch"), function(){
            if(!$(this).prop("disabled")){
                a +=`|${$(this).val()}-${c_type_smarts[$(this).prop("checked")]}`;
            }
        });
        $("#smart-edit-switchs").val(`${a.substring(1, a.length)}`);
    },
    toggleSub1: function(ind){
        $(`#sync-event-item-${ind}`).find(".acc_item_arr").toggleClass("arr_down");
        $(`#sync-event-content-${ind}`).toggleClass("acc_con_visible");
    },
    getSensorOptions: async()=>{
        var did = await $("#dev-slct").val();
        const data = await app.cryptedPost(
            "ajax/smart/get_sensors.php",
            { did: did}
        );
        var opts = await `<option value="0" typ="">Hepsi</option>`;
        if(data.status == "OK"){
            var sensors = await data.sensors;
            $.each(sensors, function(key, sensor){
                opts += `<option value="${sensor.id}" typ="${sensor.type}">${sensor.name}</option>`;
            });
            await $("#sen-slct").html(opts);
        }
    },
    getSensorOptionsForEdit: async()=>{
        var did = await $("#dev-edit-slct").val();
        const data = await app.cryptedPost(
            "ajax/smart/get_sensors.php",
            { did: did}
        );
        var opts = await `<option value="0" typ="">Hepsi</option>`;
        if(data.status == "OK"){
            var sensors = await data.sensors;
            $.each(sensors, function(key, sensor){
                opts += `<option value="${sensor.id}" typ="${sensor.type}">${sensor.name}</option>`;
            });
            await $("#sen-edit-slct").html(opts);
        }
    },
    checkLoginForAddEvent: async()=>{
        var suid = await window.localStorage.getItem("suid");
        if(suid == null || suid == undefined || suid == ""){
            await Page.showSnackMessage("Olay senaryosu ekleyebilmek için, Lütfen Akıllı Ev üyeliğinizle giriş yapınız.");
            await Page.route('smartLogin');
        }
        else{
            await Page.route('addEvent');
        }
    },
    toggleListItem: function(id){
        if($(`#list-checkbox-${id}`).prop("checked")){
            $(`#smart-list-item-${id}`).addClass("mdl-list__item_active");
            $(`#smart-item-content-${id}`).addClass("mdl-item-text_active");

            $(`#list-switch-${id}`).removeAttr("disabled").parent().removeClass("is-disabled");
            
        }else{
            $(`#smart-list-item-${id}`).removeClass("mdl-list__item_active");
            $(`#smart-item-content-${id}`).removeClass("mdl-item-text_active");
            $(`#list-switch-${id}`).attr("disabled","disabled").parent().addClass("is-disabled");
        }
        this.putSelectedItems();
    },
    toggleListItemForEdit: function(id){
        if($(`#list-edit-checkbox-${id}`).prop("checked")){
            $(`#smart-edit-list-item-${id}`).addClass("mdl-list__item_active");
            $(`#smart-edit-item-content-${id}`).addClass("mdl-item-text_active");

            $(`#list-edit-switch-${id}`).removeAttr("disabled").parent().removeClass("is-disabled");
            
        }else{
            $(`#smart-edit-list-item-${id}`).removeClass("mdl-list__item_active");
            $(`#smart-edit-item-content-${id}`).removeClass("mdl-item-text_active");
            $(`#list-edit-switch-${id}`).attr("disabled","disabled").parent().addClass("is-disabled");
        }
        this.putSelectedItemsForEdit();
    },
    eventSave: async()=>{
        await $("#event-add-form .text-error").each(function(){ $(this).html(""); });
        const data = await app.cryptedPost(
            "ajax/smart/event_save.php",
            $("#event-add-form").serializeArray()
        );
        if(data.status == "OK"){
            await Page.route('syncSmartHome');
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#event-add-form #${key}-err`).html(value);
            });
        }
    },
    eventEditSave: async()=>{
        await $("#event-edit-form .text-error").each(function(){ $(this).html(""); });
        const data = await app.cryptedPost(
            "ajax/smart/event_edit_save.php",
            $("#event-edit-form").serializeArray()
        );
        if(data.status == "OK"){
            await Page.route('syncSmartHome');
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#event-edit-form #${key}-err`).html(value);
            });
        }
    },
    deleteEvent: async(event_id)=>{
        await ft_confirm("Bu senkronizasyonu silmek istediğinize Emin Misiniz?",async()=>{
            const data =await  app.cryptedPost("ajax/smart/event_del.php", { 'eid': event_id});
            data.status == "OK"&& await $(`#sync-event-item-${event_id}, #sync-event-content-${event_id}`).remove();
        });
    },
    editEvent: async(event_id)=>{
        var suid = await window.localStorage.getItem("suid");
        if(suid == null || suid == undefined || suid == ""){
            await Page.showSnackMessage("Olay senaryosu ekleyebilmek için, Lütfen Akıllı Ev üyeliğinizle giriş yapınız.");
            await Page.route('smartLogin', [event_id]);
        }
        else{
            await Page.route('editEvent', [event_id]);
        }
    }

};

