{literal}
	<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
<script>
	$(function()
	{	
		var loc=document.location.href;
		addr=loc.replace("http://"+document.domain+"/","");
		var reg=/\/\?.+/;
		folder=addr.replace(reg,"");
		var flashData="{/literal}{$swfUrl}{literal}&domain="+document.domain+"&folder="+folder;
		$('#flash').html('<embed align="center" width="520" height="600" src="'+flashData+'"/>');		
	});
	   
    var nameArray = new Array();
    
    function addSheetToAssignStep(name, isLast){        
        nameArray.push(name);        
        if (isLast) {
            document.getElementById("wait").style.display = "block";
            startMerge();
        }
    }
    
    
    function startMerge(){       
        var parStr = "name_0=" + nameArray[0];
        for (var i = 1; i < nameArray.length; i++) {
            parStr += "&name_" + i + "=" + nameArray[i];
        }
        parStr += "&count=" + nameArray.length;        
		MSDSData="companyID={/literal}{$companyID}{literal}&"+parStr;
       	$.ajax({
      	url: "modules/ajax/recognizeMSDS.php",      		
      	type: "GET",
      	async: false,
      	data: MSDSData,      			
      	dataType: "html",
      	success: function (response) 
      		{   
      			Pdocument.getElementById("wait").style.display = "none";
		        eval(response);
		        document.getElementById('saveButton').disabled = 0;
		        document.getElementById('flashPart').style.display = "none";									
      		}        		   			   	
		});
    }    
    
    
</script>
{/literal}

<div id="flashPart" align="center">
    If you have problems with upload, please try <a style="color:black" href="?action=msdsUploader&step=main&itemID={$request.category}&id={$request.id}&basic=yes">basic uploader</a>.
    <br>
    <div id='flash'>
	</div>    
    <br>
    If you have problems with upload, please try <a style="color:black" href="?action=msdsUploader&step=main&itemID={$request.category}&id={$request.id}&basic=yes">basic uploader</a>.
</div>
{if $failedSheets} 
<table align="center" cellpadding="0" cellspacing="0" style="margin-top:15px">
    <tr>
        <td>
            <div class="bl_o">
                <div class="br_o">
                    <div class="tl_o">
                        <div class="tr_o">
                            <center>
                                <h3 style="margin:1px">Failed sheets</h3>
                            </center>
                            <table align="center">
                                {section name=i loop=$cntFailed}
                                <tr>
                                    <td>
                                        <b>{$failedSheets[i].msdsName}</b>
                                    </td>
                                    <td>
                                        {$failedSheets[i].reason}
                                    </td>
                                </tr>
                                {/section} 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="tail_orange">
        </td>
    </tr>
</table>
{/if} 
{***************************/err*************************************}
{*shadow_table*} 
<table class="" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td valign="top" class="report_uploader_t_l">
        </td>
        <td valign="top" class="report_uploader_t">
        </td>
        <td valign="top" class="report_uploader_t_r">
        </td>
    </tr>
    <tr>
        <td valign="top" class="report_uploader_l">
        </td>
        <td valign="top" class="report_uploader_c_msds">
            {*shadow_table*}
            <center>
                <h1><b>MSDS UPLOADER</b></h1>
            </center>
            MSDS sheets will be assigned to products by name. Sample: "17-033-A.pdf" = product 17-033-A.
            <form name="form" action="" method="get">
                <table align="center" cellpadding="5" id="assignTable">                 
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <h2>
                                    <div id="wait" style="display:none;">
                                        Please, wait...
                                    </div>
                                </h2>
                            </td>
                        </tr>
                    </tbody>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input type="submit" id='saveButton' class="button" name="button" value="Save" disabled>
                        </td>
                    </tr>
                </table>
				<input type="hidden" id="sheetRecCount" name="sheetRecCount" value="0">
				<input type="hidden" id="sheetUnrecCount" name="sheetUnrecCount" value="0">
				<input type='hidden' name='action' value='msdsUploader'>
				<input type='hidden' name='step' value='save'>
				<input type='hidden' name='itemID' value={$request.category}>
				<input type='hidden' name='id' value={$request.id}>
            </form> 
			{*/shadow_table*} 
        </td>
        <td valign="top" class="report_uploader_r">
        </td>
    </tr>
    <tr>
        <td valign="top" class="report_uploader_b_l">
        </td>
        <td valign="top" class="report_uploader_b">
        </td>
        <td valign="top" class="report_uploader_b_r">
        </td>
    </tr>
</table>
{*/shadow_table*} 
