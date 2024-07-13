<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/../classes/file-utils.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../default-time-zone.php');

trait EmailTemplate
{

  protected static function getEmailContent($messageHtml, $title, $mainHeading = '')
  {

    return <<<EMAIL

      <!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
      <html lang="en">

      <head>

        <!--[if gte mso 9]>
          <xml>
            <o:OfficeDocumentSettings>
              <o:AllowPNG/>
              <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
          </xml>
          <![endif]-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="x-apple-disable-message-reformatting">
        <!--[if !mso]><!-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
        <title>$title</title>
        <meta name="robots" content="noindex" />
      </head>

      <body style="margin: 0; padding: 0; ">
        <div style="min-height: 100vh; position: relative; font-family: Montserrat, sans-serif; font-size: clamp(0.8rem, 0.5rem + 0.5vw, 1vw); padding: clamp(1rem, 0.5rem + 0.5svw + 0.75svh, 4rem) clamp(1.5rem, 0.75rem + 0.5svw + 0.75svh, 6rem);">
          <main style="min-height: 75vh; text-align: center;">
            <h1 style="font-size: calc(1.45rem + 1.25vw);">$mainHeading</h1>
            $messageHtml
          </main>

          <section style="text-align: center; padding: 20px 0;">
            <table style="margin: 0 auto;">
              <tr>
                <td style="padding-right:calc(0.5rem + 0.5vw + 0.5vh); position: relative; bottom: -2px;">
                  <a href="https://twitter.com/iVOTEpupsrc" style="display: inline-block; width:clamp(2rem, 0.5rem + 1dvw + 2dvh, 5rem);">
                    <img src="https://iili.io/dft53lt.png" border="0" width="100%">
                  </a>
                </td>
                <td style="padding: 0 calc(0.5rem + 0.5vw + 0.5vh); position: relative; bottom: -2px;">
                  <a href="https://www.facebook.com/profile.php?id=61558930417110" style="display: inline-block; width:clamp(2rem, 0.5rem + 1dvw + 2dvh, 5rem);">
                    <img src="https://iili.io/dft5FUX.png" border="0" width="100%">
                  </a>
                </td>
                <td style="padding: 0 calc(0.5rem + 0.5vw + 0.5vh); position: relative; bottom: -2px;">
                  <a href="https://www.instagram.com/ivotepupsrc/" style="display: inline-block; width:clamp(2rem, 0.5rem + 1dvw + 2dvh, 5rem);">
                    <img src="https://iili.io/dft5JiN.png" border="0" width="100%">
                  </a>
                </td>
                <td style="padding-left:calc(0.5rem + 0.5vw + 0.5vh); vertical-align: middle;">
                  <a href="#" style="display: inline-block; padding: clamp(0.01rem, 0.005rem + 0.5vw + 0.75vh, 2rem) clamp(1.5rem, 0.75rem + 0.5vw + 0.75vh, 3rem); text-decoration: none; background-color: #4870c4; color: whitesmoke; font-weight: bolder;">Unsubscribe from this list</a>
                </td>
              </tr>
            </table>

          </section>

          <footer style="text-align: center; padding: 20px;">
            <div>
              <img src="https://iili.io/dftxpDB.png" alt="iVote Logo" border="0" width="20%">
            </div>
            <div style="padding-top: 10px;">
              <p>iVOTE is an Automated Election System (AES) for the student organizations of the PUP Santa Rosa Campus.</p>
              <p style="color: #ea423c; font-weight: bold;"><span style="color: #4870c4;">© 2024 BSIT 3-1.</span> All Rights Reserved</p>
              <div>Polytechnic University of the Philippines</div>
              <div>SANTA ROSA CAMPUS</div>
              <div>LCA Blvd. Bgry. Tagapo, Santa Rosa City, Laguna, 4026</div>
            </div>
          </footer>
        </div>

      </body>

      </html>
    
EMAIL;
  }
}


?>



<!-- GMAIL Messes with my layout here -->

<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<html lang="en">

<head> -->

<!--[if gte mso 9]>
    <xml>
      <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
<!-- <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="x-apple-disable-message-reformatting"> -->
<!--[if !mso]><!-->
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
<!--<![endif]-->
<!-- <title>$title</title>
  <meta name="robots" content="noindex" />
  <title></title>
</head>

<body style="margin: unset; padding:unset;">
  <div style="min-height:100dvh; position:relative; font-family: Montserrat, sans-serif; font-size: clamp(0.8rem, 0.5rem + 0.5vw, 1vw); padding: clamp(1rem, 0.5rem + 0.5svw + 0.75svh, 4rem) clamp(1.5rem, 0.75rem + 0.5svw + 0.75svh, 6rem);">
    <main style="min-height: 75vh">
      <h1 style="font-size: calc(1.45rem + 1.25vw); text-align:center;">$mainHeading</h1>
        $messageHtml
    </main>

    <section style="display: flex; justify-content:center; gap:5vh; padding-top:1rem; padding-bottom:1rem;">
      <div style="display:flex; align-content:center; flex-wrap:wrap; gap:calc(1rem + 1vw + 1vh); transform:translateX(10%);">
        <a href="https://twitter.com/iVOTEpupsrc" style=" width:clamp(2rem, 0.5rem + 1dvw + 2dvh, 5rem);"><img src="https://iili.io/dft53lt.png" border="0" width="100%"></a>
        <a href="https://www.facebook.com/profile.php?id=61558930417110" style=" width:clamp(2rem, 0.5rem + 1dvw + 2dvh, 5rem);"><img src="https://iili.io/dft5FUX.png" alt="Connect with us" border="0" width="100%"></a>
        <a href="https://www.instagram.com/ivotepupsrc/" style=" width:clamp(2rem, 0.5rem + 1dvw + 2dvh, 5rem);"><img src="https://iili.io/dft5JiN.png" border="0" width="100%"></a>
      </div>
      <a href="*|UNSUB|*" style=" width: fit-content; padding: clamp(1rem, 0.5rem + 0.5svw + 0.75svh, 2rem) clamp(1.5rem, 0.75rem + 0.5svw + 0.75svh, 3rem); text-decoration:none; background-color:#4870c4; color: whitesmoke; font-weight: bolder; transform:translateX(15%);">Unsubscribe from this list.</a>
    </section>

    <footer style="display: flex; flex-direction:column; bottom: 0; padding-bottom: 5vh; left: 0; width: 100%; max-height: 50dvh; overflow-x:hidden;">
      <div style="width: 100%; display:flex; justify-content: center; padding: 0 clamp(1.5rem, 0.75rem + 0.5svw + 0.75svh, 6rem);">
        <img src="https://iili.io/dftxpDB.png" alt="iVote Logo" border="0" width="20%">
      </div>
      <div style="width: 100%; display:flex; flex-direction:column; align-content: center; flex-wrap:wrap; text-align:center; padding: 0 clamp(1.5rem, 0.75rem + 0.5svw + 0.75svh, 6rem);">
        <p>iVOTE is an Automated Election System (AES) for the student organizations of the PUP Santa Rosa Campus.</p>
        <p style="color: #ea423c; font-weight: bold;"><span style="color: #4870c4;">© 2024 BSIT 3-1.</span> All Rights Reserved</p>
        <div>
          Polytechnic University of the Philippines
        </div>
        <div>
          SANTA ROSA CAMPUS
        </div>
        <div>
          LCA Blvd. Bgry. Tagapo, Santa Rosa City, Laguna, 4026
        </div>
      </div>
    </footer>
  </div>

</body>

</html> -->