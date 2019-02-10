<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/" class="tasks-switch__item">Повестка дня</a>
        <a href="/" class="tasks-switch__item">Завтра</a>
        <a href="/" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?=$show_complete_tasks === 1 ? 'checked' : ''; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($task_items as $task_item): ?>
        <?php if($show_complete_tasks === 1 || $task_item['done'] === false): ?>
            <tr class="tasks__item task <?=$task_item['done'] ? 'task--completed' : '';?> <?=check_if_due_within_24h($task_item['due_date']) ? 'task--important' : '';?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1" <?=$task_item['done'] === true ? 'checked' : ''; ?>>
                        <span class="checkbox__text"><?=strip_tags($task_item['name']);?></span>
                    </label>
                </td>

                <td class="task__file">
                    <a class="download-link" href="#"><?=strip_tags($task_item['category_name']);?>.psd</a>
                </td>

                <td class="task__date"><?=strip_tags($task_item['due_date']);?></td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
