<div class="contents-main">
<h2>概要</h2>
<table border="0" cellspacing="1" cellpadding="8" summary=" ">
    <!--{foreach from=$arrSystemInfo item=info}-->
        <tr>
            <th>
            <!--{$info.title|h}-->
            </td>
            <td>
            <!--{$info.value|h|nl2br}-->
            </td>
        </tr>
    <!--{/foreach}-->
</table>

<h2>PHP情報</h2>
<iframe src="?mode=info" height="500" frameborder="0" style="width: 100%;"></iframe>
</div>
