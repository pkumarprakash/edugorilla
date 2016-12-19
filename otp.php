<?php
function edugorilla_otp()
{
	$otp_form = $_POST['otp_form'];
	if($otp_form == "self")
    {
    	$edugorilla_mno = $_POST['edugorilla_mno'];
    	if(!preg_match("/([0-9]{10}+)/",$edugorilla_mno)) $error = "INVALID";
    
    	if(empty($error)) 
        {
        	$success = "OTP sent successfully";
        }
    }
?>
  <div class="wrap">
    <h1>EduGorilla OTP</h1>
        <?php
            if($success)
            {
        ?>
                <div class="updated notice">
                    <p><?php echo $success;?></p>
                </div>
        <?php
            }
        ?>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Mobile No.<sup><font color="red">*</font></sup></th>
                    <td>
                        <input name="edugorilla_mno" value="<?php echo $edugorilla_mno;?>" placeholder="Type mobile no. here...">
                        <font color="red"><?php echo $error;?></font>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="hidden" name="otp_form" value="self">
                        <input type="submit" class="button button-primary" value="Send OTP">
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}
?>