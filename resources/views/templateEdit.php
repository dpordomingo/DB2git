<form action="/edit?id=<?=$this->e($template->id)?>" method="post">
	<pre>
		ID: <?=$this->e($template->id)?><br />
		NAME: <?=$this->e($template->name)?><br />
		CODE: <textarea rows="5" name="code"><?=$this->e($template->code)?></textarea><br />
		check: <input type="text" name="checkCode" readonly="readonly" value="<?=$this->e($template->checkCode)?>"><br />
		<input type="submit" value="save" /> <input type="reset" onclick="javascript:window.location='/list';" value="cancel">
	</pre>
</form>

<?php if ($error): ?>
	<pre style="color:red;">
		<?=$this->e($error)?><br />
		<textarea rows="5" name="oldCode"><?=$this->e($oldCode)?></textarea>

	</pre>
<?php endif ?>