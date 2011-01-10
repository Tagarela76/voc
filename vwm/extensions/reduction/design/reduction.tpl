</form>
{literal}
<script type='text/javascript'>	
function changeFactorARE()
{	
	$('#mulFactorARE').css('display','none');
	$('#changedMulFactorARE').css('display','inline-block');		
}

function changeFactorTargetEmission()
{	
	$('#mulFactorTargetEmission').css('display','none');
	$('#changedMulFactorTargetEmission').css('display','inline-block');	
}
</script>
{/literal}	

<hidden id= 'facilityId' value='{$facilityId}'>

<table cellspacing="0" cellpadding="0" align="center" width="100%">
     <tr>
         <td valign="top" class="report_uploader_t_l_orange"></td>
         <td valign="top" class="report_uploader_t_orange"></td>                 
         <td valign="top" class="report_uploader_t_r_orange"></td>                          
	</tr>				
	<tr>
		 <td valign="top" class="report_uploader_l_orange"></td>				  
         <td valign="top" class="report_uploader_c_orange">                  				            
		{section name=i loop=$reduction}	
		
			<div class="Plans_hd_orange">
			<span>{$reduction[i].year}</span>	
			</div>
		    <div class="padd3">
				ARE = {$reduction[i].weightOfSolid} {$unittype} of solids * {$mulFactorARE} = <span>{$reduction[i].ARE}</span> {$unittype}
				<br>
				Target Emission = {$reduction[i].ARE} {$unittype} * {$mulFactorTargetEmission} = <b>{$reduction[i].targetEmission} {$unittype}	
				</b>
				<br>
				Annual Actual Solvent Emission = <div style = "{if $solventPlan[i] gt $reduction[i].targetEmission}font-weight:bold;color:red;{/if}display:inline" >{$solventPlan[i]}</div>
			
		    </div>
			<br>	
	
		{/section}
	      	
			 </td>		        
			 <td valign="top" class="report_uploader_r_orange"></td>	 	        
		 </tr>	          
		 <tr>		                 
                <td valign="top" class="report_uploader_b_l_orange"></td>      
                <td valign="top" class="report_uploader_b_orange"></td>             
                <td valign="top" class="report_uploader_b_r_orange"></td>                                              				
		 </tr>		      
 </table>    		 
		
		      
<div align="center" >	
<div align="center" style="width:93%">	   
	<div class="control_panel" align="left">
        <div class="control_panel_tl">
            <div class="control_panel_tr">
                <div class="control_panel_bl">
                    <div class="control_panel_br">
                        <div class="control_panel_center">
	
<div class="modif_reduction"> 

	Multiplication factor for ARE is  
	<span id='mulFactorARE' {if $error.type eq 'ARE'}style="display:none;"{/if}>
		{$mulFactorARE} (<a href='#mulFactorARE' id='changeARE' onClick='changeFactorARE()'>Change</a>)			
	</span> 
		
	{* HIDDEN *}
	<span id='changedMulFactorARE' {if $error.type neq 'ARE'}style='display:none' {else} style='display:inline-block;' {/if}>	
	<form name='factorARE' enctype="multipart/form-data" action="?action=browseCategory&category=facility&id={$request.id}&bookmark=reduction" method='POST'>
		<input type='text' name='textARE' {if $error.type eq 'ARE'}value = '{$error.value}'{else}value = '{$mulFactorARE}'{/if}>
		<input type='submit' name='saveARE' value='Save'>
		{*ERROR*}
		{if $error.type eq 'ARE'} 
			<div style = 'color:red;font-size:10px;'>
			<img src='design/user/img/alert1.gif' height=10  >{$error.error}
			</div>
		{/if}
		{*/ERROR*}	
	</form>
	</span> 
	{* HIDDEN *}  
	&nbsp
	 Multiplication factor for Target Emission is 
	 <span id='mulFactorTargetEmission'  {if $error.type eq 'TE'}style="display:none;"{/if}>
	 	{$mulFactorTargetEmission} (<a href='#mulFactorTargetEmission' id='changeTargetEmission' onClick='changeFactorTargetEmission()'>Change</a>) 
	 </span>
	 
	 {* HIDDEN *}
	 <span id='changedMulFactorTargetEmission' {if $error.type neq 'TE'}style='display:none' {else} style='display:inline-block;' {/if}>
	 <form name='factorTE' enctype="multipart/form-data" action="?action=browseCategory&category=facility&id={$request.id}&bookmark=reduction" method='POST'>	
	 	<input type='text' name='textTargetEmission' {if $error.type eq 'TE'}value = '{$error.value}'{else}value = '{$mulFactorTargetEmission}'{/if}>
	 	<input type='submit' name='saveTargetEmission' value='Save'>
	 	{*ERROR*}
		{if $error.type eq 'TE'} 
			<div style = 'color:red;font-size:10px;'>
			<img src='design/user/img/alert1.gif' height=10  style="float:left;">{$error.error}
			</div>
		{/if}
		{*/ERROR*}
	 </form>
	 </span>
	 {* HIDDEN *}
</div></div></div></div></div></div></div></div></div>


