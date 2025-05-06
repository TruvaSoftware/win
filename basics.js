/*
*   Genel Kullanım İçin Hazılanmış Fonksiyonlar
*
*
*/

/*
*   Değişken "" veya null veya undefined mi diye kontrol eder.
*/
is_empty = a => { var b = a || "yes_its_empty";  return b == "yes_its_empty" && a !=0 ? true : false; };

write_console = m => { debug_mode ? console.log(m) : null; };

putWifi = (ssid, pass) => { $("#set-wifi-form #wifi-ssid").val(ssid).parent().addClass("is-focused"); $("#set-wifi-form #wifi-pass").val(pass).parent().addClass("is-focused"); };

tw_confirm = (message, callback) => { navigator.notification.confirm(message, callback , "TeknoWin", ["Evet", "Hayır"]); };

ft_confirm = (message, scb, ccb=()=>{}) => {  navigator.notification.confirm(message, (a) =>{ a==1 ? scb() : ccb(); } , "TeknoWin", ["Evet", "Hayır"]); };

scrollToDate = () =>{
    var date =  $("#timeline-date-input").val();
    var target = $('#timeline-day-' + date.replace(/\./g, '-'));
    var surface = $("#time-line-card .mdl-card__supporting-text");
    if(target.length > 0){
        surface.animate({
            scrollTop: surface.scrollTop() + target.offset().top - surface.offset().top - 10
        }, 0);
    }
    else{
        Page.showSnackMessage(`${date} tarihine ait kayıt bulunamadı.`);
    }
}

hideKeyboard = () =>{
    $('#timeline-date-input').blur();
}
imageDownload = (did, time, ftime,fromNow=0) =>{
    if (did == null && time == null && ftime == null) {
        return;
    }
    else {
        var fnwPath = ["","/now"];
        var url = `http://alarm.teknowin.com/records${fnwPath[fromNow]}/${did}/${time}.jpg`;
        var folder_name = "Download";
        var file_name = `${ftime}`;

        var permissions = cordova.plugins.permissions;
        permissions.checkPermission(permissions.WRITE_EXTERNAL_STORAGE, function( status ){
            if ( status.hasPermission ) {

                downloadFile(url, folder_name, file_name);

            }
            else {

                permissions.requestPermission(permissions.WRITE_EXTERNAL_STORAGE, 
                    function( status ) {
                        if(status.hasPermission){
                            downloadFile(url, folder_name, file_name);
                        }
                        else{
                            Page.showSnackMessage(`İndirme yapabilmek için, "Depolama Alanı"na erişim izni vermeniz gerekmektedir.`);
                        }
                    }, function error() {
                    });

            }
          });


    }

}
downloadFile = (URL, Folder_Name, File_Name) =>{
    

    if (URL == null && Folder_Name == null && File_Name == null) {
        return;
    }
    else {
                //step to request a file system 
        window.requestFileSystem  = window.requestFileSystem || window.webkitRequestFileSystem;
        window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, fileSystemSuccess, fileSystemFail);

        function fileSystemSuccess(fileSystem) {
            var download_link = encodeURI(URL);
            ext = download_link.substr(download_link.lastIndexOf('.') + 1); //Get extension of URL

            var directoryEntry = fileSystem.root; // to get root path of directory
            directoryEntry.getDirectory(Folder_Name, { create: true, exclusive: false }, onDirectorySuccess, onDirectoryFail); // creating folder in sdcard
            var rootdir = fileSystem.root;
            var fp = rootdir.nativeURL; // Returns Fulpath of local directory

            fp = fp + "/" + Folder_Name + "/" + File_Name + "." + ext; // fullpath and name of the file which we want to give
            // download function call
            console.log(fp);
            var fileTransfer = new FileTransfer();
            // File download function with URL and local path
            fileTransfer.download(download_link, fp,
                function (entry) {
                    Page.showSnackMessage(`İndirme Tamamlandı: ${entry.fullPath}`);

                },
                function (error) {
                    Page.showSnackMessage(`İndirme Başarısız.`+error);
                    console.log(error);
                }
            );
        }

        function onDirectorySuccess(parent) {
            // Directory created successfuly
        }

        function onDirectoryFail(error) {
            //Error while creating directory
            
        }

        function fileSystemFail(evt) {
            //Unable to access file system
            Page.showSnackMessage(evt.target.error.code);
        }
      }
}

showTakePhoto = (title,did,pic_id,dbid,pid,rid)=>{
    var dialog = document.querySelector('#takePhotoDialog');
        
    if (! dialog.showModal) {
        dialogPolyfill.registerDialog(dialog);
    }
    $("#takePhotoDialog .mdl-dialog__title").html(title);
    $("#takePhotoDialog .take-photo-img").attr("src","http://alarm.teknowin.com/records/now/"+did+"/" +pic_id);
    $("#zoom-img").attr("onclick",`Page.route('PhotoView',[${did},${pic_id.split(".")[0]},[null],[${dbid},${pid},${rid}],1])`);
    $("#download_img").attr("onclick",`imageDownload(${did},${pic_id.split(".")[0]},'${title}',1)`);

    dialog.showModal();
    dialog.querySelector('.close').addEventListener('click', function() { dialog.close(); });
}