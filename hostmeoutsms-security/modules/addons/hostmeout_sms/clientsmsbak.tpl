Hi welcome {$client->firstname}!
{if $apikey}
<div class="body" id="mg-wrapper" data-target=".body" data-spy="scroll" data-twttr-rendered="true">
<h3>API Credentials</h3>
<table class="table table-striped table-framed table-centered">
    <tr><td>API URL</td><td>https://hostmeout.com/modules/addons/hostmeout_sms/smsapi.php</td></tr>
                      <tr><td>API E-mail: </td><td>{$client->email}</td></tr>
                      <tr><td>User ID: </td><td>{$client->id}</td></tr>
                      <tr><td>API Key: </td><td><input type="text" id="api_key" value="{$apikey}" readonly style="width:250px;" /> </td></tr>
                      <tr><td>APi Credit: </td><td>{$clientsstats.creditbalance}</td></tr>
                      <tr><td>APi Cost: </td><td>₦0.99NG/per sms</td></tr>
                      </table>

</div>
{/if}
{if $is_clientsmsenabled eq "yes"}

<p>{$LANG.availcreditbal}: <a href="clientarea.php?action=addfunds" title="Add Funds To Your Account"><strong>{$clientsstats.creditbalance}</strong></a></p>


<form action="" method="post">
        <input type="hidden" name="action" value="save" />
            <div style="text-align: left;background-color: whiteSmoke;margin: 0px;padding: 10px;">
                <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
                    <tbody>
                        <tr>
                            <td class="fieldlabel" width="30%">Sender ID</td>
                            <td class="fieldarea">
                                <input id="textbox" name="senderid" type="text" placeholder="Enter ur Sender ID" style="width:498px;padding:5px"><br>
                                
                            </td>
                        </tr>

                        <tr>
                            <td class="fieldlabel" width="30%">Phone Numbers</td>
                            <td class="fieldarea">
                                <input id="textbox" name="phone" type="text" placeholder="Enter the numbers, seperate with comma" style="width:498px;padding:5px"><br>
                                
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="30%">Message</td>
                            <td class="fieldarea">
                               <textarea cols="70" rows="20" name="message" style="width:498px;padding:5px"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="30%">Print Debug log</td>
                            <td class="fieldlabel"><input type="checkbox" name="debug" value="ON"></td>
                        </tr>
                    </tbody>
                </table>
            <p align="center"><button class="btn" type="submit" value="Send"><i class="fa fa-commenting"></i> Send SMS</button></p>
        </form>
<p>You are currently sending SMS at the rate of {formatCurrency($smscost)}/per sms</p>
{$smssent}
{$smserror}
{$debug}
 </div>
{else}
<div class="alert alert-error">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        SMS Module is Currently Not Enabled for Clients!
    </ul>
</div>
{/if}