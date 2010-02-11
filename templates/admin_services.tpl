{strip}
<div class="admin liberty">
	<div class="header">
		<h1>{tr}Set Service Preferences{/tr}</h1>
	</div>

	<div class="body">
		{assign var=system_default_format value=$gBitSystem->getConfig('default_format')}
		{formfeedback hash=$feedback}

		{form}
			<table class="data">
				<caption>{tr}Available Services{/tr}</caption>
				{foreach from=$gLibertySystem->mContentTypes item=ctype key=p name=ctypes}
					{if $prev_package != $ctype.handler_package}
						<tr>
							<th class="alignleft">{tr}Package{/tr} - {$ctype.handler_package|ucfirst}</th>
							{foreach from=$gLibertySystem->mServices item=pkg key=service name=services}
								<th class="width25p">
									{$service}
								</th>
							{/foreach}
						</tr>
						{assign var=prev_package value=$ctype.handler_package}
					{/if}
					<tr class="{cycle values="odd,even"}">
						<td title="{$p}">{$ctype.content_description}</td>
						{foreach from=$gLibertySystem->mServices item=pkg key=service name=services}
							{assign var=config_key value=service_$guid}
							<td class="aligncenter">
								{* we inverse the checked check since these are a negation of allowing the format type - present to user like things are normal *}
								<input id="{$p}_{$service}" type="checkbox" value="{$p}" name="service_guids[{$service}][{$p}]" title="{$service}" {if !$LCConfig.$p.$config_key}checked="checked"{/if} />
							</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>

			<div class="submit">
				<input type="submit" name="save" value="{tr}Apply Changes{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .users -->
{/strip}
