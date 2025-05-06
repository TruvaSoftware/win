/*
*   Alarm uyarılarını dinlemek için, 5sn'lik periyotlarla,
*   sorgu yapan Class
*/
var listener = {

    start:async()=>{

        get_alerts_interval = await setInterval(async () =>{ // 5 Saniyelik Interval
            console.log(alert_dids);
            /*
            * Veri tabanından uyarıları iste.
            */
            const data = await app.cryptedPost( "ajax/device/get_alerts.php", {'muid': master_db_id});

            if(data.status == "OK"){
                var total_alert =await 0;
                var datas = await data.datas;

                for(var i=0; i<datas.length;i++){

                    if(datas[i].s_alerts){ /* Sensör uyarısı varsa */

                        await dev.start_alert_sound();
                        var alerts = await datas[i].s_alerts;
                        var a_ln = await alerts.length;
                        total_alert += await a_ln;
                        if(alert_dids.indexOf(datas[i].id) < 0){
                            alert_dids.push(datas[i].id);
                        }

                        

                        if(datas[i].type=="8" || datas[i].type=="9"){

                            if(active_dev_page!=datas[i].id){

                                var text = `<p class="bottom-alert-device-path">${datas[i].pname} > ${datas[i].rname} > ${datas[i].name}</p>`;
                                var alert_ids = [];
                                for(var j=0;j<a_ln; j++){
                                    text += `<div class="bottom-alert-sensor-line"><div class="sensor-icon-sm bg-warning"><img src="img/sensors/motion.png"/></div>  ${datas[i].name} [Hareket Sensörü]</div>`;
                                    alert_ids.push(alerts[j].db_id);
                                }
                                var btns = [
                                    `<a id="ft-bottom-alert-alarm-close-btn" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect cmd-btn" onclick="dev['stopAlarm'].apply(null, ['${datas[i].id}', null])">Kapat</a>`,
                                    `<a id="ft-bottom-alert-alarm-mute-btn" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect cmd-btn" onclick="dev['setMute'].apply(null, ['${datas[i].id}', null])">Sessize Al</a>`,
                                    `<a id="ft-bottom-alert-alarm-ignore-btn" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect cmd-btn" onclick="alertFT.ignore([${alert_ids}])">Yoksay</a>`
                                ];
                                await alertFT.show(
                                    text, 
                                    dev_type_icon[datas[i].type],
                                    datas[i].id,
                                    btns
                                );
                                
                            }else{

                                for(var j = 0 ; j < a_ln ; j++){

                                    if(alerts[j].sid != "motion"){
                                        await dev.putSensorAlert(alerts[j].sid);
                                    }
                                    
                                }
            
                                await Page.disableSMBtns();
                                await Page.centerBtnSetWarning();

                            }

                        }
                        else if(datas[i].type=="0"){


                            if(active_dev_page!=datas[i].id){
                                var isExistRoom = datas[i].rname ? ` > ${datas[i].rname}` : ``;
                                var text = `<p class="bottom-alert-device-path">${datas[i].pname}${isExistRoom} > ${datas[i].name}</p>`;
                                var alert_ids = [];
                                for(var j=0;j<a_ln; j++){
                                    text += `<div class="bottom-alert-sensor-line"><div class="sensor-icon-sm bg-warning"><img src="img/sensors/${dev_type_icon[alerts[j].type]}.png"/></div>  ${alerts[j].name} [${titles[alerts[j].type]}]</div>`;
                                    alert_ids.push(alerts[j].db_id);
                                }
                                var btns = [
                                    `<a id="ft-bottom-alert-alarm-close-btn" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect cmd-btn" onclick="dev['stopAllAlarms'].apply(null, ['${datas[i].id}', null])">Kapat</a>`,
                                    `<a id="ft-bottom-alert-alarm-mute-btn" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect cmd-btn" onclick="dev['muteMaster'].apply(null, ['${datas[i].id}', null])">Sessize Al</a>`,
                                    `<a id="ft-bottom-alert-alarm-ignore-btn" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect cmd-btn" onclick="alertFT.ignore([${alert_ids}])">Yoksay</a>`
                                ];
                                await alertFT.show(
                                    text, 
                                    dev_type_icon[datas[i].type], 
                                    datas[i].id,
                                    btns
                                );

                            }
                            else{

                                for(var k = 0 ; k < a_ln ; k++){
                                    await dev.putSensorAlert(alerts[k].sid);
                                }
                                await Page.disableModBtns();

                                await $("#sensor-pages-btn").html(`SENSÖRLER <span id="alert-count-chip" class="bg-warning"><span id="alert-count">${a_ln}</span></span>`);
                                await Page.showBottomPageButton();

                            }

                        }

                    }
                    else{
                        var al_id_index = alert_dids.indexOf(datas[i].id);
                        if(al_id_index > -1){
                            alert_dids.splice(al_id_index, 1);
                        }


                        if(datas[i].type=="8" || datas[i].type=="9"){

                            if(active_dev_page==datas[i].id){

                                await dev.getSensors(datas[i].id);
                                await Page.enableSMBtns();

                                if(datas[i].status==11){
                                    await Page.reverseSMBtns("#m-set-passive-btn", "#m-set-active-btn");
                                    await Page.centerBtnSetActive();
                                }
                                else if(datas[i].status==12){
                                    await Page.reverseSMBtns("#m-set-active-btn", "#m-set-passive-btn");
                                    await Page.centerBtnSetPassive();
                                }

                            }
                            else{
                                await alertFT.hide(datas[i].id);
                            }

                        }
                        else if(datas[i].type=="0"){

                            if(active_dev_page==datas[i].id){

                                await dev.getSensors(datas[i].id);
                                await Page.enableModBtns();

                                await $("#sensor-pages-btn").html(`SENSÖRLER`);
                                await Page.hideBottomPageButton();

                            }
                            
                            else{
                                await alertFT.hide(datas[i].id);
                            }

                        }

                    }

                    /*
                    *   Sensör durum değişikliği varsa
                    */
                    if(datas[i].s_chnges){
                        var s =await datas[i].s_chnges;
                        if(s.ct=="sPrec"){
                            await Page.showSnackMessage(`${datas[i].name} cihazının hassasiyeti değiştirildi.`);
                        }else if(s.ct=="sNotf"){
                            await Page.showSnackMessage(`${datas[i].name} cihazının dış bildirim düzeni değiştirildi.`);
                        }
                        else{
                            await Page.showSnackMessage(`${s.sid} ID'li ${status_messages[s.ct]}`);
                            await dev.getSensors();
                        }

                    }
                    
                    /*
                    *   Sensör durum değişikliği yoksa
                    */
                    else{
                    }
                    
                    /*
                    *   Mod değişikliği varsa
                    */
                    if(datas[i].m_chnges && datas[i].m_chnges!=22){
                        await Page.showSnackMessage("[ "+ datas[i].name +" ] "+alarm_modes_messages[datas[i].m_chnges]); 
                    }
                    /*
                    *   Mod değişikliği yoksa
                    */
                    else{
                    }

                    if(datas[i].ins_photo && datas[i].ins_photo!=null){
                        if($("#takePhotoDialog").attr("open")==undefined){
                            showTakePhoto(datas[i].ins_photo['time'], datas[i].did, datas[i].ins_photo['photo'],datas[i].id,datas[i].pid,datas[i].rid);

                        }
                    }
                    /*
                    * Cihaz sayfası açıksa, açık olan sayfaya cihazınının durumunu bildir.
                    */
                    if(active_dev_page==datas[i].id){
                        const st = await dev.checkAlarmStatus(datas[i].id, datas[i].status, datas[i].type);
                        await $("#alarm-status").html(st);
                    }
                    

                }
                if(total_alert<=0){
                    await dev.stop_alert_sound();
                }

            }
            else if(data.status=="NO" && data.logout ){
                await user.Logout();
                await Page.showSnackMessage(`Başka bir cihazdan giriş yapıldığı için oturumunuz sonlandırılmıştır.`, 5000);
            }


        },5000);  /* 5 Saniyelik Interval Sonu */

    },

    stop: function(){

        if(get_alerts_interval!=null){
            clearInterval(get_alerts_interval);
            get_alerts_interval = null;
        }

    }
};
