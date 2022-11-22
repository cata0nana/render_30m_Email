<?php
   /*
   input:
   
   User $user - User object
   array $config - config array
   array $emails - array of emails
   */
   
   require_once './autolink.php';
   
   // Load HTML Purifier
   $purifier_config = HTMLPurifier_Config::createDefault();
   $purifier_config->set('HTML.Nofollow', true);
   $purifier_config->set('HTML.ForbiddenElements', array("img"));
   $purifier = new HTMLPurifier($purifier_config);
   
   \Moment\Moment::setLocale($config['locale']);
   
   $mailIds = array_map(function ($mail) {
       return $mail->id;
   }, $emails);
   $mailIdsJoinedString = filter_var(join('|', $mailIds), FILTER_SANITIZE_SPECIAL_CHARS);
   
   // define bigger renderings here to keep the php sections within the html short.
   function niceDate($date) {
       $m = new \Moment\Moment($date, date_default_timezone_get());
       return $m->calendar();
   }
   
   function printMessageBody($email, $purifier) {
       global $config;
   
       // To avoid showing empty mails, first purify the html and plaintext
       // before checking if they are empty.
       $safeHtml = $purifier->purify($email->textHtml);
   
       $safeText = htmlspecialchars($email->textPlain);
       $safeText = nl2br($safeText);
       $safeText = \AutoLinkExtension::auto_link_text($safeText);
   
       $hasHtml = strlen(trim($safeHtml)) > 0;
       $hasText = strlen(trim($safeText)) > 0;
   
       if ($config['prefer_plaintext']) {
           if ($hasText) {
               echo $safeText;
           } else {
               echo $safeHtml;
           }
       } else {
           if ($hasHtml) {
               echo $safeHtml;
           } else {
               echo $safeText;
           }
       }
   }
   
   ?>
<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="assets/bootstrap/4.1.1/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
      <link rel="stylesheet" href="assets/fontawesome/v5.0.13/all.css">
      <title>
         <?php
            echo $emails ? "(" . count($emails) . ") " : "";
            echo $user->address ?>
      </title>
      <link rel="stylesheet" href="assets/spinner.css">
      <link rel="stylesheet" href="assets/custom.css">
      <script>
         var mailCount = <?php echo count($emails)?>;
         setInterval(function() {
             var r = new XMLHttpRequest();
             r.open("GET", "?action=has_new_messages&address=<?php echo $user->address?>&email_ids=<?php echo $mailIdsJoinedString?>", true);
             r.onreadystatechange = function() {
                 if(r.readyState != 4 || r.status != 200) return;
                 if(r.responseText > 0) {
                     console.log("There are", r.responseText, "new mails.");
                     document.getElementById("new-content-avalable").style.display = 'block';
                     // If there are no emails displayed, we can reload the page without losing any state.
                     if(mailCount === 0) {
                         location.reload();
                     }
                 }
             };
             r.send();
         }, 15000);
      </script>
