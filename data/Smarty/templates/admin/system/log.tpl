<div class="contents-main">
<p>直近の<!--{$line_max}-->行</p>
<table class="list log">
    <tr>
        <th>日時</th>
        <th>パス</th>
        <th>内容</th>
    </tr>
    <!--{foreach from=$tpl_ec_log item=line}-->
        <tr>
            <td class="date"><!--{$line.date|h}--></td>
            <td class="path"><!--{$line.path|h}--></td>
            <td class="body"><!--{$line.body|h|nl2br}--></td>
        </tr>
    <!--{/foreach}-->
</table>
</div>
