{strip}
<div class="admin liberty">
	<div class="header">
		<h1>{tr}Set Services Preferences{/tr}</h1>
	</div>
	<div class="body">
		{formfeedback hash=$feedback}
		{form}
			<table class="data">
				<caption>{tr}Available Services{/tr}</caption>
					{foreach from=$gLibertySystem->mServices item=service key=service_name}
						{assign var=config_key value=service_$service_name}
						{cycle values="odd,even" assign="rowstyle"}
						<tr class="{$rowstyle}">
							<th class="alignleft" style="text-align:left">{tr}{$service_name|ucfirst}{/tr}</th>
							{foreach from=$gLibertySystem->mContentTypes item=ctype key=p name=ctypes}
							<th>
								{$ctype.content_description}
							</th>
							{/foreach}
						</tr>
						<tr class="{$rowstyle}">
							{* list the service and its description *}
							<td title="{$service_name}">
								{tr}(pkg:{$service.package|ucfirst}){/tr}<br />
								{$service.description}
							</td>
							{if $service.required}
								<td colspan="{$gLibertySystem->mContentTypes|@count}" style="text-align:center"><em>This is a required service and should not be disabled</em></td>
							{else}
								{foreach from=$gLibertySystem->mContentTypes item=ctype key=p name=ctypes}
									{* create option for each ctype *}
									<td class="aligncenter" style="width:25px; padding:0 15px">
										<select name="service_guids[{$service_name}][{$p}]" id="{$p}_{$service}">
											<option value="" 			{if !$LCConfigSettings.$p.$config_key}selected="selected"{/if}				>Include</option>
											<option value="required" 	{if $LCConfigSettings.$p.$config_key eq 'required' }selected="selected"{/if}>Require</option>
											<option value="n" 			{if $LCConfigSettings.$p.$config_key eq 'n'}selected="selected"{/if}		>Exclude</option>
										</select>
									</td>
								{/foreach}
							{/if}
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