<!--       <script>
         $(document).ready(function() {
             $("#about_l").click(function() {
                 $("#contact").collapse("toggle", {
                     toggle: false
                 });
             });
         });
      </script> -->

      <script>
         function myFunction() {
             if(!$("#contact").hasClass('show')) {
                 alert("Uncollapsed");
             } else {
                 $('#contact').collapse('toggle');
                 $('#about').collapse('toggle');
             }
             /*alert("Hello! I am an alert box!!");*/
             /*$('.collapse').collapse('hide');*/
             /*           $('#contact').collapse('hide');
                        sleep(10000)*/
             /*           $('#about').collapse('show');*/
             /*           $('#contact').on('hidden.bs.collapse', function () {
                         alert("Hello! I am an alert box!!");*/
         }
      </script>
   </head>
   <body>
      <div class="tgme_background_wrap">
         <canvas id="tgme_background" class="tgme_background default" width="50" height="50" data-colors="dbddbb,6ba587,d5d88d,88b884"></canvas>
         <div class="tgme_background_pattern default"></div>
      </div>
      <div id="new-content-avalable">
         <div class="alert alert-info alert-fixed" role="alert"> <strong>New emails</strong> have arrived.
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()"> <i class="fas fa-sync"></i> Reload! </button>
         </div>
         <!-- move the rest of the page a bit down to show all content -->
         <div style="height: 3rem">&nbsp;</div>
      </div>
      <header>
         <!--     <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
              <a class="navbar-brand" href="#">Navbar</a>
            </nav>
            </div> -->
         <div class="container">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
               <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
                  <ul class="navbar-nav mr-auto">
                     <li class="nav-item "> <a class="nav-link" data-toggle="collapse" aria-expanded="false" aria-controls="term_and_pivacy" href="#term_and_pivacy">Home</a> 
                     </li>
                     <li class="nav-item"> <a class="nav-link" data-toggle="collapse" aria-expanded="false" aria-controls="term_and_pivacy" href="#term_and_pivacy">
                        Terms and Pivacy</a> 
                     </li>
                     <!--             <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                        </li>
                        -->
                  </ul>
               </div>
               <div class="mx-auto order-0">
                  <a class="navbar-brand mx-auto" href="#"> <img src="assets/img/w2.png" width="45" height="45" alt=""></a>
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2"> <span class="navbar-toggler-icon"></span> </button>
               </div>
               <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
                  <ul class="navbar-nav ml-auto">
                     <li class="nav-item ">
                        <a class="nav-link" data-toggle="collapse" aria-expanded="false" aria-controls="about_us" href="#about_us">
                        About us
                        </a> 
                     </li>
                     <li class="nav-item ">
                        <a class="nav-link" data-toggle="collapse" aria-expanded="false" aria-controls="contact_us" href="#contact_us">
                        Contact Us
                        </a> 
                     </li>
                  </ul>
               </div>
            </nav>
            <p class="lead "> Your disposable mailbox is ready. </p>
                <iframe data-aa='2086335' loading='lazy' src='//ad.a-ads.com/2086335?size=970x90' style='width:970px; height:90px; border:0px; padding:0; overflow:hidden; background-color: transparent;'></iframe>

         <!--
               
            <div class="my-col">

            </div> -->


      <div class="columnsn">
        <div class="cardn flow1">
            <div class="my-address-block"> <span id="my-address"><?php echo $user->address ?></span>
               &nbsp;<button class="copy-button" data-clipboard-target="#my-address">Copy</button>
               </div>

        </div>

        <div class="cardn2 flow2">
         <button type="button" class="change-button" data-toggle="collapse" title="choose your own address" data-target=".change-address-toggle" aria-controls="address-box-normal address-box-edit" aria-expanded="false"> <i class="fas fa-magic"></i> Change address </button>
           

        </div>
    </div>
            <form class="collapse change-address-toggle" id="address-box-edit" action="?action=redirect" method="post">
               <div class="card">
                  <div class="card-body">
                     <p>
                        <a href="?action=random" role="button" class="btn btn-dark"> <i class="fa fa-random"></i> Open random mailbox </a>
                     </p>
                     or create your own address:
                     <div class="form-row align-items-center">
                        <div class="col-sm">
                           <label class="sr-only" for="inlineFormInputName">username</label>
                           <input name="username" type="text" class="form-control" id="inlineFormInputName" placeholder="username" value="<?php echo $user->username ?>"> 
                        </div>
                        <div class="col-sm-auto my-1">
                           <label class="sr-only" for="inlineFormInputGroupUsername">Domain</label>
                           <div class="input-group">
                              <div class="input-group-prepend">
                                 <div class="input-group-text">@</div>
                              </div>
                              <select class="custom-select" id="inlineFormInputGroupUsername" name="domain">
                              <?php
                                 foreach ($config['domains'] as $aDomain) {
                                     $selected = $aDomain === $user->domain ? ' selected ' : '';
                                     print "<option value='$aDomain' $selected>$aDomain</option>";
                                 }
                                 ?>
                              </select>
                           </div>
                        </div>
                        <div class="col-auto my-1">
                           <button type="submit" class="btn btn-primary">Open mailbox</button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </header>
      <main>
         <div class="container">
            
            <div id="email-list" class="list-group" style="color: #fff;background-color: #343a40;">
               <?php
                  foreach ($emails as $email) {
                      $safe_email_id = filter_var($email->id, FILTER_VALIDATE_INT); ?>
               <a class="list-group-item list-group-item-action email-list-item" data-toggle="collapse" href="#mail-box-<?php echo $email->id ?>" role="button" aria-expanded="false" aria-controls="mail-box-<?php echo $email->id ?>">
                  <div class="media">
                     <button class="btn btn-white open-collapse-button"> <i class="fas fa-caret-right expand-button-closed"></i> <i class="fas fa-caret-down expand-button-opened"></i> </button>
                     <div class="media-body">
                        <h6 class="list-group-item-heading"><?php echo filter_var($email->fromName, FILTER_SANITIZE_SPECIAL_CHARS) ?>
                           <span class="text-muted"><?php echo filter_var($email->fromAddress, FILTER_SANITIZE_SPECIAL_CHARS) ?></span>
                           <small class="float-right"
                              title="<?php echo $email->date ?>"><?php echo niceDate($email->date) ?></small>
                        </h6>
                        <p class="list-group-item-text text-truncate" style="width: 75%">
                           <?php echo filter_var($email->subject, FILTER_SANITIZE_SPECIAL_CHARS); ?>
                        </p>
                     </div>
                  </div>
               </a>
               <div id="mail-box-<?php echo $email->id ?>" role="tabpanel" aria-labelledby="headingCollapse1" class="card-collapse collapse" aria-expanded="true">
                  <div class="card-body">
                     <div class="card-block email-body">
                        <div class="float-right primary"> <a class="btn btn-outline-primary btn-sm" download="true" role="button" href="<?php echo " ?action=download_email&email_id=$safe_email_id&address=$user->address" ?>">
                           Download
                           </a> <a class="btn btn-outline-danger btn-sm" role="button" href="<?php echo " ?action=delete_email&email_id=$safe_email_id&address=$user->address" ?>">
                           Delete
                           </a> 
                        </div>
                        <?php printMessageBody($email, $purifier); ?>
                     </div>
                  </div>
               </div>
               <?php
                  } ?>
               <?php
                  if (empty($emails)) {
                      ?>
               <div id="empty-mailbox">
                  <p>The mailbox is empty. Checking for new emails automatically. </p>
                  <div class="spinner">
                     <div class="rect1"></div>
                     <div class="rect2"></div>
                     <div class="rect3"></div>
                     <div class="rect4"></div>
                     <div class="rect5"></div>
                  </div>
                  
               </div>
               <?php
                  } ?>
            </div>
         </div>
      </main>
      <footer style="background-color: #343a40 !important;">
         <div class="container">
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4438661758143218"
     crossorigin="anonymous"></script>
            <small class="text-justify quick-summary" style="color: #fff;">
            This is a disposable mailbox service. Whoever knows your username, can read your emails.
            Emails will be deleted after 30 days.
            <a data-toggle="collapse"  href="#about"
               aria-expanded="false"
               aria-controls="about contact">
            Show Details
            </a>
            </small>

            <div id="omh">
               <div id="term_and_pivacy" data-parent="#omh" class="card card-body collapse" style="max-width: 40rem">
                  <p class="text-justify">This term and pivacy.
                  By using the temporary email address services provided by V0V0.nl.eu.org, you agree to be bound by the terms and conditions contained below.</p>



