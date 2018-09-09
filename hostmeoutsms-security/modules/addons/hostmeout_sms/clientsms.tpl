<div class="bannersubpage">
  <h1>{$pagetitle}</h1>
  <ul class="liststyle7">
    <li><strong>Send Bulk</strong> SMS</li>
    <li><strong>Robust</strong> API</li>
    <li><strong>Reliable</strong> Licensing</li>
  </ul>
  <!--bannersubpage--> 
</div>

<div class="content">
<div id="whmcsthemes">
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <div class="nav-collapse">
		<ul class="nav">
			<li><a href="clientarea.php">Home</a></li>
		</ul>
    <ul class="nav">
        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Services&nbsp;<b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="clientarea.php?action=products">My Services</a></li>
             <li><a href="https://hostmeout.com/index.php?m=hostmeout_sms">Send Bulk SMS</a></li>
                        <li class="divider"></li>
            <li><a href="cart.php">Order New Services</a></li>
            <li><a href="cart.php?gid=addons">View Available Addons</a></li>
          </ul>
        </li>
      </ul>


		  <ul class="nav">
			<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Domains&nbsp;<b class="caret"></b></a>
			  <ul class="dropdown-menu">
				<li><a href="clientarea.php?action=domains">My Domains</a></li>
				<li class="divider"></li>
				<li><a href = "index.php?m=resellerclubmods_tools&action=suggestdomain">Suggest Domain</a></li>
                                <li><a href="cart.php?gid=renewals">Renew Domains</a></li>
				<li><a href="cart.php?a=add&domain=register">Register a New Domain</a></li>				<li><a href="cart.php?a=add&domain=transfer">Transfer Domains to Us</a></li>                				<li class="divider"></li>
				<li><a href="domainchecker.php"></a></li>
			  </ul>
			</li>
		  </ul>
		  <ul class="nav">
			<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Billing&nbsp;<b class="caret"></b></a>
			  <ul class="dropdown-menu">
				<li><a href="clientarea.php?action=invoices">My Invoices</a></li>
				<li><a href="clientarea.php?action=quotes">My Quotes</a></li>
				<li class="divider"></li>
				<li><a href="clientarea.php?action=addfunds">Add Funds</a></li>				<li><a href="clientarea.php?action=masspay&all=true">Mass Payment</a></li>							  </ul>
			</li>
		  </ul>

		  <ul class="nav">
			<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Support&nbsp;<b class="caret"></b></a>
			  <ul class="dropdown-menu">
				<li><a href="supporttickets.php">Tickets</a></li>
				<li><a href="knowledgebase.php">Knowledgebase</a></li>
				<li><a href="downloads.php">Downloads</a></li>
				<li><a href="serverstatus.php">Network Status</a></li>
			  </ul>
			</li>
		  </ul>

		  <ul class="nav">
			<li><a href="submitticket.php">Open Ticket</a></li>
		  </ul>

		  <ul class="nav">
            <li><a href="affiliates.php">Affiliates</a></li>
		  </ul>
		  <ul class="nav pull-right">
			<li class="dropdown">
			  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Hello, {$client->firstname}!&nbsp;<b class="caret"></b></a>
			  <ul class="dropdown-menu">
				<li><a href="clientarea.php?action=details">Edit Account Details</a></li>
								<li><a href="clientarea.php?action=contacts">Contacts/Sub-Accounts</a></li>
				<li><a href="clientarea.php?action=addfunds">Add Funds</a></li>				<li><a href="clientarea.php?action=emails">Email History</a></li>
				<li><a href="clientarea.php?action=changepw">Change Password</a></li>
				<li class="divider"></li>
				<li><a href="logout.php">Logout</a></li>
			  </ul>
			</li>
		  </ul>

        </div><!-- /.nav-collapse -->
      </div>
    </div><!-- /navbar-inner -->
  </div><!-- /navbar -->


<div class="whmcscontainer" style="border-radius:0 0 8px 8px; border:1px solid #333; border-top:0;box-shadow:0px 0px 33px 1px #999; padding-top:15px;">
    <div class="contentpadded">
Hi and welcome {$client->firstname}!
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
            <p align="center"><button class="btn" type="submit" value="Send"><i class="fa fa-comment"></i> Send SMS</button></p>
        </form>
<p>You are currently sending SMS at the rate of {formatCurrency($smscost)}/per sms</p>
{if $smssent}
<div class="alert alert-success">
        <ul>
       {$smssent}
    </ul>
</div>
{/if}

{if $smserror}
<div class="alert alert-error">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$smserror}
    </ul>
</div>
{/if}

{if $debug}
<div class="alert alert-warning">
 <ul>{$debug}</ul>
</div>
{/if}

 </div>
</div>
</div>
{else}
<div class="alert alert-error">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        SMS Module is Currently Not Enabled for Clients!
    </ul>
</div>
{/if}