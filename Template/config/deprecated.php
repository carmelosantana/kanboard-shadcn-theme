<?php if ($isSettingsPage && $this->user->isAdmin()): ?>
<p class="alert alert-info">
    Shadcn Theme is replaced by Harbor.
    <?= $this->url->link('Migrate in ModMenu', 'ModMenuController', 'show', ['plugin' => 'ModMenu']) ?>
</p>
<?php endif ?>