<p >By using the temporary email address services provided by V0V0.nl.eu.org, you agree to be bound by the terms and conditions contained below.</p >



<p >Generally, this service prohibits:</p >

<p >Any illegal activity.
The use of bots of any kind.
Over-use our resources by repeatedly making many requests to our servers.
Reverse engineer, circumvent or attack our website for the purpose of gaining unauthorised privileges.
Use of our service for purposes other than intended.</p >


<p >This service prohibits messages, that:</p >

<p >are unsolicited commercial email (spam).
are harassing, abusive, defamatory, obscene, in bad faith, unethical or otherwise illegal content.</p >
<p >distribute trojans, viruses or other malicious computer software.
are intending to commit fraud, impersonating other persons, phishing, scams, or related crime.
distribute intellectual property without ownership or a license to distribute such property.
breach, in any way, the terms of service, privacy policy or rules of this web site or the recipients'.</p >


<p >V0V0.nl.eu.org provides an inbound-only email service. It is not possible for users to send emails from our service.</p >



<p >V0V0.nl.eu.org provides temporary email accounts without user registration. Unlike other email services which require a password to protect a user's email account, Adhoc-Email accounts are not protected and therefore can be accessed by anyone who knows your email address(s). Emails sent to an Adhoc-Email address should be considered public.</p >



