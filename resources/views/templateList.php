<pre>
	<?php foreach($templates as $template): ?>
	    <a href="/edit?id=<?=$this->e($template->id)?>">[edit]</a> <?=$this->e($template->name)?><br />
	<?php endforeach ?>
</pre>
