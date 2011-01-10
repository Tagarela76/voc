<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>{$title}</title>
        <link href="style.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="modules/js/trackingSystem.js"></script>
    </head>    
        <table width="100%" height="100%" cellspacing="0" cellpadding="0">
            <tr>
                {include file="tpls:logo.tpl"} 
                <td valign="top">
                    {*table center*} 
                    <table class="cell2" cellspacing="0" cellpadding="0" height="100%">
                        <tr>
                            <td valign="top" align="center">
                                <table width="100%" height="100%" height="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            {include file="tpls:tpls/categoriesListLeft.tpl"} 
                                        </td>
                                        <td valign="top" class="foot_block">
                                        	{*Categories List*}
											{include file="tpls:tpls/login_categoriesList.tpl"}
                                         {*******************************************************}
                                         	{*ajax-preloader*}
                                         	<div style="height:16px;text-align:center;">
                                            	 <div id="preloader" style="display:none">
                                                	 <img src='images/ajax-loader.gif'>
                                             	</div>
                                         	</div>
										 	<div id="trackingContainer">										 		
										 	</div>{*include file="tpls/track/index.tpl"*}
                                            <br>
                                            <br>
                                        </td>
                                    </tr>
                                </table>
                                {*/table center*} 
                            </td>
                            {include file="tpls:footer.tpl"} 
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