<p >Our domain names may be changed or be removed at any time, and without warning. All email addresses are temporary and should be used as such.</p >



<p >V0V0.nl.eu.org has no control over third party resources and is not responsible or liable for their content or services offered. V0V0.nl.eu.org has no control over and not responsible for any content of email messages, including their attachments.</p >



<p >We do not perform any malware scanning on emails, including attachments. All emails are to be considered un-safe, and if opened, done so at your own risk.</p >



<p >For abuse prevention purposes we normally keep logs of the email addresses which were created, the IP address used to create those addresses, along with the date & time they were created. We will keep these logs for as long as we see necessary, or as required to do so. If you abuse our service, we may provide these logs to the authorities where an appropriate court order has been provided. Do not abuse our service!</p >



<p >Complaints or abuse reports should be reported to us using Session Messenger. Our Session ID is 059b96edf17642a8539e09088f103541ada3208ebfd839e9ae500ef43898917d02. Our Session ID may change from time to time. We may take up to 90 days to process complaints and abuse reports. We no longer accept any communications by any other means, for any reason, period.</p >



<p >If you need to contact us for any other reason, please use the Session Messenger details above.</p >



<p >This site uses a session cookie to maintain application state. Cookies may also be set by third party services such as Google for the purposes of ad delivery and tracking.</p >



<p >This service is provided "as is" and without any express or implied warranties, including, without limitation, the implied warranties of merchantability and fitness for a particular purpose.</p >
               </div>

                <div id="about_us"  data-parent="#omh"class="card card-body collapse" style="max-width: 40rem">
                  <p class="text-justify">about_us .</p>
               </div>

                <div id="contact_us"  data-parent="#omh"class="card card-body collapse" style="max-width: 40rem">
                  <p class="text-justify">Contact Us.</p>
               </div>
<!--                <div id="content-article__info_third-2" data-parent="#omh" class="content-article__info collapse"> Book 2 article 1 </div> -->
            </div>

            <div class="panel-group" id="accor">

               <div class="card card-body collapse" id="about" style="max-width: 40rem">
                  <p class="text-justify">This disposable mailbox keeps your main mailbox clean from spam.</p>
                  <p class="text-justify">Just choose an address and use it on websites you don't trust and don't want to use your main email address. Once you are done, you can just forget about the mailbox. All the spam stays here and does not fill up your main mailbox. </p>
                  <p class="text-justify"> You select the address you want to use and received emails will be displayed automatically. There is no registration and no passwords. If you know the address, you can read the emails. <strong>Basically, all emails are public. So don't use it for sensitive data.</strong> </p>
               </div>
            </div>
            <p> <small>Powered by
               <a
                  href=""><strong>H-Yacine-84/strong></a>
               </small> 
            </p>
         </div>
      </footer>
      <!-- jQuery first, then Popper.js, then Bootstrap JS -->
      <script src="assets/jquery/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="assets/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
      <script src="assets/bootstrap/4.1.1/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
      <script src="assets/clipboard.js/clipboard.min.js" integrity="sha384-8CYhPwYlLELodlcQV713V9ZikA3DlCVaXFDpjHfP8Z36gpddf/Vrt47XmKDsCttu" crossorigin="anonymous"></script>
      <script>
         clipboard = new ClipboardJS('[data-clipboard-target]');
         $(function() {
             $('[data-tooltip="tooltip"]').tooltip()
         });
         /** from https://github.com/twbs/bootstrap/blob/c11132351e3e434f6d4ed72e5a418eb692c6a319/assets/js/src/application.js */
         clipboard.on('success', function(e) {
             $(e.trigger).attr('title', 'Copied!').tooltip('_fixTitle').tooltip('show').tooltip('_fixTitle');
             e.clearSelection();
         });
      </script>
   </body>
</html>
