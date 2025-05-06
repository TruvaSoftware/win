
var onInit = {
    deviceReady: async () =>{
        // Uygulama ilk açıldığında yapılacaklar

        await FCMPlugin.onTokenRefresh(function(token){
            if(token !="NULL"){
                n_token = token;
            }
        });
        await FCMPlugin.getToken(function(token){
            if(token !="NULL"){
                n_token = token;
                console.log(token);
            }
        });
        await FCMPlugin.onNotification(function(data){
         
            if (null==data){
                alert("data is null");
            }else{
                if(data.wasTapped){
                    //Notification was received on device tray and tapped by the user.
                    console.log( JSON.stringify(data) );
                    Page.showSnackMessage(data.body);
                }else{
                    //Notification was received in foreground. Maybe the user needs to be notified.
                    console.log( JSON.stringify(data) );
                    Page.showSnackMessage(data.body);
                }         
            }
        });
        //Subscribe to topic must be last or other callback will not work
        await FCMPlugin.subscribeToTopic('teknowin_global');

        const checkLogin = await user.firstLogin();

        if(checkLogin == 1){
            await app.delay(1000);
            master_db_id =await user.getMasterUserID();
            await Page.route("home");
        }else{
            await app.delay(1000);
            await user.Logout();
        }
        my_media = await new Media(alert_sound_path,function () { console.log("Audio Success"); },function (err) { console.log("Audio Error: " + JSON.stringify(err)); } );

    },
    login : async () =>{
        // Kullanıcı giriş sayfası açıldığında yapılacaklar
        await $("#login-form #log-key").val(device.uuid);
        await $("#login-form #log-token").val(n_token);
        await $("#login-form #log-firm-key").val(firm_key);
    },
    home : async () =>{
        // Ana sayfa açıldığında yapılacaklar

        const username = await window.localStorage.getItem("usern");
        await $("#menu-uname").html(username);

        const menu = await Page.getAvaibleMenu();
        await $("#menu").html(menu);


        await $("#home-settings-menu").html(
            `<li class="mdl-menu__item" onclick="Page.route('addPlace')">Yer Ekle</li>`
        );

        const data = await app.cryptedPost(
            "ajax/place/place_list.php",
            {
                'mid': master_db_id
            }
        );
        if(data.status == "OK"){
            const places = await data.places;

            var html = await "";
            await $.each(places, function(key, value){
                html +=
                        `<div id="place-item-${value.id}" class="mdl-list__item place-item">
                            <span class="mdl-list__item-primary-content" onclick="place.toggle(${value.id})">
                                <img src="img/home.png">
                                <span>${value.name}</span>
                            </span>
                            <button id="room-add-btn-${value.id}" class="mdl-button mdl-js-button mdl-button--icon" style="display: none">
                                <i class="material-icons">more_vert</i>
                            </button>
                            <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="room-add-btn-${value.id}">
                                <li class="mdl-menu__item" onclick="dev.addDevice(${value.id}, 0)">Alarm Ekle</li>
                                <li class="mdl-menu__item" onclick="Page.route('addRoom',[${value.id}])">Oda Ekle</li>
                            </ul>
                            <span class="mdl-list__item-secondary-content place-item-content" onclick="place.toggle(${value.id})">
                                <a class="mdl-list__item-secondary-action" href="#"><i class="material-icons">keyboard_arrow_down</i></a>
                            </span>                                            
                        </div>
                        <div id="place-content-${value.id}" class="mdl-list__item_content place-item-content">
                            <div class="room-list">`;
                var alarms = value.alarms;
                $.each(alarms, function(alarm_key, alarm_value){
                    html +=
                        `<div class="room btn-alarm" onclick="Page.route('alarmDevice', [${alarm_value.id}, ${value.id}, 0])">
                            <img src="img/sensors/master.png">
                            <span>${alarm_value.name}</span>
                        </div>`;
                });
                var rooms = value.rooms;
                $.each(rooms, function(room_key, room_value){
                    html +=
                        `<div class="room" onclick="Page.route('alarmRoom', [${value.id}, ${room_value.id}])">
                            <img src="img/room.png">
                            <span>${room_value.name}</span>
                        </div>`;
                });
                html += `</div></div>`;

            });
            await $("#place-room-list").html(html);
            await $.each($("#place-list .mdl-list__item"), function(index){
                var t = $(this);
                    setTimeout(function(){
                        $("#place-list .mdl-list__item").eq(index).addClass("place-list-item-visible");
                    },175*index);
            });
            await $.each($(".room-list"), function(){
                var t = $(this);
                t.css("width", ((t.find(".room").length *110) + 10)+"px");
            }); 
        }


        await componentHandler.upgradeDom();
        

    },
    users : async () =>{
        // Kullanıcılar sayfası açıldığında yapılacaklar

        var html ="";
        const data = await app.cryptedPost( "ajax/user/user_list.php", {'uid': master_db_id});
        if(data.status=="OK"){
            var u_data = await data.data;
            if(!is_empty(u_data)){
                var ln = await u_data.length;
                for(var i = 0 ; i < ln ; i++){
    
                    html += await `<div id="trial-${u_data[i].id}" class="mdl-list__item mdl-list__item--two-line">
                                        <span class="mdl-list__item-primary-content">
                                            <i class="material-icons mdl-list__item-avatar">person</i>
                                            <span>${u_data[i].name} ${u_data[i].lastname}</span>
                                            <span class="mdl-list__item-sub-title">${u_data[i].username}</span>
                                        </span>
                                        <button id="users-settings-btn-${u_data[i].id}" class="mdl-button mdl-js-button mdl-button--icon">
                                            <i class="material-icons">more_vert</i>
                                        </button>
                                        <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="users-settings-btn-${u_data[i].id}">
                                            <li class="mdl-menu__item" onclick="user.edit(${u_data[i].id})">Bilgileri Düzenle</li>
                                            <li class="mdl-menu__item" onclick="Page.route('editAuthority',[${u_data[i].id}])">Yetkileri Düzenle</li>
                                            <li class="mdl-menu__item" onclick="user.delete(${u_data[i].id}, '${u_data[i].name} ${u_data[i].lastname}')">Kullanıcıyı Sil</li>
                                        </ul>
                                    </div>`;
                }
                await $("#user-list").empty().append(html);

                await $.each($("#user-list .mdl-list__item"), function(index){
                    var t = $(this);
                        setTimeout(function(){
                            t.addClass("users-list-item-visible");
                        },175*index);
                });
            }
            else{
                await $("#user-list").empty();
            }
            await componentHandler.upgradeDom();

        }
    },

    addUser : async () =>{
        // Kullanıcı ekle sayfası açıldığında yapılcaklar
        var uid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");

        await $("#user-sub").val(master_db_id);
        await $("#reg-firm-key").val(firm_key);
        await $("#u-reg-usid").val(uid);
        await $("#u-reg-code").val(code);
        

    },
    masterUserRegister: async() =>{
        // Ana kullanıcı oluştur sayfası açıldığında yapılacaklar
        await $("#master-user-register-form #mreg-code").val(device.uuid);
        await $("#master-user-register-form #mreg-token").val(n_token);
        await $("#master-user-register-form #mreg-firm-key").val(firm_key);

    },
    setHomeOutDelay: async(did)=>{
        // Evden çıkış süresi değiştirme sayfası açıldığında yapılacaklar

        const data = await app.cryptedPost(
            "ajax/device/get_out_time.php",
            {
                'did': did
            }
        );

        if(data.status == "OK"){
            await $(".time-list-box .mdl-radio__button").each(function(index){ $(this).prop("checked", false).parent().removeClass('is-checked'); });
            await $(`#set-time-${data.time}`).parent().addClass('is-checked');
            await $("#time-set-dev-id").val(data.did);
            await componentHandler.upgradeDom();
        }
    },
    editDevice: async(id, name, pid, rid, mid=0) =>{
        // Cihaz düzenleme sayfası açıldığında yapılacaklar

        await $("#editDevice #dev-id").val(id).parent().addClass("is-focused");
        await $("#editDevice #dev-name").val(name).parent().addClass("is-focused");

        var uid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");

        await $("#edit-dev-usid").val(uid);
        await $("#edit-dev-code").val(code);
        
        if(mid==0){
            $("#editDevice-back-btn").attr("onclick",`Page.route('alarmDevice', [${id}, ${pid}, ${rid}])`);
        }
        else{
            $("#editDevice-back-btn").attr("onclick",`Page.route('alarmDevice', [${mid}, ${pid}, ${rid}])`);
        }
        $("#dev-edit-save-btn").attr("onclick",`dev.editSave(${id}, ${pid}, ${rid})`);

        
    },
    editUser: async()=>{
        // Kullanıcı düzenleme sayfası açıldığında yapılacaklar

        var uid = await $("#user-edit-form #user-id").val();
        var usid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");

        await $("#ue-reg-usid").val(usid);
        await $("#ue-reg-code").val(code);

        const data = await app.cryptedPost(
            "ajax/user/get_user_info.php", {
                'log-cmd': 'get-user-info',
                'uid': uid
            }
        );

        if(data.status == "OK"){
            await $("#u-edit-name").val(data.name).parent().addClass("is-focused");
            await $("#u-edit-lastname").val(data.lastname).parent().addClass("is-focused");
            await $("#u-edit-username").val(data.username).parent().addClass("is-focused");
            await $("#u-edit-email").val(data.mail).parent().addClass("is-focused");
            await $("#u-edit-cellp").val(data.cellphone).parent().addClass("is-focused");
            await componentHandler.upgradeDom();
        }
    },
    userSettings:async()=>{
        // Kullanıcı düzenleme sayfası açıldığında yapılacaklar

        var uid = await window.localStorage.getItem("uid");
        var usid = uid;
        var code = await window.localStorage.getItem("log-key");
        await $("#user-setting-password, #user-setting-repassword").val("");
        await $("#user-setting-uid, #user-setting-usid").val(usid);
        await $("#user-setting-code").val(code);

        const data = await app.cryptedPost(
            "ajax/user/get_user_info.php", {
                'log-cmd': 'get-user-info',
                'uid': uid
            }
        );

        if(data.status == "OK"){
            await $("#user-setting-name").val(data.name).parent().addClass("is-focused").addClass("is-dirty");
            await $("#user-setting-lastname").val(data.lastname).parent().addClass("is-focused").addClass("is-dirty");
            await $("#user-setting-username").val(data.username).parent().addClass("is-focused").addClass("is-disabled").addClass("is-dirty");
            await $("#user-setting-email").val(data.mail).parent().addClass("is-focused").addClass("is-disabled").addClass("is-dirty");
            await $("#user-setting-cellp").val(data.cellphone).parent().addClass("is-focused").addClass("is-dirty");
            await componentHandler.upgradeDom();
        }

    },
    setWifi: async()=>{
        var list =await "";
        var wifis_s = await window.localStorage.getItem("wifis");
        var wifis = await JSON.parse(wifis_s);
        if(wifis !=null){
            for(var i = wifis.length - 1; i>=0; i--){
                list +=  await   `<li class="mdl-list__item" onclick="putWifi('${wifis[i].s}', '${wifis[i].p}')">
                                <span class="mdl-list__item-primary-content">
                                    <i class="material-icons mdl-list__item-icon">wifi</i>
                                    ${wifis[i].s}
                                </span>
                            </li>`;
    
            }
            await $("#wifi-history-list").html(list);

        }

    },
    forgotPassword: async()=>{

        $("#forgot-mail-error, #forgot-password-success").html("");
        $("#forgot-mail").val("").parent().removeClass("is-dirty").removeClass("is-focused");

    },
    timeLine: async(did=0,pid=0,rid=0)=>{
        $(".date-pick").datepicker();
        $( "#timeline-date-input" ).datepicker($.datepicker.regional[ "tr" ]);

        $("#timeline-date-input").datepicker('setDate', new Date());


        if(did!=0){
           $("#timeLine .back-btn").attr("onclick",`Page.route('alarmDevice', [${did}, ${pid}, ${rid}])`);
           back = [did, pid, rid];
        }
        else{
            $("#timeLine .back-btn").attr("onclick",`Page.route('home')`);
            back = [0,0,0];
        }


        const data = await app.cryptedPost(
            "ajax/device/get_logs.php", {'did': did}
        );

        if(data.status == "OK"){
            var html = await "";
            if(data.logs != null){
                const logs = await data.logs;
                var log_ln = await logs.length;
                for(var i=0;i<log_ln;i++){
    
                    if(logs[i]['type']==null && logs[i]['devs']==null){
    
                        html += await   `<li id="timeline-day-${logs[i]['time'].replace(/\./g, '-')}" class="day-line">${logs[i]['time']}</li>`
    
                    }
                    else{
                        if(logs[i]['pics']){
                            html += await   `<li>
                                                <div class="time-line-icon">
                                                    <img src="img/sensors/${dev_type_icon[logs[i]['type']]}.png">
                                                </div>
                                                <div class="time-line-middle">
                                                    <div class="time-line-date-time">
                                                        <span class="time-line-left-count">${logs[i]['count']}</span>
                                                        <span class="time-line-right-time">${logs[i]['time']}</span>
                                                    </div>
                                                    <div class="time-line-text pic-text">${logs[i]['devs']}</div>
                                                    <button  aria-expanded="false" role="button" tabindex="0" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored pic-btn" onclick="Page.route('alarmPhotos', [${logs[i]['devid']},[${logs[i]['pics']}],[${back}]])">
                                                        <i class="material-icons">photo_camera</i>
                                                    </button>
                                                </div>
                                            </li>`;
                        }
                        else{
                            html += await   `<li>
                                                <div class="time-line-icon">
                                                    <img src="img/sensors/${dev_type_icon[logs[i]['type']]}.png">
                                                </div>
                                                <div class="time-line-middle">
                                                    <div class="time-line-date-time">
                                                        <span class="time-line-left-count">${logs[i]['count']}</span>
                                                        <span class="time-line-right-time">${logs[i]['time']}</span>
                                                    </div>
                                                    <div class="time-line-text">${logs[i]['devs']}</div>
                                                </div>
                                            </li>`;
                        }
                        
                    }
    
                }
            }
            else{
                html = await "Herhangi bir alarm geçmişi bulunamadı";
            }

            await $("#time-line-list").html(html);
            await $("#time-line-card .mdl-card__supporting-text").animate({ scrollTop: 0 }, 0);

        }
    },
    setPrecision: async()=>{
        // Hassasiyet değiştirme sayfası açıldığında yapılacaklar

        const data = await app.cryptedPost(
            "ajax/c_sensor.php",
            {
                'cmd': 'get-precision',
                'sensor-id': $("#precision-set-dev-id").val() 
            }
        );
        if(data.status == "OK"){
            await $(".precision-list-box .mdl-radio__button").each(function(index){ $(this).prop("checked", false).parent().removeClass('is-checked'); });
            await $(`#set-precision-${data.precision}`).attr("checked", true).parent().addClass('is-checked');
            await componentHandler.upgradeDom();
        }
        
    },
    settings: async()=>{
        
    },
    editAuthority: async(user_id)=>{
        var usid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");
 
        await $("#u-eauth-usid").val(usid);
        await $("#u-eauth-code").val(code);
        await $("#u-eauth-user-id").val(user_id);
        
        const data = await app.cryptedPost(
            "ajax/user/get_authority.php",
            $("#edit-authority-form").serializeArray()
        );
        if(data.status == "OK"){
            $("#edit-auth-title-text").html(`${data.name} adlı kullanıcınızın yetkilerini düzenleyin.`);
            var places = await data.places;
            var s_status = [""," checked" ];

            var html = await "";
            await $.each( places, function( key, value ) {
                html += `<li id="authority-list-item-${key}" class="mdl-list__item lamp_list_item_active">
                <span class="mdl-list__item-primary-content">
                <i class="material-icons  mdl-list__item-avatar"><img src="img/home.png" class="s-lm-icon"></img></i>
                ${value.name}
                </span>
                <span class="mdl-list__item-secondary-action">
                <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="authority-switch-${key}">
                <input type="checkbox" id="authority-switch-${key}" value="${$.trim(value.id)}" onchange="user.changeAuthorityText()" class="mdl-switch__input authority-switchs"${s_status[value.authority]}/>
                </label>
                </span>
                </li>`;
            });
            await $("#authority-places-list").html(html);

            await componentHandler.upgradeDom();

        }

    },
    smartLogin: async(event_id=null)=>{
        var usid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");
        if(event_id!=null){
            await $("#s-log-event-id").val(event_id);
        }
        await $("#s-log-usid").val(usid);
        await $("#s-log-code").val(code);
    },
    addEvent: async()=>{
        var suid = await window.localStorage.getItem("suid");
        var usid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");

        await $("#e-add-usid").val(usid);
        await $("#e-add-code").val(code);
        
        const data = await app.cryptedPost(
            "ajax/smart/get_devices.php",
            {}
        );
        var opts = await `<option value="0" typ="">Cihaz Seçiniz</option>`;
        if(data.status == "OK"){
            var devs = await data.devices;
            $.each(devs, function(key, dev){
                opts += `<option value="${dev.id}" typ="${dev.type}">${dev.name}</option>`;
            });
            await $("#dev-slct").html(opts);
        }

        const s_data = await app.cryptedPost(
            "ajax/smart/get_smarts.php",
            {'s-uid': suid}
        );

        if(s_data.status == "OK"){
            var lamps = await s_data.lamps;
            var mid = await user.getMasterUserID();
            await $("#s-mid").val(mid);
            var html = await "";
            await $.each( lamps, function( key, value ) {
                html +=     `<li id="smart-list-item-${value.id}" class="mdl-list__item smart-list-item">
                                <span class="mdl-list__item-primary-content" id="smart-item-content-${value.id}">
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="list-checkbox-${value.id}">
                                        <input type="checkbox" id="list-checkbox-${value.id}" class="mdl-checkbox__input" onchange="smart.toggleListItem(${value.id})"/>
                                    </label>
                                    ${value.adi}
                                </span>
                                <span class="mdl-list__item-secondary-action">
                                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="list-switch-${value.id}">
                                        <input type="checkbox" id="list-switch-${value.id}" name="lm-list[]" value="${$.trim(value.id)}" class="mdl-switch__input s-switch" onchange="smart.putSelectedItems()" disabled/>
                                    </label>
                                </span>
                            </li>`;
            });
            await $("#event-add-lm-list").html(`<ul id="smartLampsList" class="demo-list-control mdl-list">${html}</ul>`);
            
            var switchs = await s_data.switchs;
            var html2 = await "";
            await $.each( switchs, function( key, value ) {
                html2 +=     `<li id="smart-list-item-${value.id}" class="mdl-list__item smart-list-item">
                                <span class="mdl-list__item-primary-content" id="smart-item-content-${value.id}">
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="list-checkbox-${value.id}">
                                        <input type="checkbox" id="list-checkbox-${value.id}" class="mdl-checkbox__input" onchange="smart.toggleListItem(${value.id})"/>
                                    </label>
                                    ${value.adi}
                                </span>
                                <span class="mdl-list__item-secondary-action">
                                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="list-switch-${value.id}">
                                        <input type="checkbox" id="list-switch-${value.id}" name="ws-list[]" value="${$.trim(value.id)}" class="mdl-switch__input s-switch" onchange="smart.putSelectedItems()" disabled/>
                                    </label>
                                </span>
                            </li>`;
            });
            await $("#switchs-panel").html(`<ul id="smartLampsList" class="demo-list-control mdl-list">${html2}</ul>`);
                        
            var valves = await s_data.valves;
            var html3 = await "";
            await $.each( valves, function( key, value ) {
                html3 +=     `<li id="smart-list-item-${value.id}" class="mdl-list__item smart-list-item">
                                <span class="mdl-list__item-primary-content" id="smart-item-content-${value.id}">
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="list-checkbox-${value.id}">
                                        <input type="checkbox" id="list-checkbox-${value.id}" class="mdl-checkbox__input" onchange="smart.toggleListItem(${value.id})"/>
                                    </label>
                                    ${value.adi}
                                </span>
                                <span class="mdl-list__item-secondary-action">
                                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="list-switch-${value.id}">
                                        <input type="checkbox" id="list-switch-${value.id}" name="vl-list[]" value="${$.trim(value.id)}" class="mdl-switch__input s-switch" onchange="smart.putSelectedItems()" disabled/>
                                    </label>
                                </span>
                            </li>`;
            });
            await $("#valves-panel").html(`<ul id="smartLampsList" class="demo-list-control mdl-list">${html3}</ul>`);
            
            
            await componentHandler.upgradeDom();

        }
    },

    syncSmartHome: async()=>{

        const data = await app.cryptedPost(
            "ajax/smart/get_events.php",
            {'mid': master_db_id}
        );

        if(data.status == "OK"){

            var events = await data.events;
            var html = await "";
            $.each(events, function(key, value){
                html += `<div id="sync-event-item-${value.id}" class="acc_item sub1">
                            <div class="acc_item_arr" onclick="smart.toggleSub1(${value.id})"><i class="material-icons edit-place-arrow">keyboard_arrow_right</i></div>
                            <div class="acc_item_text">
                                <div class="edit-pla-labels">
                                    <div id="sync-event-item-label-${value.id}" class="edit-pla-label text-label-visible" onclick="smart.toggleSub1(${value.id})">
                                        <span>${value.name}</span>
                                    </div>
                                </div>
                                <div class="edit-pla-btns">
                                    <div class="edit-pla-cmd-btns btns-visible">
                                        <div class="edit-pla-edit-btn" onclick="smart.editEvent(${value.id})"><i class="material-icons">edit</i></div>
                                        <div class="edit-pla-del-btn" onclick="smart.deleteEvent(${value.id})"><i class="material-icons">delete_outline</i></div>
                                    </div>
                                </div>
                            </div>
                        </div>                
                        <div id="sync-event-content-${value.id}" class="acc_content con1">
                            <div class="ft-treeview">
                                <ul>`;
                                
                                if(value.sen_name==null && value.sen_type==null){
                                    html+=`<li class="sub"><img src="img/sensors/${dev_type_icon[value.dev_type]}.png" class="ft-treeview-sub-icon bg-frozen"> ${value.dev_name} <i>' tetiklendiğinde</i></li>`;
                                }
                                else{
                                    html+=`<li class="sub"><img src="img/sensors/${dev_type_icon[value.dev_type]}.png" class="ft-treeview-sub-icon bg-frozen"> ${value.dev_name}</li>
                                    <li class="sub"><img src="img/sensors/${dev_type_icon[value.sen_type]}.png" class="ft-treeview-sub-icon bg-active"> ${value.sen_name} <i>' tetiklendiğinde</i></li>`;
                                }

                                var smarts = value.smarts;
                                $.each(smarts, function(key2, value2){
                                    html+=`<li class="item"><div class="ft-treeview-line"></div> <img src="img/${value2.smd_type}.png" class="ft-treeview-item-icon ${smart_data_bg[value2.data]}"> ${value2.smd_name}</li>`;
                                });
                                
                html += `<ul>
                    </div>
                </div>`;


            });

            $("#syncronization-list").html(html);


            /*
                                    <li class="sub"><img src="img/sensors/master.png" class="ft-treeview-sub-icon bg-frozen"> Demo Alarm</li>
                                    <li class="sub"><img src="img/sensors/motion.png" class="ft-treeview-sub-icon bg-active"> Harekt Sensörü <i>' tetiklendiğinde</i></li>
                                    <li class="item"><div class="ft-treeview-line"></div> <img src="img/lm.png" class="ft-treeview-item-icon"> Lamba 1</li>
                                    <li class="item"><div class="ft-treeview-line"></div><img src="img/lm.png" class="ft-treeview-item-icon"> Lamba 2</li>
            */

        }
/*


       var suid = await window.localStorage.getItem("suid");
       var usid = await window.localStorage.getItem("uid");
       var code = await window.localStorage.getItem("log-key");

       await $("#s-log-usid").val(usid);
       await $("#s-log-code").val(code);
       
       if(is_empty(suid)){
            await $("#smart-login-form").show();
            await $("#smart-lamp-form").hide();
        }else{
            var suid = await window.localStorage.getItem("suid");

            const data = await app.cryptedPost(
                "ajax/c_smarts.php",
                {
                    's-cmd': 'get-devices',
                    's-uid': suid
                }
            );
            
            if(data.status == "OK"){
                var devices = await data.content;
                var s_status = ["",""," checked" ];
                var s_vars = [" disabled", "", ""];
                var s_list_bg = ["", " lamp_list_item_active", " lamp_list_item_active"];
                var mid = await user.getMasterUserID();
                await $("#s-mid").val(mid);
                var html = await "";
                await $.each( devices, function( key, value ) {
                    html += `<li id="smart-list-item-${key}" class="mdl-list__item lamp_list_item_active">
                    <span class="mdl-list__item-primary-content">
                    <i class="material-icons  mdl-list__item-avatar"><img src="img/lm.png" class="s-lm-icon"></img></i>
                    ${value.adi}
                    </span>
                    <span class="mdl-list__item-secondary-action">
                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="list-switch-${key}">
                    <input type="checkbox" id="list-switch-${key}" value="${$.trim(value.id)}" class="mdl-switch__input s-switch"/>
                    </label>
                    </span>
                    </li>`;
                });
                await $("#smartLampsList").html(html);
                await smart.putSelectedLamps();

                await componentHandler.upgradeDom();

                await $(".s-switch").change(smart.putSelectedLamps);

                await $("#smart-lamp-form").show();

            }
        }
    */

    },
    editEvent: async(event_id)=>{
        var suid = await window.localStorage.getItem("suid");
        var usid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");

        await $("#e-edit-usid").val(usid);
        await $("#e-edit-code").val(code);
        await $("#e-edit-id").val(event_id);
        
        const data = await app.cryptedPost(
            "ajax/smart/event_edit.php",
            {'suid':suid, 'eid':event_id}
        );

        if(data.status == "OK"){

            var opts = await `<option value="0" typ="">Cihaz Seçiniz</option>`;
            var devs = await data.devices;
            $.each(devs, function(dev_key, dev){
                opts += `<option value="${dev.id}" typ="${dev.type}">${dev.name}</option>`;
            });
            await $("#dev-edit-slct").html(opts);


            var event = await data.event;
            await $("#event-edit-name").val(event.name).parent().addClass("is-focused").addClass("is-dirty");
            await $("#dev-edit-slct").val(event.did).parent().addClass("is-focused").addClass("is-dirty");
            await smart.getSensorOptionsForEdit();
            await $("#sen-edit-slct").val(event.sid).parent().addClass("is-focused").addClass("is-dirty");

            var s_status = [" disabled",""," checked" ];
            var c_status = ["",""," checked" ];

            var i_status = [""," mdl-list__item_active", " mdl-list__item_active"];
            var lamps = await data.smarts.lms;
            var html_lm = await "";
            await $.each( lamps, function( lm_key, lm_val ) {
                html_lm +=     `<li id="smart-edit-list-item-${lm_val.id}" class="mdl-list__item smart-list-item${i_status[lm_val.on_off]}">
                                <span class="mdl-list__item-primary-content" id="smart-edit-item-content-${lm_val.id}">
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="list-edit-checkbox-${lm_val.id}">
                                        <input type="checkbox" id="list-edit-checkbox-${lm_val.id}" class="mdl-checkbox__input" onchange="smart.toggleListItemForEdit(${lm_val.id})"${c_status[lm_val.on_off]}/>
                                    </label>
                                    ${lm_val.adi}
                                </span>
                                <span class="mdl-list__item-secondary-action">
                                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="list-edit-switch-${lm_val.id}">
                                        <input type="checkbox" id="list-edit-switch-${lm_val.id}" name="lm-list[]" value="${$.trim(lm_val.id)}" class="mdl-switch__input s-edit-switch" onchange="smart.putSelectedItemsForEdit()"${s_status[lm_val.on_off]}/>
                                    </label>
                                </span>
                            </li>`;
            });
            await $("#lamps-edit-panel").html(`<ul id="smartLampsList" class="demo-list-control mdl-list">${html_lm}</ul>`);
            
            var switchs = await data.smarts.wss;
            var html_ws = await "";
            await $.each( switchs, function( ws_key, ws_val ) {
                html_ws +=     `<li id="smart-edit-list-item-${ws_val.id}" class="mdl-list__item smart-list-item${i_status[ws_val.on_off]}">
                                <span class="mdl-list__item-primary-content" id="smart-edit-item-content-${ws_val.id}">
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="list-edit-checkbox-${ws_val.id}">
                                        <input type="checkbox" id="list-edit-checkbox-${ws_val.id}" class="mdl-checkbox__input" onchange="smart.toggleListItemForEdit(${ws_val.id})"${c_status[ws_val.on_off]}/>
                                    </label>
                                    ${ws_val.adi}
                                </span>
                                <span class="mdl-list__item-secondary-action">
                                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="list-edit-switch-${ws_val.id}">
                                        <input type="checkbox" id="list-edit-switch-${ws_val.id}" name="ws-list[]" value="${$.trim(ws_val.id)}" class="mdl-switch__input s-edit-switch" onchange="smart.putSelectedItemsForEdit()"${s_status[ws_val.on_off]}/>
                                    </label>
                                </span>
                            </li>`;
            });
            await $("#switchs-edit-panel").html(`<ul id="smartLampsList" class="demo-list-control mdl-list">${html_ws}</ul>`);
                                         
            var valves = await data.smarts.vls;
            var html_vl = await "";
            await $.each( valves, function( vl_key, vl_val ) {
                html_vl +=     `<li id="smart-edit-list-item-${vl_val.id}" class="mdl-list__item smart-list-item${i_status[vl_val.on_off]}">
                                <span class="mdl-list__item-primary-content" id="smart-edit-item-content-${vl_val.id}">
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="list-edit-checkbox-${vl_val.id}">
                                        <input type="checkbox" id="list-edit-checkbox-${vl_val.id}" class="mdl-checkbox__input" onchange="smart.toggleListItemForEdit(${vl_val.id})"${c_status[vl_val.on_off]}/>
                                    </label>
                                    ${vl_val.adi}
                                </span>
                                <span class="mdl-list__item-secondary-action">
                                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="list-edit-switch-${vl_val.id}">
                                        <input type="checkbox" id="list-edit-switch-${vl_val.id}" name="vl-list[]" value="${$.trim(vl_val.id)}" class="mdl-switch__input s-edit-switch" onchange="smart.putSelectedItemsForEdit()"${s_status[vl_val.on_off]}/>
                                    </label>
                                </span>
                            </li>`;
            });
            await $("#valves-edit-panel").html(`<ul id="smartLampsList" class="demo-list-control mdl-list">${html_vl}</ul>`);
            await smart.putSelectedItemsForEdit();
            await componentHandler.upgradeDom();

        }

    },
    addPlace: async() =>{
        var usid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");
 
        await $("#place-add-usid").val(usid);
        await $("#place-add-code").val(code);
        await $("#place-uid").val(master_db_id);

    },
    addRoom: async(pid) =>{
        var usid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");
 
        await $("#room-add-usid").val(usid);
        await $("#room-add-code").val(code);
        await $("#room-uid").val(master_db_id);
        await $("#room-sub").val(pid);
    },
    alarmRoom: async(pid, rid)=>{
        $("#add-device-btn").attr("onclick",`dev.addDevice(${pid}, ${rid})`);
        const data = await app.cryptedPost(
            "ajax/device/get_devices.php", 
            {
                'pid': pid,
                'rid': rid
            }
        );

        if(data.status == "OK"){
            $("#device-list-path").html(`${data.pname} / ${data.rname}`);
            const devices = await data.devices;

            var html = await "";
            await $.each(devices, function(key, value){
                html +=
                        `<div id="device-item-${value.id}" class="mdl-list__item device-item">
                            <span class="mdl-list__item-primary-content" onclick="Page.route('alarmDevice', [${value.id}, ${pid}, ${rid}])">
                                <img src="img/sensors/${dev_type_icon[value.type]}.png">
                                <span>${value.name}</span>
                            </span>
                            <button id="device-add-btn-${value.id}" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect device-add" style="display: none" onclick="Page.route('addDevice',[${value.id},${value.type}])">
                                <i class="material-icons">add</i>Sensör Ekle
                            </button>
                            <span class="mdl-list__item-secondary-content place-item-content">
                                <a class="mdl-list__item-secondary-action device-route-btn" onclick="Page.route('alarmDevice', [${value.id}, ${pid}, ${rid}])"><i class="material-icons">keyboard_arrow_right</i></a>
                            </span>                                            
                        </div>`;

            });
            $("#device-sensor-list").html(html);
            
            await $.each($("#device-sensor-list .mdl-list__item"), function(index){
                var t = $(this);
                    setTimeout(function(){
                        $("#device-sensor-list .mdl-list__item").eq(index).addClass("device-list-item-visible");
                    },175*index);
            });

            await $("#room-settings-menu").html(
                `<li class="mdl-menu__item" onclick="dev.addDevice(${pid}, ${rid})">Cihaz Ekle</li>`
            );


            $.each($(".device-list"), function(){
                var t = $(this);
                t.css("width", ((t.find(".device").length *110) + 10)+"px");
            });

            $("#stop-edit-btn").hide();

        }
    },
    alarmDevice: async(did,pid,rid=0)=>{
        /*
        *   Cihaz ekranına gelindiğinde, cihaz uyarısını kapat 
        */
        active_dev_page = await did;
        await alertFT.hide(did);

        /*
        *   Sensör ekle butonuna cihaz ID'sini ata
        */
        await $("#stop-all-alarm-btn").attr("onclick", `dev.stopAllAlarms(${did})`);
        await $(".mdl-ft-btn .history-btn").attr("onclick", `Page.route('timeLine', [${did},${pid},${rid}])`);
        

        /*
        *   Cihaz detaylarını sunucudan al
        */
        const data = await app.cryptedPost(
            "ajax/device/get_device_detail.php", 
            { 'did':did }
        );

        if(data.status == "OK"){
            if(rid==0){
                await $("#alarmDevice .back-btn").attr("onclick",`Page.route('home')`);
            }
            else{
                await $("#alarmDevice .back-btn").attr("onclick",`Page.route('alarmRoom', [${data.pid}, ${data.rid}])`);
            }
            var path = await data.type==8 || data.type==9 ? `${data.pname} / ${data.rname} / ${data.dname}` : `${data.pname} / ${data.dname}`;
            await $("#device-path").html(path);
            const st = await dev.checkAlarmStatus(did);
            await $("#alarm-status").html(st);
            await dev.putSensors(data.data, data.pid, data.rid, data.type);

            /*
            *   Buttonları cihaz türüne göre yeniden düzenle
            */
            if(data.type=="8" || data.type=="9"){
                $("#master_cmd_panel, #sensor-pages-btn, #sensors .ft-bottom-page-header").hide();
                $("#motion_cmd_panel").show();
                $("#sensors").addClass("ft-bottom-page-settled").addClass("ft-bottom-page-active");
                $("#sensor-list").addClass("ft-bottom-page-content-settled");
                await $("#device-settings-menu").html(
                    `<li class="mdl-menu__item" onclick="dev.addSensor(${did},${pid},${rid})">Siren Ekle</li>
                    <li class="mdl-menu__item" onclick="Page.route('editDevice', [${did}, '${data.dname}', ${pid}, ${rid}])">Cihazı Düzenle</li>
                    <li class="mdl-menu__item" onclick="Page.route('editDeviceNotify' ,[${did}, ${pid}, ${rid}])">Dış Bildirimleri Düzenle</li>
                    <li class="mdl-menu__item" onclick="Page.route('setMotionPrecision' ,[${did}, ${pid}, ${rid}])">Hassasiyeti Düzenle</li>
                    <li class="mdl-menu__item" onclick="dev.resetDevice(${did})">Cihazı Resetle</li>`
                );
                
            }else if(data.type=="0"){
                $("#motion_cmd_panel").hide();
                $("#master_cmd_panel, #sensor-pages-btn, #sensors .ft-bottom-page-header").show();
                $("#sensors").removeClass("ft-bottom-page-settled").removeClass("ft-bottom-page-active");
                $("#sensor-list").removeClass("ft-bottom-page-content-settled");
                await $("#device-settings-menu").html(
                    `<li class="mdl-menu__item" onclick="dev.addSensor(${did},${pid},${rid})">Sensör Ekle</li>
                    <li class="mdl-menu__item" onclick="Page.route('editDevice', [${did}, '${data.dname}', ${pid}, ${rid}])">Cihazı Düzenle</li>
                    <li class="mdl-menu__item" onclick="Page.route('setHomeOutDelay',[${did}])">Evden Çıkış Süresi Değiştir</li>`
                );
            }
            if(data.type=="8"){
                $("#m-stop-alarm-btn .material-btn-title2").html("KAPALI");
                $("#m-stop-alarm-btn img").attr("src","img/close.png");
                $("#m-stop-alarm-btn").attr("cmd","stopAlarm");


            }
            else if(data.type="9"){
                $("#m-stop-alarm-btn .material-btn-title2").html("ANLIK");
                $("#m-stop-alarm-btn img").attr("src","img/take_photo.png");
                $("#m-stop-alarm-btn").attr("cmd","takePhoto");

            }
            /*
            *   Button boyutlarını düzenle
            */
            await $(".command-panel button").each(function (index) { 
                var t= $(this);
                var width = t.css("width");
                t.css("height",width);
                t.attr("did", did);
            });
        }

        await componentHandler.upgradeDom();

    },
    addDevice : async (id, type, dcode, pid, rid, mid=0) =>{
        // Kullanıcı ekle sayfası açıldığında yapılcaklar
        var uid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");

        await $("#dev-add-usid").val(uid);
        await $("#dev-add-code").val(code);
        
        await $("#device-add-form #register-title").val(dev_add_page_titles[type] + " Düzenle");
        await $("#dev-add-did").val(id);
        await $("#dev-add-type").val(type);
        await $("#dev-add-dcode").val(dcode);
        await $("#dev-add-pid").val(pid);
        await $("#dev-add-rid").val(rid);
        await $("#dev-add-mid").val(mid);
        await $("#dev-add-name").val(`${dev_default_names[type]} ${id}`).parent().addClass("is-focused");

        if(mid==0){
            await $("#add-device-back-btn").attr("onclick", `Page.route('home')`);
        }
        else{
            await $("#add-device-back-btn").attr("onclick", `Page.route('alarmDevice',[${mid}, ${pid}, ${rid}])`);
        }
        await $("#dev-add-save-btn").attr("onclick", `dev.addSave(${mid},${pid}, ${rid})`);

    },
    editDeviceNotify: async(did, pid, rid)=>{
        var uid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");

        await $("#edit-notify-usid").val(uid);
        await $("#edit-notify-code").val(code);
        await $("#edit-notify-did").val(did);

        await $("#editDeviceNotify-back-btn").attr("onclick", `Page.route('alarmDevice',[${did}, ${pid}, ${rid}])`);
        await $("#editDeviceNotify-save-btn").attr("onclick", `dev.saveDeviceNotify(${did}, ${pid}, ${rid})`);

        

        const data = await app.cryptedPost(
            "ajax/device/get_notify.php",
            {
                'did': did
            }
        );
        
        if(data.status == "OK"){
            await $("#edit-notify-form .mdl-radio__button").each(function(index){ $(this).prop("checked", false).parent().removeClass('is-checked'); });

            await $(`#edit-notify-form #option-${data.state}`).prop("checked", true).parent().addClass('is-checked');
            await componentHandler.upgradeDom();

        }
    },
    setMotionPrecision:async(did, pid, rid)=>{
        var uid = await window.localStorage.getItem("uid");
        var code = await window.localStorage.getItem("log-key");
        await $("#setMotionPrecision-back-btn").attr("onclick", `Page.route('alarmDevice',[${did}, ${pid}, ${rid}])`);
        await $("#setMotionPrecision-save-btn").attr("onclick", `dev.saveMotionPrecision(${did}, ${pid}, ${rid})`);

        await $("#motion-precision-usid").val(uid);
        await $("#motion-precision-code").val(code);
        await $("#motion-precision-did").val(did);

        const data = await app.cryptedPost(
            "ajax/device/get_m_precision.php",
            {
                'did': did
            }
        );
        
        if(data.status == "OK"){
            await $("#motion-precision-form .mdl-radio__button").each(function(index){ $(this).prop("checked", false).parent().removeClass('is-checked'); });

            await $(`#motion-precision-form #motion-prec-${data.prec}`).prop("checked", true).parent().addClass('is-checked');
            await componentHandler.upgradeDom();

        }

    },
    editPlaceRoom: async()=>{
        const data = await app.cryptedPost(
            "ajax/place/place_edit_list.php",
            {
                'mid': master_db_id
            }
        );
        if(data.status == "OK"){

            var places = await data.places;
            if(places == null){
                var html = await `<p class="no-record">Kayıtlı yeriniz bulunmamaktadır.</p>`;
            }
            else{
                var html = await `<div class="acc">`;
                await $.each(places, function(key1, place){

                    html += `<div id="place-item-${place.id}" class="acc_item sub1">
                                <div class="acc_item_arr" onclick="place.toggleSub1(${place.id})"><i class="material-icons edit-place-arrow">keyboard_arrow_right</i></div>
                                <div class="acc_item_text">
                                    <div class="edit-pla-labels">
                                        <div id="edit-pla-label-${place.id}" class="edit-pla-label text-label-visible" onclick="place.toggleSub1(${place.id})">
                                            <img src="img/home.png">
                                            <span>${place.name}</span>
                                        </div>
                                        <div id="edit-pla-text-${place.id}" class="edit-pla-text">
                                            <input type="text" id="edit-pla-input-${place.id}" value="${place.name}">
                                        </div>
                                    </div>
                                    <div class="edit-pla-btns">
                                        <div id="edit-pla-cmd-btns-${place.id}" class="edit-pla-cmd-btns btns-visible">
                                            <div class="edit-pla-edit-btn" onclick="place.edit(${place.id})"><i class="material-icons">edit</i></div>
                                            <div class="edit-pla-del-btn" onclick="place.delete(${place.id})"><i class="material-icons">delete_outline</i></div>
                                        </div>
                                        
                                        <div id="edit-pla-ok-btns-${place.id}" class="edit-pla-ok-btns">
                                            <div class="edit-pla-edit-btn" onclick="place.editOK(${place.id})"><i class="material-icons">done</i></div>
                                            <div class="edit-pla-del-btn" onclick="place.editNO(${place.id})"><i class="material-icons">close</i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                    html += `<div id="place-cont-${place.id}" class="acc_content con1">`;
                    

                    var rooms = place.rooms;
                    var bdevs = place.bdevs;

                    if(rooms == null && bdevs == null){
                        html += `<p class="no-record">Bu yerde eklenmiş bir oda veya Alarm Cihazı bulunmamaktadır.</p>`;
                    }
                    else{

                        html += `<div class="acc">`;
                        $.each(bdevs, function(key3, bdev){
                            html += `<div id="device-item-${place.id}-0-${bdev.id}" class="acc_item sub2 no-arr">
                                            <div class="acc_item_arr"><i class="material-icons edit-place-arrow">keyboard_arrow_right</i></div>
                                            <div class="acc_item_text">
                                                <div class="edit-pla-labels">
                                                    <div id="edit-pla-label-b-${place.id}-${bdev.id}" class="edit-pla-label text-label-visible">
                                                        <img src="img/sensors/master.png">
                                                        <span>${bdev.name}</span>
                                                    </div>
                                                    <div id="edit-pla-text-b-${place.id}-${bdev.id}" class="edit-pla-text">
                                                        <input type="text" id="edit-pla-input-b-${place.id}-${bdev.id}" value="${bdev.name}">
                                                    </div>
                                                </div>
                                                <div class="edit-pla-btns">
                                                    <div id="edit-pla-cmd-btns-b-${place.id}-${bdev.id}" class="edit-pla-cmd-btns btns-visible">
                                                        <div class="edit-pla-edit-btn" onclick="place.edit4(${place.id},${bdev.id})"><i class="material-icons">edit</i></div>
                                                        <div class="edit-pla-del-btn" onclick="place.deleteDevice(${place.id},0,${bdev.id})"><i class="material-icons">delete_outline</i></div>
                                                    </div>
                                                    
                                                    <div id="edit-pla-ok-btns-b-${place.id}-${bdev.id}" class="edit-pla-ok-btns">
                                                        <div class="edit-pla-edit-btn" onclick="place.editOK4(${place.id},${bdev.id})"><i class="material-icons">done</i></div>
                                                        <div class="edit-pla-del-btn" onclick="place.editNO4(${place.id},${bdev.id})"><i class="material-icons">close</i></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;


                        });
                        
                        $.each(rooms, function(key2, room){
                            html += `<div id="room-item-${place.id}-${room.id}" class="acc_item sub2">
                                            <div class="acc_item_arr" onclick="place.toggleSub2(${place.id},${room.id})"><i class="material-icons edit-place-arrow">keyboard_arrow_right</i></div>
                                            <div class="acc_item_text">
                                                <div class="edit-pla-labels">
                                                    <div id="edit-pla-label-${place.id}-${room.id}" class="edit-pla-label text-label-visible" onclick="place.toggleSub2(${place.id},${room.id})">
                                                        <img src="img/room.png">
                                                        <span>${room.name}</span>
                                                    </div>
                                                    <div id="edit-pla-text-${place.id}-${room.id}" class="edit-pla-text">
                                                        <input type="text" id="edit-pla-input-${place.id}-${room.id}" value="${room.name}">
                                                    </div>
                                                </div>
                                                <div class="edit-pla-btns">
                                                    <div id="edit-pla-cmd-btns-${place.id}-${room.id}" class="edit-pla-cmd-btns btns-visible">
                                                        <div class="edit-pla-edit-btn" onclick="place.edit2(${place.id},${room.id})"><i class="material-icons">edit</i></div>
                                                        <div class="edit-pla-del-btn" onclick="place.deleteRoom(${place.id},${room.id})"><i class="material-icons">delete_outline</i></div>
                                                    </div>
                                                    
                                                    <div id="edit-pla-ok-btns-${place.id}-${room.id}" class="edit-pla-ok-btns">
                                                        <div class="edit-pla-edit-btn" onclick="place.editOK2(${place.id},${room.id})"><i class="material-icons">done</i></div>
                                                        <div class="edit-pla-del-btn" onclick="place.editNO2(${place.id},${room.id})"><i class="material-icons">close</i></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;

                            html += `<div id="room-cont-${place.id}-${room.id}" class="acc_content con2">`;
                            
                            var devs = room.devs;

                            if(devs == null){
                                html +=`<p class="no-record">Bu odada kayıtlı bir cihazınız bulunmamaktadır.</p>`;
                            }
                            else{

                                html += `<div class="acc">`;
                                $.each(devs, function(key3, dev){

                                    html += `<div id="device-item-${place.id}-${room.id}-${dev.id}" class="acc_item sub3 no-arr">
                                                <div class="acc_item_arr"><i class="material-icons edit-place-arrow">keyboard_arrow_right</i></div>
                                                <div class="acc_item_text">
                                                    <div class="edit-pla-labels">
                                                        <div id="edit-pla-label-${place.id}-${room.id}-${dev.id}" class="edit-pla-label text-label-visible">
                                                            <img src="img/sensors/${dev_type_icon[dev.type]}.png">
                                                            <span>${dev.name}</span>
                                                        </div>
                                                        <div id="edit-pla-text-${place.id}-${room.id}-${dev.id}" class="edit-pla-text">
                                                            <input type="text" id="edit-pla-input-${place.id}-${room.id}-${dev.id}" value="${dev.name}">
                                                        </div>
                                                    </div>
                                                    <div class="edit-pla-btns">
                                                        <div id="edit-pla-cmd-btns-${place.id}-${room.id}-${dev.id}" class="edit-pla-cmd-btns btns-visible">
                                                            <div class="edit-pla-edit-btn" onclick="place.edit3(${place.id},${room.id},${dev.id})"><i class="material-icons">edit</i></div>
                                                            <div class="edit-pla-del-btn" onclick="place.deleteDevice(${place.id},${room.id},${dev.id})"><i class="material-icons">delete_outline</i></div>
                                                        </div>
                                                        
                                                        <div id="edit-pla-ok-btns-${place.id}-${room.id}-${dev.id}" class="edit-pla-ok-btns">
                                                            <div class="edit-pla-edit-btn" onclick="place.editOK3(${place.id},${room.id},${dev.id})"><i class="material-icons">done</i></div>
                                                            <div class="edit-pla-del-btn" onclick="place.editNO3(${place.id},${room.id},${dev.id})"><i class="material-icons">close</i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`;



                                });
                                html += `</div>`;

                            }
                            

                            html += `</div>`;


                        });
                        html += `</div>`;

                    }

                    html += `</div>`;
                });

                html += await `</div>`;

            }
            
            await $("#edit-place-room-list").html(html);

        }
    },
    alarmPhotos: async(did, ids,back)=>{
        if(back[0]==0 && back[1]==0 && back[2]==0){
            $("#alarmPhotos .mdl-layout__drawer-button").attr("onclick", `Page.route('timeLine')`);

        }
        else{
            $("#alarmPhotos .mdl-layout__drawer-button").attr("onclick", `Page.route('timeLine',[${back[0]}, ${back[1]}, ${back[2]}])`);

        }
        const data = await app.cryptedPost(
            "ajax/device/get_times.php",
            {
                'times': ids
            }
        );

        if(data.status == "OK"){
            html = "";
            $.each(ids, function( index, value ) {
                html += `<div class="demo-card-image mdl-card mdl-shadow--2dp" style="background: url(http://alarm.teknowin.com/records/${did}/${value}.jpg) center / cover">
                            <div class="mdl-card__title mdl-card--expand" onclick="Page.route('PhotoView',[${did},${value},[${ids}],[${back}]])">
                                <span class="material-icons zoom-centered">zoom_in</span>
                            </div>
                            <div class="mdl-card__actions">
                            <span class="demo-card-image__filename">
                                ${data.times[value]}
                                <span onclick="imageDownload(${did},${value},'${data.times[value]}')" class="material-icons icon-righted">get_app</span>
                            </span>
                            </div>
                        </div><br>`;
            });
            $("#photo-list").html(html);
        }
        
    },
    PhotoView: async(did,time,ids,back,fromNow=0)=>{
        var fnwPath = ["","/now"];
        if(fromNow==1){
            var dialog = document.querySelector('#takePhotoDialog');
            dialog.close();
            $("#PhotoView .mdl-layout__drawer-button").attr("onclick", `Page.route('alarmDevice', [${back}])`);

        }
        else{
            $("#PhotoView .mdl-layout__drawer-button").attr("onclick", `Page.route('alarmPhotos',[${did},[${ids}],[${back}]])`);
        }


        var canvas = document.getElementById('mainCanvas');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight - 58;
        var f_zoom = 0;

        var image_x = 0, image_y = 0;
        var zoom = 0.5;
        var mouse_x = 0, mouse_y = 0, finger_dist = 0;
        var source_image_obj = new Image();
        source_image_obj.addEventListener('load', function() {reset_settings();}, false); // Reset (x,y,zoom) when new image loads
    

        source_image_obj.src = `http://alarm.teknowin.com/records${fnwPath[fromNow]}/${did}/${time}.jpg`;  // load the image
    
        function update_canvas() {
            var mainCanvas= document.getElementById("mainCanvas");
            var mainCanvasCTX = document.getElementById("mainCanvas").getContext("2d");
        
            var canvas_w = mainCanvas.width, canvas_h = mainCanvas.height; // make things easier to read below
            mainCanvasCTX.clearRect(0, 0, canvas_w, canvas_h);
            mainCanvasCTX.drawImage(source_image_obj, image_x - (canvas_w * zoom / 2), image_y - (canvas_h * zoom / 2), canvas_w * zoom, canvas_h * zoom, 0, 0, canvas_w, canvas_h);
        }
    
        function reset_settings() {
            var mainCanvas= document.getElementById("mainCanvas");
            var mainCanvasCTX = document.getElementById("mainCanvas").getContext("2d");
            var canvas_w = mainCanvas.width, canvas_h = mainCanvas.height;
            image_x = source_image_obj.width  / 2;
            image_y = source_image_obj.height / 2;
            zoom = source_image_obj.width / canvas_w;
            f_zoom = source_image_obj.width / canvas_w;

            update_canvas();  // Draw the image in its new position
        }
    
        canvas.addEventListener('wheel', function(e) {
            if(e.deltaY<0){
                if((zoom * 1.5)<f_zoom){
                zoom = zoom * 1.5;
                update_canvas();
                }
                else if((zoom * 1.5)==f_zoom){
                reset_settings();
                }
            
            } else {
            if((zoom / 1.5)<f_zoom){
                zoom = zoom / 1.5;
                update_canvas();
                }
                else if((zoom / 1.5)==f_zoom){
                reset_settings();
                }
            }
        }, false);

        function get_distance(e) {
            var diffX = e.touches[0].clientX - e.touches[1].clientX;
            var diffY = e.touches[0].clientY - e.touches[1].clientY;
            return Math.sqrt(diffX * diffX + diffY * diffY); // Pythagorean theorem
        }
        
        canvas.addEventListener('touchstart', function(e) {
            //console.log("touchstart");
            if(e.touches.length > 1) { // if multiple touches (pinch zooming)
            finger_dist = get_distance(e); // Save current finger distance
            } // Else just moving around
            mouse_x = e.touches[0].clientX; // Save finger position
            mouse_y = e.touches[0].clientY; //
        }, false);
    
        canvas.addEventListener('touchmove', function(e) {
            
            e.preventDefault(); // Stop the window from moving
            //console.log("touchmove");

            if(e.touches.length > 1) { // If pinch-zooming
            var new_finger_dist = get_distance(e); // Get current distance between fingers
            zoom = zoom * Math.abs(finger_dist / new_finger_dist); // Zoom is proportional to change
            finger_dist = new_finger_dist; // Save current distance for next time
            } else { // Else just moving around
            image_x = image_x + (zoom * (mouse_x - e.touches[0].clientX)); // Move the image
            image_y = image_y + (zoom * (mouse_y - e.touches[0].clientY)); //
            mouse_x = e.touches[0].clientX; // Save finger position for next time
            mouse_y = e.touches[0].clientY; //
            }
            update_canvas(); // draw the new position
        }, false);
    
        canvas.addEventListener('touchend', function(e) {
            e.preventDefault(); 

            mouse_x = e.changedTouches[0].clientX; mouse_y = e.changedTouches[0].clientY; // could be down to 1 finger, back to moving image
            //console.log("touchend");
            //console.log(zoom + " ? " + f_zoom);
            if(zoom>=f_zoom){
            reset_settings();
            }
        }, false);
    }
};

