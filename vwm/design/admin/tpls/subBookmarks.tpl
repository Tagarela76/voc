{if $request.category == "salescontacts"}
    {$bookmarks[]->name}
 <div align="right"  class="link_bookmark">
        <a href="?action=addItem&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="addItem">Add new bookmark</a>
{if $request.subBookmark}
        <a href="?action=edit&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="editItem">Edit bookmark <b>{$request.subBookmark|escape}</b></a>
        <a href="?action=deleteItem&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="deleteItem">Delete bookmarks</a>
        {*<a href=""><b>All bookmarks</b></a>*}
{else}
        <a href="?action=edit&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="editItem">Edit bookmark <b>{$request.bookmark}</b></a>
        <a href="?action=deleteItem&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="deleteItem">Delete bookmarks</a>
        {*<a href=""><b>All bookmarks</b></a>*}
    
{/if}   
</div>
{/if}