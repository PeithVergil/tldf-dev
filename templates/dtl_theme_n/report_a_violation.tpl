{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{if $form.res}
		<div class="error_msg">{$form.res}</div>
	{/if}
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons2">
				<a href="{$site_root}/contact.php">
					<img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me">
				</a>&nbsp;
				<a href="{$site_root}/contact.php">
					<img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me">
				</a>
			</div>
		</div>
		<div>
			<div class="hdr2">
				<label title="แจ้งทีมงานเพื่อตรวจสอบ">{$lang.section.report_a_violation}</label>
			</div>
			<div class="det-14-2">
				<label title="เมื่อพบเห็นข้อความหรือรูปภาพในโปรไฟล์ที่ไม่เหมาะสม">"{$lang.report_a_violation.box_text_2}"</label>
			</div>
			<div style="padding-top:10px;" class="text_head">
				<form method="post" action="{$form.action}">
					<div style="padding-bottom: 10px;">
						<label title="ชื่อ"> {if $err_field.name}<font class="error">{/if}
							{$lang.report_a_violation.name}
							{if $err_field.name}</font>{/if}: </label>
						<br />
						<input type="text" name="name" class="text" value="{$form.name}" style="width:180px;" />
					</div>
					<div style="padding-bottom: 10px;">
						<label title="อีเมล์"> {if $err_field.email}<font class="error">{/if}
							{$lang.report_a_violation.email}
							{if $err_field.email}</font>{/if}: </label>
						<br />
						<input type="text" name="email" class="text" value="{$form.email}" style="width:180px;" />
					</div>
					<div style="padding-bottom: 10px;">
						<label title="เบอร์โทรศัพท์"> {if $err_field.phone}<font class="error">{/if}
							{$lang.report_a_violation.phone}
							{if $err_field.phone}</font>{/if}: </label>
						<br />
						<input type="text" name="phone" class="text" value="{$form.phone}" style="width:180px;" />
					</div>
					<div style="padding-bottom: 10px;">
						<label title="ข้อมูลที่ต้องการแจ้งให้ทีมงานได้ทราบ"> {if $err_field.description}<font class="error">{/if}
							{$lang.report_a_violation.description}
							{if $err_field.description}</font>{/if}: </label>
						<br />
						<textarea name="description" style="width:330px; height:150px;" >{$form.description}</textarea>
					</div>
					<p class="basic-btn_here _mleft15">
						<b></b><span>
						<input type="submit" name="submit" value="Submit" />
						</span> 
					</p>
				</div>
			</form>
		</div>
	</div>
</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}