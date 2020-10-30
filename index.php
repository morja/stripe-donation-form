<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$currencies = ["USD" => "$", "EUR" => "€", "GBP" => "£"];
$amounts = [10, 20, 50, 100];

include('config.php');
?>
<html>
  <head>
    <title>Donate</title>
    <link rel="stylesheet" href="main.css">
<!-- Load Stripe.js on your website. -->
<script src="https://js.stripe.com/v3"></script>
<!-- Create a button that your customers click to complete their purchase. Customize the styling to suit your branding. -->
<script type="text/javascript">
function showhide(id){
  if (document.getElementById) {
    var divid = document.getElementById(id);
    var divs = document.getElementsByClassName("currency_option");
    for(var i=0;i<divs.length;i++) {
      divs[i].style.display = "none";
    }
    var divs = document.getElementsByClassName('amount');
    for(var i=0;i<divs.length;i++) {
      divs[i].classList.remove('active');
    }
    var divs = document.getElementsByName('other_amount');
    for(var i=0;i<divs.length;i++) {
      divs[i].value = 'Other Amount';
    }
    divid.style.display = "block";
    document.getElementById(id+"_custom").style.display = "block";
  } 
  return false;
}
function choose(elem){
  if (document.getElementById) {
    var divid = document.getElementById(elem.id);
    var divs = document.getElementsByClassName(elem.className);
    for(var i=0;i<divs.length;i++) {
      divs[i].classList.remove('active');
    }
    divid.classList.add('active');
  } 
  return false;
}
function unchoose(className){
  if (document.getElementById) {
    var divs = document.getElementsByClassName(className);
    for(var i=0;i<divs.length;i++) {
      divs[i].classList.remove('active');
    }
  } 
  return false;
}
</script>
  </head>
<body>
<center>
<div id="buttons">
  <div class="row row1" style="display: none;">
    <button id="method_once" name="method" onclick="choose(this);" value="payment" class="method active">ONCE</button>
    <button id="method_monthly" name="method" onclick="choose(this);" value="subscription" class="method">MONTHLY</button>
  </div> 
  <div class="row row2">
    <button id="currency_usd" name="currency" onclick="unchoose('amount'); choose(this); showhide(this.value);" class="currency active" value="usd">USD</button>
    <button id="currency_eur" name="currency" onclick="unchoose('amount'); choose(this); showhide(this.value);" class="currency" value="eur">EUR</button>
    <button id="currency_gbp" name="currency" onclick="unchoose('amount'); choose(this); showhide(this.value);" class="currency" value="gbp">GBP</button>
  </div> 
  <div class="row row3">
      <?php 
      foreach ($currencies as $currency => $symbol) {
        ?> <div id="<?php echo strtolower($currency);?>" class="currency_option"> <?php
        foreach ($amounts as $amount) {
           ?><button id="button_<?php echo strtolower($currency.$amount);?>" onclick="choose(this);" role="link" type="button" name="fixed_amount" class="amount" value="<?php echo $amount; ?>"><?php echo $symbol; ?><?php echo $amount; ?></button><?php
        } 
        ?> </div> 
      <?php }  ?>
  </div>
  <div class="row row4">
    <div>
      <?php 
      foreach ($currencies as $currency => $symbol) {?>
      <div id="<?php echo strtolower($currency);?>_custom" class="currency_option custom_amount_left">
        <div class="table">
          <div class="currsym"><?php echo $symbol; ?></div>
          <div class="custom_amount">
            <input id="input_<?php echo strtolower($currency);?>" type="text" onfocus="choose(this); this.value = '';" onblur="if(this.value=='') {this.value='Other Amount';}" onkeyup="value=value.replace(/[^\d]/g,'')" name="other_amount" value="Other Amount" min="5" class="amount">
           </div>
        </div>
       </div>
      <?php }  ?>
     </div>
     <button id="checkout-button" role="link" type="button" name="checkout-button" class="">DONATE</button>
  </div>

      <div id="html_element"></div>
</div>
<div id="error-message"></div>

<?php if ($config['recaptcha_version_v2']) { ?>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
<?php } else { ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $config['recaptcha_site']; ?>"></script>
<?php } ?>

<script type="text/javascript">

<?php if ($config['recaptcha_version_v2']) { ?>
    var onloadCallback = function() {
        grecaptcha.render('html_element', {
          'sitekey' : '<?php echo $config['recaptcha_site']; ?>'
        });
    };
    var checkoutButton = document.getElementById('checkout-button');
    checkoutButton.addEventListener('click', function (e) {
        var recaptcha_response = grecaptcha.getResponse();
        doPayment(recaptcha_response);
    });
<?php } else { ?>

    var checkoutButton = document.getElementById('checkout-button');
    checkoutButton.addEventListener('click', function (e) {
        grecaptcha.ready(function() {
          grecaptcha.execute('<?php echo $config['recaptcha_site']; ?>', {action: 'submit'}).then(function(recaptcha_response) {
              // Add your logic to submit to your backend server here.
              //console.log(recaptcha_response);
              doPayment(recaptcha_response);
          });
        });
    });
<?php } ?>

var stripe = Stripe('<?php echo $config['stripe_publishable_key'];?>');

function doPayment(recaptcha_response) {  
    var mode = 'payment';
    var currency = 'usd';
    var amount = '10';
    var actives = document.getElementsByClassName('active');

    //console.log(actives);
    for (var i = actives.length - 1; i >= 0; i--) {
      if(actives[i].name == "method") {
        mode = actives[i].value;
      } else if(actives[i].name == "currency") {
        currency = actives[i].value;
      } else if(actives[i].name == "fixed_amount") {
        amount = parseInt(actives[i].value);
      } else if(actives[i].name == "other_amount") {
        amount = parseInt(actives[i].value);
      }
    }

    if (amount < 5) {
        document.getElementById('error-message').innerHTML = 'Minimum amount is 5';
        return false;
    }

    //console.log(mode); console.log(currency); console.log(amount); console.log(recaptcha_response);

    fetch("/create-session.php", {
        method: "POST",
        headers: {
        'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            mode: mode,
            amount: amount*100,
            currency: currency,
            recaptcha_response: recaptcha_response
        }),
      })
        .then(function (response) {
            //console.log(response.text());
            return response.json();
        })
        .then(function (session) {
            if (session.id) {
                return stripe.redirectToCheckout({ sessionId: session.id });
            } else {
                document.getElementById('error-message').innerHTML = session.error;
            }
        })
        .then(function (result) {
          // If redirectToCheckout fails due to a browser or network
          // error, you should display the localized error message to your
          // customer using error.message.
          if (result.error) {
            alert(result.error.message);
          }
        })
        .catch(function (error) {
          console.error("Error:", error);
        });
  }
</script>
</center>

</body>
</html>