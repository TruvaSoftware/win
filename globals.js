//##############################################################################################################
// Global Variables
//##############################################################################################################
//##############################################################################################################
var platform = "browser"; // Platform information ("Android","browser","iOS","Mac OS X")
//##############################################################################################################
var server_url = "http://alarm.teknowin.com/"; // Changeable server address from [server_url]
//##############################################################################################################
var login_status = 0; // Checkable login status from [login_status]
//##############################################################################################################
var debug_mode = true; // Konsolda işlemleri izleyebilmek için
//##############################################################################################################
var firm_key = "teknowin"; // App Firm Key
//##############################################################################################################
var active_page = "intro"; // Checkable active page id [active_page]
var active_dev_page = "";
//##############################################################################################################
var master_db_id = null; // Checkable master device database id [master_db_id]
//##############################################################################################################
var is_master = false; // Checkable master or user [is_master]
//##############################################################################################################
var alert_sound_path ="file:///android_asset/www/media/alert.mp3"; 
//var alert_sound_path = "media/alert.mp3"; // Alert sound path
var alert_count = 0; // Alert sensor count
var alert_sound_ringing = false; // Alert sound status [true or false]
var my_media = null;
var alert_sound; // Alert sound path
var alert_dids = [];
//##############################################################################################################
var n_token; // Firebase token
//##############################################################################################################
var get_alerts_interval = null; // Sensor alerts interval object
var alarm_devices_status = {};
//##############################################################################################################
var qr_settings = {
      preferFrontCamera : false, // iOS and Android
      showFlipCameraButton : false, // iOS and Android
      showTorchButton : true, // iOS and Android
      torchOn: false, // Android, launch with the torch switched on (if available)
      saveHistory: false, // Android, save scan history (default false)
      prompt : "Cihazın üzerindeki QR kodu dikdörtgenin içine okutunuz.", // Android
      resultDisplayDuration: 500, // Android, display scanned text for X ms. 0 suppresses it entirely, default 1500
      formats : "QR_CODE,PDF_417", // default: all but PDF_417 and RSS_EXPANDED
      orientation : "portrait", // Android only (portrait|landscape), default unset so it rotates with the device
      disableAnimations : true, // iOS
      disableSuccessBeep: false // iOS and Android
};
//##############################################################################################################
var titles = [
      "Ana Cihaz", "Siren", "Manyetik Sensör", "Hareket Sensörü", "Duman Sensörü", "Gaz Sensörü", 
      "Su Sensörü" , "Panik Butonu","Smart Motion", "Smart Motion Cam", "Alarm Keypad"
];
//##############################################################################################################
var dev_default_names = [
      "Ana Cihaz", "Siren", "Manyetik Sensör", "Hareket Sensörü", "Duman Sensörü", "Gaz Sensörü", 
      "Su Sensörü" , "Panik Butonu", "Smart Motion", "Smart Motion Cam", "Alarm Keypad"
];
//##############################################################################################################
var dev_add_page_titles = [
      "Cihaz", "Siren", "Sensör", "Sensör", "Sensör", "Sensör", 
      "Sensör" , "Sensör", "Cihaz", "Cihaz", "Keypad"
];
//##############################################################################################################
var dev_type_icon = [
      "master", "siren", "entry", "motion", "smoke", "gas", "water", "panic","smart_motion","smart_motion_cam",
      "alarm_keypad"
];
//##############################################################################################################
var status_titles = [
      "bg-passive", "bg-active", "bg-warning"
];
//##############################################################################################################
var smart_data_bg = [
      "", "bg-passive", "bg-active"
];
//##############################################################################################################
var alert_status = [
      "", "", "bg-warning-reverse"
];
//##############################################################################################################
var status_change_titles = [
      "Sensörü Aktifleştir", "Sensörü Pasifleştir"
];
//##############################################################################################################
var status_rv = [
      1, 0, 0
];
//##############################################################################################################
var alarm_modes_codes = [
      'mod1', // Evden Çıkış
      'mod2', // Gece Modu
      'mod3', // Eve Giriş Modu
];
//##############################################################################################################
var alarm_modes_titles = {
      '1': 'Evden Çıkış Modu', 
      '2': 'Gece Modu', 
      '3': 'Eve Giriş Modu', 

      '10': 'Kurulum Modu', 
      '11': '<b style="color:#3f51b5">Alarm Aktif</b>', 
      '12': '<b style="color:#c3c3c3">Alarm Pasif</b>', 
      '13': '', 

      '20': 'Kurulum Modu', 
      '21': 'Cihaz Başlatıldı.',
      '22': '', 
      '23': 'Kapalı',
      '24': '',
      '25': ''
};
//##############################################################################################################
var mode_btns = {
      '1': 'home-out-btn', 
      '2': 'night-btn', 
      '3': 'home-in-btn',

      '11': 'm-set-active-btn', 
      '12': 'm-set-passive-btn', 

      '23': 'close-btn'
};
//##############################################################################################################
var alarm_modes_messages = {
      '1': 'Evden Çıkış Modu kuruldu.', 
      '2': 'Gece Modu kuruldu.', 
      '3': 'Eve Giriş Modu kuruldu.', 

      '10': 'Cihaz Kurulum Moduna Döndü.', 
      '11': 'Alarm Aktifleştirildi.', 
      '12': 'Alarm Pasifleştirildi.', 
      '13': 'Alarm Sessize Alındı.', 


      '20': 'Cihaz Kurulum Moduna Döndü.', 
      '21': 'Ana Cihaz Başlatıldı.',
      '22': 'Alarm Çalıyor', 
      '23': 'Alarm Kapatıldı',
      '24': 'Alarm Sessize Alındı.',
      '25': 'Evden çıkış süresi değiştirildi.'
};
//##############################################################################################################
var status_messages = {
      'cSenA':'sensör aktifleştirildi.',
      'cSenP':'sensör pasifleştirildi.',
      'cSrnA':'siren aktifleştirildi.',
      'cSrnP':'siren pasifleştirildi.',
      'nSenA':'sensör eklendi.',
      'nSenP':'sensör eklendi.',
      'nSrnA':'siren eklendi.',
      'nSrnP':'siren eklendi.',
      'nKypA':'keypad eklendi.',
      'nKypP':'keypad eklendi.',
      'srn':'siren eklendi.',


      'sens1':'sensörün hassasiyeti değiştirildi.',
      'sens2':'sensörün hassasiyeti değiştirildi.',
      'sens3':'sensörün hassasiyeti değiştirildi.',
      'sens4':'sensörün hassasiyeti değiştirildi.',
      'sens0':'sensörün hassasiyeti değiştirildi.',
      'stRef':'sensörün referans noktası değiştirildi.',

};
//##############################################################################################################
var status_chip_titles = [
      '<span id="sensor-status-chip" class="status-chip bg-passive">Pasif</span>', 
      '<span id="sensor-status-chip" class="status-chip bg-active">Aktif</span>',
      '<span id="sensor-status-chip" class="status-chip bg-warning">Tetiklendi</span>'
];
//##############################################################################################################
var user_status_icon = {
      'admin':'star_rate', 'user':'person'
};
//##############################################################################################################
var user_status_title = {
      'admin':'[ Ana Kullanıcı ]', 'user':''
};
//##############################################################################################################
var soundPaths = {
      "Android": "file:///android_asset/www/media/alert.mp3", "browser":"media/alert.mp3", "iOS":""
};
//##############################################################################################################
//Alarm Menus
var withOutMasterUser = {
      'master':`<a class="mdl-navigation__link" href="javascript:dev.addSensor()"><i class="material-icons">library_add</i>Sensör Cihazı Ekle</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;users&#39;)"><i class="material-icons">supervisor_account</i>Kullanıcılar</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;setHomeOutDelay&#39;)"><i class="material-icons">timer</i>Evden Çıkış Süresi</a>
      <a class="mdl-navigation__link" href="javascript:user.Logout()"><i class="material-icons">exit_to_app</i>Çıkış Yap</a>`,
      
      'motion':`<a class="mdl-navigation__link" href="javascript:dev.addSiren2SM()"><i class="material-icons">library_add</i>Siren Ekle</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;users&#39;)"><i class="material-icons">supervisor_account</i>Kullanıcılar</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;settings&#39;)"><i class="material-icons">settings_applications</i>Ayarlar</a>
      <a class="mdl-navigation__link" href="javascript:user.Logout()"><i class="material-icons">exit_to_app</i>Çıkış Yap</a>`
}


