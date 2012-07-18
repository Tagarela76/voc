<td class="bg_left" valign="top" width="220px ">
    {if $permissions.showOverCategory && ($permissions.root.view || ($request.action != 'addItem' || $request.category != 'facility' ))}
	<h3 id="left">{$upCategoryName}</h3>
    <table style="table-layout: fixed;" width="100%" cellpadding="0" cellspacing="0" class="menu_padd_b">
        <tr>
            <td valign="top">
                {section name=i loop=$upCategory}
                <div align="left" width="100%" {if $upCategory[i].id==$leftCategoryID}class="left_m_active" {else}class="left_m" {/if}>
                    <div align="left" width="100%">
                        <ul class="link">
                            <li>
                                <a href="{$upCategory[i].url}"   class="id_company">{$upCategory[i].name|escape} </a>
                            </li>
                        </ul>
                    </div>
                </div>
                {/section} 
            </td>
        </tr>
    </table>
    {/if}
</td>
