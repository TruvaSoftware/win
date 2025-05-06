var place = {
    save: async () =>{
        await $("#place-add-form .text-error").each(function(){ $(this).html(""); });
        const data = await app.cryptedPost("ajax/place/place_add.php", $("#place-add-form").serializeArray());
        if(data.status == "OK"){
            await Page.route("home");
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#place-add-form #${key}-err`).html(value);
            });
        }
    },
    roomSave: async () =>{
        await $("#room-add-form .text-error").each(function(){ $(this).html(""); });
        const data = await app.cryptedPost("ajax/place/room_add.php", $("#room-add-form").serializeArray());
        if(data.status == "OK"){
            await Page.route("home");
        }
        else if(data.status == "NO"){
            await $.each( data.error, function( key, value ) {
                $(`#room-add-form #${key}-err`).html(value);
            });
        }
    },
    toggle: function(index){
        var t = $(`#place-content-${index}`);
        t.toggleClass("place-content-extend");
        var arrow = t.hasClass("place-content-extend") ? "keyboard_arrow_up" : "keyboard_arrow_down";
        $(`#place-item-${index} .mdl-list__item-secondary-action .material-icons`).html(arrow);
        if(t.hasClass("place-content-extend")){
            $(`#place-item-${index} .mdl-list__item-secondary-content`).hide();
            $(`#room-add-btn-${index}`).show();
            $.each($(`#place-content-${index} .room-list .room`), function(){ $(this).addClass("room-visible"); });
        }
        else{
            $(`#room-add-btn-${index}`).hide();
            $(`#place-item-${index} .mdl-list__item-secondary-content`).show();
            $.each($(`#place-content-${index} .room-list .room`), function(){ $(this).removeClass("room-visible"); });
        }

    },
    delete: async(id)=>{
        await ft_confirm("Bu yeri silmek istediğinize Emin Misiniz?\r\n UYARI: Bu yere ait, tüm odalar ve cihazlar da silinecektir!",async()=>{
            const data =await  app.cryptedPost("ajax/place/place_del.php", { 'pid': id});
            data.status == "OK"&& await $(`#place-item-${id}, #place-cont-${id}`).remove();
        });
    },
    deleteRoom: async(pid, rid)=>{
        await ft_confirm("Bu odayı silmek istediğinize Emin Misiniz?\r\n UYARI: Bu odaya ait tüm cihazlar da silinecektir!",async()=>{
            const data =await  app.cryptedPost("ajax/place/room_del.php", { 'pid': pid, 'rid': rid});
            data.status == "OK"&& await $(`#room-item-${pid}-${rid}, #room-cont-${pid}-${rid}`).remove();
        });
    },
    deleteDevice: async(pid, rid, did)=>{
        await ft_confirm("Bu cihazı silmek istediğinize Emin Misiniz?\r\n UYARI: Bu cihaza ait tüm sensörler de silinecektir!",async()=>{
            const data =await  app.cryptedPost("ajax/place/device_del.php", { 'pid': pid, 'rid': rid, 'did': did});
            data.status == "OK"&& await $(`#device-item-${pid}-${rid}-${did}`).remove();
        });
    },
    edit: function(index){
        $(`#edit-pla-label-${index}, #edit-pla-text-${index}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-${index}, #edit-pla-ok-btns-${index}`).toggleClass("btns-visible");
    },
    editOK: async(index)=>{
        const data =await app.cryptedPost("ajax/place/place_edit.php", { 'pid': index, 'name': $(`#edit-pla-input-${index}`).val()});
        if(data.status=="OK"){
            $(`#edit-pla-label-${index} span`).html($("#edit-pla-input-${index}").val());
            $(`#edit-pla-label-${index}, #edit-pla-text-${index}`).toggleClass("text-label-visible");
            $(`#edit-pla-cmd-btns-${index}, #edit-pla-ok-btns-${index}`).toggleClass("btns-visible");
        }
    },
    editNO: function(index){
        $(`#edit-pla-label-${index},#edit-pla-text-${index}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-${index},#edit-pla-ok-btns-${index}`).toggleClass("btns-visible");
    },
    edit2: function(index, index2){
        $(`#edit-pla-label-${index}-${index2},#edit-pla-text-${index}-${index2}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-${index}-${index2},#edit-pla-ok-btns-${index}-${index2}`).toggleClass("btns-visible");
    },
    editOK2: async(index, index2)=>{
        const data =await app.cryptedPost("ajax/place/room_edit.php", { 'pid': index, 'rid': index2, 'name': $(`#edit-pla-input-${index}-${index2}`).val()});
        if(data.status=="OK"){
            $(`#edit-pla-label-${index}-${index2} span`).html($(`#edit-pla-input-${index}-${index2}`).val());
            $(`#edit-pla-label-${index}-${index2},#edit-pla-text-${index}-${index2}`).toggleClass("text-label-visible");
            $(`#edit-pla-cmd-btns-${index}-${index2},#edit-pla-ok-btns-${index}-${index2}`).toggleClass("btns-visible");
        }
    },
    editNO2: function(index, index2){
        $(`#edit-pla-label-${index}-${index2},#edit-pla-text-${index}-${index2}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-${index}-${index2},#edit-pla-ok-btns-${index}-${index2}`).toggleClass("btns-visible");
    },
    edit3: function(index, index2, index3){
        $(`#edit-pla-label-${index}-${index2}-${index3},#edit-pla-text-${index}-${index2}-${index3}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-${index}-${index2}-${index3},#edit-pla-ok-btns-${index}-${index2}-${index3}`).toggleClass("btns-visible");
    },
    editOK3: async(index, index2, index3)=>{
        const data =await app.cryptedPost("ajax/place/device_edit.php", { 'pid': index, 'rid': index2, 'did': index3, 'name': $(`#edit-pla-input-${index}-${index2}-${index3}`).val()});
        if(data.status=="OK"){
            $(`#edit-pla-label-${index}-${index2}-${index3} span`).html($(`#edit-pla-input-${index}-${index2}-${index3}`).val());
            $(`#edit-pla-label-${index}-${index2}-${index3},#edit-pla-text-${index}-${index2}-${index3}`).toggleClass("text-label-visible");
            $(`#edit-pla-cmd-btns-${index}-${index2}-${index3},#edit-pla-ok-btns-${index}-${index2}-${index3}`).toggleClass("btns-visible");
        }
    },
    editNO3: function(index, index2, index3){
        $(`#edit-pla-label-${index}-${index2}-${index3},#edit-pla-text-${index}-${index2}-${index3}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-${index}-${index2}-${index3},#edit-pla-ok-btns-${index}-${index2}-${index3}`).toggleClass("btns-visible");
    },
    edit4: function(index, index3){
        $(`#edit-pla-label-b-${index}-${index3},#edit-pla-text-b-${index}-${index3}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-b-${index}-${index3},#edit-pla-ok-btns-b-${index}-${index3}`).toggleClass("btns-visible");
    },
    editOK4: async(index, index3)=>{
        const data =await app.cryptedPost("ajax/place/device_edit.php", { 'pid': index, 'rid': 0, 'did': index3, 'name': $(`#edit-pla-input-b-${index}-${index3}`).val()});
        if(data.status=="OK"){
            $(`#edit-pla-label-b-${index}-${index3} span`).html($(`#edit-pla-input-b-${index}-${index3}`).val());
            $(`#edit-pla-label-b-${index}-${index3},#edit-pla-text-b-${index}-${index3}`).toggleClass("text-label-visible");
            $(`#edit-pla-cmd-btns-b-${index}-${index3},#edit-pla-ok-btns-b-${index}-${index3}`).toggleClass("btns-visible");
        }
    },
    editNO4: function(index, index3){
        $(`#edit-pla-label-b-${index}-${index3},#edit-pla-text-b-${index}-${index3}`).toggleClass("text-label-visible");
        $(`#edit-pla-cmd-btns-b-${index}-${index3},#edit-pla-ok-btns-b-${index}-${index3}`).toggleClass("btns-visible");
    },
    toggleSub1: function(ind){
        $(`#place-item-${ind}`).find(".acc_item_arr").toggleClass("arr_down");
        $(`#place-cont-${ind}`).toggleClass("acc_con_visible");
    },
    toggleSub2: function(ind1, ind2){
        $(`#room-item-${ind1}-${ind2}`).find(".acc_item_arr").toggleClass("arr_down");
        $(`#room-cont-${ind1}-${ind2}`).toggleClass("acc_con_visible");
    }
};