var withMasterUser = {
      'master':`<a class="mdl-navigation__link" href="javascript:Page.route(&#39;masterUserRegister&#39;)"><i class="material-icons">verified_user</i>Ana Kullanıcı Oluştur</a>
      <a class="mdl-navigation__link" href="javascript:dev.addSensor()"><i class="material-icons">library_add</i>Sensör Cihazı Ekle</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;users&#39;)"><i class="material-icons">supervisor_account</i>Kullanıcılar</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;setHomeOutDelay&#39;)"><i class="material-icons">timer</i>Evden Çıkış Süresi</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;settings&#39;)"><i class="material-icons">settings_applications</i>Ayarlar</a>
      <a class="mdl-navigation__link" href="javascript:user.Logout()"><i class="material-icons">exit_to_app</i>Çıkış Yap</a>`,
      
      'motion':`<a class="mdl-navigation__link" href="javascript:Page.route(&#39;masterUserRegister&#39;)"><i class="material-icons">verified_user</i>Ana Kullanıcı Oluştur</a>
      <a class="mdl-navigation__link" href="javascript:dev.addSiren2SM()"><i class="material-icons">library_add</i>Siren Ekle</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;users&#39;)"><i class="material-icons">supervisor_account</i>Kullanıcılar</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;settings&#39;)"><i class="material-icons">settings_applications</i>Ayarlar</a>
      <a class="mdl-navigation__link" href="javascript:user.Logout()"><i class="material-icons">exit_to_app</i>Çıkış Yap</a>`
}

var masterUserMenu =
      `<a class="mdl-navigation__link" href="javascript:Page.route(&#39;users&#39;)"><i class="material-icons">supervisor_account</i>Kullanıcılar</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;timeLine&#39;)"><i class="material-icons">timeline</i>Alarm Geçmişi</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;settings&#39;)"><i class="material-icons">settings_applications</i>Ayarlar</a>
      <a class="mdl-navigation__link" href="javascript:user.Logout(true)"><i class="material-icons">exit_to_app</i>Çıkış Yap</a>`;

var userMenu =
      `<a class="mdl-navigation__link" href="javascript:Page.route(&#39;timeLine&#39;)"><i class="material-icons">timeline</i>Alarm Geçmişi</a>
      <a class="mdl-navigation__link" href="javascript:Page.route(&#39;settings&#39;)"><i class="material-icons">settings_applications</i>Ayarlar</a>
      <a class="mdl-navigation__link" href="javascript:user.Logout(true)"><i class="material-icons">exit_to_app</i>Çıkış Yap</a>`;
