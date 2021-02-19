<?php 
    require_once 'formspam.php';
    $isbanned = formSpam::check(); /* Does the current visitor match any of the rules? */
    if ($isbanned) {
        /* If they are, show an error message with a reference ID */
        formSpam::sendToOblivion($isbanned);
        return;
    } elseif (!empty($_POST['name']) || !empty($_POST['email'])) {
        /* In case the visitor is not banned and the honeypots were triggerred, ban them */
        formSpam::ban();
    }
?>
<form id="contactform" action="https://formsubmit.io/send/your-formsubmit-id-here" method="post">
<?php
    /* Recreate the contact form */
    foreach ($_POST as $a => $b) {
        echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
    }
?>
<noscript><input type="submit" value="Click here if you are not redirected."/></noscript>
</form>
<script type="text/javascript">
    // Submit the form once the page loads
    window.onload = function(){
        document.forms['contactform'].submit();
    }
</script>
