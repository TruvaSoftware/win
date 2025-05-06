<?php 
    
    function send_reset_mail($mail, $hash, $ad_soyad){
        
        include('class.phpmailer.php');
        $mail_html = "
        <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
           <head>
              <meta content='text/html; charset=UTF-8' http-equiv='Content-Type' />
              <meta content='telephone=no' name='format-detection' />
              <meta content='width=mobile-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no' name='viewport' />
              <meta content='IE=9; IE=8; IE=7; IE=EDGE' http-equiv='X-UA-Compatible' />
              <title>Eğitimdeyiz</title>

              <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&subset=greek-ext' rel='stylesheet'>

              <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js'></script>

              <style type='text/css'>
                 /* This is to overwrite Outlook.com’s Embedded CSS */
                 table {border-collapse:separate;}
                 a, a:link, a:visited {text-decoration: none; color: #00788a;}
                 h2,h2 a,h2 a:visited,h3,h3 a,h3 a:visited,h4,h5,h6,.t_cht {color:#000 !important;}
                 p {margin-bottom: 0}
                 .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td {line-height: 100%;}                            
                 .ExternalClass {width: 100%;}/**This is to center your email in Outlook.com************/
                 /* General Resets */
                 #outlook a {padding:0;}
                 body, #body-table {height:100% !important; width:100% !important; margin:0 auto; padding:0; line-height:100% !important; font-family: 'Lato', sans-serif;}
                 img, a img {border:0; outline:none; text-decoration:none;}
                 .image-fix {display:block;}
                 table, td {border-collapse:collapse;}
                 /* Client Specific Resets */
                 .ReadMsgBody {width:100%;} .ExternalClass{width:100%;}
                 .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100% !important;}
                 .ExternalClass * {line-height: 100% !important;}
                 table, td {mso-table-lspace:0pt; mso-table-rspace:0pt;}
                 img {outline: none; border: none; text-decoration: none; -ms-interpolation-mode: bicubic;}
                 body, table, td, p, a, li, blockquote {-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%;}
                 body.outlook img {width: auto !important;max-width: none !important;}
                 /* Start Template Styles */
                 /* Main */
                 body{ -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
                 body, #body-table {background-color: #ffffff; margin:0 auto !important;; text-align:center !important;}
                 p {padding:0; margin: 0; line-height: 24px; font-family: 'Open Sans',sans-serif;}
                 a, a:link {color: #1c344d;text-decoration: none !important;}
                 .footer-link a, .nav-link a {color: #fff6e5;}
                 /* Yahoo Mail */
                 .thread-item.expanded .thread-body{background-color: #000000 !important;}
                 .thread-item.expanded .thread-body .body, .msg-body{display:block !important;}
                 #body-table .undoreset table {display: table !important;table-layout: fixed !important;}
                 /* Start Media Queries */
                 @media only screen and (max-width: 800px) {
                 a[href^='tel'], a[href^='sms'] {text-decoration: none;pointer-events: none;    cursor: default;}
                 .mobile_link a[href^='tel'], .mobile_link a[href^='sms'] {text-decoration: default;    pointer-events: auto;cursor: default;}    
                 *[class].mobile-width {width: 600px !important; padding: 0 4px;}
                 *[class].center-stack {padding-bottom:30px !important; text-align:center !important; height:auto !important;}
                 *[class].bottom-stack {padding-bottom:30px !important;}
                 *[class].stack {padding-bottom:30px !important; height: auto !important;}
                 *[class].gallery {padding-bottom: 20px!important;}
                 *[class].fluid-img {height:auto !important; max-width:600px !important; width: 100% !important;}
                 *[class].block {display: block!important;}
                 *[class].midaling { width:100% !important; border:none !important; }
                 *[class].full-width-layout {width: 100%!important;}
                 *[class].test-hidden-space{ height:40px !important;}
                 *[class].test-center{ width:100% !important; height:auto !important;text-align:center;}    
                 *[class].bg-res1{ background-position: center center !important;background-size: 100% 200% !important; padding:40px 0px !important;}
                 *[class].bg-res2{ background-position: center center !important;background-size: 100% 200% !important;padding:40px 0px !important;}
                 *[class].res-space{ height:50px !important;}
                 }
                 @media only screen and (max-width: 799px) { 
                 *[class].content-width-bg { width:700px !important; }
                 *[class].center-bg { width:350px !important; }
                 *[class].center-bg-image { width:350px !important;}
                 *[class].hidden-space{ display:none;}
                 }
                 @media only screen and (max-width: 700px) {    
                 *[class].content-width-bg { width:600px !important; }     
                 *[class].center-bg { width:300px !important; }
                 *[class].center-bg-image { width:300px !important;} 
                 }
                 @media only screen and (max-width: 640px) {
                 *[class].content-width-bg { width:100% !important; }     
                 *[class].center-bg {text-align:center !important; margin:0 auto  !important; width:100% !important; }
                 *[class].center-bg-image {text-align:center !important;margin:0 auto  !important; width:100% !important;}
                 *[class].mobile-width {width: 480px!important; padding: 0 4px;}
                 *[class].content-width {width: 360px!important;}
                 *[class].no-padding {padding:0px !important;}
                 *[class].icon-columns{ width:360px !important; border:none !important; }
                 *[class].center {text-align:center !important; height:auto !important;margin:0 auto  !important;  width:100%;}
                 *[class].center-btn {text-align:center !important; height:auto !important; margin:0 auto; display:block;}
                 #inspired-edit .ui-sortable-helper{left:60px !important;  border-top:1px solid #2385f3 !important;;border-bottom:1px solid #2385f3 !important;}
                 *[class].hidden-space1{ display:none;}
                 *[class].center-bg {text-align:center !important; margin:0 auto  !important; width:100% !important; }
                 *[class].center-bg-image {text-align:center !important;margin:0 auto  !important; width:100% !important;}
                 }
                 @media only screen and (max-width: 480px) {
                 *[class].full-width {width: 100%!important;}
                 *[class].mobile-width {width: 360px!important; padding: 0 4px;}
                 *[class].content-width {width: 300px!important;}
                 *[class].icon-columns{ width:300px !important; border:none !important; }
                 *[class].center {text-align:center !important; height:auto !important; margin:0 auto  !important; width:100%;}
                 *[class].center-btn {text-align:center !important; height:auto !important; margin:0 auto; display:block;}
                 *[class].center-stack {padding-bottom:30px !important; text-align:center !important; height:auto !important;}
                 *[class].stack {padding-bottom:30px !important; height: auto !important;}
                 *[class].gallery {padding-bottom: 20px!important;}
                 *[class].midaling { width:100% !important; border:none !important; }
                 *[class].center-bg {text-align:center !important; margin:0 auto  !important; width:100% !important; }
                 *[class].center-bg-image {text-align:center !important;margin:0 auto  !important; width:100% !important;}
                 }
                 @media only screen and (max-width: 360px) {
                 *[class].full-width {width: 100%!important;}
                 *[class].mobile-width {width: 100%!important; padding: 0 4px;}
                 *[class].content-width {width: 280px!important;}
                 *[class].icon-columns{ width:290px !important; border:none !important; }
                 *[class].center {text-align:center !important; height:auto !important;margin:0 auto  !important;  width:100%;}
                 *[class].center-btn {text-align:center !important; height:auto !important; margin:0 auto; display:block;}
                 *[class].center-stack {padding-bottom:30px !important; text-align:center !important; height:auto !important;}
                 *[class].stack {padding-bottom:30px !important; height: auto !important;}
                 *[class].gallery {padding-bottom: 20px!important;}
                 *[class].fluid-img {height:auto !important; max-width:600px !important; width: 100% !important; min-width:320px !important;}
                 *[class].midaling { width:100% !important; border:none !important;}
                 #inspired-edit .ui-sortable-helper{left:0px !important;  border-top:1px solid #2385f3 !important;;border-bottom:1px solid #2385f3 !important;}
                 *[class].center-bg {text-align:center !important; margin:0 auto  !important; width:100% !important; }
                 *[class].center-bg-image {text-align:center !important;margin:0 auto  !important; width:100% !important;}
                 }
              </style>

           </head>

           <body yahoo='fix' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>

              <table align='center' width='100%' cellspacing='0' bgcolor='#ffffff' cellpadding='0' border='0' class='full-width white_bg'>
                 <tbody>
                    <tr>
                       <td class='res-space' height='60'></td>
                    </tr>
                 </tbody>
              </table>

              <table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='ffffff' style='table-layout:fixed;' class='white_bg'>
                 <tbody>
                    <tr>
                       <td>
                          <table cellpadding='0' cellspacing='0' border='0' align='center' width='800' class='mobile-width' style='border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;'>
                             <tbody>
                                <tr>
                                   <td align='center'>
                                      <table cellpadding='0' cellspacing='0' border='0' align='center' width='600' class='content-width'>
                                         <tbody>
                                            <tr>
                                               <td align='center'>
                                                  <table cellpadding='0' cellspacing='0' border='0' align='left' class='center' style='border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;'>
                                                     <tbody>
                                                        <tr>
                                                           <td align='center' valign='middle'><a href='#' style='color: #16a085; display:block; max-height:100%;' ><img src='http://user.teknowin.com/teknowin.png' alt='' width='236' height='50'  /></a></td>
                                                        </tr>
                                                     </tbody>
                                                  </table>
                                               </td>
                                            </tr>
                                         </tbody>
                                      </table>
                                   </td>
                                </tr>
                             </tbody>
                          </table>
                       </td>
                    </tr>
                 </tbody>
              </table>

              <table align='center' width='100%' cellspacing='0' bgcolor='#ffffff' cellpadding='0' border='0' class='full-width white_bg'>
                 <tbody>
                    <tr>
                       <td class='res-space' height='60'></td>
                    </tr>
                 </tbody>
              </table>

              <table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='#ffffff' class='white_bg' style='table-layout:fixed;'>
                 <tbody>
                    <tr>
                       <td>
                          <table border='0' align='center' width='800' cellpadding='0' cellspacing='0' class='mobile-width' style='border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;'>
                             <tbody>
                                <tr>
                                   <td align='center'>
                                      <table cellspacing='0' cellpadding='0' border='0' width='600' class='content-width'>
                                         <tbody>
                                            <tr>
                                               <td>
                                                  <table cellspacing='0' cellpadding='0' border='0' width='100%' align='center' class='content-width center'>
                                                     <tbody>

                                                        <tr>
                                                           <td style='font-family: Open Sans,sans-serif; font-size: 25px; mso-line-height-rule:exactly; font-weight:700; line-height:30px;color:#1e1e1e; text-transform:uppercase;' class='title_color'>
                                                              Şifre Sıfırlama
                                                           </td>
                                                        </tr>
                                                        <tr>
                                                           <td height='5' style='font-size: 5px; line-height: 5px;'>&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                           <td>
                                                              <table cellspacing='0' cellpadding='0' border='0' width='55' class='content-width'>
                                                                 <tbody>
                                                                    <tr>
                                                                       <td><a href='#' style='border-style: none !important; border: 0 !important;'><img src='http://user.teknowin.com/s-line.png' width='55' height='25' alt='' /></a></td>
                                                                    </tr>
                                                                 </tbody>
                                                              </table>
                                                           </td>
                                                        </tr>
                                                        <tr>
                                                           <td height='5' style='font-size: 5px; line-height: 5px;'>&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                           <td style='font-family: Open Sans,sans-serif; font-size: 13px; mso-line-height-rule:exactly; line-height:21px; font-weight:300;color: #393939;' class='text_color'>
                                                                Merhaba, ".$ad_soyad.",<br><br>
                                                                Teknowin Alarm uygulama şifrenizi sıfırlama isteğinde bulundunuz. Aşağıdaki linke tıklayarak yeni şifre oluşturabilirsiniz.<br><br>
                                                                <a href='http://alarm.teknowin.com/reset_password.php?h=".$hash."&t=true'>Şifrenizi sıfırlamak için tıklayınız.</a><br><br><br><br>
                                                                Eğer bu değişiklik talebinin tarafınızdan yapılmadığını düşünüyorsanız lütfen <a href='http://alarm.teknowin.com/reset_password.php?h=".$hash."&t=false'>tıklayın.</a>
                                                           </td>
                                                        </tr>

                                                     </tbody>
                                                  </table>
                                               </td>
                                            </tr>
                                            <tr>
                                               <td height='50' style='font-size: 50px; line-height: 20px;'>&nbsp;</td>
                                            </tr>
                                         </tbody>
                                      </table>
                                   </td>
                                </tr>
                             </tbody>
                          </table>
                       </td>
                    </tr>
                 </tbody>
              </table>

              <table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#ffffff' class='full-width white_bg'>
                 <tbody>
                    <tr>
                       <td height='60'></td>
                    </tr>
                 </tbody>
              </table>

              <table cellpadding='0' cellspacing='0' border='0' bgcolor='#f7f7f7' align='center' width='100%'  style='border-top:1px solid #e1e1e1' class='full-width grey_bg'>
                 <tbody>
                    <tr>
                       <td>
                          <table border='0' align='center' width='800' cellpadding='0' cellspacing='0' class='mobile-width' style='border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;'>
                             <tbody>
                                <tr>
                                   <td align='center'>
                                      <table cellspacing='0' cellpadding='0' border='0' width='600' class='content-width'>
                                         <tbody>
                                            <tr>
                                               <td height='10' style='font-size: 10px; line-height: 10px;'>&nbsp;</td>
                                            </tr>                   
                                            <tr>
                                               <td>
                                                  <table cellspacing='0' cellpadding='0' border='0' align='center' class='content-width'>
                                                     <tbody>
                                                        <tr>
                                                           <td align='center' style='font-family: Open Sans,sans-serif; font-size: 13px; mso-line-height-rule:exactly; font-weight:400;line-height:30px;color:#999999;' class='dark_color center'>
                                                               &copy; 2019 Teknowin Akıllı Ev Sistemleri.
                                                           </td>
                                                        </tr>
                                                     </tbody>
                                                  </table>
                                               </td>
                                            </tr>
                                            <tr>
                                               <td height='10' style='font-size: 10px; line-height: 10px;'>&nbsp;</td>
                                            </tr>
                                         </tbody>
                                      </table>
                                   </td>
                                </tr>
                             </tbody>
                          </table>
                       </td>
                    </tr>
                 </tbody>
              </table>
           </body>
        </html>";

        $kmail = new PHPMailer();
        $kmail->CharSet="utf-8";
        $kmail->AddAddress($mail); 
        $kmail->Subject  = "Teknowin / Şifre Sıfırlama"; 
        $kmail->Body     = $mail_html; 
        $kmail->IsSMTP(); 
        $kmail->Host     = 'mail.teknowin.com'; 
        $kmail->Port     = '587';
        $kmail->SMTPAuth = true;
        $kmail->Username = 'noreply@teknowin.com';
        $kmail->Password = 'MwmMW3Vx';
        $kmail->IsHTML(true);
        $kmail->From     = 'noreply@teknowin.com'; 
        $kmail->FromName = 'teknowin.com';
        $kmail->Send();        
    }


?>