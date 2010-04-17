{strip}
<div class="admin liberty">
	<div class="header">
		<h1>{tr}Assign Content Type Formats{/tr}</h1>
	</div>

	<div class="body">
		{assign var=system_default_format value=$gBitSystem->getConfig('default_format')}
		{formfeedback hash=$feedback}

		{form}
			<table class="data">
				<caption>{tr}Available Content Types{/tr}</caption>
				{foreach from=$gLibertySystem->mContentTypes item=ctype key=p name=ctypes}
					{if $prev_package != $ctype.handler_package}
						<tr>
							<th class="alignleft">{tr}Package{/tr} - {$ctype.handler_package|ucfirst}</th>
							{foreach name=formatPlugins from=$gLibertySystem->mPlugins item=plugin key=guid}
								{if $plugin.is_active eq 'y' and $plugin.edit_field and $plugin.plugin_type eq 'format'}
									<th class="width25p">
										{$plugin.edit_label}
									</th>
								{/if}
							{/foreach}
						</tr>
						{assign var=prev_package value=$ctype.handler_package}
					{/if}
					<tr class="{cycle values="odd,even"}">
						<td title="{$p}">{$gLibertySystem->getContentTypeName($ctype.content_type_guid)}</td>
						{foreach name=formatPlugins from=$gLibertySystem->mPlugins item=plugin key=guid}
							{if $plugin.is_active eq 'y' and $plugin.edit_field and $plugin.plugin_type eq 'format'}
								{assign var=config_key value=format_$guid}
								{assign var=default_config_key value=default_format_$p}
								{if $LCConfigSettings.$p.default_format && ($LCConfigSettings.$p.default_format && $system_default_format)}
									{assign var=default_format value=$LCConfigSettings.$p.default_format}
								{else}
									{assign var=default_format value=$system_default_format}
								{/if}
								<td class="aligncenter">
									(<span class='small'>{if $guid eq $system_default_format}system default{else}default{/if})</span>{html_radios values=$guid name=$default_config_key checked=$default_format}
									{* we inverse the checked check since these are a negation of allowing the format type - present to user like things are normal *}
									<input id="{$p}_{$plugin.plugin_guid}" type="checkbox" value="{$p}" name="plugin_guids[{$plugin.plugin_guid}][{$p}]" title="{$plugin.format_name}" 
									{if !$LCConfigSettings.$p.$config_key}checked="checked"{/if}
									/>
								</td>
							{/if}
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
