{if $request.category == "salescontacts"}
 <div align="right"  class="link_bookmark">
        <a href="?action=addItem&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="addItem">Add new bookmark</a>
{if $request.subBookmark}
        <a href="?action=edit&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="editItem">Edit bookmark <b>{$request.subBookmark|capitalize:true}</b></a>
        <a href="?action=deleteItem&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="deleteItem">Delete bookmark <b>{$request.subBookmark|capitalize:true}</b></a>
        {*<a href=""><b>All bookmarks</b></a>*}
{else}
        <a href="?action=edit&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="editItem">Edit bookmark <b>{$request.bookmark|capitalize:true}</b></a>
        <a href="?action=deleteItem&category=bookmarks&bookmark=contacts{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"  name="action" value="deleteItem">Delete bookmark <b>{$request.bookmark|capitalize:true}</b></a>
        {*<a href=""><b>All bookmarks</b></a>*}
    
{/if}    
</div>
{/if}