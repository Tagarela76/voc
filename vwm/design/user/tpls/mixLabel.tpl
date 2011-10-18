<div style="padding:7px;">
    <table align="center" cellpadding="0" cellspacing="0">        
		<tr>
			<td  colspan="2" style="padding:5px 5px 0 5px">
				<img src="images/logo_new.png"/>
			</td>
		</tr>
		
        <tr>
            <td width="50%" valign="top" style="padding:0 2px 0 5px">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td colspan="2" style="border-width:0px;" height="20px">
                            <b>Mix ID {$usage->mix_id}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Usage Description:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->description} 
                            </div>
                        </td>
                    </tr>                   
                    <tr>
                        <td class="" height="20">
                            Rule:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->rule.rule_nr_us} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Creation date:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->creation_time} 
                            </div>
                        </td>
                    </tr>     
					{if $usage->exempt_rule} 
                    <tr>
                        <td class="" height="20">
                            Exempt Rule:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->exempt_rule} 
                            </div>
                        </td>
                    </tr>
					{/if}
                </table>
            </td>
            <td width="50%"style="padding:0 5px 0 2px" valign="top">
                <table width="100%"cellpadding="0" cellspacing="0">                                        
                    <tr>
                        <td class="" height="20px">
                            VOC:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->voc} {$unittypeObj->getNameByID($companyDetails.voc_unittype_id)}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            VOCLX:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->voclx} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            VOCWX:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->vocwx} 
                            </div>
                        </td>
                    </tr>                   
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:5px 5px 0 5px">
                <table width="100%"cellpadding="0" cellspacing="0">
                    <tr>
                        <td height="30" width="300">
                            <b>Supplier</b>
                        </td>
                        <td width="100">
                            <b>Product NR</b>
                        </td>
                        <td width="200">
                            <b>Description</b>
                        </td>
                         <td width="100">
                            <b>Quantity</b>
                        </td>                        
                    </tr>
                    {*section name=i loop=$productCount*}
                    {foreach from=$usage->products item=product}
                    <tr>
                        <td >
                            {$product->supplier}
                        </td>
                        <td >
                            {$product->product_nr}
                        </td>
                        <td >
                            {$product->name}
                        </td>
                        <td>
                            <div align="left">
                                &nbsp; {$product->quantity}&nbsp; {$product->unittypeDetails.name}
                            </div>
                        </td>                     
                    </tr>
                    {/foreach}
                </table>
            </td>
        </tr>   
		
		<tr>
			<td  colspan="2" style="padding:5px 5px 0 5px">
			<table width="100%"cellpadding="0" cellspacing="0">
				<tr height="30">
					<td ><b>Case Number</b></td>
					<td><b>Description</b></td>
				</tr>
			{foreach from=$components item=component}
				<tr>
					<td>{$component->cas}</td>
					<td>{$component->description}</td>
				</tr>
			{/foreach}
			</table>		
			</td>
		</tr>
		
		<tr>
			<td width="50%" valign="top" style="padding:0 2px 0 5px">                
			<table width="100%"cellpadding="0" cellspacing="0">
				<tr height="30">
					<td ><b>Chemical Classification</b></td>
					<td>{$chemicalClassification}</td>
				</tr>			
			</table>		
			</td>
			
			<td width="50%" valign="top" style="padding:0 2px 0 5px">                
			<table width="100%"cellpadding="0" cellspacing="0">
				<tr height="30">
					<td ><b>Health Hazards Requirements</b></td>
					<td>{$healthHazardous}</td>
				</tr>			
			</table>		
			</td>
		</tr>
    </table>
				
		
    
</div>