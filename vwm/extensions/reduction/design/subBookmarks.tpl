<div align="right"  class="link_bookmark">	
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=month" name="monthlyAnchor" {if $request.tab eq 'month'}class="active_link" {/if} >monthly</a> 		
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=quarter" name="quartelyAnchor" {if $request.tab eq 'quarter'}class="active_link" {/if} >quarterly</a> 		
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=semi-year" name="semiannuallyAnchor" {if $request.tab eq 'semi-year'}class="active_link" {/if} >semi-­annually</a>		
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=year" name="yearlyAnchor" {if $request.tab eq 'year'}class="active_link" {/if} >yearly</a>
</div>