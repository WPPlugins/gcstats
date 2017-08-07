<div class="wrap">
	<?php
	if(isset($msg_updated)){
		?>
		<div id="message" class="updated fade"><p><strong><?php echo $msg_updated;?></strong></p></div>
		<?php
	}
	?>
    <h2>gcStats - Options</h2>
    <form action="admin.php?page=gcStats_options&action=save_options" method="post">
        <input type='hidden' name='gcstats_option_page' value='general' /><input type="hidden" name="action" value="update" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="dflt_acc_name">
                        Default Account Name
                    </label>
                </th>
                <td>
                    <select name="dflt_acc_name" id="dflt_acc_name">
                    	<?php
						foreach($accountnames as $k=>$v){
							echo '<option value="'.$v->accountname.'">'.$v->accountname.'</option>';
						}
						?>
                    </select>
                </td>
            </tr>
			<!--
            <tr valign="top">
                <th scope="row">
                    Cachejudge Account
                </th>
                <td>
                    <fieldset>
                        <legend class="hidden">
                            Account
                        </legend>
                        <table>
                            <tr>
                                <td>
                                    <label for="cj_acc_name">Name</label>
                                </td>
                                <td>
                                    <input type="text" name="cj_acc_name" id="cj_acc_name" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="cj_acc_pass">Password</label>
								</td>
								<td>
                                    <input type="password" name="cj_acc_pass" id="cj_acc_pass" class="regular-text" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>-->
        </table>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="Save Changes" />
        </p>
    </form>
</div>