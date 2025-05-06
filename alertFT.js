/*
*   Alarm uyarılarını belirtmek için kullanılan alert Class'ı
*/
var alertFT = {

    show: function(message, sensor_type, device_id, btns, hold=null){
        if(!$(`#ft-bottom-alert-${device_id}`).length){

            $(".ft-bottom-alert-wrap").append(
                `<div id="ft-bottom-alert-${device_id}" class="ft-bottom-alert-box">
                    <div class="demo-card-wide mdl-card mdl-shadow--2dp">
                        <div class="mdl-card__supporting-text">
                            <div id="ft-bottom-alert-icon" class="mdl-list__item-avatar">
                                <img src="img/sensors/${sensor_type}.png">
                            </div>
                            <div id="ft-bottom-alert-text" class="bottom-alert-message-text">
                                ${message}
                            </div>
                                
                        </div>
                        <div class="mdl-card__actions mdl-card--border">
                        ${btns[0]}
                        ${btns[1]}
                        ${btns[2]}
                        </div>
                        <div class="mdl-card__menu">
                        <button id="ft-bottom-alert-go-alarm" class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect" onclick="Page.route('alarmDevice',[${device_id}])">
                            <i class="material-icons">keyboard_arrow_right</i>
                        </button>
                        </div>
                    </div>
                </div>`
            );
            setTimeout(function(){
                $(`#ft-bottom-alert-${device_id}`).addClass("ft-bottom-alert-show");
    
            },10);
            if(hold){
                setTimeout(alertFT.hide(device_id),hold);
            }
        }
        else{
            $(`#ft-bottom-alert-${device_id} #ft-bottom-alert-text`).html(message);
            $(`#ft-bottom-alert-${device_id} .mdl-card__actions`).html(btns[0]+btns[1]+btns[2]);
        }

    },
    hide: function(device_id){

        $(`#ft-bottom-alert-${device_id}`).removeClass("ft-bottom-alert-show");
        setTimeout(()=>{ $(`#ft-bottom-alert-${device_id}`).remove(); },500);

    },
    ignore: async(alert_ids)=>{
        const data =await app.cryptedPost("ajax/device/ignore_alerts.php", { 'ids': alert_ids});
        if(data.status=="OK"){ }
    }
};
