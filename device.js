var dev = {

    /*
    * Alarm Ana Cihaz Ekleme Fonksiyonu
    */
    addDevice: async (pid, rid) =>{

        await cordova.plugins.barcodeScanner.scan( async (result) =>{
            if (!result.cancelled && result.format == "QR_CODE") {

                var qrdata = await $.parseJSON(result.text);
                var hash = await $.md5(`Tekno${qrdata.id}Win${qrdata.id}2019`);
                if(hash == qrdata.code){

                    if(rid==0){
                        if(qrdata.type==0){
                            await Page.route('addDevice', [qrdata.id, qrdata.type, qrdata.code, pid, rid, 0]);
                        }
                        else{
                            await Page.showSnackMessage("Yer ana dizinine yalnızca 'Alarm Ana Cihaz' ekleyebilirsiniz.");
                        }
                    }
                    else{
                        await Page.route('addDevice', [qrdata.id, qrdata.type, qrdata.code, pid, rid, 0]);
                    }

                }

            }
        }, async (error) =>{ await Page.showSnackMessage(error); }, qr_settings );
    },

    /*
    * Cihaz Ekleme Kaydet Fonksiyonu
    */
    addSave: async (mid, pid, rid) =>{
        const data = await app.cryptedPost( "ajax/device/add_device.php", $("#device-add-form").serializeArray());
        if(data.status == "OK"){
            if(mid==0){
                if(rid==0){
                    await Page.route('home');
                }
                else{
                    await Page.route('alarmRoom', [pid, rid]);
                }
            }
            else{
                await Page.route('alarmDevice', [mid, pid, rid]);
            }
            
            
        }
        else if(data.status == "NO"){
            await Page.showSnackMessage(data.error);
        }
    },
        
    /*
    * Smart Motion Cihazına Siren Ekleme Fonksiyonu
    */
    addSiren2SM: async () =>{

        await cordova.plugins.barcodeScanner.scan( async (result) =>{
            if (!result.cancelled && result.format == "QR_CODE") {
                const qrdata = await $.parseJSON(result.text);
                const data = await app.cryptedPost(
                    "ajax/c_sensor.php",
                    {
                        'cmd': 'add-sm-siren', 
                        'sid': qrdata.id, 
                        'type': qrdata.type, 
                        'mid': master_db_id 
                    }
                );

                if(data.status == "OK"){
                    await Page.showSnackMessage(`${qrdata.id} ID'li cihaz eklendi.`);
                    await dev.getSensors(active_dev_page);
                }
                else if(data.status == "NO"){
                    await Page.showSnackMessage(data.error);
                }
            }
        }, async (error) =>{ await Page.showSnackMessage(error); }, qr_settings );
    },
    /*
    * Alarm Ana Cihaza Sensör ve Siren Ekleme Fonksiyonu
    */
    addSensor: async (mid, pid, rid) =>{

        await cordova.plugins.barcodeScanner.scan( async (result) =>{
            if (!result.cancelled && result.format == "QR_CODE") {
                var qrdata = await $.parseJSON(result.text)
                var hash = await $.md5(`Tekno${qrdata.id}Win${qrdata.id}2019`);
                if(hash == qrdata.code){
                    await Page.route('addDevice', [qrdata.id, qrdata.type, qrdata.code, pid, rid, mid]);
                }
            }
        }, async (error) =>{ await Page.showSnackMessage(error); }, qr_settings );
    },
    /*
    * Alarm Ana Cihaza ve Smart Motion Cihazına Mod Kurulum Fonksiyonu
    */
    setMode: async (did, mode) =>{
        const data = await app.cryptedPost( 
            "ajax/device/set_master.php",
            { 
                'cmd': 'set-mode',
                'did': did, 
                'mode': mode 
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut Gönderildi.");
        }
        else if(data.status == "NO"){
            await Page.showSnackMessage("Başarısız");
        }
    },
    /*
    * Alarm Ana Cihazının Evden Çıkış Süresini Ayarlama Fonksiyonu
    */
    setHomeOutDelay: async () =>{
        var outTime = null;
        await $(".time-list-box .mdl-radio__button").each(function (index){
            var t = $(this);
            if(t.parent(".demo-list-radio").hasClass("is-checked")){
                outTime = t.val();
            }
        });

        const data = await app.cryptedPost(
            "ajax/device/set_out_time.php",
            { 'db_did': $("#time-set-dev-id").val(), 'time' : outTime }
        );

        if(data.status == "OK"){
            await app.delay(500);
            await Page.route("home");
        }else{
            await Page.showSnackMessage("Evden çıkış süresi değiştirilemedi.");
        }
        
        
    },
    
    /*
    * Alarm Ana Cihazın ve Smart Motion Cihazınını Siren ve Sensör 
    * Listesini Getiren Fonksiyon
    */
    getSensors: async (did) =>{
        /*
        alert_count = await 0;

        if(alert_sound_ringing){
            await dev.stop_alert_sound();
        }
        */

        if(did !=null && did != ""){

            const data = await app.cryptedPost(
                "ajax/device/get_sensors.php",
                {
                    'did': did
                }
            );
    
            if(data.status == "OK"){
                var html = await "";
                await $("#sensor-list").empty();
       
                const devs = await data.data;
                var ln = await devs.length;
                if(ln > 0){
                    for(var i = 0 ; i < ln ; i++){
                        html += await  `<div id="sensor-${devs[i].did}" class="mdl-list__item mdl-list__item--two-line ${alert_status[devs[i].status]}">
                                <span class="mdl-list__item-primary-content">
                                <div id="sensor-icon-${devs[i].did}" class="mdl-list__item-avatar ${status_titles[devs[i].status]}">
                                <img src="img/sensors/${dev_type_icon[devs[i].type]}.png"></div>
                                <span>${devs[i].name}</span>
                                <span class="mdl-list__item-sub-title">${titles[devs[i].type]} ${status_chip_titles[devs[i].status]}</span>
                                </span>`;
                                
                        if(devs[i].status == 2){
                            html += await `<button id="menu-lower-${devs[i].did}" class="mdl-list__item-secondary-action mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" rel="3">
                            <i class="material-icons">close</i>
                            </button>`;
                        }
                        else{
                            if(devs[i].type==4 || devs[i].type==5){
                                html += await `<button id="menu-lower-${devs[i].did}" class="mdl-list__item-secondary-action mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" rel="3" onclick="">
                                <i class="material-icons"></i>
                                </button>
                                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="menu-lower-${devs[i].did}">
                                <li class="mdl-menu__item" onclick="dev.changeStatus(&#39;${devs[i].did}&#39;,${status_rv[devs[i].status]})">${status_change_titles[devs[i].status]}</li>
                                <li class="mdl-menu__item" onclick="Page.route('editDevice', [${devs[i].id}, '${devs[i].name}', ${data.pid}, ${data.rid}, ${devs[i].sub}])">Sensörü Düzenle</li>
                                <li class="mdl-menu__item" onclick="dev.setPrecision(&#39;${devs[i].did}&#39;)">Hassasiyet Ayarı</li>
                                </ul>`;                        
                            }else{
                                
                                html += await `<button id="menu-lower-${devs[i].did}" class="mdl-list__item-secondary-action mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" rel="3" onclick="">
                                <i class="material-icons"></i>
                                </button>
                                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="menu-lower-${devs[i].did}">                              
                                <li class="mdl-menu__item" onclick="dev.changeStatus(&#39;${devs[i].did}&#39;,${status_rv[devs[i].status]})">${status_change_titles[devs[i].status]}</li>
                                <li class="mdl-menu__item" onclick="Page.route('editDevice', [${devs[i].id}, '${devs[i].name}', ${data.pid}, ${data.rid}, ${devs[i].sub}])">Sensörü Düzenle</li>
                                </ul>`;
    
                            }
                        }
                                
                        html += await '</div>';
                    }
                    
                    await $("#sensor-list").empty().append(html);
                    await componentHandler.upgradeDom();
                }
                
                const st = await dev.checkAlarmStatus(did, data.dev_status, data.type);
                await $("#alarm-status").html(st);
            }
            else if(data.status == "NO"){
                await Page.showSnackMessage("Sensörler görüntülenemiyor.");
            }
        }

    },
    /*
    * Alarm Ana Cihazın ve Smart Motion Cihazınını Siren ve Sensör 
    * Listesini Getiren Fonksiyon
    */
    putSensors: async (devs, pid, rid, master_type=null) =>{

        if(devs!=null){
            var html = await "";
            await $("#sensor-list").empty();
            var total_alert = await 0;
            var ln = await devs.length;
            if(ln > 0){
                for(var i = 0 ; i < ln ; i++){
                    html += await  `<div id="sensor-${devs[i].did}" class="mdl-list__item mdl-list__item--two-line ${alert_status[devs[i].status]}">
                            <span class="mdl-list__item-primary-content">
                            <div id="sensor-icon-${devs[i].did}" class="mdl-list__item-avatar ${status_titles[devs[i].status]}">
                            <img src="img/sensors/${dev_type_icon[devs[i].type]}.png"></div>
                            <span>${devs[i].name}</span>
                            <span class="mdl-list__item-sub-title">${titles[devs[i].type]} ${status_chip_titles[devs[i].status]}</span>
                            </span>`;
                            
                    if(devs[i].status == 2){
                        total_alert += await 1;
                        html += await `<button id="menu-lower-${devs[i].did}" class="mdl-list__item-secondary-action mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" rel="3">
                        <i class="material-icons">close</i>
                        </button>`;
                    }
                    else{
                        if(devs[i].type==4 || devs[i].type==5){
                            html += await `<button id="menu-lower-${devs[i].did}" class="mdl-list__item-secondary-action mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" rel="3" onclick="">
                            <i class="material-icons"></i>
                            </button>
                            <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="menu-lower-${devs[i].did}">
                            <li class="mdl-menu__item" onclick="dev.changeStatus(&#39;${devs[i].did}&#39;,${status_rv[devs[i].status]})">${status_change_titles[devs[i].status]}</li>
                            <li class="mdl-menu__item" onclick="Page.route('editDevice', [${devs[i].id}, '${devs[i].name}', ${pid}, ${rid}, ${devs[i].sub}])">Sensörü Düzenle</li>
                            <li class="mdl-menu__item" onclick="dev.setPrecision(&#39;${devs[i].did}&#39;)">Hassasiyet Ayarı</li>
                            </ul>`;                        
                        }else{
                            
                            html += await `<button id="menu-lower-${devs[i].did}" class="mdl-list__item-secondary-action mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" rel="3" onclick="">
                            <i class="material-icons"></i>
                            </button>
                            <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="menu-lower-${devs[i].did}">                              
                            <li class="mdl-menu__item" onclick="dev.changeStatus(&#39;${devs[i].did}&#39;,${status_rv[devs[i].status]})">${status_change_titles[devs[i].status]}</li>
                            <li class="mdl-menu__item" onclick="Page.route('editDevice', [${devs[i].id}, '${devs[i].name}', ${pid}, ${rid}, ${devs[i].sub}])">Sensörü Düzenle</li>
                            </ul>`;

                        }
                    }
                            
                    html += await '</div>';
                }

                if(total_alert>0){
                    await dev.start_alert_sound();
                    alert_sound_ringing=await true;
                    if(master_type=="8"){
                        await Page.disableSMBtns();
            
                        await $("#center-btn").removeClass("center-btn-bg-active").removeClass("center-btn-bg-passive").addClass("bg-warning");                        
                        await $("#center-btn .center-circle").css(
                            {
                                "-ms-animation": "ripple 1s ease infinite", 
                                "-moz-animation":"ripple 1s ease infinite",
                                "-webkit-animation": "ripple 1s ease infinite", 
                                "animation":"ripple 1s ease infinite",
                                "border":"2px solid crimson" 
                            }
                        );
                    }
                    else{
                        await Page.disableModBtns();
                        await $("#sensor-pages-btn").html(`SENSÖRLER <span id="alert-count-chip" class="bg-warning"><span id="alert-count">${ln}</span></span>`);
                        await Page.showBottomPageButton();
                    }
                }
                await $("#sensor-list").empty().append(html);
                await componentHandler.upgradeDom();
            }
            
        }
    },

    /*
    * Siren ve Sensörleri tek tek Aktif veya Pasif Eden Fonksiyon
    */
    changeStatus: async (id, st) =>{
        const data = await app.cryptedPost(
            "ajax/c_sensor.php", {
                'cmd': 'change-status',
                'sid': id, 'st': st
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut Gönderildi.");
            await dev.getSensors(active_dev_page);
        }else{
            await Page.showSnackMessage(`Başarısız.${st} ${id}`);
        }
    },

    /*
    * Duman ve Gaz  Sensörleri için Hassasiyet Ayarı Yapılan Fonksiyon
    */
    setPrecision: async (id) =>{
        await $("#precision-set-dev-id").val(id);
        await Page.route("setPrecision");
    },
    
    savePrecision: async (id) =>{
        var precision =await 1;
        await $(".precision-list-box .mdl-radio__button").each(function (index){
            if($(this).prop("checked") == true){ precision = $(this).val(); }
        });
        await console.log(`Hassasiyet: ${precision}`);

        const data = await app.cryptedPost(
            "ajax/c_sensor.php",
            {
                'cmd': 'save-precision',
                'sensor-id': $("#precision-set-dev-id").val(),
                'precision' : precision
            }
        );

        if(data.status == "OK"){
            await app.delay(500);
            await Page.route("home");
        }else{
            await Page.showSnackMessage("Sensör hassasiyeti değiştirilemedi.");
        }
    },

    /*
    * Duman ve Gaz  Sensörleri için Referans Noktası Belirleyen Fonksiyon
    *
    * İPTAL
   
        setReference: async (id) =>{
            navigator.notification.confirm(
                'Bu sensöre referans noktası belirlemek istediğinizde Tamam\'a basın', // message
                async (btnIndex) =>{
                    if(btnIndex==1){
                        const data = await app.cryptedPost(
                            "ajax/c_sensor.php",
                            {
                                'cmd': 'set-reference',
                                'sensor-id': id
                            }
                        );

                        if(data.status == "OK"){
                            await Page.showSnackMessage("Referans komutu gönderildi.");
                        }else{
                            await Page.showSnackMessage("Hata: Referans komutu gönderildi.");
                        }
                    }
                },
                'Teknowin',
                ['Tamam','İptal']
            );
        },
    */

    /*
    * Cihaz Düzenlemesini Kaydetme Fonksiyon
    */
    editSave: async(id, pid, rid) => {
        if(is_empty($("#editDevice #dev-name").val())){
            await $("#editDevice .text-error").html("Sensör adı boş bırakılamaz");
        }else{
            const data = await app.cryptedPost(
                "ajax/device/edit_device.php",
                $("#device-edit-form").serializeArray()
            );

            if(data.status == "OK"){
                await Page.showSnackMessage("Değişiklikler kaydedildi.");
                await Page.route('alarmDevice', [data.id, data.pid, data.rid]);
            }else{
                await Page.showSnackMessage("Değişiklikler kaydedilemedi.");
            }
        }
    },

    /*
    * Ana Cihaz Wi-Fi Ayarlarını Gönderen Fonksiyon
    */
    setWifi: async()=>{
        const ssid = await $("#wifi-ssid").val();
        const pass = await $("#wifi-pass").val();
        var new_wifi = await {'s':ssid, 'p':pass};
        var wifis_s = await window.localStorage.getItem("wifis");
        var wifis = await JSON.parse(wifis_s);
        var wifis = wifis==null ? Array() : wifis;
        for(var i=0; i<wifis.length; i++){
            if(wifis[i].s==ssid){
                wifis.splice( i, 1 );
            }
        }

        await wifis.push(new_wifi);
        await window.localStorage.setItem("wifis", JSON.stringify(wifis));
        const result = await app.getS(
            "http://192.168.4.1:18780/setParams",
            {
                's': ssid,
                'p': pass
            }
        );

        if(result == 1){
            await Page.showSnackMessage("Modem bilgileri gönderildi.");
            
            await Page.route("home");
        }else{
            await Page.showSnackMessage("Modem bilgileri gönderilemedi.");
        }
    },

    /*
    * Alarm Durumunu Sorgulayan Fonksiyon
    */
    checkAlarmStatus: async(did, mode = null, type = null) =>{
        var result = await 0;
        if(is_empty(mode)){
            const data =await app.cryptedPost(
                "ajax/device/get_status.php",
                {
                    'did': did
                }
            );
            if(data.status == "OK"){
                mode = await data.mode;
                type= await data.type;
                result = await alarm_modes_titles[mode];
            }else{
                result = await "Error: Alarm status check error.";
            }
        }
        else{
            result = await alarm_modes_titles[mode];
        }
        if(alert_dids.indexOf(did) < 0){
            if(mode<=3 || mode==23){
                await $(".command-panel .mod-btn-passive").prop("disabled", false).removeClass("mod-btn-passive").addClass("mod-btn-active");
                await $(`#${mode_btns[mode]}`).prop("disabled", true).removeClass("mod-btn-active").addClass("mod-btn-passive");
            }
            else if(mode==20){
                await $(".command-panel .mod-btn-passive").prop("disabled", true).removeClass("mod-btn-active").addClass("mod-btn-passive");
            }
            
            else if(mode==11){
                await Page.reverseSMBtns("#m-set-passive-btn", "#m-set-active-btn");
                await Page.centerBtnSetActive();

            }
            else if(mode==12){
                await Page.reverseSMBtns("#m-set-active-btn", "#m-set-passive-btn");
                await Page.centerBtnSetPassive();
            }
        }
        else{
                    
            if(type=="8" || type=="9"){
                await Page.disableSMBtns();
            }else if(type=="0"){
                await Page.disableModBtns();
            }
        }

        return result;
    },

    /*
    * Alarmı Kapatan Fonksiyon
    */
    stopAllAlarms: async(did, mod)=>{

        const data = await app.cryptedPost(
            "ajax/device/set_master.php",
            {
                'cmd': 'set-stop',
                'did': did
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut gönderildi.");
            await dev.getSensors(active_dev_page);
            await Page.hideBottomPageButton();
        }
    },

    /*
    * Alarmı Sessizleştiren Fonksiyon
    */
    muteMaster: async(did, mod)=>{
        const data = await app.cryptedPost(
            "ajax/device/set_master.php",
            {
                'cmd': 'set-mute',
                'did': did
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut gönderildi.");
        }else{
            await Page.showSnackMessage("Komut gönderilemedi.");
        }
    },

    /*
    * Sensör Uyarılarını Sensör Listesine Yerleştiren Fonksiyon
    */
    putSensorAlert: function(sid){
        $(`#sensor-${sid}`).removeClass("bg-warning-reverse").addClass("bg-warning-reverse");
        $(`#sensor-icon-${sid}, #sensor-${sid} #sensor-status-chip`).removeClass("bg-active").addClass("bg-warning");
        $(`#menu-lower-${sid}`).attr("onClick", `dev.stopSensorWarning(${sid})`);
        $(`#menu-lower-${sid} i`).html("close");
        $(`#sensor-${sid} .mdl-menu__container`).remove();
    },


    /*
    * SMART MOTION
    */

    setActive: async (did, mod) =>{

        const data = await app.cryptedPost(
            "ajax/device/set_master.php",
            {
                'cmd': 'sm-active',
                'did': did
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut Gönderildi.");
        }
        else if(data.status == "NO"){
            await Page.showSnackMessage("Başarısız");
        }
    },
    setPassive: async (did, mod) =>{

        const data = await app.cryptedPost(
            "ajax/device/set_master.php",
            {
                'cmd': 'sm-passive',
                'did': did
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut Gönderildi.");
        }
        else if(data.status == "NO"){
            await Page.showSnackMessage("Başarısız");
        }
    },
    stopAlarm: async (did, mod) =>{

        const data = await app.cryptedPost(
            "ajax/device/set_master.php",
            {
                'cmd': 'sm-stop',
                'did': did
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut gönderildi.");
            await dev.getSensors(active_dev_page);
            await Page.hideBottomPageButton();
        }

    },
    setMute: async (did, mod) =>{

        const data = await app.cryptedPost(
            "ajax/device/set_master.php",
            {
                'cmd': 'sm-mute',
                'did': did
            }
        );
        
        if(data.status == "OK"){
            await Page.showSnackMessage("Komut gönderildi.");
            await dev.getSensors(active_dev_page);
            await Page.hideBottomPageButton();
        }
    },
    takePhoto: async (did, mod) =>{

        const data = await app.cryptedPost(
            "ajax/device/set_master.php",
            {
                'cmd': 'smc-take-photo',
                'did': did
            }
        );

        if(data.status == "OK"){
            await Page.showSnackMessage("Komut gönderildi.");
        }

    },
    start_alert_sound: function(){
        if(!alert_sound_ringing){
            alert_sound = setInterval(function(){ my_media.stop(); my_media.play(); }, 500);
            alert_sound_ringing = true;
            console.log("alert_sound_ringing : false => true");
        }
        else{
            console.log("Ses zaten çalınıyor.");
        }
    },
    stop_alert_sound: function(){
        if(alert_sound_ringing){
            clearInterval(alert_sound);
            
            if(my_media){
                my_media.stop();
                my_media.release();
            }

            alert_sound_ringing = false;
            console.log("alert_sound_ringing : true => false");

        }
    },
    toggle: function(ind){

        var t = $("#device-content-" + ind );
        t.toggleClass("device-content-extend");
        var arrow = t.hasClass("device-content-extend") ? "keyboard_arrow_up" : "keyboard_arrow_down";
        $("#device-item-"+ ind +" .mdl-list__item-secondary-action .material-icons").html(arrow);
        if(t.hasClass("device-content-extend")){
            $("#device-item-"+ ind +" .mdl-list__item-secondary-content").hide();
            $("#device-add-btn-" +ind).show();
        }
        else{
            $("#device-add-btn-" +ind).hide();
            $("#device-item-"+ ind +" .mdl-list__item-secondary-content").show();

        }

   },
   saveDeviceNotify: async (did, pid, rid) =>{
        const data = await app.cryptedPost(
            "ajax/device/set_notify.php",
            $("#edit-notify-form").serializeArray()
        );
        
        if(data.status == "OK"){
            await Page.showSnackMessage("Kaydedildi.");
            await Page.route('alarmDevice', [did, pid, rid]);
        }
        
   },
   saveMotionPrecision: async (did, pid, rid) => {
        const data = await app.cryptedPost(
            "ajax/device/set_m_precision.php",
            $("#motion-precision-form").serializeArray()
        );
        
        if(data.status == "OK"){
            await Page.showSnackMessage("Kaydedildi.");
            await Page.route('alarmDevice', [did, pid, rid]);

        }
   },
   showEditDeviceBtns: async() =>{
       $(".device-route-btn").hide();
       $(".device-delete-btn").show();
       $("#stop-edit-btn").show();
   },
   hideEditDeviceBtns: async() =>{
       $(".device-route-btn").show();
       $(".device-delete-btn").hide();
       $("#stop-edit-btn").hide();
   },
   deleteDevice:async(did)=>{

        await tw_confirm("Cihaz ve bu cihaza ekli olan siren silinecektir. Bu cihazı silmek istediğinize emin misiniz?", async(a)=>{
            if(a==1){

                const data = await app.cryptedPost(
                    "ajax/device/delete_device.php",
                    {
                        'did': did
                    }
                );
                
                if(data.status == "OK"){
                    $("#device-item-" + did).css({"transform":"scaleX(0)", "margin-right":"-150%", "z-index":"-1"});
                    setTimeout(function(){
                        $("#device-item-" + did).remove();
                    },500);
                }
                else{
                    Page.showSnackMessage("Cihaz silinemedi, tekrar deneyiniz.");
                }


            }
        });
   },
   resetDevice: async(did)=>{
    await tw_confirm("Bu cihazı resetlemek istediğinize emin misiniz?", async(a)=>{
        if(a==1){

            const data = await app.cryptedPost(
                "ajax/device/reset_device.php",
                {
                    'did': did
                }
            );
            
            if(data.status == "OK"){
                Page.showSnackMessage("Komut gönderildi.");
            }
            else{
                Page.showSnackMessage("Cihaz resetlenemedi, tekrar deneyiniz.");
            }
        }
    });
   }
};

