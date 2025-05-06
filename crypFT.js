var crypFT = {
    
    /*
    *   Değişim için kullanılan karakter seti
    */
    chars: {
            'A':'x', 'B':'S', 'C':'o', 'D':'1', 'E':'s', 'F':'T', 
            'G':'t', 'H':'3', 'I':'y', 'J':'e', 'K':'d', 'L':'f', 
            'M':'g', 'N':'h', 'O':'r', 'P':'q', 'Q':'j', 'R':'c',	
            'S':'B', 'T':'F', 'U':'+', 'V':'m', 'W':'7', 'X':'a',
            'Y':'8', 'Z':'5', 'a':'X', 'b':'4', 'c':'R', 'd':'K', 
            'e':'J', 'f':'L', 'g':'M', 'h':'N', 'i':'9', 'j':'Q', 
            'k':'v', 'l':'n', 'm':'V', 'n':'l', 'o':'C', 'p':'6', 
            'q':'P', 'r':'O', 's':'E', 't':'G', 'u':'2', 'v':'k',
            'w':'/', 'x':'A', 'y':'I', 'z':'0', '0':'z', '1':'D', 
            '2':'u', '3':'H', '4':'b', '5':'Z', '6':'p', '7':'W',
            '8':'Y', '9':'i', '+':'U', '/':'w', '=':'='
    },
    /*
    *   Değişim fonksiyonu
    */

    convert: function (data) {
        if (data[0] && (typeof data[0]) == "object") {
            data = this.clearArray(data);
        }
        var text = this.b2a(JSON.stringify(data));
        var newText = new Array();
        for(var i=0;i<text.length;i++){
            newText[i] = this.chars[text[i]];
        }
        return newText.join("").toString();
    },
    /*
    *   Base64 Encode fonksiyonu
    *
    *   Türkçe Karakterler için, encodeURIComponent ve 
    *   unescape fonksiyonlarıyla dönüşümü yapılmıştır.
    */
    b2a: function(data){
        return btoa(unescape(encodeURIComponent(data)))
    },
    /*
    *   Base64 Decode fonksiyonu
    */
    a2b: function(data){
        return atob(data);
    },
    /*
    *   JQUERY realizeArray fonksiyonundan çıkan data:
    *   [{name:"isim",value:"Mehmet"},{name:"soyisim",value:"Yılmaz"}]
    *   clearArray'den geçtikten sonra:
    *   {isim:"Mehmet",soyisim:"Yılmaz"}
    */
    clearArray:function (form){
        var post = {};
        $.each(form, function(key, value){
            post[value['name']] = value['value'];
        });
        return post;
    }
}