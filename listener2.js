/*
*   Alarm uyarılarını dinlemek için, 5sn'lik periyotlarla,
*   sorgu yapan Class
*/
var listener = {

    start:async()=>{

    },



    /*
    * Alarm Ana Cihaz İçin
    */
    master:async() =>{

        get_alerts_interval = await setInterval(async () =>{ // 5 Saniyelik Interval

            /*
            * Veri tabanından uyarıları iste.
            */
            const data = await app.cryptedPost( "ajax/c_sensor.php", { 'cmd': 'get-alerts', 'uid': master_db_id});

            if(data.status == "OK"){ // Cevap Alındıysa
                if(data.data != ""){ // Sensör uyarısı varsa
                    alert_count =await 0;
                    const alerts = await data.data;
                    var ln = await alerts.length;
                    alert_count =await ln;
                    await dev.start_alert_sound();

                    for(var i = 0 ; i < ln ; i++){
                        await dev.putSensorAlert(alerts[i].sid);
                    }
                    await Page.disableModBtns();

                    await $("#sensor-pages-btn").html(`SENSÖRLER <span id="alert-count-chip" class="bg-warning"><span id="alert-count">${alert_count}</span></span>`);
                    await Page.showBottomPageButton();
                }
                else{ // Sensör uyarısı yoksa
                    if(alert_count != 0){ // Önceden kalmış bir uyarı varsa
                        await $("#sensor-pages-btn").html('SENSÖRLER');
                        await dev.stop_alert_sound();
                        await dev.getSensors();
                        await Page.enableModBtns();
                    }
                }

                if(data.data2 != null){ // Mod değişimi varsa

                    if(alarm_modes_titles[data.data2] != ""){ await $("#alarm-status").html(alarm_modes_titles[data.data2]); }
                    await Page.showSnackMessage(alarm_modes_messages[data.data2]);
                    
                    await dev.stop_alert_sound();
                    await dev.getSensors();
                }

                if(data.data3 != null){ // Mod değişimi varsa
                    var s =await data.data3;
                    await Page.showSnackMessage(`${s.sid} ID'li ${status_messages[s.ct]}`);
                    await dev.getSensors();
                }
                const st = await dev.checkAlarmStatus(data.dev_status);
                await $("#alarm-status").html(st);
            }
        },5000);  // 5 Saniyelik Interval Sonu

    },
    /*
    * Smart Motion İçin
    */
    motion: async() =>{

        get_alerts_interval = await setInterval(async () =>{ // 5 Saniyelik Interval
            /*
            *   Veri tabanından uyarıları iste.
            */
            const data = await app.cryptedPost(
                "ajax/c_sensor.php",
                {
                    'cmd': 'get-alerts',
                    'uid': master_db_id
                }
            );

            if(data.status == "OK"){ // Cevap Alındıysa
                var alerts_data = data.data || 'Yok';
                alerts_data = alerts_data == 'Yok' ? alerts_data : JSON.stringify(alerts_data);
                var modes = data.data2 || 'Yok';
                var changes = data.data3 || 'Yok';

                write_console(`###############################################\nSensör Uyarısı\t\t\t: ${alerts_data}\nMod Değişimi\t\t\t: ${modes}\nSensör Durum Değişikliği\t: ${changes}`);
                if(data.data != ""){ // Sensör uyarısı varsa
                    
                    await dev.start_alert_sound();

                    alert_count = await 0;
                    const alerts = await data.data;
                    var ln = await alerts.length;
                    alert_count = await ln;

                    for(var i = 0 ; i < ln ; i++){

                        if(alerts[i].sid != "motion"){
                            await dev.putSensorAlert(alerts[i].sid);
                        }
                        
                    }

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
                else{ // Sensör uyarısı yoksa

                    alert_count = await 0;
                    await dev.stop_alert_sound();
                    await dev.getSensors();
                    await Page.enableSMBtns();

                }

                if(data.data2 != null){ // Mod değişimi varsa

                    if(!is_empty(alarm_modes_titles[data.data2])){
                        await $("#alarm-status").html(alarm_modes_titles[data.data2]);
                    }
                    await Page.showSnackMessage(alarm_modes_messages[data.data2]);

                    if(data.data2 != 22){
                        await dev.stop_alert_sound();
                    }
                    await dev.getSensors();

                }

                if(data.data3 != null){ // Sensör veya siren durumunda bir değişiklik olursa

                    var s = await data.data3;
                    await Page.showSnackMessage(`${s.sid} ID'li ${status_messages[s.ct]}`);
                    await dev.getSensors();

                }
                if(alert_count == 0){

                    const st = await dev.checkAlarmStatus(data.dev_status);
                    await $("#alarm-status").html(st);

                }
            }


        },5000);  // 5 Saniyelik Interval Sonu

    },
    stop: function(){

        if(get_alerts_interval!=null){
            clearInterval(get_alerts_interval);
            get_alerts_interval = null;
        }

    }
};
