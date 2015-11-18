<pre>
	<?php foreach($templates as $template): ?>
	    <a href="/edit?id=<?=$this->e($template->id)?>">[edit]</a> <?=$this->e($template->name)?><br />
	<?php endforeach ?>

	<?php if($isUpdatable): ?>
		<a href="/update">Update from repository</a>
	<?php elseif($isUnsynced): ?>
		<span style="color:red">repository has divergencies</span>
	<?php else: ?>
		<span style="color:green">project is updated</span>
	<?php endif ?>
</pre>